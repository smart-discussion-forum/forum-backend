<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

    Route::post('/quiz', [QuizController::class, 'store']);
    Route::post('/quiz/{id}/questions', [QuizController::class, 'addQuestions']);

