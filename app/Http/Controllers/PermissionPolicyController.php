<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Http\Requests\SoftDeletePermission;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionPolicyController extends Controller
{
    // Метод для получения списка разрешений
    public function index()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    // Метод для получения конкретного разрешения
    public function show($id)
    {
        $permission = Permission::findOrFail($id);
        return response()->json($permission);
    }

    // Метод для создания нового разрешения
    public function store(CreatePermissionRequest $request)
    {
        $permission = Permission::create($request->all());
        return response()->json(['message' => 'разрешение создано успешно', 'permission' => $permission], 201);
    }

    // Метод для обновления разрешения
    public function reload(UpdatePermissionRequest $request, $role_id)
    {
        $permission = Permission::findOrFail($role_id);
        $permission->update($request->all());
        return response()->json(['message' => 'обновил разрешение', 'permission' => $permission]);
    }

    // Метод для жесткого удаления разрешения
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return response()->json(['message' => 'удалил разрешение']);
    }

    // Метод для мягкого удаления разрешения
    public function softDelete($id)
    {
        $permission = Permission::findOrFail($id);

        $permission->delete();
        return response()->json(['message' => 'мягкий стул удалил']);
    }

    // Метод для восстановления мягко удаленной роли
    public function restore($id)
    {
        $permission = Permission::withTrashed()->findOrFail($id);
        $permission->restore();
        return response()->json(['message' => 'все все вернул извини']);
    }
}
