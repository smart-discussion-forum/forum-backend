@extends('layouts.app')
@section('content')
    <div class="page-card" style="max-width:1100px; margin:24px auto; padding:24px;">
        <div class="screen-title" style="color:var(--text); margin-bottom:8px;">Quizzes</div>
        <p style="text-align:center; color:var(--muted); margin-top:0; margin-bottom:22px;">View scheduled quizzes, their status, and announcements.</p>
        <div class="table-card">
            <table>
        <tr><th>Title</th><th>Start</th><th>Status</th><th>Actions</th></tr>
        @forelse($quizzes as $quiz)
            <tr>
                <td><a href="/quizzes/{{ $quiz->id }}">{{ $quiz->title }}</a></td>
                <td>{{ $quiz->start_time->format('d M H:i') }}</td>
                <td>
                    <span class="status-pill">{{ $quiz->announced_at ? 'Announced' : ucfirst($quiz->status) }}</span>
                </td>
                <td class="quiz-actions">
                    @if(auth()->user()->role->value === 'Lecturer' && !$quiz->announced_at)
                        <form method="POST" action="/quizzes/{{ $quiz->id }}/announce" style="display:inline;">
                            @csrf
                            <button type="submit" class="chat-btn">Announce</button>
                        </form>
                    @elseif($quiz->announced_at)
                        <span class="sidebar-copy">Sent {{ $quiz->announced_at->format('d M H:i') }}</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="4">No quizzes yet.</td></tr>
        @endforelse
    </table>
    @if(auth()->user()->role ->value === 'Lecturer')
        <div style="display:flex; justify-content:flex-end; margin-top:18px;">
            <a href="/quizzes/create" class="btn">Create Quiz</a>
        </div>
    @endif
        </div>
    </div>
@endsection
