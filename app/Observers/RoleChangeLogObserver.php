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

        $ignoredFields = ['updated_at', 'deleted_at', 'deleted_by'];
        if (!$role->wasChanged(array_diff(array_keys($role->getAttributes()), $ignoredFields))) {
            return;
        }
        $original = $role->getOriginal();
        $oldValue = json_encode($original) ?: 'Nothing';

        if ($role->wasChanged('deleted_at')) {
            return;
        }
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
    public function restored(Role $role)
    {
        ChangeLog::create([
            'entity_type' => 'Role',
            'entity_id' => $role->id,
            'old_value' =>  json_encode(['status' => 'Deleted']),
            'new_value' => json_encode($role->getAttributes()),
            'mutated_by' => Auth::id(),
            'created_by' => Auth::id(),
        ]);
    }
}
