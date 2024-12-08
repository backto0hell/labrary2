<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('get-list-user')
            ? Response::allow()
            : Response::deny('У вас нет прав для выполнения этого запроса');
    }

    public function view(User $user)
    {
        return $user->hasPermission('read-user')
            ? Response::allow()
            : Response::deny('У вас нет прав для выполнения этого запроса');
    }

    public function create(User $user)
    {
        return $user->hasPermission('create-user')
            ? Response::allow()
            : Response::deny('У вас нет прав для выполнения этого запроса');
    }

    public function update(User $user)
    {
        return $user->hasPermission('update-user')
            ? Response::allow()
            : Response::deny('У вас нет прав для выполнения этого запроса');
    }

    public function delete(User $user)
    {
        return $user->hasPermission('delete-user')
            ? Response::allow()
            : Response::deny('У вас нет прав для выполнения этого запроса');
    }

    public function restore(User $user)
    {
        return $user->hasPermission('restore-user')
            ? Response::allow()
            : Response::deny('У вас нет прав для выполнения этого запроса');
    }
}
