<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::orderBy('quiz_id', 'desc')->get();

        return view('quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('quizzes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'target_category' => 'required|string|max:100',
            'start_time' => 'required|date',
            'duration_minutes' => 'required|integer|min:1',
            'raw_questions' => 'required|string',
        ]);

        $quiz = Quiz::create([
            'Lecturer_id' => auth()->id(),
            'Title' => $data['title'],
            'Target_category' => $data['target_category'],
            'Publish_time' => $data['start_time'],
            'Duration' => $data['duration_minutes'],
        ]);

        $lines = array_filter(array_map('trim', explode("\n", $data['raw_questions'])));

       foreach ($lines as $line) {
            $parts = array_map('trim', explode('|', $line));

            if (count($parts) !== 4) {
                continue;
            }

            [$questionText, $optionsCsv, $correctIndex, $marks] = $parts;
            $options = array_map('trim', explode(',', $optionsCsv));

            QuizQuestion::create([
                'quiz_id' => $quiz->quiz_id,
                'Question' => $questionText,
                'Options' => json_encode($options),
                'Correct_answer' => $correctIndex,
                'Marks' => (int) $marks,
            ]);
        }

        return redirect('/quizzes')->with('success', 'Quiz created successfully.');
    }

public function show($id)
{
    $quiz = Quiz::with('questions')->findOrFail($id);
    $isOwner = auth()->id() === $quiz->Lecturer_id;
    $now = now();

    if ($isOwner) {
        return view('quizzes.owner-view', compact('quiz'));
    }

    if ($now->lt($quiz->start_time)) {
        return redirect('/quizzes')->with('error', 'This quiz has not started yet.');
    }

    if ($now->gt($quiz->end_time)) {
        return redirect('/quizzes')->with('error', 'This quiz has already closed.');
    }
if ($now->lt($quiz->start_time)) {
    return redirect('/quizzes')->with('error', 'This quiz has not started yet.');
}

if ($now->gt($quiz->end_time)) {
    return redirect('/quizzes')->with('error', 'This quiz has already closed.');
}

$existingAttempt = QuizAttempt::where('quiz_id', $quiz->quiz_id)
    ->where('Student_id', auth()->id())
    ->first();

if ($existingAttempt) {
    return redirect('/quizzes/results/' . $existingAttempt->id);
}
    $quiz->setRelation('questions', $quiz->questions->map(function ($q) {
        return [
            'question' => $q->Question,
            'options' => $q->options_array,
        ];
    }));

    return view('quizzes.take', compact('quiz'));
}
public function edit($id)
{
    $quiz = Quiz::with('questions')->findOrFail($id);

    if (auth()->id() !== $quiz->Lecturer_id) {
        abort(403, 'You can only edit your own quiz.');
    }

    if ($quiz->status !== 'upcoming') {
        return redirect('/quizzes/' . $quiz->quiz_id)->with('error', 'This quiz can no longer be edited once it has started.');
    }

    return view('quizzes.edit', compact('quiz'));
}

public function update(Request $request, $id)
{
    $quiz = Quiz::findOrFail($id);

    if (auth()->id() !== $quiz->Lecturer_id) {
        abort(403, 'You can only edit your own quiz.');
    }

    if ($quiz->status !== 'upcoming') {
        return redirect('/quizzes/' . $quiz->quiz_id)->with('error', 'This quiz can no longer be edited once it has started.');
    }

    // ...rest stays the same
}
public function submit(Request $request, $id)
{
    $quiz = Quiz::with('questions')->findOrFail($id);

    // Block duplicate submissions
    $existingAttempt = QuizAttempt::where('quiz_id', $quiz->quiz_id)
        ->where('Student_id', auth()->id())
        ->first();

    if ($existingAttempt) {
        return redirect('/quizzes/results/' . $existingAttempt->id);
    }

    $now = now();
    $graceEnd = $quiz->end_time->copy()->addSeconds(10);

    if ($now->lt($quiz->start_time) || $now->gt($graceEnd)) {
        return redirect('/quizzes')->with('error', 'This quiz is not currently open for submissions.');
    }

    $answers = $request->input('answers', []);
    $score = 0;

    foreach ($quiz->questions as $i => $question) {
        $submittedAnswer = $answers[$i] ?? null;
        if ($submittedAnswer !== null && (string) $submittedAnswer === (string) $question->Correct_answer) {
            $score += $question->Marks;
        }
    }

    $attempt = QuizAttempt::create([
        'quiz_id' => $quiz->quiz_id,
        'Student_id' => auth()->id(),
        'Score' => $score,
        'Auto_submitted' => $request->boolean('auto_submitted', false),
    ]);

    try {
        broadcast(new \App\Events\QuizAttemptSubmitted($attempt))->toOthers();
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('Quiz attempt broadcast failed: ' . $e->getMessage());
    }

    return redirect('/quizzes/results/' . $attempt->id);
}
public function submissions($id)
{
    $quiz = Quiz::with('questions')->findOrFail($id);

    if (auth()->id() !== $quiz->Lecturer_id) {
        abort(403, 'You can only view submissions for your own quiz.');
    }

    $submissions = $quiz->attempts()->with('student:id,name')->get();

    return view('quizzes.submissions', compact('quiz', 'submissions'));
}
public function upcomingCheck()
{
    if (auth()->user()->role->value !== 'student') {
        return response()->json(['upcoming' => null]);
    }

    $quiz = Quiz::whereRaw('1=1')
        ->get()
        ->filter(function ($q) {
            return $q->announced_at && now()->lt($q->start_time);
        })
        ->sortBy(fn ($q) => $q->start_time)
        ->first();

    if (!$quiz) {
        return response()->json(['upcoming' => null]);
    }

    return response()->json([
        'upcoming' => [
            'id' => $quiz->quiz_id,
            'title' => $quiz->title,
            'seconds_until_start' => (int) now()->diffInSeconds($quiz->start_time, false),
        ],
    ]);
}
    public function announce($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->markAnnounced();

        return back()->with('success', 'Quiz announced to students.');
    }

    public function results($submissionId)
    {
        $attempt = QuizAttempt::with('quiz.questions')->findOrFail($submissionId);
        $quiz = $attempt->quiz;

        $totalMarks = $quiz->questions->sum('Marks');
        $percentage = $totalMarks > 0 ? ($attempt->Score / $totalMarks) * 100 : 0;

        $grade = $percentage >= 80 ? 'A' : ($percentage >= 60 ? 'B' : ($percentage >= 40 ? 'C' : 'F'));
        $feedback = $percentage >= 60
            ? 'Well done! You have a solid understanding of this topic.'
            : 'Consider reviewing this topic further.';

        $submission = $attempt;

        return view('quizzes.results', compact('submission', 'quiz', 'grade', 'feedback'));
    }
}