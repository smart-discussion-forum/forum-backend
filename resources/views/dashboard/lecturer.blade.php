@extends('layouts.app')

@section('content')
<div class="page-card" style="max-width:980px; margin:30px auto; padding:30px;">
    <h2 class="screen-title" style="color:var(--text); text-align:left; margin:0 0 8px;">Lecturer Dashboard</h2>
    <p style="color:var(--muted); margin-bottom:28px;">
        Welcome back, {{ auth()->user()->name }}. You're signed in as
        <span style="color:var(--text); font-weight:600;">Lecturer</span>.
    </p>

    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:14px;">
        <h3 style="color:var(--text); font-weight:600; font-size:1.125rem; margin:0;">Your groups</h3>
        <a href="{{ route('groups.create') }}" class="dash-btn" style="margin:0; padding:8px 14px; font-size:0.875rem;">+ New group</a>
    </div>

    @if($myGroups->isNotEmpty())
        <div style="display:flex; flex-wrap:wrap; gap:10px; margin-bottom:28px;">
            @foreach ($myGroups as $group)
                <span style="display:inline-flex; align-items:center; padding:10px 16px; border-radius:12px; background:rgba(255,255,255,0.72); border:1px solid rgba(148,163,184,0.22); color:var(--text); font-weight:600; font-size:0.95rem;">
                    {{ $group->name }}
                </span>
            @endforeach
        </div>

        <div style="margin:0 0 20px; padding:16px 18px; border-radius:16px; background:rgba(255,255,255,0.55); border:1px solid rgba(148,163,184,0.22);">
            <label for="lecturerGroupSelect" style="display:block; color:var(--muted); font-size:0.875rem; font-weight:600; margin-bottom:8px;">
                Choose a group for topic and participation actions
            </label>
            <select id="lecturerGroupSelect" style="width:100%; max-width:360px; padding:10px 12px; border-radius:10px; border:1px solid rgba(148,163,184,0.35); background:#fff; color:var(--text); font-size:0.95rem;">
                @foreach ($myGroups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
    @else
        <p style="color:var(--muted); margin:0 0 28px;">
            No groups yet. Create a group first, then you can add topics and view participation.
        </p>
    @endif

    <h3 style="color:var(--text); font-weight:600; font-size:1.125rem; margin-bottom:12px;">Lecturer actions</h3>
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:16px;">
        @if($myGroups->isNotEmpty())
            <a id="btnCreateTopic" href="/groups/{{ $myGroups->first()->id }}/topics/create" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Create topic</a>
            <a id="btnParticipation" href="{{ route('groups.statistics', $myGroups->first()->id) }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Participation</a>
            <a id="btnViewTopics" href="/groups/{{ $myGroups->first()->id }}/topics" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">View topics</a>
        @endif
        <a href="{{ route('quizzes.create') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Schedule a quiz</a>
        <a href="{{ route('chat') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Group Chat</a>
        <a href="{{ route('recommendations.index') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Recommended topics</a>
        @if($myGroups->isEmpty())
            <a href="{{ route('groups.create') }}" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Create a group</a>
        @endif
    </div>
</div>

@if($myGroups->isNotEmpty())
<script>
    (function () {
        const select = document.getElementById('lecturerGroupSelect');
        const createTopic = document.getElementById('btnCreateTopic');
        const participation = document.getElementById('btnParticipation');
        const viewTopics = document.getElementById('btnViewTopics');

        if (!select) return;

        const updateLinks = () => {
            const id = select.value;
            if (createTopic) createTopic.href = `/groups/${id}/topics/create`;
            if (participation) participation.href = `/groups/${id}/statistics`;
            if (viewTopics) viewTopics.href = `/groups/${id}/topics`;
        };

        select.addEventListener('change', updateLinks);
        updateLinks();
    })();
</script>
@endif
@endsection
