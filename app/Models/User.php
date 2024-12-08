<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_and_roles', 'user_id', 'role_id')
            ->withPivot('deleted_at', 'created_by', 'created_at', 'updated_at')
            ->wherePivotNull('deleted_at');
    }

    public function permissions()
    {
        return $this->roles()->with('permissions')->get()
            ->flatMap(function ($role) {
                return $role->permissions;
            })->pluck('name')->unique();
    }
    public function hasPermission($permission)
    {
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('code', $permission)) {
                return true;
            }
        }

        return false;
    }
}
