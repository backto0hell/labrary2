<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Requests\SoftDeleteRequest;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::whereNull('roles.deleted_at')->get();
        return response()->json($roles);
    }

    public function show($id): JsonResponse
    {
        $this->authorize('view', Role::class);

        $role = Role::whereNull('roles.deleted_at')->with(['permissions' => function ($query) {
            $query->whereNull('permissions.deleted_at')
                ->whereNull('roles_and_permissions.deleted_at');
        }])->findOrFail($id);

        return response()->json($role);
    }

    public function store(CreateRoleRequest $request): JsonResponse
    {
        $this->authorize('create', Role::class);

        $roleData = $request->validated();
        $roleData['created_by'] = Auth::id();

        $role = Role::create($roleData);
        return response()->json(['message' => 'Role created successfully', 'role' => $role], 201);
    }

    public function update(UpdateRoleRequest $request, $id): JsonResponse
    {
        $this->authorize('update', Role::class);

        DB::transaction(function () use ($request, $id) {
            $role = Role::findOrFail($id);
            $role->update(array_merge(
                $request->all(),
                ['created_by' => Auth::id()]
            ));
        });

        return response()->json(['message' => 'Role updated successfully.']);
    }

    public function destroy($id): JsonResponse
    {
        $this->authorize('delete', Role::class);

        DB::transaction(function () use ($id) {
            $role = Role::findOrFail($id);
            $role->forceDelete();
        });

        return response()->json(['message' => 'Role deleted permanently.']);
    }

    public function softDelete(SoftDeleteRequest $request, $id): JsonResponse
    {
        $this->authorize('delete', Role::class);

        DB::transaction(
            function () use ($request, $id) {
                $role = Role::findOrFail($id);
                if ($request->has('roles_id')) {
                    foreach ($request->roles_id as $rolesId) {
                        $role->roles()->updateExistingPivot($rolesId, [
                            'deleted_at' => now(),
                        ]);
                    }
                }
                $role->deleted_by = Auth::id();
                $role->save();
                $role->delete();
            }
        );
        return response()->json(['message' => 'Role and permissions soft-deleted successfully.']);
    }

    public function restore($id): JsonResponse
    {
        $this->authorize('restore', Role::class);

        $role = Role::withTrashed()->find($id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }
        $role->restore();
        $role->deleted_by = null;
        $role->save();
        return response()->json(['message' => 'Role restored successfully']);
    }
}
