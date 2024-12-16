<?php

namespace App\Observers;

use App\Models\User;
use App\Models\ChangeLog;
use Illuminate\Support\Facades\Auth;

class UserChangeLogObserver
{
    public function created(User $user)
    {
        $oldValue = '{}'; // Пустой JSON, если значение отсутствует

        ChangeLog::create([
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'old_value' => 'Created',
            'new_value' => $user->toJson(),
            'mutated_by' => 1,
            'created_by' => 1,
        ]);
    }

    public function updated(User $user)
    {
        $ignoredFields = ['updated_at', 'deleted_at', 'deleted_by'];
        if (!$user->wasChanged(array_diff(array_keys($user->getAttributes()), $ignoredFields))) {
            return;
        }
        $original = $user->getOriginal();
        $oldValue = json_encode($original) ?: 'Nothing'; // Пустой JSON, если значение null

        ChangeLog::create([
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'old_value' => $oldValue,
            'new_value' => $user->toJson(),
            'mutated_by' => Auth::id(),
            'created_by' => Auth::id(),
        ]);
    }

    public function restored(User $user)
    {
        ChangeLog::create([
            'entity_type' => 'Role',
            'entity_id' => $user->id,
            'old_value' =>  json_encode(['status' => 'Deleted']),
            'new_value' => json_encode($user->getAttributes()),
            'mutated_by' => Auth::id(),
            'created_by' => Auth::id(),
        ]);
    }
}
