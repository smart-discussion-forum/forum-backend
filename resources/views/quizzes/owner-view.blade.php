@extends('layouts.app')
@section('content')
    <div class="page-card" style="max-width:900px; margin:24px auto; padding:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div class="screen-title" style="margin:0; text-align:left;">{{ $quiz->title }}</div>
            <a href="/quizzes" class="dash-btn">Back to Quizzes</a>
        </div>
        
        <p style="color:var(--muted); margin-top:0;">
            {{ $quiz->target_category }} &bull;
            Starts {{ $quiz->start_time->format('d M H:i') }} &bull;
            Ends {{ $quiz->end_time->format('d M H:i') }} &bull;
            Status: {{ ucfirst($quiz->status) }} &bull;
            {{ $quiz->questions->count() }} question{{ $quiz->questions->count() != 1 ? 's' : '' }}
        </p>

<div style="display:flex; gap:12px; margin-top:20px;">
    @if($quiz->status === 'upcoming')
        <a href="/quizzes/{{ $quiz->quiz_id }}/edit" class="btn">Edit Quiz</a>
    @endif
    <a href="/quizzes/{{ $quiz->quiz_id }}/submissions" class="dash-btn">View Submissions</a>
</div>
    </div>
@endsection