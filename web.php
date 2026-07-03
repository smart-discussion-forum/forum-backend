<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', fn() => view('welcome'));
Route::get('/rules', fn() => view('rules'));

Route::get('/register', fn() => view('auth.register'));
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Protected
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', fn() => view('dashboard'));

    // Groups
    Route::get('/groups', [GroupController::class, 'index']);
    Route::post('/groups', [GroupController::class, 'store']);
    Route::get('/groups/{group}', [GroupController::class, 'show']);
    Route::post('/groups/{group}/join', [GroupController::class, 'join'])->middleware('student');
    Route::post('/groups/{group}/leave', [GroupController::class, 'leave']);

    // Discussion Forum
    Route::get('/topics', [TopicController::class, 'index']);
    Route::get('/topics/create', [TopicController::class, 'create']);
    Route::post('/topics', [TopicController::class, 'store']);
    Route::get('/topics/{id}', [TopicController::class, 'show']);
    Route::post('/topics/{topicId}/posts', [TopicController::class, 'storePost']);

    // Quiz Management
    Route::get('/quizzes', [QuizController::class, 'index']);
    Route::get('/quizzes/create', [QuizController::class, 'create']);
    Route::post('/quizzes', [QuizController::class, 'store']);
    Route::get('/quizzes/{id}', [QuizController::class, 'show']);
    Route::post('/quizzes/{id}/submit', [QuizController::class, 'submit']);
    Route::get('/quizzes/results/{submissionId}', [QuizController::class, 'results']);
});
