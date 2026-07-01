@extends('layouts.app')
@section('content')
    <div class="auth-card" style="max-width:720px; margin:24px auto; padding:28px;">
        <div class="screen-title" style="color:var(--text); margin-bottom:8px;">Quiz results</div>
        <div class="panel">
            <label>Score:</label>
            <input type="text" value="{{ $submission->score }} / {{ count($quiz->questions) }}" readonly>
            <label>Grade:</label>
            <input type="text" value="{{ $grade }}" readonly>
        </div>
        <div class="panel">{{ $feedback }}</div>
        <a href="/quizzes" class="btn">View Quizzes</a>
    </div>
@endsection
