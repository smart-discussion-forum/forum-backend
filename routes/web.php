<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
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

    // CHANGED: was `fn() => view('dashboard')`. DashboardController now
    // picks admin/lecturer/student.blade.php based on auth()->user()->role,
    // so this one route shows a different screen per role.
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/profile/password', [AuthController::class, 'updatePassword']);
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');

    // Discussion Forum
    Route::get('/topics', [TopicController::class, 'index'])->name('topics.index');
    Route::get('/topics/create', [TopicController::class, 'create'])->name('topics.create');
    Route::post('/topics', [TopicController::class, 'store'])->name('topics.store');
    Route::get('/discussions', [TopicController::class, 'discussions'])->name('discussions.index');
    Route::get('/discussions/{id}', [TopicController::class, 'show'])->name('discussions.show');
    Route::get('/topics/{id}', [TopicController::class, 'show'])->name('topics.show');
    Route::post('/topics/{topicId}/posts', [TopicController::class, 'storePost']);
    Route::post('/topics/{topicId}/posts/{postId}/reaction', [TopicController::class, 'toggleReaction']);

    // Quiz Management
    Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
    Route::get('/quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
    Route::post('/quizzes', [QuizController::class, 'store'])->name('quizzes.store');
    Route::get('/quizzes/{id}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quizzes/{id}/submit', [QuizController::class, 'submit']);
    Route::post('/quizzes/{id}/announce', [QuizController::class, 'announce']);
    Route::get('/quizzes/results/{submissionId}', [QuizController::class, 'results'])->name('quizzes.results');

    // Study Groups (RBAC)
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/{id}', [GroupController::class, 'show'])->name('groups.show');
    Route::post('/groups/{id}/leave', [GroupController::class, 'leave']);

    Route::middleware('lecturer')->group(function () {
        Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
        // NEW — you didn't have a create-form route, only the POST. Add a
        // create() method + view on GroupController if lecturers need a
        // form page rather than a modal/inline form on /groups.
        Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    });

    Route::middleware('student')
        ->post('/groups/{id}/join', [GroupController::class, 'join'])
        ->name('groups.join');

    // NEW — Admin-only. Requires an 'admin' middleware alias registered in
    // bootstrap/app.php next to your existing 'lecturer' / 'student' ones.
    // GroupController::statistics() is also new — add it, or point this at
    // wherever your per-group stats logic actually lives.
    Route::middleware('admin')->group(function () {
        Route::get('/groups/{id}/statistics', [GroupController::class, 'statistics'])->name('groups.statistics');
    });
});
