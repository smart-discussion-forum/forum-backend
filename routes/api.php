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
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminStatisticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'apiLogin']);
Route::post('/register', [AuthController::class, 'apiRegister']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/messages/send', [MessageController::class, 'send'])->middleware('not_blacklisted');
    Route::get('/messages/group/{groupId}', [MessageController::class, 'getMessages']);
    Route::post('/quizzes',[\App\Http\Controllers\QuizController::class, 'apiStore']);
   //Browse all groups (mine + joinable)
   Route::get('/groups/browse', [GroupController::class, 'browse']);
   Route::post('/groups/{id}/join', [GroupController::class, 'join']);
   Route::post('/groups/{id}/leave', [GroupController::class, 'leave']);

   // Recommendations
    Route::get('/recommendations', [RecommendationController::class, 'apiIndex']);
   
    // Groups (my groups)
    Route::get('/groups', function (Request $request) {
        return response()->json($request->user()->groups()->orderBy('name')->get());
    });
// Fetch members for a group
    Route::get('/groups/{id}/members', [GroupController::class, 'members']);

    //Quizzes
    Route::get('/quizzes', [\App\Http\Controllers\QuizController::class, 'listCheck']);
    Route::put('/quizzes/{id}', [\App\Http\Controllers\QuizController::class, 'apiUpdate']);
    Route::post('/quizzes/{id}/announce', [\App\Http\Controllers\QuizController::class, 'apiAnnounce']);
    Route::get('/quizzes/{id}/questions', [\App\Http\Controllers\QuizController::class, 'apiQuestions']);
        
    // Direct messages
    Route::post('/direct-messages/send', [DirectMessageController::class, 'send'])->middleware('not_blacklisted');
    Route::get('/direct-messages/{userId}', [DirectMessageController::class, 'getConversation']);

    // Moderation: warnings & blacklist
    Route::post('/warnings', [WarningController::class, 'issue']);
    Route::get('/warnings', [WarningController::class, 'index']);
    Route::get('/blacklist-status', [BlacklistController::class, 'apiStatus']);
    Route::get('/blacklist', [BlacklistController::class, 'index']);
    Route::post('/blacklist/{blacklistId}/lift', [BlacklistController::class, 'lift']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);

    // Quiz attempts
    Route::post('/quiz/{id}/attempt', [QuizAttemptController::class, 'startAttempt']);
    Route::post('/quiz/attempt/{attemptId}/answer', [QuizAttemptController::class, 'submitAnswer']);
    Route::post('/quiz/attempt/{attemptId}/submit', [QuizAttemptController::class, 'submitFullAttempt']);
    Route::get('/quiz/attempt/{attemptId}/results', [QuizAttemptController::class, 'studentResults']);
    Route::get('/quiz/{quizId}/results', [QuizAttemptController::class, 'lecturerResults']);

    // Topics
    Route::get('/topics/search', [TopicController::class, 'search']);
    Route::get('/groups/{groupId}/topics', [TopicController::class, 'index']);
    Route::post('/groups/{groupId}/topics', [TopicController::class, 'store'])->middleware(['lecturer', 'not_blacklisted']);
    Route::get('/topics/{topicId}', [TopicController::class, 'show']);

    // Posts
    Route::get('/topics/{topicId}/posts', [PostController::class, 'index']);
    Route::post('/topics/{topicId}/posts', [PostController::class, 'store'])->middleware('not_blacklisted');

    // Admin: user management (warnings / blacklist) — same controller the
    // webapp uses at /admin/users, exposed here under /api for the desktop
    // app. AdminUserController returns JSON automatically when the request
    // expects it (desktop sends Accept: application/json).
    Route::middleware('admin')->group(function () {
        Route::get('/admin/users', [AdminUserController::class, 'index']);
        Route::post('/admin/users/run-inactivity-check', [AdminUserController::class, 'runInactivityCheck']);
        Route::post('/admin/users/{user}/warn', [AdminUserController::class, 'warn']);
        Route::post('/admin/users/{user}/blacklist', [AdminUserController::class, 'blacklist']);
        Route::post('/admin/users/{user}/reinstate', [AdminUserController::class, 'reinstate']);

        // Admin: overall group statistics — wired for when the desktop
        // "Statistics" screen is hooked up to real data (see note below).
        Route::get('/admin/statistics', [AdminStatisticsController::class, 'index']);
        Route::get('/admin/statistics/{id}', [AdminStatisticsController::class, 'show']);

        // Admin: manage groups — same data/actions as the webapp's Manage
        // Groups page (name, creator, members, topics, edit, delete).
        Route::get('/groups/manage', [GroupController::class, 'manage']);
        Route::put('/groups/{id}', [GroupController::class, 'update']);
        Route::delete('/groups/{id}', [GroupController::class, 'destroy']);
    });
});
