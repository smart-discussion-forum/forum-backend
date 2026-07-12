<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizAttemptController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'apiLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/messages/send', [MessageController::class, 'send']);
    Route::get('/messages/group/{groupId}', [MessageController::class, 'getMessages']);
    Route::post('/quiz/{id}/attempt', [QuizAttemptController::class, 'startAttempt']);
    Route::post('/quiz/attempt/{attemptId}/answer', [QuizAttemptController::class, 'submitAnswer']);
    Route::post('/quiz/attempt/{attemptId}/submit', [QuizAttemptController::class, 'submitFullAttempt']);
    Route::get('/quiz/attempt/{attemptId}/results', [QuizAttemptController::class, 'studentResults']);
    Route::get('/quiz/{quizId}/results', [QuizAttemptController::class, 'lecturerResults']);
});

    