@extends('layouts.app')
@section('content')
    <div class="page-card" style="max-width:980px; margin:30px auto; padding:30px;">
        <div class="screen-title" style="color:var(--text); margin-bottom:10px;">Dashboard</div>
        <p style="color:var(--muted); text-align:center; margin:0 0 24px;">Choose a section to continue.</p>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:16px;">
            <a href="/chat" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Group Chat</a>
            <a href="/quizzes" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Quizzes</a>
            <a href="{{ route('recommendations.index') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Recommendations</a>
            <a href="{{ route('groups.statistics', auth()->user()->groups()->first()?->id ?? 1) }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Statistics</a>
            <a href="/profile" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Profile</a>
        </div>
    </div>
@endsection
