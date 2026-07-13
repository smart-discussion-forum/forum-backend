<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\Quiz;
use App\Models\Submission;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        // Fixed: Quiz has $timestamps = false, so no created_at exists to
        // sort by via latest(). Sorting by Publish_time instead.
        $quizzes = Quiz::orderBy('Publish_time', 'desc')->get();
        return view('quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        if (auth()->user()->role !== RoleEnum::Lecturer) {
            abort(403);
        }

        return view('quizzes.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== RoleEnum::Lecturer) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'duration_minutes' => 'required|integer|min:1',
            'target_category' => 'nullable|string',
            'raw_questions' => 'required|string',
        ]);

        $questions = [];
        foreach (explode("\n", trim($data['raw_questions'])) as $line) {
            if (!trim($line)) continue;
            [$question, $optionsRaw, $correctIndex] = array_map('trim', explode('|', $line));
            $questions[] = [
                'question' => $question,
                'options' => array_map('trim', explode(',', $optionsRaw)),
                'correct_index' => (int) $correctIndex,
            ];
        }

        // NOT FIXED: this still writes fields that don't exist as columns
        // on the real quizzes table. See chat for details.
        Quiz::create([
            'lecturer_id' => auth()->id(),
            'title' => $data['title'],
            'questions' => $questions,
            'start_time' => $data['start_time'],
            'duration_minutes' => $data['duration_minutes'],
            'target_category' => $data['target_category'] ?? null,
            'status' => 'scheduled',
        ]);

        return redirect('/quizzes')->with('success', 'Quiz created.');
    }

    public function announce($id)
    {
        if (auth()->user()->role !== RoleEnum::Lecturer) {
            abort(403);
        }

        // NOT FIXED: announced_at / status columns don't exist on quizzes.
        $quiz = Quiz::findOrFail($id);
        $quiz->update([
            'announced_at' => now(),
            'status' => 'announced',
        ]);

        return back()->with('success', 'Quiz announcement sent.');
    }

    public function show($id)
    {
        // NOT FIXED: start_time / end_time don't exist on quizzes.
        $quiz = Quiz::findOrFail($id);
        $now = now();

        if ($now->lt($quiz->start_time)) {
            return back()->with('success', 'Quiz has not started yet.');
        }
        if ($now->gt($quiz->end_time)) {
            return redirect('/quizzes')->with('success', 'Quiz has closed.');
        }

        return view('quizzes.take', compact('quiz'));
    }

    public function submit(Request $request, $id)
    {
        // NOT FIXED: $quiz->questions and the Submission model don't exist.
        $quiz = Quiz::findOrFail($id);
        $answers = $request->input('answers', []);

        $score = 0;
        foreach ($quiz->questions as $i => $q) {
            if (isset($answers[$i]) && (int) $answers[$i] === $q['correct_index']) {
                $score++;
            }
        }

        $submission = Submission::updateOrCreate(
            ['quiz_id' => $quiz->id, 'student_id' => auth()->id()],
            ['answers' => $answers, 'score' => $score, 'submitted_at' => now()]
        );

        return redirect('/quizzes/results/' . $submission->id);
    }

    public function results($submissionId)
    {
        // NOT FIXED: depends on the same missing Submission model.
        $submission = Submission::with('quiz')->findOrFail($submissionId);
        $quiz = $submission->quiz;
        $total = count($quiz->questions);
        $percent = $total > 0 ? ($submission->score / $total) * 100 : 0;

        $grade = $percent >= 80 ? 'A' : ($percent >= 60 ? 'B' : ($percent >= 40 ? 'C' : 'D'));
        $feedback = $percent >= 60
            ? 'Good job — you passed this quiz.'
            : 'Keep practicing — review the topic materials.';

        return view('quizzes.results', compact('submission', 'quiz', 'grade', 'feedback'));
    }
}
