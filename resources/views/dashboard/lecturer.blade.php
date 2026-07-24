@extends('layouts.app')

@section('header')
    <h2 class="screen-title" style="color:var(--text);">Lecturer Dashboard</h2>
@endsection

@section('content')
@php
    $firstGroup = $myGroups->first();
@endphp
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
                <li style="padding:10px 0; border-top:1px solid var(--muted); display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; align-items:center;">
                    <span style="color:var(--text); font-weight:600;">{{ $group->name }}</span>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <a href="/groups/{{ $group->id }}/topics/create" class="dash-btn" style="margin:0; padding:6px 12px; font-size:0.8rem;">Create topic</a>
                        <a href="{{ route('groups.statistics', $group->id) }}" class="dash-btn" style="margin:0; padding:6px 12px; font-size:0.8rem;">Participation</a>
                        <a href="/groups/{{ $group->id }}/topics" class="dash-btn" style="margin:0; padding:6px 12px; font-size:0.8rem;">View topics</a>
                    </div>
                </li>
            @empty
                <li style="padding:8px 0; color:var(--muted);">No groups yet. Create a group first, then you can add topics and view participation.</li>
            @endforelse
        </ul>
    </div>

    <h3 style="color:var(--text); font-weight:600; font-size:1.125rem; margin-bottom:12px;">Lecturer actions</h3>
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:16px;">
        <a href="{{ route('quizzes.create') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Schedule a quiz</a>

        @if($firstGroup)
            <a href="/groups/{{ $firstGroup->id }}/topics/create" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Create Topics</a>
            <a href="{{ route('groups.statistics', $firstGroup->id) }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Participation</a>
        @else
            <a href="{{ route('groups.create') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Create a group first</a>
        @endif

        <a href="{{ route('chat') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Group Chat / Topics</a>
        <a href="{{ route('recommendations.index') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Recommended topics</a>
    </div>

</div>
@endsection
