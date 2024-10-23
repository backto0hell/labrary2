<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', function () {
    return response()->json(['error' => 'Unauthenticated'], 401);
})->name('login');