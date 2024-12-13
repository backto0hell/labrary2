<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UsersAndRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserRoleController extends Controller
{
    use AuthorizesRequests;
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $usersAndRoles = UsersAndRoles::whereNull('deleted_at')->get();
        return response()->json($usersAndRoles);
    }

    public function show($user_id): JsonResponse
    {
        $this->authorize('view', User::class);

        $userRoles = UsersAndRoles::where('user_id', $user_id)
            ->whereNull('deleted_at')
            ->with(['role'])
            ->get();

        $roles = [];
        foreach ($userRoles as $userRole) {
            $role = $userRole->role;
            if ($role) {
                $roles[] = [
                    'user_name' => $userRole->user->username,
                    'role_name' => $role->name,
                ];
            }
        }

        return response()->json($roles);
    }

    public function store(Request $request, $id): JsonResponse
    {
        $this->authorize('create', User::class);

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'role_id' => 'required|array',
            'role_id.*' => 'exists:roles,id'
        ]);

        $createdBy = Auth::id();

        $user->roles()->syncWithoutDetaching(array_fill_keys($validated['role_id'], [
            'created_by' => $createdBy,
            'created_at' => now(),
            'updated_at' => now()
        ]));

        return response()->json(['message' => 'Роль успешно выдана']);
    }

    public function destroy($id, $roleId): JsonResponse
    {
        $this->authorize('delete', User::class);

        $user = User::findOrFail($id);
        $user->roles()->detach($roleId);

        return response()->json(['message' => 'Роль успешно удалена']);
    }

    public function softDelete($userId, $roleId): JsonResponse
    {
        $this->authorize('delete', User::class);

        $user = User::findOrFail($userId);

        $user->roles()->updateExistingPivot($roleId, [
            'deleted_at' => now(),
            'deleted_by' => Auth::id()
        ]);

        return response()->json(['message' => 'Роль мягко удалена у пользователя']);
    }

    public function restore($id, $roleId): JsonResponse
    {
        $this->authorize('restore', User::class);

        $userRole = UsersAndRoles::withTrashed()
            ->where('user_id', $id)
            ->where('role_id', $roleId)
            ->firstOrFail();

        $userRole->restore();
        $userRole->deleted_by = null;
        $userRole->save();

        return response()->json(['message' => 'Роль для пользователя успешно восстановлена']);
    }
}
