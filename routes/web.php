<?php

use App\Http\Controllers\GitHookController;
use App\Http\Controllers\GitHubWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', function () {
    return 'Вы не авторизованы';
})->name('login');

Route::post('/hooks/git', [GitHookController::class, 'validateSecretKey']);

Route::post('/git-webhook', [GitHubWebhookController::class, 'handleWebhook']);
