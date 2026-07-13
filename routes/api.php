<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DirectMessageController;
use App\Http\Controllers\WarningController;
use App\Http\Controllers\BlacklistController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'apiLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/messages/send', [MessageController::class, 'send'])->middleware('not_blacklisted');
    Route::get('/messages/group/{groupId}', [MessageController::class, 'getMessages']);
    Route::post('/direct-messages/send', [DirectMessageController::class, 'send'])->middleware('not_blacklisted');
    Route::get('/direct-messages/{userId}', [DirectMessageController::class, 'getConversation']);

    Route::post('/warnings', [WarningController::class, 'issue']);
    Route::get('/warnings', [WarningController::class, 'index']);

    Route::get('/blacklist', [BlacklistController::class, 'index']);
    Route::post('/blacklist/{blacklistId}/lift', [BlacklistController::class, 'lift']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);
});
