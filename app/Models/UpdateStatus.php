<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpdateStatus extends Model
{
    protected $table = 'update_status';
    protected $fillable = ['is_updating'];
}

