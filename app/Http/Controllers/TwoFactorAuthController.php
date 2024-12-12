<?php


namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\TwoFACodeMail;

class TwoFactorAuthController extends Controller
{

    public function send2FACode(Request $request)
    {
        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Пользователь не найден'], 404);
        }
        $requestCount = $user->two_fa_attempts ?? 0;
        $lastRequestTime = $user->two_fa_last_request_at;

        if ($requestCount >= 3 && $lastRequestTime && Carbon::parse($lastRequestTime)->addSeconds(30)->isFuture()) {
            return response()->json(['error' => 'Слишком много запросов, попробуйте позже.'], 429);
        }

        $twoFACode = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        DB::table('users')->where('email', $request->email)->update([
            'two_fa_code' => $twoFACode,
            'two_fa_expires_at' => $expiresAt,
            'two_fa_last_request_at' => now(),
            'two_fa_attempts' => $requestCount + 1,
        ]);

        Mail::to($user->email)->send(new TwoFACodeMail($twoFACode));

        return response()->json(['message' => 'Код отправлен на вашу почту']);
    }


    public function verify2FACode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|numeric',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Пользователь не найден'], 404);
        }

        if ($user->two_fa_code !== (int) $request->code) {
            return response()->json(['error' => 'Неверный код'], 403);
        }

        if ($user->two_fa_expires_at < now()) {
            return response()->json(['error' => 'Код устарел'], 403);
        }

        DB::table('users')->where('id', $user->id)->update([
            'two_fa_code' => null,
            'two_fa_expires_at' => null
        ]);

        $token = $user->createToken('AccessToken')->plainTextToken;

        return response()->json([
            'message' => 'Успешная авторизация',
            'token' => $token
        ]);
    }
}
