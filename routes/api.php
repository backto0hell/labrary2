<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionLogController;
use App\Http\Controllers\RoleLogController;
use App\Http\Controllers\UserLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\TwoFactorAuthController;

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

// 2FA
Route::post('/toggle-2fa', [TwoFactorAuthController::class, 'toggle2FA']); // подключение двухфакторки
Route::post('/send-2fa-code', [TwoFactorAuthController::class, 'send2FACode']); // повторная отправка кода
Route::post('/verify-2fa', [TwoFactorAuthController::class, 'verify2FACode']); // подтверждение входа

Route::middleware('auth:sanctum')->group(function () {
    // регистрация, вход и работа с токенами
    Route::get('auth/me', [AuthController::class, 'me']); // инфа обо мне
    Route::post('auth/out', [AuthController::class, 'logout']); // выход с акка
    Route::get('auth/tokens', [AuthController::class, 'listTokens']); // показать все активные токены
    Route::post('auth/out_all', [AuthController::class, 'revokeAllTokens']); // отзыв всех токенов
    Route::put('auth/update', [AuthController::class, 'updateAccount']); // изменение данных аккаунта

    // Маршруты управления ролевой политикой (Роли)
    Route::get('ref/policy/role', [RoleController::class, 'index']);
    Route::get('ref/policy/role/{id}', [RoleController::class, 'show']);
    Route::post('ref/policy/role', [RoleController::class, 'store']);
    Route::put('ref/policy/role/{id}', [RoleController::class, 'update']);
    Route::delete('ref/policy/role/{id}', [RoleController::class, 'destroy']);
    Route::delete('ref/policy/role/{id}/soft', [RoleController::class, 'softDelete']);
    Route::post('ref/policy/role/{id}/restore', [RoleController::class, 'restore']);

    // Маршруты управления ролевой политикой (Разрешения)
    Route::get('ref/policy/permission', [PermissionController::class, 'index']);
    Route::get('ref/policy/permission/{id}', [PermissionController::class, 'show']);
    Route::post('ref/policy/permission', [PermissionController::class, 'store']);
    Route::put('ref/policy/permission/{id}', [PermissionController::class, 'update']);
    Route::delete('ref/policy/permission/{id}', [PermissionController::class, 'destroy']);
    Route::delete('ref/policy/permission/{id}/soft', [PermissionController::class, 'softDelete']);
    Route::post('ref/policy/permission/{id}/restore', [PermissionController::class, 'restore']);

    // Маршруты управления ролями пользователей 
    Route::get('ref/user', [UserRoleController::class, 'index']);
    Route::get('ref/user/{id}/role', [UserRoleController::class, 'show']);
    Route::post('ref/user/{id}/role', [UserRoleController::class, 'store']);
    Route::delete('ref/user/{id}/role/{roleId}', [UserRoleController::class, 'destroy']);
    Route::delete('ref/user/{id}/role/{roleId}/soft', [UserRoleController::class, 'softDelete']);
    Route::post('ref/user/{id}/role/{roleId}/restore', [UserRoleController::class, 'restore']);

    // Маршруты логирования
    Route::get('/ref/user/{id}/story', [UserLogController::class, 'getUserChangeLog']);
    Route::get('/ref/policy/role/{id}/story', [RoleLogController::class, 'getRoleChangeLog']);
    Route::get('/ref/policy/permission/{id}/story', [PermissionLogController::class, 'getPermissionChangeLog']);

    // Восстановление логов
    Route::post('/ref/user/{id}/restore', [AuthController::class, 'restoreFromHistory']);
    Route::post('/ref/role/{id}/restore', [RoleController::class, 'restoreFromHistory']);
    Route::post('/ref/permission/{id}/restore', [PermissionController::class, 'restoreFromHistory']);
});
