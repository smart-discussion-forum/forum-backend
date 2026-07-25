@extends('layouts.app')

@section('content')
<div class="page-card" style="max-width:980px; margin:30px auto; padding:30px;">
    <h2 class="screen-title" style="color:var(--text); text-align:left; margin:0 0 8px;">Admin Dashboard</h2>
    <p style="color:var(--muted); margin-bottom:24px;">
        Welcome back, {{ auth()->user()->name }}. You're signed in as
        <span style="color:var(--text); font-weight:600;">Admin</span>.
    </p>

    <h3 style="color:var(--text); font-weight:600; font-size:1.125rem; margin-bottom:12px;">Admin actions</h3>
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:16px;">
        <a href="{{ route('admin.users.index') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Manage Users</a>
        <a href="{{ route('admin.statistics.index') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Statistics</a>
        <a href="{{ route('groups.manage') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Manage groups</a>
    </div>
</div>
@endsection
