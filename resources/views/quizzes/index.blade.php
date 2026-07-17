@extends('layouts.app')
@section('content')
    <div class="page-card" style="max-width:1100px; margin:24px auto; padding:24px;">
        <div class="screen-title" style="color:var(--text); margin-bottom:8px;">Quizzes</div>
        <p style="text-align:center; color:var(--muted); margin-top:0; margin-bottom:22px;">View scheduled quizzes, their status, and announcements.</p>
        <div class="table-card">
            <table>
                <tr><th>Title</th><th>Group</th><th>Start</th><th>Status</th><th>Actions</th></tr>
                <tbody id="quizzesBody">
                @forelse($quizzes as $quiz)
                    <tr data-quiz-id="{{ $quiz->quiz_id }}">
                        <td>
                            @if($quiz->myAttemptId)
                                <a href="/quizzes/results/{{ $quiz->myAttemptId }}">{{ $quiz->title }}</a>
                            @else
                                <a href="/quizzes/{{ $quiz->id }}">{{ $quiz->title }}</a>
                            @endif
                        </td>
                        <td>{{ $quiz->group->name ?? 'Unknown group' }}</td>
                        <td>{{ $quiz->start_time->format('d M H:i') }}</td>
                        <td>
                            <span class="status-pill">{{ ucfirst($quiz->status) }}{{ $quiz->announced_at ? ' · Sent' : '' }}</span>
                        </td>
                        <td class="quiz-actions">
                            @if(auth()->id() === $quiz->Lecturer_id && !$quiz->announced_at)
                                <form method="POST" action="/quizzes/{{ $quiz->quiz_id }}/announce" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="chat-btn">Announce</button>
                                </form>
                            @elseif($quiz->announced_at)
                                <span class="sidebar-copy">Sent {{ $quiz->announced_at->format('d M H:i') }}</span>
                            @endif
                            @if($quiz->myAttemptId)
                                <span class="sidebar-copy">Completed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No quizzes yet.</td></tr>
                @endforelse
                </tbody>
            </table>
            @if(auth()->user()->role->value === 'Lecturer')
                <div style="display:flex; justify-content:flex-end; margin-top:18px;">
                    <a href="/quizzes/create" class="btn">Create Quiz</a>
                </div>
            @endif
        </div>
    </div>

    @if(auth()->user()->role->value !== 'Admin')
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script>
        const token = @json(session('api_token'));
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: @json(env('REVERB_APP_KEY')),
            wsHost: @json(env('REVERB_HOST', 'localhost')),
            wsPort: 8080,
            wssPort: 8080,
            forceTLS: @json(env('REVERB_SCHEME', 'http') === 'https'),
            enabledTransports: ['ws', 'wss'],
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    Authorization: 'Bearer ' + token,
                    Accept: 'application/json',
                },
            },
        });

        const myGroupIds = @json(auth()->user()->groups()->pluck('groups.id'));

        myGroupIds.forEach(groupId => {
            window.Echo.private('group.' + groupId)
                .listen('.quiz.announced', (e) => {
                    if (document.querySelector(`tr[data-quiz-id="${e.id}"]`)) {
                        return;
                    }
                    const tbody = document.getElementById('quizzesBody');
                    const emptyCell = tbody.querySelector('td[colspan="5"]');
                    if (emptyCell) emptyCell.closest('tr').remove();

                    const row = document.createElement('tr');
                    row.dataset.quizId = e.id;
                    row.innerHTML = `
                        <td><a href="/quizzes/${e.id}">${e.title}</a></td>
                        <td>-</td>
                        <td>${new Date(e.start_time).toLocaleString()}</td>
                        <td><span class="status-pill">Upcoming · Sent</span></td>
                        <td></td>
                    `;
                    tbody.prepend(row);
                });
        });
</script>
    @endif

    <script>
        const csrfToken = @json(csrf_token());

        function buildStatusLabel(q) {
            const status = q.status.charAt(0).toUpperCase() + q.status.slice(1);
            return q.announced ? status + ' · Sent' : status;
        }

        function buildActionsHtml(q) {
            let html = '';

            if (q.can_announce) {
                html += '<form method="POST" action="/quizzes/' + q.id + '/announce" style="display:inline;">' +
                    '<input type="hidden" name="_token" value="' + csrfToken + '">' +
                    '<button type="submit" class="chat-btn">Announce</button></form>';
            } else if (q.announced) {
                html += '<span class="sidebar-copy">Sent ' + (q.announced_at_display || '') + '</span>';
            }

            if (q.my_attempt_id) {
                html += '<span class="sidebar-copy">Completed</span>';
            }

            return html;
        }

        // Polling fallback: refreshes the quiz list every 5s regardless of
        // whether websockets/Reverb are working. This is what keeps the
        // table in sync even if the Echo listener above never fires.
        function pollQuizList() {
            fetch('/quizzes/list-check')
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('quizzesBody');
                    const quizzes = data.quizzes || [];

                    if (quizzes.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5">No quizzes yet.</td></tr>';
                        return;
                    }

                    const emptyCell = tbody.querySelector('td[colspan="5"]');
                    if (emptyCell) emptyCell.closest('tr').remove();

                    quizzes.forEach(q => {
                        let row = tbody.querySelector('tr[data-quiz-id="' + q.id + '"]');

                        const titleCell = q.my_attempt_id
                            ? '<a href="/quizzes/results/' + q.my_attempt_id + '">' + q.title + '</a>'
                            : '<a href="/quizzes/' + q.id + '">' + q.title + '</a>';

                        if (!row) {
                            row = document.createElement('tr');
                            row.dataset.quizId = q.id;
                            tbody.prepend(row);
                        }

                        row.innerHTML =
                            '<td>' + titleCell + '</td>' +
                            '<td>' + q.group_name + '</td>' +
                            '<td>' + q.start_time_display + '</td>' +
                            '<td><span class="status-pill">' + buildStatusLabel(q) + '</span></td>' +
                            '<td class="quiz-actions">' + buildActionsHtml(q) + '</td>';
                    });
                })
                .catch(() => {});
        }

        pollQuizList();
        setInterval(pollQuizList, 5000);
    </script>
@endsection