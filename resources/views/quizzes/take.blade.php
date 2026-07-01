@extends('layouts.app')
@section('content')
    <div style="float:right; background:white; padding:4px 10px; border:1px solid #999;" id="timer">--:--</div>
    <div class="screen-title">Quiz taking screen</div>
    <form method="POST" action="/quizzes/{{ $quiz->id }}/submit" id="quizForm">
        @csrf
        @foreach($quiz->questions as $i => $q)
            <p><strong>{{ $q['question'] }}</strong></p>
            @foreach($q['options'] as $j => $option)
                <label style="display:block;">
                    <input type="radio" name="answers[{{ $i }}]" value="{{ $j }}" style="width:auto;"> {{ $option }}
                </label>
            @endforeach
        @endforeach
        <div style="text-align:right; margin-top:10px;">
            <button type="submit" class="btn">Submit</button>
        </div>
    </form>
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
