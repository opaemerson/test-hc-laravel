<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\AuthApiMiddleware;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthApiController;

// AUTHENTICATE
Route::post('/authenticate', [AuthApiController::class, 'authenticate'])->name('authenticate');

Route::middleware(AuthApiMiddleware::class)->group(function () {
    Route::apiResource('users', UserController::class);
});

