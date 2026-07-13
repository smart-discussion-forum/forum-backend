@extends('layouts.app')
@section('content')
    <div class="page-card" style="max-width:980px; margin:30px auto; padding:30px;">
        <div class="screen-title" style="color:var(--text); margin-bottom:10px;">Dashboard</div>
        <p style="color:var(--muted); text-align:center; margin:0 0 24px;">Choose a section to continue.</p>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:16px;">
            <a href="/topics" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Topics</a>
            <a href="/discussions" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Discussions</a>
            <a href="/quizzes" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Quizzes</a>
            <a href="/profile" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Profile</a>
            <a href="/chat" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Group Chat</a>
        </div>
    </div>
@endsection
