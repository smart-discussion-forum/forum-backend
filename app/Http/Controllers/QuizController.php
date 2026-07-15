<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;

class QuizController extends Controller
{
public function index()
{
    $user = auth()->user();

    if ($user->role->value === 'Admin') {
        $quizzes = Quiz::orderBy('quiz_id', 'desc')->get();
    } else {
        $myGroupIds = $user->groups()->pluck('groups.id')->map(fn ($id) => (string) $id);
        $quizzes = Quiz::orderBy('quiz_id', 'desc')
            ->get()
            ->filter(function ($q) use ($myGroupIds, $user) {
                $inMyGroup = $myGroupIds->contains((string) $q->group_id);
                $isOwner = $q->Lecturer_id === $user->id;
                $isAnnounced = (bool) $q->announced_at;

                return $inMyGroup && ($isOwner || $isAnnounced);
            })
            ->values();
    }

    $myAttempts = QuizAttempt::where('Student_id', $user->id)
        ->get()
        ->keyBy('quiz_id');

    $quizzes = $quizzes->map(function ($quiz) use ($myAttempts) {
        $quiz->myAttemptId = $myAttempts->has($quiz->quiz_id) ? $myAttempts[$quiz->quiz_id]->id : null;
        return $quiz;
    });

    return view('quizzes.index', compact('quizzes'));
}

    public function create()
    {
        $groups = auth()->user()->groups;

        return view('quizzes.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'group_id' => 'required|integer',
            'start_time' => 'required|date',
            'duration_minutes' => 'required|integer|min:1',
            'raw_questions' => 'required|string',
        ]);

        $isMember = auth()->user()->groups()->where('groups.id', $data['group_id'])->exists();
        if (!$isMember) {
            abort(403, 'You can only create quizzes for groups you belong to.');
        }

        $quiz = Quiz::create([
            'Lecturer_id' => auth()->id(),
            'Title' => $data['title'],
            'Target_category' => $data['group_id'],
            'Publish_time' => $data['start_time'],
            'Duration' => $data['duration_minutes'],
        ]);
        \Illuminate\Support\Facades\Cache::forget('quiz_announced_' . $quiz->quiz_id);

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

        if ($now->lt($quiz->start_time->copy()->subSeconds(5))) {
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

        $groups = auth()->user()->groups;

        return view('quizzes.edit', compact('quiz', 'groups'));
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

        $data = $request->validate([
            'title' => 'required|string|max:150',
            'group_id' => 'required|integer',
            'start_time' => 'required|date',
            'duration_minutes' => 'required|integer|min:1',
            'raw_questions' => 'required|string',
        ]);

        $isMember = auth()->user()->groups()->where('groups.id', $data['group_id'])->exists();
        if (!$isMember) {
            abort(403, 'You can only assign quizzes to groups you belong to.');
        }

        $quiz->update([
            'Title' => $data['title'],
            'Target_category' => $data['group_id'],
            'Publish_time' => $data['start_time'],
            'Duration' => $data['duration_minutes'],
        ]);

        $quiz->questions()->delete();

        $lines = array_filter(array_map('trim', explode("\n", $data['raw_questions'])));

        foreach ($lines as $line) {
            $parts = array_map('trim', explode('|', $line));
            if (count($parts) !== 4) continue;

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

        return redirect('/quizzes/' . $quiz->quiz_id)->with('success', 'Quiz updated successfully.');
    }

    public function submit(Request $request, $id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);

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

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->quiz_id,
            'Student_id' => auth()->id(),
            'Score' => 0,
            'Auto_submitted' => $request->boolean('auto_submitted', false),
        ]);

        $score = 0;

        foreach ($quiz->questions as $i => $question) {
            $submittedAnswer = $answers[$i] ?? null;
            $isCorrect = $submittedAnswer !== null && (string) $submittedAnswer === (string) $question->Correct_answer;

            if ($isCorrect) {
                $score += $question->Marks;
            }

            QuizAnswer::create([
                'attempt_id' => $attempt->Attempt_id,
                'question_id' => $question->Question_id,
                'submitted_answer' => $submittedAnswer,
                'is_correct' => $isCorrect,
            ]);
        }

        $attempt->Score = $score;
        $attempt->save();

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

 public function listCheck()
{
    $user = auth()->user();

    if ($user->role->value === 'Admin') {
        $quizzes = Quiz::orderBy('quiz_id', 'desc')->get();
    } else {
        $myGroupIds = $user->groups()->pluck('groups.id')->map(fn ($id) => (string) $id);
        $quizzes = Quiz::orderBy('quiz_id', 'desc')
            ->get()
            ->filter(function ($q) use ($myGroupIds, $user) {
                $inMyGroup = $myGroupIds->contains((string) $q->group_id);
                $isOwner = $q->Lecturer_id === $user->id;
                $isAnnounced = (bool) $q->announced_at;

                return $inMyGroup && ($isOwner || $isAnnounced);
            })
            ->values();
    }

    $myAttempts = QuizAttempt::where('Student_id', $user->id)
        ->get()
        ->keyBy('quiz_id');

    $payload = $quizzes->map(function ($quiz) use ($myAttempts) {
        return [
            'id' => $quiz->quiz_id,
            'title' => $quiz->title,
            'group_name' => $quiz->group->name ?? 'Unknown group',
            'start_time' => $quiz->start_time?->toIso8601String(),
            'start_time_display' => $quiz->start_time?->format('d M H:i'),
            'announced' => (bool) $quiz->announced_at,
            'status' => $quiz->status,
            'my_attempt_id' => $myAttempts->has($quiz->quiz_id) ? $myAttempts[$quiz->quiz_id]->id : null,
        ];
    })->values();

    return response()->json(['quizzes' => $payload]);
}
public function upcomingCheck()
{
    $user = auth()->user();

    if ($user->role->value !== 'student') {
        return response()->json(['upcoming' => null]);
    }

    $myGroupIds = $user->groups()->pluck('groups.id')->map(fn ($id) => (string) $id);

    $quiz = Quiz::all()
        ->filter(function ($q) use ($myGroupIds) {
            return $q->announced_at
                && now()->lt($q->start_time)
                && $myGroupIds->contains((string) $q->group_id);
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

        if (auth()->id() !== $quiz->Lecturer_id) {
            abort(403, 'You can only announce your own quiz.');
        }

        $quiz->markAnnounced();

        try {
            broadcast(new \App\Events\QuizAnnounced($quiz))->toOthers();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Quiz announce broadcast failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Quiz announced to students.');
    }

    public function results($submissionId)
    {
        $attempt = QuizAttempt::with(['quiz.questions', 'answers.question'])->findOrFail($submissionId);
        $quiz = $attempt->quiz;

        if (auth()->id() !== $attempt->Student_id && auth()->id() !== $quiz->Lecturer_id) {
            abort(403, 'You are not authorized to view this result.');
        }

        $totalMarks = $quiz->questions->sum('Marks');
        $percentage = $totalMarks > 0 ? ($attempt->Score / $totalMarks) * 100 : 0;

        $grade = $percentage >= 80 ? 'A' : ($percentage >= 60 ? 'B' : ($percentage >= 40 ? 'C' : 'F'));
        $feedback = $percentage >= 60
            ? 'Well done! You have a solid understanding of this topic.'
            : 'Consider reviewing this topic further.';

        $submission = $attempt;

        $breakdown = $attempt->answers->map(function ($answer) {
            $question = $answer->question;
            $options = $question ? $question->options_array : [];

            return [
                'question' => $question->Question ?? 'Unknown question',
                'your_answer' => isset($options[(int) $answer->submitted_answer]) ? $options[(int) $answer->submitted_answer] : 'No answer',
                'correct_answer' => $question ? ($options[(int) $question->Correct_answer] ?? null) : null,
                'is_correct' => (bool) $answer->is_correct,
                'marks' => $question->Marks ?? 0,
            ];
        });

        return view('quizzes.results', compact('submission', 'quiz', 'grade', 'feedback', 'breakdown'));
    }
}