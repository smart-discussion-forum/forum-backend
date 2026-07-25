@extends('layouts.app')
@section('content')
    <div class="page-card" style="max-width:900px; margin:24px auto; padding:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; flex-wrap:wrap; gap:12px;">
            <div class="screen-title" style="margin:0; text-align:left;">{{ $quiz->title }} &mdash; Answer Key</div>
            <a href="/quizzes/{{ $quiz->quiz_id }}" class="dash-btn">Back to Quiz</a>
        </div>

        <p style="color:var(--muted); margin-top:0; margin-bottom:24px;">
            {{ $quiz->questions->count() }} question{{ $quiz->questions->count() != 1 ? 's' : '' }}
        </p>

        @forelse($quiz->questions as $i => $question)
            <div class="panel" style="margin-bottom:16px;">
                <p style="margin-top:0;"><strong>{{ $i + 1 }}. {{ $question->Question }}</strong>
                    <span style="color:var(--muted); font-size:12px; font-weight:400;">({{ $question->Marks }} mark{{ $question->Marks != 1 ? 's' : '' }})</span>
                </p>
                @foreach($question->options_array as $j => $option)
                    @php
                        $isCorrect = (string) $j === (string) $question->Correct_answer;
                    @endphp
                    <div style="padding:8px 12px; border-radius:8px; margin-bottom:6px; {{ $isCorrect ? 'background:rgba(22,163,74,0.12); border:1px solid #16a34a;' : 'background:rgba(0,0,0,0.03);' }}">
                        {{ $option }}
                        @if($isCorrect)
                            <span style="color:#16a34a; font-weight:700; margin-left:6px;">&check; Correct answer</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @empty
            <div class="empty-panel">This quiz has no questions.</div>
        @endforelse
    </div>
@endsection