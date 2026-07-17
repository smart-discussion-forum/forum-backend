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
        <form method="POST" action="/quizzes/{{ $quiz->id }}/submit" id="quizForm" onsubmit="return handleManualSubmit(event)">
            @csrf
            <input type="hidden" name="auto_submitted" id="autoSubmitted" value="0">
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
        let isAutoSubmit = false;
        const startTime = new Date("{{ $quiz->start_time->toIso8601String() }}").getTime();
        const endTime = new Date("{{ $quiz->end_time->toIso8601String() }}").getTime();
        const timerEl = document.getElementById('timer');
        const form = document.getElementById('quizForm');
        const autoSubmittedInput = document.getElementById('autoSubmitted');
        const submitBtn = form.querySelector('button[type=submit]');

        let quizStarted = false;
        let isSubmitted = false;

        function formatMmSs(ms) {
            const total = Math.max(0, Math.floor(ms / 1000));
            const mins = Math.floor(total / 60);
            const secs = total % 60;
            return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
        }

        function enableSubmit() {
            submitBtn.disabled = false;
        }

        function disableSubmit() {
            submitBtn.disabled = true;
        }

        // Prevent leaving the page while quiz is in progress and not submitted
        function beforeUnloadHandler(e) {
            if (!isSubmitted && quizStarted) {
                e.preventDefault();
                e.returnValue = '';
                return '';
            }
        }

        function tick() {
            const now = Date.now();

            // If there is plenty of time before start, show start time and keep submit disabled
            if (now < startTime - 30000) {
                const startDate = new Date(startTime);
                // show a friendly static message until 30s-to-start
                timerEl.textContent = 'Starts ' + startDate.toLocaleString();
                quizStarted = false;
                disableSubmit();
                return;
            }

            // If within 30s before start but not yet started, show countdown-to-start
            if (now < startTime) {
                const remainingToStart = startTime - now;
                timerEl.textContent = 'Starts in ' + formatMmSs(remainingToStart);
                quizStarted = false;
                disableSubmit();
                return;
            }

            // Quiz has started (now >= startTime)
            if (!quizStarted) {
                quizStarted = true;
                enableSubmit();
                window.addEventListener('beforeunload', beforeUnloadHandler);
            }

            const diff = Math.max(0, endTime - now);
            timerEl.textContent = formatMmSs(diff);

            if (diff <= 0) {
                clearInterval(interval);
                isAutoSubmit = true;
                isSubmitted = true;
                autoSubmittedInput.value = '1';
                window.removeEventListener('beforeunload', beforeUnloadHandler);
                form.submit();
            }
        }

        // initialize state
        disableSubmit();
        const interval = setInterval(tick, 1000);
        tick();

        function handleManualSubmit(e) {
            if (!quizStarted) {
                alert('The quiz has not started yet. You cannot submit answers before the start time.');
                e.preventDefault();
                return false;
            }

            if (isAutoSubmit) return true;

            if (!confirm('Are you sure you want to submit your answers? You cannot change them after this.')) {
                e.preventDefault();
                return false;
            }

            isSubmitted = true;
            window.removeEventListener('beforeunload', beforeUnloadHandler);
            return true;
        }
    </script>
@endsection
