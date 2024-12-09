<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use App\Models\RolesAndPermissions;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    use AuthorizesRequests;

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Permission::class);

        $permissions = Permission::whereNull('deleted_at')->get();
        return response()->json($permissions);
    }

    public function show($id): JsonResponse
    {
        $this->authorize('view', Permission::class);

        $permission = Permission::whereNull('deleted_at')->findOrFail($id);
        return response()->json($permission);
    }

    public function store(CreatePermissionRequest $request): JsonResponse
    {
        $this->authorize('create', Permission::class);

        $permissionData = $request->validated();
        $permissionData['created_by'] = Auth::id();

        $permission = Permission::create($permissionData);
        return response()->json(['message' => 'Permission created successfully', 'permission' => $permission], 201);
    }

    public function update(UpdatePermissionRequest $request, $id): JsonResponse
    {
        $this->authorize('update', Permission::class);

        DB::transaction(function () use ($request, $id) {
            $permission = Permission::findOrFail($id);
            $permission->update(array_merge(
                $request->all(),
                ['created_by' => Auth::id()]
            ));
        });

        return response()->json(['message' => 'Permission updated successfully.']);
    }

    public function destroy($id): JsonResponse
    {
        $this->authorize('delete', Permission::class);

        DB::transaction(function () use ($id) {
            $permission = Permission::findOrFail($id);
            $permission->forceDelete();
        });

        return response()->json(['message' => 'Permission permanently deleted.']);
    }

    public function softDelete($id): JsonResponse
    {
        $this->authorize('delete', Permission::class);

        DB::transaction(function () use ($id) {
            $permission = Permission::findOrFail($id);
            $permission->updateQuietly(['deleted_by' => Auth::id()]);
            $permission->delete();
        });

        return response()->json(['message' => 'Permission soft deleted successfully.']);
    }

    public function restore($id): JsonResponse
    {
        $this->authorize('restore', Permission::class);

        $permission = Permission::withTrashed()->find($id);
        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 404);
        }
        $permission->restore();
        $permission->deleted_by = null;
        $permission->save();
        return response()->json(['message' => 'Permission restored successfully']);
    }


    public function assignPermissionsToRole(Request $request, Role $role)
    {
        $this->authorize('updateRolePermission', Permission::class);

        $userId = Auth::id();
        $permissionIds = $request->input('permissions');

        foreach ($permissionIds as $permissionId) {
            DB::table('roles_and_permissions')->insert([
                'role_id' => $role->id,
                'permission_id' => $permissionId,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Permissions assigned successfully']);
    }

    public function getUserPermissions(User $user)
    {

        $this->authorize('showPermissionsUser', Permission::class);

        $roles = $user->roles()->with('permissions')->get();

        $permissions = $roles->flatMap(function ($role) {
            return $role->permissions;
        })->unique('id')->values();

        return response()->json([
            'roles' => $roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }),
            'permissions' => $permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ];
            }),
        ], 200);
    }


    public function removePermissionsFromRole(Request $request, Role $role)
    {
        $this->authorize('deleteRolePermission', Permission::class);

        $permissions = $request->input('permissions');
        if (!$permissions || !is_array($permissions)) {
            return response()->json(['error' => 'Invalid permissions format'], 400);
        }

        RolesAndPermissions::where('role_id', $role->id)
            ->whereIn('permission_id', $permissions)
            ->delete();

        return response()->json(['message' => 'Permissions removed successfully'], 200);
    }
}
