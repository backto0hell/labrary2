<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/out', [AuthController::class, 'logout']);
    Route::get('auth/tokens', [AuthController::class, 'listTokens']);
    Route::post('auth/out_all', [AuthController::class, 'revokeAllTokens']);
});

Route::get('/test', function () {
    return response()->json(['message' => 'It works!']);
});

