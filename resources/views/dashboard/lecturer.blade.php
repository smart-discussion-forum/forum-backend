@extends('layouts.app')

@section('header')
    <h2 class="screen-title" style="color:var(--text);">Lecturer Dashboard</h2>
@endsection

@section('content')
<div class="page-card" style="max-width:980px; margin:30px auto; padding:30px;">

    <p style="color:var(--muted); margin-bottom:24px;">
        Welcome back, {{ auth()->user()->name }}. You're signed in as
        <span style="color:var(--text); font-weight:600;">Lecturer</span>.
    </p>

    <div class="page-card" style="margin:0 0 24px; padding:18px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h3 style="color:var(--text); font-weight:600; font-size:1.125rem; margin:0;">Your groups</h3>
            <a href="{{ route('groups.create') }}" class="dash-btn" style="margin:0; padding:8px 14px; font-size:0.875rem;">+ New group</a>
        </div>
        <ul style="list-style:none; padding:0; margin:0;">
            @forelse ($myGroups as $group)
                <li style="padding:8px 0; border-top:1px solid var(--muted); display:flex; justify-content:space-between;">
                    <a href="{{ route('groups.show', $group) }}" style="color:var(--text);">{{ $group->name }}</a>
                    <span style="color:var(--muted); font-size:0.875rem;">{{ $group->quizzes_count }} quizzes</span>
                </li>
            @empty
                <li style="padding:8px 0; color:var(--muted);">You haven't created a group yet.</li>
            @endforelse
        </ul>
    </div>

    <h3 style="color:var(--text); font-weight:600; font-size:1.125rem; margin-bottom:12px;">Lecturer actions</h3>
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:16px;">
        <a href="{{ route('quizzes.create') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Schedule a quiz</a>
        <a href="{{ route('chat') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Group Chat / Topics</a>
        <a href="{{ route('recommendations.index') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Recommended topics</a>
    </div>

</div>
@endsection