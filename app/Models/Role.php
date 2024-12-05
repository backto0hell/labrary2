<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'created_by',
        'deleted_by'
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'roles_and_permissions');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_and_roles');
    }
}
