@extends('layouts.app')
@section('content')
    <div class="screen-title">Quizzes</div>
    <table>
        <tr><th>Title</th><th>Start</th><th>Status</th></tr>
        @forelse($quizzes as $quiz)
            <tr>
                <td><a href="/quizzes/{{ $quiz->id }}">{{ $quiz->title }}</a></td>
                <td>{{ $quiz->start_time->format('d M H:i') }}</td>
                <td>{{ $quiz->status }}</td>
            </tr>
        @empty
            <tr><td colspan="3">No quizzes yet.</td></tr>
        @endforelse
    </table>
    <a href="/quizzes/create" class="btn">Create Quiz (lecturer)</a>
@endsection
