<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class RolesAndPermissions extends Model
{
    use SoftDeletes;
    protected $casts = [
        'created_by' => 'integer',
    ];
}
