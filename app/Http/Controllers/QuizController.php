<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;

class QuizController extends Controller

{
// POST /quiz — Create a quiz (Lecturer only)
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'title'           => 'required|string|max:150',
            'target_category' => 'required|string|max:100',
            'Publish_time'    => 'required',
            'Duration'        => 'required|integer|min:1',
        ]);

        // Make sure only lecturers can create quizzes
        //if ($request->user()->role !== 'Lecturer') {
         //   return response()->json([
         //       'message' => 'Only lecturers can create quizzes'
          //  ], 403);
       // }

        // Create the quiz
        $quiz = Quiz::create([
            'lecturer_id'     => 1,
            'title'           => $request->title,
            'target_category' => $request->target_category,
            'Publish_time'    => $request->Publish_time,
            'Duration'=> $request->Duration,
        ]);

        return response()->json([
            'message' => 'Quiz created successfully',
            'quiz'    => $quiz
        ], 201);
    }

    // POST /quiz/{id}/questions — Add questions to a quiz
    public function addQuestions(Request $request, $id)
    {
        // Find the quiz
        $quiz = Quiz::find($id);
        if (!$quiz) {
            return response()->json([
                'message' => 'Quiz not found'
            ], 404);
        }

        // Make sure only the lecturer who created it can add questions
        if ($request->user()->id !== $quiz->lecturer_id) {
            return response()->json([
                'message' => 'You can only add questions to your own quiz'
            ], 403);
        }

        // Validate questions
        $request->validate([
            'questions'                 => 'required|array|min:1',
            'questions.*.Question'      => 'required|string',
            'questions.*.Options'       => 'required|string',
            'questions.*.Correct_answer'=> 'required|string',
            'questions.*.Marks'         => 'required|integer|min:1',
        ]);

        // Save each question
        $saved = [];
        foreach ($request->questions as $q) {
            $saved[] = QuizQuestion::create([
                'Quiz_id'        => $quiz->id,
                'Question'       => $q['Question'],
                'Options'        => $q['Options'],
                'Correct_answer' => $q['Correct_answer'],
                'Marks'          => $q['Marks'],
            ]);
        }

        return response()->json([
            'message'   => 'Questions added successfully',
            'questions' => $saved
        ], 201);
    }

}