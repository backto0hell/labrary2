<?php

namespace App\Http\Controllers;


use App\Http\Resources\AuthResource;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Метод регистрации пользователя
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'birthday' => $request->birthday,
        ]);

        if ($user) {
            return response()->json([
                'Логин' => $request->username,
                'Почта' => $request->email,
                'Пароль' => $request->password,
            ], 201);
        }
    }

    // Метод авторизации пользователя
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = User::where('username', $request->username)->first();

            // Получаем максимальное количество активных токенов из переменной окружения
            $maxActiveTokens = env('MAX_ACTIVE_TOKENS', 5);


            // Проверяем количество активных токенов у пользователя
            $activeTokensCount = $user->tokens()->where('revoked', false)->count();
            if ($activeTokensCount >= $maxActiveTokens) {
                return response()->json(['error' => 'Превышено максимальное количество активных токенов'], 400);
            }

            if ($user) {
                $token = $user->createToken('AccessToken')->plainTextToken;
                return response()->json([
                    'message' => 'Вы успешно авторизовались',
                    'token' => $token,
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
        $tokenId = $request->tokenId;
        $token = $request->user()->tokens()->find($tokenId);
        if ($token) {
            $token->delete();
            return response()->json(['message' => 'Вы успешно вышли'], 200);
        }
        return response()->json(['error' => 'Укажите токен'], 400);
    }
    // Список авторизованных токенов пользователя
    public function listTokens(Request $request)
    {
        $tokens = $request->user()->tokens->map(function ($token) {
            return [
                'id' => $token->id,
                'Токен' => $token->token,
            ];
        });

        return response()->json($tokens);
    }
    // Ревокация всех токенов пользователя
    public function revokeAllTokens(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Все токены успешно уничтожены'], 200);
    }
}
