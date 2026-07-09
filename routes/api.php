<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TopicController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'apiLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/messages/send', [MessageController::class, 'send']);
    Route::get('/messages/group/{groupId}', [MessageController::class, 'getMessages']);
    Route::get('/topics/search', [TopicController::class, 'search']);
    Route::get('/groups/{groupId}/topics', [TopicController::class, 'index']);
    Route::post('/groups/{groupId}/topics', [TopicController::class, 'store']);
    Route::get('/topics/{topicId}', [TopicController::class, 'show']);
    Route::get('/topics/{topicId}/posts', [PostController::class, 'index']);
    Route::post('/topics/{topicId}/posts', [PostController::class, 'store']);
});
