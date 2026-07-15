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
                            <span class="status-pill">{{ $quiz->announced_at ? 'Announced' : ucfirst($quiz->status) }}</span>
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
                        <td><span class="status-pill">Announced</span></td>
                        <td></td>
                    `;
                    tbody.prepend(row);
                });
        });
    </script>
    @endif
@endsection