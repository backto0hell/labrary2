<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use App\Models\ChangeLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        DB::beginTransaction();

        try {
            $permissionData = $request->validated();
            $permissionData['created_by'] = Auth::id();

            $permission = Permission::create($permissionData);

            DB::commit();
            return response()->json(['message' => 'Permission created successfully', 'permission' => $permission], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Permission creation failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(UpdatePermissionRequest $request, $id): JsonResponse
    {
        $this->authorize('update', Permission::class);

        DB::beginTransaction();

        try {
            $permission = Permission::findOrFail($id);
            $permission->update(array_merge(
                $request->all(),
                ['created_by' => Auth::id()]
            ));

            DB::commit();
            return response()->json(['message' => 'Permission updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Permission update failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        $this->authorize('delete', Permission::class);

        DB::beginTransaction();

        try {
            $permission = Permission::findOrFail($id);
            $permission->forceDelete();

            DB::commit();
            return response()->json(['message' => 'Permission permanently deleted.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Permission deletion failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function softDelete($id): JsonResponse
    {
        $this->authorize('delete', Permission::class);

        DB::beginTransaction();

        try {
            $permission = Permission::findOrFail($id);
            $permission->updateQuietly(['deleted_by' => Auth::id()]);
            $permission->delete();

            DB::commit();
            return response()->json(['message' => 'Permission soft deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Permission soft deletion failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function restoreFromHistory(Request $request, $id): JsonResponse
    {
        $this->authorize('update', Permission::class);

        DB::beginTransaction();

        try {
            $changeLog = ChangeLog::findOrFail($request->input('log_id'));

            $role = Permission::findOrFail($id);
            $role->update(json_decode($changeLog->old_value, true));

            DB::commit();

            return response()->json(['message' => 'Role restored successfully', 'role' => $role]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Role restoration failed.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
