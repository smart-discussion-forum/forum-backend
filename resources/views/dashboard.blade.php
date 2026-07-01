@extends('layouts.app')
@section('content')
    <div class="screen-title">Dashboard</div>
    <div style="display:flex; flex-direction:column; gap:12px; max-width:50%; margin:0 auto;">
        <a href="/topics" class="dash-btn">Discussions</a>
        <a href="/quizzes" class="dash-btn">Quiz</a>
    </div>
@endsection
