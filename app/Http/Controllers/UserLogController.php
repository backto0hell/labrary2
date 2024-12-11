<?php

namespace App\Http\Controllers;

use App\Models\ChangeLog;
use App\Models\User;
use App\DTO\ChangeLogDTO;
use App\DTO\ChangeLogCollectionDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserLogController extends Controller
{
    use AuthorizesRequests;

    public function getUserChangeLog($id): JsonResponse
    {
        $this->authorize('getStory', User::class);

        $logs = ChangeLog::where('entity_type', 'User')->where('entity_id', $id)->get();

        $logDTOs = $logs->map(function ($log) {
            return new ChangeLogDTO(
                $log->entity_type,
                $log->entity_id,
                $log->old_value,
                $log->new_value,
                $log->mutated_by,
                $log->created_by,
                $log->created_at
            );
        });

        return response()->json(new ChangeLogCollectionDTO($logDTOs->toArray()));
    }
}
