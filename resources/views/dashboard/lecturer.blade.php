@extends('layouts.app')

@section('header')
    <h2 class="screen-title" style="color:var(--text);">Lecturer Dashboard</h2>
@endsection

@section('content')
<div class="page-card" style="max-width:980px; margin:30px auto; padding:32px;">

    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:24px;">
        <div>
            <div style="font-size:20px; font-weight:700; color:var(--text);">Welcome back, {{ auth()->user()->name }}</div>
            <div style="color:var(--muted); font-size:14px; margin-top:2px;">Signed in as <span style="font-weight:600; color:var(--text);">Lecturer</span></div>
        </div>
        <a href="{{ route('groups.create') }}" class="btn" style="margin:0;">+ New group</a>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap:14px; margin-bottom:28px;">
        <div class="panel" style="padding:18px; text-align:center;">
            <div style="font-size:26px; font-weight:800; color:var(--text);">{{ $myGroups->count() }}</div>
            <div style="color:var(--muted); font-size:13px; margin-top:4px;">Groups</div>
        </div>
        <a href="{{ route('notifications.index') }}" class="panel" style="padding:18px; text-align:center; text-decoration:none; display:block;">
            <div style="font-size:26px; font-weight:800; color:var(--text);">{{ auth()->user()->unreadNotifications()->count() }}</div>
            <div style="color:var(--muted); font-size:13px; margin-top:4px;">Notifications</div>
        </a>
        <div class="panel" style="padding:18px; text-align:center;">
            <div style="font-size:26px; font-weight:800; color:var(--text);">{{ $myGroups->sum('quizzes_count') }}</div>
            <div style="color:var(--muted); font-size:13px; margin-top:4px;">Quizzes scheduled</div>
        </div>
    </div>

    <div class="panel" style="margin:0 0 28px; padding:20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <h3 style="color:var(--text); font-weight:700; font-size:1.1rem; margin:0;">Your groups</h3>
            <a href="{{ route('groups.manage') }}" style="color:var(--accent-strong); font-size:13px; font-weight:600; text-decoration:none;">Manage all →</a>
        </div>

        @forelse ($myGroups as $group)
            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; padding:12px 0; {{ !$loop->first ? 'border-top:1px solid var(--line);' : '' }}">
                <div>
                    <a href="{{ route('groups.show', $group) }}" style="color:var(--text); text-decoration:none; font-weight:600; font-size:15px;">
                        {{ $group->name }}
                    </a>
                    <div style="color:var(--muted); font-size:12px; margin-top:3px;">
                        {{ $group->members_count }} {{ \Illuminate\Support\Str::plural('student', $group->members_count) }} · {{ $group->quizzes_count }} {{ \Illuminate\Support\Str::plural('quiz', $group->quizzes_count) }}
                    </div>
                </div>
                <a href="{{ route('groups.statistics', $group->id) }}" class="dash-btn" style="margin:0; padding:8px 14px; font-size:13px; white-space:nowrap;">Stats</a>
            </div>
        @empty
            <div style="color:var(--muted); padding:8px 0;">
                You haven't created a group yet — start one to schedule quizzes and chat with students.
            </div>
        @endforelse
    </div>

    <h3 style="color:var(--text); font-weight:700; font-size:1.1rem; margin-bottom:14px;">Lecturer actions</h3>
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap:16px;">
        <a href="{{ route('quizzes.create') }}" class="dash-btn" style="margin:0; text-align:center; padding:20px 16px; font-weight:600;">Schedule a quiz</a>
        <a href="{{ route('groups.manage') }}" class="dash-btn" style="margin:0; text-align:center; padding:20px 16px; font-weight:600;">Manage groups</a>
        <a href="{{ route('chat') }}" class="dash-btn" style="margin:0; text-align:center; padding:20px 16px; font-weight:600;">Group chat / topics</a>
        <a href="{{ route('recommendations.index') }}" class="dash-btn" style="margin:0; text-align:center; padding:20px 16px; font-weight:600;">Recommended topics</a>
    </div>

</div>
@endsection