<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HookLog extends Model
{
    use HasFactory;

    protected $table = 'hook_logs';
    protected $fillable = [
        'ip_address',
        'action',
        'details',
    ];
}
