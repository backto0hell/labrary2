<?php

namespace App\Providers;

use App\Models\HookLog;
use App\Observers\GitHookLogObserver;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Observers\UserChangeLogObserver;
use App\Observers\RoleChangeLogObserver;
use App\Observers\PermissionChangeLogObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }
    public function boot(): void
    {
        Validator::extendImplicit('password', function ($attribute, $value, $parameters, $validator) {
            return false;
        });
        User::observe(UserChangeLogObserver::class);
        Role::observe(RoleChangeLogObserver::class);
        Permission::observe(PermissionChangeLogObserver::class);
        HookLog::observe(GitHookLogObserver::class);
    }
}
