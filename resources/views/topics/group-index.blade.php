@extends('layouts.app')

@section('content')
    <div class="page-card" style="max-width:900px; margin:30px auto; padding:30px;">
        <div style="display:flex; justify-content:space-between; gap:16px; align-items:center; margin-bottom:18px; flex-wrap:wrap;">
            <div>
                <div class="screen-title" style="margin-bottom:6px;">{{ $group->name }} Topics</div>
                <p style="color:var(--muted); margin:0;">No topics in this group yet.</p>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="/chat?group={{ $group->id }}" class="dash-btn">Back to Chat</a>
                @if(in_array(auth()->user()->role, [\App\Enums\RoleEnum::Lecturer, \App\Enums\RoleEnum::Admin], true))
                    <a href="/groups/{{ $group->id }}/topics/create" class="dash-btn">New topic</a>
                @endif
            </div>
        </div>

        <div class="empty-panel">
            @if(in_array(auth()->user()->role, [\App\Enums\RoleEnum::Lecturer, \App\Enums\RoleEnum::Admin], true))
                Create the first topic to start a discussion thread.
            @else
                A lecturer hasn’t created any topics for this group yet.
            @endif
        </div>
    </div>
@endsection
