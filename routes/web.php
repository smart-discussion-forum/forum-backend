<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BlacklistController;

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
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/profile/password', [AuthController::class, 'updatePassword']);
    Route::get('/chat',[ChatController::class,'index'])->name ('chat');
    Route::get('/notifications', [NotificationController::class, 'page'])->name('notifications.index');
    Route::get('/blacklist-status', [BlacklistController::class, 'status'])->name('blacklist.status');
    Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');
    // Groups
// Groups
Route::get('/groups/manage', [GroupController::class, 'manage'])->name('groups.manage')->middleware('lecturer');
Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create')->middleware('lecturer');
Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
Route::post('/groups', [GroupController::class, 'store'])->name('groups.store')->middleware('lecturer');
Route::get('/groups/{id}/statistics', [GroupController::class, 'statistics'])->name('groups.statistics');
Route::get('/groups/{id}/edit', [GroupController::class, 'edit'])->name('groups.edit')->middleware('lecturer');
Route::put('/groups/{id}', [GroupController::class, 'update'])->name('groups.update')->middleware('lecturer');
Route::delete('/groups/{id}', [GroupController::class, 'destroy'])->name('groups.destroy')->middleware('lecturer');
Route::post('/groups/{id}/join', [GroupController::class, 'join'])->name('groups.join');
Route::post('/groups/{id}/leave', [GroupController::class, 'leave'])->name('groups.leave');
Route::get('/groups/{id}', [GroupController::class, 'show'])->name('groups.show');
// Topics, now scoped under a group's chat
    Route::get('/groups/{groupId}/topics', [TopicController::class, 'groupIndex']);
    Route::get('/groups/{groupId}/topics/create', [TopicController::class, 'groupCreate']);
    Route::post('/groups/{groupId}/topics', [TopicController::class, 'groupStore'])->middleware('not_blacklisted');
    Route::get('/groups/{groupId}/topics/{id}', [TopicController::class, 'groupShow']);
    Route::post('/groups/{groupId}/topics/{topicId}/posts', [TopicController::class, 'groupStorePost'])->middleware('not_blacklisted');
    Route::get('/discussions', [TopicController::class, 'index'])->name('discussions.index');
    Route::get('/discussions/{id}', [TopicController::class, 'index'])->name('discussions.show');
    // Quiz Management
Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
Route::get('/quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
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

    // Admin: user management (warnings / blacklist)
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index')->middleware('admin');
    Route::post('/admin/users/{user}/warn', [AdminUserController::class, 'warn'])->name('admin.users.warn')->middleware('admin');
    Route::post('/admin/users/{user}/blacklist', [AdminUserController::class, 'blacklist'])->name('admin.users.blacklist')->middleware('admin');
    Route::post('/admin/users/{user}/reinstate', [AdminUserController::class, 'reinstate'])->name('admin.users.reinstate')->middleware('admin');
});
