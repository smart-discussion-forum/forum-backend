<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\QuizAttemptController;
use App\Http\Controllers\WarningController;
use App\Http\Controllers\BlacklistController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DirectMessageController;
use App\Http\Controllers\RecommendationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'apiLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/messages/send', [MessageController::class, 'send'])->middleware('not_blacklisted');
    Route::get('/messages/group/{groupId}', [MessageController::class, 'getMessages']);

    // Direct messages
    Route::post('/direct-messages/send', [DirectMessageController::class, 'send'])->middleware('not_blacklisted');
    Route::get('/direct-messages/{userId}', [DirectMessageController::class, 'getConversation']);

    // Moderation: warnings & blacklist
    Route::post('/warnings', [WarningController::class, 'issue']);
    Route::get('/warnings', [WarningController::class, 'index']);
    Route::get('/blacklist', [BlacklistController::class, 'index']);
    Route::post('/blacklist/{blacklistId}/lift', [BlacklistController::class, 'lift']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);

    // Recommendations
    Route::get('/recommendations', [RecommendationController::class, 'apiIndex']);

    // Quiz attempts
    Route::post('/quiz/{id}/attempt', [QuizAttemptController::class, 'startAttempt']);
    Route::post('/quiz/attempt/{attemptId}/answer', [QuizAttemptController::class, 'submitAnswer']);
    Route::post('/quiz/attempt/{attemptId}/submit', [QuizAttemptController::class, 'submitFullAttempt']);
    Route::get('/quiz/attempt/{attemptId}/results', [QuizAttemptController::class, 'studentResults']);
    Route::get('/quiz/{quizId}/results', [QuizAttemptController::class, 'lecturerResults']);

    // Topics
    Route::get('/topics/search', [TopicController::class, 'search']);
    Route::get('/groups/{groupId}/topics', [TopicController::class, 'index']);
    Route::post('/groups/{groupId}/topics', [TopicController::class, 'store'])->middleware('not_blacklisted');
    Route::get('/topics/{topicId}', [TopicController::class, 'show']);

    // Posts
    Route::get('/topics/{topicId}/posts', [PostController::class, 'index']);
    Route::post('/topics/{topicId}/posts', [PostController::class, 'store'])->middleware('not_blacklisted');
});