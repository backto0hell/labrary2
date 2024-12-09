<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use \App\Http\Middleware\CheckPermission;
use Illuminate\Support\Facades\Auth;


Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    // регистрация, вход и работа с токенами
    Route::get('auth/me', [AuthController::class, 'me']); // инфа обо мне
    Route::post('auth/out', [AuthController::class, 'logout']); // выход с акка
    Route::get('auth/tokens', [AuthController::class, 'listTokens']); // показать все активные токены
    Route::post('auth/out_all', [AuthController::class, 'revokeAllTokens']); // отзыв всех токенов

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

    // Отношения разрешений к ролям
    Route::post('/roles/{role}/permissions', [PermissionController::class, 'assignPermissionsToRole']); // выдать разрешение роли
    Route::get('/users/{user}/permissions', [PermissionController::class, 'getUserPermissions']); // получить список имеющихся разрешений
    Route::delete('/roles/{role}/permissions', [PermissionController::class, 'removePermissionsFromRole']); // удаление разрешений у роли
});
