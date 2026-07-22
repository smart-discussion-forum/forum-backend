<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', fn() => view('welcome'));
Route::get('/rules', fn() => view('rules'));

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Protected
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', fn() => view('dashboard'));
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/profile/password', [AuthController::class, 'updatePassword']);
    Route::get('/chat',[ChatController::class,'index'])->name ('chat');

    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');
    Route::post('/groups/{group}/join', [GroupController::class, 'join'])->name('groups.join');
    Route::post('/groups/{group}/leave', [GroupController::class, 'leave'])->name('groups.leave');

// Topics, now scoped under a group's chat
    Route::get('/groups/{groupId}/topics', [TopicController::class, 'groupIndex']);
    Route::get('/groups/{groupId}/topics/create', [TopicController::class, 'groupCreate']);
    Route::post('/groups/{groupId}/topics', [TopicController::class, 'groupStore']);
    Route::get('/groups/{groupId}/topics/{id}', [TopicController::class, 'groupShow']);
    Route::post('/groups/{groupId}/topics/{topicId}/posts', [TopicController::class, 'groupStorePost']);
    // Quiz Management
Route::get('/quizzes', [QuizController::class, 'index']);
Route::get('/quizzes/create', [QuizController::class, 'create']);
Route::get('/quizzes/upcoming-check', [QuizController::class, 'upcomingCheck']);
Route::get('/quizzes/list-check', [QuizController::class, 'listCheck']);
Route::get('/quizzes/results/{submissionId}', [QuizController::class, 'results']);
Route::post('/quizzes', [QuizController::class, 'store']);
Route::get('/quizzes/{id}', [QuizController::class, 'show']);
Route::get('/quizzes/{id}/edit', [QuizController::class, 'edit']);
Route::put('/quizzes/{id}', [QuizController::class, 'update']);
Route::get('/quizzes/{id}/submissions', [QuizController::class, 'submissions']);
Route::post('/quizzes/{id}/submit', [QuizController::class, 'submit']);
Route::post('/quizzes/{id}/announce', [QuizController::class, 'announce']);
});
