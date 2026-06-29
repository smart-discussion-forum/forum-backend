@extends('layouts.app')
@section('content')
    <div class="screen-title">Quiz results</div>
    <label>score:</label>
    <input type="text" value="{{ $submission->score }} / {{ count($quiz->questions) }}" readonly>
    <label>Grade:</label>
    <input type="text" value="{{ $grade }}" readonly>
    <div class="panel">{{ $feedback }}</div>
    <a href="/quizzes" class="btn">view results</a>
@endsection
