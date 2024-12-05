<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\SoftDeletePermission;

class RolePolicyController extends Controller
{
    // Метод для получения списка ролей
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    // Метод для получения конкретной роли
    public function show($id)
    {
        $role = Role::findOrFail($id);
        return response()->json($role);
    }

    // Метод для создания новой роли
    public function store(CreateRoleRequest $request)
    {
        $role = Role::create($request->all());
        return response()->json(['message' => 'ролька создана', 'role' => $role], 201);
    }

    // Метод для обновления роли
    public function reload(UpdateRoleRequest $request, $role_id)
    {
        $role = Role::findOrFail($role_id);
        $role->update($request->all());
        $roleData = $request->only(['name', 'code', 'description']);

        if (!empty($roleData)) {
            $roleData['created_by'] = (int) $request->attributes->get('userId');
            $role->update($roleData);
        }

        if ($request->has('permission_ids')) {
            $role->permissions()->syncWithoutDetaching(
                $request->permission_ids,
                ['created_by' => (int) $request->attributes->get('userId')]
            );
            return response()->json(['message' => 'роль обновлена', 'role' => $role]);
        }
    }

    // Метод для жесткого удаления роли
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return response()->json(['message' => 'роль удалена жеска']);
    }

    // Метод для мягкого удаления роли
    public function softDelete($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return response()->json(['message' => 'жидко удалил']);
    }

    // Метод для восстановления мягко удаленной роли
    public function restore($id)
    {
        $role = Role::withTrashed()->findOrFail($id);
        $role->restore();
        return response()->json(['message' => 'восстановил жидкий роль']);
    }
}
