@extends('layouts.app')
@section('content')
    <div class="auth-card" style="max-width:900px; margin:24px auto; padding:28px;">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:18px; flex-wrap:wrap;">
            <div>
                <div class="screen-title" style="color:var(--text); margin:0; text-align:left;">Quiz</div>
                <div style="color:var(--muted); font-size:13px;">{{ $quiz->title }}</div>
            </div>
            <div style="background:rgba(79,124,168,0.12); padding:8px 14px; border-radius:999px; font-weight:700; color:#20324a;" id="timer">--:--</div>
        </div>
        <form method="POST" action="/quizzes/{{ $quiz->id }}/submit" id="quizForm">
            @csrf
            @foreach($quiz->questions as $i => $q)
                <div class="panel" style="margin-bottom:16px;">
                    <p style="margin-top:0;"><strong>{{ $q['question'] }}</strong></p>
                    @foreach($q['options'] as $j => $option)
                        <label style="display:block; margin-bottom:8px;">
                            <input type="radio" name="answers[{{ $i }}]" value="{{ $j }}" style="width:auto;"> {{ $option }}
                        </label>
                    @endforeach
                </div>
            @endforeach
            <div style="text-align:right; margin-top:10px;">
                <button type="submit" class="btn">Submit</button>
            </div>
        </form>
    </div>
    <script>
        const endTime = new Date("{{ $quiz->end_time->toIso8601String() }}").getTime();
        const timerEl = document.getElementById('timer');
        const form = document.getElementById('quizForm');
        function tick() {
            const now = Date.now();
            const diff = Math.max(0, endTime - now);
            const mins = Math.floor(diff / 60000);
            const secs = Math.floor((diff % 60000) / 1000);
            timerEl.textContent = String(mins).padStart(2,'0') + ':' + String(secs).padStart(2,'0');
            if (diff <= 0) { clearInterval(interval); form.submit(); }
        }
        const interval = setInterval(tick, 1000);
        tick();
    </script>
@endsection
