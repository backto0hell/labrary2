<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\RolePolicyController;
use App\Http\Controllers\PermissionPolicyController;
use App\Http\Middleware\CheckPermissions;

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    // регистрация, вход и работа с токенами
    Route::get('auth/me', [AuthController::class, 'me']); // инфа обо мне
    Route::post('auth/out', [AuthController::class, 'logout']); // выход с акка
    Route::get('auth/tokens', [AuthController::class, 'listTokens']); // показать все активные токены
    Route::post('auth/out_all', [AuthController::class, 'revokeAllTokens']); // отзыв всех токенов

    // маршруты управления пользователями
    Route::/*middleware(CheckPermissions::class . ':get-list-user')->*/get('ref/user', [UserRoleController::class, 'index']); // список пользователей
    Route::/*middleware(CheckPermissions::class . ':read-user')->*/get('ref/user/{id}/role', [UserRoleController::class, 'show']); // получение ролей пользователя
    Route::/*middleware(CheckPermissions::class . ':update-user')->*/post('ref/user/{id}/role', [UserRoleController::class, 'store']); // присвоение ролей
    Route::/*middleware(CheckPermissions::class . ':delete-user')->*/delete('ref/user/{id}/role/{role_id}', [UserRoleController::class, 'destroy']); // жесткое удаление
    Route::/*middleware(CheckPermissions::class . ':delete-user')->*/delete('ref/user/{id}/role/{role_id}/soft', [UserRoleController::class, 'softDelete']); // мягкое удаление
    Route::/*middleware(CheckPermissions::class . ':restore-user')->*/post('ref/user/{id}/role/{role_id}/restore', [UserRoleController::class, 'restore']); // восстановление мягко удаленной роли

    // маршруты управления ролевой политикой
    Route::/*middleware(CheckPermissions::class . ':get-list-role')->*/get('ref/policy/role', [RolePolicyController::class, 'index']); // список ролей
    Route::/*middleware(CheckPermissions::class . ':read-role')->*/get('ref/policy/role/{id}', [RolePolicyController::class, 'show']); // получение конкретной роли
    Route::/*middleware(CheckPermissions::class . ':create-role')->*/post('ref/policy/role', [RolePolicyController::class, 'store']); // создание роли
    Route::/*middleware(CheckPermissions::class . ':update-role')->*/put('ref/policy/role/{id}', [RolePolicyController::class, 'reload']); // обновление роли
    Route::/*middleware(CheckPermissions::class . ':delete-role')->*/delete('ref/policy/role/{id}', [RolePolicyController::class, 'destroy']); // жесткое удаление
    Route::/*middleware(CheckPermissions::class . ':soft-delete-role')->*/delete('ref/policy/role/{id}/soft', [RolePolicyController::class, 'softDelete']); // мягкое удаление
    Route::/*middleware(CheckPermissions::class . ':restore-role')->*/post('ref/policy/role/{id}/restore', [RolePolicyController::class, 'restore']); // восстановление мягко удаленной роли

    // маршруты управления разрешениями
    Route::/*middleware(CheckPermissions::class . ':get-list-permission')->*/get('ref/policy/permission', [PermissionPolicyController::class, 'index']); // список разрешений
    Route::/*middleware(CheckPermissions::class . ':read-permission')->*/get('ref/policy/permission/{id}', [PermissionPolicyController::class, 'show']); // получение конкретного разрешения
    Route::/*middleware(CheckPermissions::class . ':create-permission')->*/post('ref/policy/permission', [PermissionPolicyController::class, 'store']); // создание разрешения
    Route::/*middleware(CheckPermissions::class . ':update-permission')->*/put('ref/policy/permission/{id}', [PermissionPolicyController::class, 'reload']); // обновление разрешения
    Route::/*middleware(CheckPermissions::class . ':delete-permission')->*/delete('ref/policy/permission/{id}', [PermissionPolicyController::class, 'destroy']); // жесткое удаление разрешения
    Route::/*middleware(CheckPermissions::class . ':soft-delete-permission')->*/delete('ref/policy/permission/{id}/soft', [PermissionPolicyController::class, 'softDelete']); // мягкое удаление разрешения
    Route::/*middleware(CheckPermissions::class . ':restore-permission')->*/post('ref/policy/permission/{id}/restore', [PermissionPolicyController::class, 'restore']); // восстановление мягко удаленной роли

});
