@extends('layouts.app')

@section('header')
    <h2 class="screen-title" style="color:var(--text);">Admin Dashboard</h2>
@endsection

@section('content')
<div class="page-card" style="max-width:980px; margin:30px auto; padding:30px;">

    <p style="color:var(--muted); margin-bottom:24px;">
        Welcome back, {{ auth()->user()->name }}. You're signed in as
        <span style="color:var(--text); font-weight:600;">Admin</span>.
    </p>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:16px; margin-bottom:24px;">
        <div class="page-card" style="margin:0; padding:18px; text-align:center;">
            <p style="color:var(--muted); font-size:0.875rem; margin:0 0 6px;">Groups you own</p>
            <p style="color:var(--text); font-size:1.75rem; font-weight:700; margin:0;">{{ $groupCount }}</p>
        </div>
        <div class="page-card" style="margin:0; padding:18px; text-align:center;">
            <p style="color:var(--muted); font-size:0.875rem; margin:0 0 6px;">Posts flagged for moderation</p>
            <p style="color:#dc2626; font-size:1.75rem; font-weight:700; margin:0;">{{ $flaggedCount }}</p>
        </div>
        <div class="page-card" style="margin:0; padding:18px; text-align:center;">
            <p style="color:var(--muted); font-size:0.875rem; margin:0 0 6px;">Your role</p>
            <p style="color:var(--text); font-size:1.75rem; font-weight:700; margin:0;">Admin</p>
        </div>
    </div>

    <h3 style="color:var(--text); font-weight:600; font-size:1.125rem; margin-bottom:12px;">Admin actions</h3>
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:16px;">
        <a href="{{ route('discussions.index') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Moderate flagged posts</a>
        <a href="{{ route('groups.statistics', 1) }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">View group statistics</a>
        <a href="{{ route('groups.manage') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Manage groups</a>
    </div>

</div>
@endsection