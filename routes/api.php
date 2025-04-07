<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::post('/login-user', [AuthController::class, 'loginuser']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/send-message', [ChatController::class, 'sendMessage'])->name('sendmessage');
    Route::post('/fetch-messages', [ChatController::class, 'fetchMessages'])->name('fetch.messages');
});

