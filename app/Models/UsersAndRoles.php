<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsersAndRoles extends Model
{
    use SoftDeletes;
    protected $table = 'users_and_roles';
    protected $fillable = ['role_id', 'permission_id', 'created_by', 'deleted_by'];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'deleted_by',
    ];
    // Связь с моделью Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // Связь с моделью User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
