<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;


class PermissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('get-list-permission')
            ? Response::allow()
            : Response::deny('У вас нет прав для выполнения этого действия');
    }

    public function view(User $user)
    {
        return $user->hasPermission('read-permission')
            ? Response::allow()
            : Response::deny('У вас нет прав для выполнения этого действия');
    }

    public function create(User $user)
    {
        return $user->hasPermission('create-permission')
            ? Response::allow()
            : Response::deny('У вас нет прав для выполнения этого действия');
    }

    public function update(User $user)
    {
        return $user->hasPermission('update-permission')
            ? Response::allow()
            : Response::deny('У вас нет прав для выполнения этого действия');
    }

    public function delete(User $user)
    {
        return $user->hasPermission('delete-permission')
            ? Response::allow()
            : Response::deny('У вас нет прав для выполнения этого действия');
    }

    public function restore(User $user)
    {
        return $user->hasPermission('restore-permission')
            ? Response::allow()
            : Response::deny('У вас нет прав для выполнения этого действия');
    }
    public function showPermissionsUser(User $user)
    {
        return $user->hasPermission('my-permissions')
            ? Response::allow()
            : Response::deny('У вас нет прав для выполнения этого действия');
    }
    public function updateRolePermission(User $user)
    {

        return $user->hasPermission('update-role-permissions')
            ? Response::allow()
            : Response::deny('У вас нет прав для выполнения этого действия');
    }
    public function deleteRolePermission(User $user)
    {
        return $user->hasPermission('delete-role-permissions')
            ? Response::allow()
            : Response::deny('У вас нет прав для выполнения этого действия');
    }
}
