<?php

namespace App\Observers;

use App\Models\Role;
use App\Models\ChangeLog;
use Illuminate\Support\Facades\Auth;

class RoleChangeLogObserver
{
    public function created(Role $role)
    {

        ChangeLog::create([
            'entity_type' => 'Role',
            'entity_id' => $role->id,
            'old_value' => 'Created',
            'new_value' => $role->toJson(),
            'mutated_by' => Auth::id(),
            'created_by' => Auth::id(),
        ]);
    }

    public function updated(Role $role)
    {
        $original = $role->getOriginal();
        $oldValue = json_encode($original) ?: 'Nothing'; // Пустой JSON, если значение null

        ChangeLog::create([
            'entity_type' => 'Role',
            'entity_id' => $role->id,
            'old_value' => $oldValue,
            'new_value' => $role->toJson(),
            'mutated_by' => Auth::id(),
            'created_by' => Auth::id(),
        ]);
    }

    public function deleted(Role $role)
    {
        $oldValue = $role->toJson();

        ChangeLog::create([
            'entity_type' => 'Role',
            'entity_id' => $role->id,
            'old_value' => $oldValue,
            'new_value' => 'Deleted',
            'mutated_by' => Auth::id(),
            'created_by' => Auth::id(),
        ]);
    }
}
