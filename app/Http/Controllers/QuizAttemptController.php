<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuizAttempt;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;

class QuizAttemptController extends Controller
{
    public function startAttempt(Request $request, $quizId)
    {
        $quiz = Quiz::where('quiz_id', $quizId)->firstOrFail();
if (now()->lt($quiz->Publish_time)) {
            return response()->json(['message' => 'This quiz is not yet available.'], 403);
        }

        // Check if the student has already started an attempt for this quiz
        $existingAttempt = QuizAttempt::where('Quiz_id', $quizId)
            ->where('Student_id', $request->user()->id)
            ->first();

        if ($existingAttempt) {
            return response()->json(['message' => 'You have already started this quiz.', 'attempt' => $existingAttempt], 409);
        }

        // Create a new quiz attempt
        $attempt = QuizAttempt::create([
            'Quiz_id' => $quizId,
            'Student_id' => $request->user()->id,
            'started_at' => now(),
            'Score' => 0,
            'Auto_submitted' => false,
        ]);

        return response()->json(['message' => 'Quiz attempt started.', 'attempt' => $attempt], 201);
    }
    public function submitAnswer(Request $request, $attemptId)
    {
        $attempt = QuizAttempt::where('Attempt_id', $attemptId)->firstorFail();
if ($attempt->Student_id !== $request->user()->id) {
            return response()->json(['message' => 'You are not authorized to submit this attempt.'], 403);
        }
        // Check if the attempt has already been submitted
        if ($attempt->submitted_at) {
            return response()->json(['message' => 'This attempt has already been submitted.'], 409);
        }
$validated = $request->validate([
    'question_id' => 'required|integer',
    'submitted_answer' => 'required|string',
]);
        // Mark the attempt as submitted save and update for this question  incase the student changes their mind 
$answer = QuizAnswer::updateOrCreate(
        [
            'attempt_id' => $attempt->Attempt_id,
            'question_id' => $validated['question_id'],
        ],
        [
            'submitted_answer' => $validated['submitted_answer'],
        ]
    );

    return response()->json([
        'message' => 'Answer saved.',
        'answer' => $answer,
    ], 200);
}
public function submitFullAttempt(Request $request, $attemptId)
{
    $attempt = QuizAttempt::where('Attempt_id', $attemptId)->firstOrFail();

    if ($attempt->Student_id !== $request->user()->id) {
        return response()->json([
            'message' => 'You are not authorized to submit this attempt.'
        ], 403);
    }

    if ($attempt->submitted_at) {
        return response()->json([
            'message' => 'This attempt has already been submitted.'
        ], 409);
    }

    $answers = QuizAnswer::where('attempt_id', $attempt->Attempt_id)->get();

    $totalScore = 0;

    foreach ($answers as $answer) {
        $question = QuizQuestion::where('Question_id', $answer->question_id)->first();

        if (!$question) {
            continue;
        }

        $isCorrect = $answer->submitted_answer === $question->Correct_answer;

        $answer->is_correct = $isCorrect;
        $answer->save();

        if ($isCorrect) {
            $totalScore += $question->Marks;
        }
    }

    $attempt->Score = $totalScore;
    $attempt->submitted_at = now();
    $attempt->save();

    return response()->json([
        'message' => 'Attempt submitted and marked.',
        'score' => $totalScore,
        'attempt' => $attempt,
    ], 200);
}
public function studentResults(Request $request, $attemptId)
{
    $attempt = QuizAttempt::where('Attempt_id', $attemptId)->firstOrFail();

    if ($attempt->Student_id !== $request->user()->id) {
        return response()->json([
            'message' => 'You are not authorized to view this attempt.'
        ], 403);
    }

    if (!$attempt->submitted_at) {
        return response()->json([
            'message' => 'This attempt has not been submitted yet.'
        ], 409);
    }

    $answers = QuizAnswer::where('attempt_id', $attempt->Attempt_id)->get();

    $feedback = $answers->map(function ($answer) {
        $question = QuizQuestion::where('Question_id', $answer->question_id)->first();

        return [
            'question' => $question->Question ?? null,
            'submitted_answer' => $answer->submitted_answer,
            'correct_answer' => $question->Correct_answer ?? null,
            'is_correct' => (bool) $answer->is_correct,
            'marks_awarded' => $answer->is_correct ? $question->Marks : 0,
        ];
    });

    return response()->json([
        'score' => $attempt->Score,
        'submitted_at' => $attempt->submitted_at,
        'auto_submitted' => (bool) $attempt->Auto_submitted,
        'feedback' => $feedback,
    ], 200);
}
public function lecturerResults(Request $request, $quizId)
{
    $quiz = Quiz::where('Quiz_id', $quizId)->firstOrFail();

    // Only the lecturer who owns this quiz can view all attempts
    if ($quiz->Lecturer_id != $request->user()->id) {
        return response()->json([
            'message' => 'You are not authorized to view results for this quiz.'
        ], 403);
    }

    $attempts = QuizAttempt::where('Quiz_id', $quizId)
        ->whereNotNull('submitted_at')
        ->get();

    $results = $attempts->map(function ($attempt) {
        return [
            'Attempt_id' => $attempt->Attempt_id,
            'Student_id' => $attempt->Student_id,
            'Score' => $attempt->Score,
            'Auto_submitted' => (bool) $attempt->Auto_submitted,
            'submitted_at' => $attempt->submitted_at,
        ];
    });

    return response()->json([
        'quiz_title' => $quiz->Title,
        'total_attempts' => $results->count(),
        'results' => $results,
    ], 200);
}
}
