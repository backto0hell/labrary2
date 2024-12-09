<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RolesAndPermissions extends Model
{
    protected $table = 'roles_and_permissions';
    protected $fillable = ['role_id', 'permission_id', 'created_by', 'deleted_by'];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'deleted_by',
    ];
}
