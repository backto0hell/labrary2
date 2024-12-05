<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class CheckPermissions
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $userId = $request->attributes->get('id');

        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::find($userId);

        if ($user->hasPermission($permission)) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Forbidden: You do not have the required permission.',
            'required_permission' => $permission
        ], 403);
    }
}
