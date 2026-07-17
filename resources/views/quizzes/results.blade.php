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

        <div class="panel">
            <div style="font-weight:700; margin-bottom:10px;">Question Breakdown</div>
            @foreach($breakdown as $item)
                <div style="margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid rgba(148,163,184,0.18);">
                    <div><strong>{{ $loop->iteration }}. {{ $item['question'] }}</strong> ({{ $item['marks'] }} mark{{ $item['marks'] > 1 ? 's' : '' }})</div>
                    <div style="color: {{ $item['is_correct'] ? '#067647' : '#b42318' }};">
                        Your answer: {{ $item['your_answer'] }} {{ $item['is_correct'] ? '✓' : '✗' }}
                    </div>
                    @if(!$item['is_correct'])
                        <div style="color:#067647;">Correct answer: {{ $item['correct_answer'] }}</div>
                    @endif
                </div>
            @endforeach
        </div>

        <a href="/quizzes" class="btn">View Quizzes</a>
    </div>
@endsection