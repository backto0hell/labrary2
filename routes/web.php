<?php

use App\Http\Controllers\GitHookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', function () {
    return 'Вы не авторизованы';
})->name('login');

Route::post('/hooks/git', [GitHookController::class, 'validateSecretKey']);