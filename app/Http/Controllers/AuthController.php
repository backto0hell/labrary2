<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\ChangeLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

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

    // Метод авторизации пользователя
    public function login(LoginRequest $request)
    {
        DB::beginTransaction();

        try {
            $credentials = $request->only('username', 'password');

            if (Auth::attempt($credentials)) {
                $user = User::where('username', $request->username)->first();

                $maxActiveTokens = env('MAX_ACTIVE_TOKENS', 5);

                $activeTokensCount = $user->tokens()->where('revoked', false)->count();
                if ($activeTokensCount >= $maxActiveTokens) {
                    DB::rollBack(); // Откатываем транзакцию при превышении лимита токенов
                    return response()->json(['error' => 'Превышено максимальное количество активных токенов'], 400);
                }

                if ($user) {
                    $token = $user->createToken('AccessToken')->plainTextToken;
                    DB::commit(); // Фиксируем транзакцию, если все прошло успешно
                    return response()->json([
                        'message' => 'Вы успешно авторизовались',
                        'token' => $token,
                    ], 200);
                }
            }

            DB::rollBack();
            return response()->json(['error' => 'Авторизация не была пройдена, ошибка при вводе данных'], 401);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при авторизации'], 500);
        }
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
