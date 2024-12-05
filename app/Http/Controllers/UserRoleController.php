<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    // Метод для получения списка пользователей
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    // Метод для получения ролей пользователя
    public function show($id)
    {
        $user = User::findOrFail($id);
        $roles = $user->roles;
        return response()->json($roles);
    }

    // Метод для присвоения ролей пользователю
    public function store(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->roles()->syncWithoutDetaching($request->roles); // Синхронизация ролей с пользователем

        return response()->json(['message' => 'роль дана']);
    }

    // Метод для жесткого удаления роли у пользователя
    public function destroy($id, $role_id)
    {
        $user = User::findOrFail($id);
        $user->roles()->detach($role_id); // Удаление роли у пользователя
        return response()->json(['message' => 'роль делит']);
    }

    // Метод для мягкого удаления роли у пользователя
    public function softDelete($id, $role_id)
    {
        $user = User::findOrFail($id);
        $role = $user->roles()->findOrFail($role_id);
        $role->pivot->delete(); // Мягкое удаление роли у пользователя
        return response()->json(['message' => 'мягкий жидкий удаление роль']);
    }

    // Метод для восстановления мягко удаленной роли у пользователя
    public function restore($id, $role_id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $role = $user->roles()->withTrashed()->findOrFail($role_id);
        $role->pivot->restore(); // Восстановление роли у пользователя
        return response()->json(['message' => 'востановлении роли юзер пользователь']);
    }
}
