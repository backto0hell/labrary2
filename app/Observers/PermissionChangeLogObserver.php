<?php

namespace App\Observers;

use App\Models\Permission;
use App\Models\ChangeLog;
use Illuminate\Support\Facades\Auth;

class PermissionChangeLogObserver
{
    public function created(Permission $permission)
    {

        ChangeLog::create([
            'entity_type' => 'Permission',
            'entity_id' => $permission->id,
            'old_value' => 'Created',
            'new_value' => $permission->toJson(),
            'mutated_by' => Auth::id(),
            'created_by' => Auth::id(),
        ]);
    }

    public function updated(Permission $permission)
    {
        $original = $permission->getOriginal();
        $oldValue = json_encode($original) ?: 'Nothing';

        ChangeLog::create([
            'entity_type' => 'Permission',
            'entity_id' => $permission->id,
            'old_value' => $oldValue,
            'new_value' => $permission->toJson(),
            'mutated_by' => Auth::id(),
            'created_by' => Auth::id(),
        ]);
    }

    public function deleted(Permission $permission)
    {
        $oldValue = $permission->toJson();

        ChangeLog::create([
            'entity_type' => 'Permission',
            'entity_id' => $permission->id,
            'old_value' => $oldValue,
            'new_value' => 'Deleted',
            'mutated_by' => Auth::id(),
            'created_by' => Auth::id(),
        ]);
    }
}
