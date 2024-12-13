<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Mail\TwoFACodeMail;
use App\Models\ChangeLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    use AuthorizesRequests;
    // Метод регистрации пользователя
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'birthday' => $request->birthday,
            ]);

            if ($user) {
                DB::commit();
                return response()->json([
                    'log-in' => $request->username,
                    'email' => $request->email,
                    'password' => $request->password,
                ], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при регистрации пользователя'], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        // Валидация данных
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = User::where('username', $request->username)->first();
            // проверка на наличие 2FA (если да)
            if ($user->is_2fa_enabled) {
                $code = rand(100000, 999999);
                DB::table('users')->where('id', $user->id)->update([
                    'two_fa_code' => $code,
                    'two_fa_expires_at' => now()->addMinutes(10),
                ]);
                Mail::to($user->email)->send(new TwoFACodeMail($code));

                return response()->json(['message' => '2FA код отправлен на вашу почту. Пожалуйста, введите код.']);
            }
            //если нет
            $maxActiveTokens = env('MAX_ACTIVE_TOKENS', 5);
            $activeTokensCount = $user->tokens()->where('revoked', false)->count();
            if ($activeTokensCount >= $maxActiveTokens) {
                DB::rollBack();
                return response()->json(['error' => 'Превышено максимальное количество активных токенов'], 400);
            }

            $token = $user->createToken('AccessToken')->plainTextToken;
            return response()->json(['message' => 'Успешная авторизация', 'token' => $token]);
        }

        return response()->json(['error' => 'Неверные учетные данные'], 401);
    }

    public function toggle2FA(Request $request)
    {
        $user = User::where('username', $request->username)->first();
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        // Проверка пароля для подтверждения смены 2FA
        if (!Hash::check($request->password, hashedValue: $user->password)) {
            return response()->json(['error' => 'Неверный пароль'], 403);
        }

        // Переключение 2FA
        $user->is_2fa_enabled = !$user->is_2fa_enabled;
        $user->two_fa_code = null;
        $user->two_fa_expires_at = null;
        $user->save();

        return response()->json([
            'message' => $user->is_2fa_enabled ? '2FA включена' : '2FA отключена',
        ]);
    }


    // Получение информации об авторизованном пользователе
    public function me(Request $request)
    {
        return new AuthResource($request->user());
    }

    // Разлогирование пользователя
    public function logout(Request $request)
    {
        DB::beginTransaction();

        try {
            $tokenId = $request->tokenId;
            $token = $request->user()->tokens()->find($tokenId);

            if ($token) {
                $token->delete();
                DB::commit();
                return response()->json(['message' => 'Вы успешно вышли'], 200);
            }

            DB::rollBack();
            return response()->json(['error' => 'Укажите токен'], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при разлогировании'], 500);
        }
    }

    // Список авторизованных токенов пользователя
    public function listTokens(Request $request)
    {
        DB::beginTransaction();

        try {
            $tokens = $request->user()->tokens->map(function ($token) {
                return [
                    'id' => $token->id,
                    'Токен' => $token->token,
                ];
            });

            DB::commit();
            return response()->json($tokens);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при получении списка токенов'], 500);
        }
    }

    // Ревокация всех токенов пользователя
    public function revokeAllTokens(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->user()->tokens()->delete();
            DB::commit();
            return response()->json(['message' => 'Все токены успешно уничтожены'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при удалении токенов'], 500);
        }
    }

    public function updateAccount(UpdateUserRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = $request->user();

            $user->update(array_filter($request->only(['username', 'email', 'birthday'])));

            if ($request->password) {
                $user->password = bcrypt($request->password);
                $user->save();
            }

            DB::commit();
            return response()->json(['message' => 'Данные аккаунта успешно обновлены'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при обновлении данных аккаунта'], 500);
        }
    }

    public function restoreFromHistory(Request $request, $id): JsonResponse
    {
        $this->authorize('update', User::class);

        DB::beginTransaction();

        try {
            // Получение log_id и поиск записи ChangeLog
            $changeLog = ChangeLog::findOrFail($request->input('log_id'));

            // Обновление роли из старых значений
            $userId = User::findOrFail($id);
            $userId->update(json_decode($changeLog->old_value, true));

            DB::commit();

            return response()->json(['message' => 'User restoraiton successfully.', 'user' => $userId]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'User restoration failed.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
