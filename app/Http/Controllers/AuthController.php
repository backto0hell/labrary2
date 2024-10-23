<?php

namespace App\Http\Controllers;


use App\Http\Resources\AuthResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Метод регистрации пользователя
    public function register(Request $request)
    {
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($user) {
            return response()->json($user, 201);
        }

        return response()->json(['error' => 'Регистрация не была пройдена успешно, ошибка при вводе данных'], 400);
    }

    // Метод авторизации пользователя
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = User::where('username', $request->username)->first();

            if ($user) {
                $token = $user->createToken('AccessToken')->plainTextToken;
                return response()->json([
                    'message' => 'Вы успешно авторизовались',
                    'token' => $token
                ], 200);
            }
        }

        return response()->json(['error' => 'Авторизация не была пройдена, ошибка при вводе данных'], 401);
    }

    // Получение информации об авторизованном пользователе
    public function me(Request $request)
    {
        return new AuthResource($request->user());
    }
    // Разлогирование пользователя
    public function logout(Request $request)
    {
        $request->user()->tokens()->where('id', $request->tokenId)->delete();
        return response()->json(['message' => 'Вы успешно вышли'], 200);
    }
    // Список авторизованных токенов пользователя
    public function listTokens(Request $request)
    {
        return response()->json($request->user()->tokens);
    }
    // Ревокация всех токенов пользователя
    public function revokeAllTokens(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Все токены успешно уничтожены'], 200);
    }
}
