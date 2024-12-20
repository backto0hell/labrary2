<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsersAndRoles extends Model
{
    use SoftDeletes;

    protected $table = 'users_and_roles';
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'deleted_by'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
