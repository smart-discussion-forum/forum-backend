 @extends('layouts.app')

@section('header')
    <h2 class="screen-title" style="color:var(--text);">My Dashboard</h2>
@endsection

@section('content')
<div class="page-card" style="max-width:980px; margin:30px auto; padding:30px;">

    <p style="color:var(--muted); margin-bottom:24px;">
        Welcome back, {{ auth()->user()->name }}. You're signed in as
        <span style="color:var(--text); font-weight:600;">Student</span>.
    </p>

    <div class="page-card" style="margin:0 0 24px; padding:18px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h3 style="color:var(--text); font-weight:600; font-size:1.125rem; margin:0;">Your groups</h3>
            <a href="{{ route('groups.index') }}" class="dash-btn" style="margin:0; padding:8px 14px; font-size:0.875rem;">Browse / join a group</a>
        </div>
        <ul style="list-style:none; padding:0; margin:0;">
            @forelse ($myGroups as $group)
                <li style="padding:8px 0; border-top:1px solid var(--muted);">
                    <a href="{{ route('groups.show', $group) }}" style="color:var(--text);">{{ $group->name }}</a>
                </li>
            @empty
                <li style="padding:8px 0; color:var(--muted);">You haven't joined a group yet.</li>
            @endforelse
        </ul>
    </div>

    <h3 style="color:var(--text); font-weight:600; font-size:1.125rem; margin-bottom:12px;">Quick links</h3>
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:16px;">
        <a href="{{ route('chat') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Open chat</a>
        <a href="{{ route('quizzes.index') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">My quizzes</a>
    </div>

</div>
@endsection