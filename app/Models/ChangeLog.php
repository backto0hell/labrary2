<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeLog extends Model
{
    use HasFactory;

    protected $table = 'change_logs';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'old_value',
        'new_value',
        'mutated_by',
        'created_by',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'deleted_by'
    ];
}
