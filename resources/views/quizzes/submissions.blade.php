@extends('layouts.app')
@section('content')
    <div class="page-card" style="max-width:900px; margin:24px auto; padding:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div class="screen-title" style="margin:0; text-align:left;">{{ $quiz->title }} — Submissions</div>
            <a href="/quizzes/{{ $quiz->quiz_id }}" class="dash-btn">Back to Quiz</a>
        </div>

        <div class="table-card">
            <div style="font-weight:700; margin-bottom:10px;">
                Student Submissions <span id="submissionCount">({{ $submissions->count() }})</span>
            </div>
            <table>
                <tr><th>Student</th><th>Score</th><th>Type</th></tr>
                <tbody id="submissionsBody">
                    @forelse($submissions as $submission)
                        <tr>
                            <td>{{ $submission->student->name ?? 'Unknown' }}</td>
                            <td>{{ $submission->Score }} / {{ $quiz->questions->sum('Marks') }}</td>
                            <td>{{ $submission->Auto_submitted ? 'Auto-submitted' : 'Submitted' }}</td>
                        </tr>
                    @empty
                        <tr id="noSubmissionsRow"><td colspan="3">No submissions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        window.Echo.private('quiz.{{ $quiz->quiz_id }}')
            .listen('.attempt.submitted', (e) => {
                const noRow = document.getElementById('noSubmissionsRow');
                if (noRow) noRow.remove();

                const tbody = document.getElementById('submissionsBody');
                const row = document.createElement('tr');
                row.innerHTML = `<td>${e.student_name}</td><td>${e.score} / {{ $quiz->questions->sum('Marks') }}</td><td>${e.auto_submitted ? 'Auto-submitted' : 'Submitted'}</td>`;
                tbody.appendChild(row);

                const countEl = document.getElementById('submissionCount');
                countEl.textContent = '(' + tbody.querySelectorAll('tr').length + ')';
            });
    </script>
    @endpush
@endsection