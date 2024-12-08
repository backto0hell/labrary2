<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
}
