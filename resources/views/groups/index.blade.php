@extends('layouts.app')

@section('title', 'Study Groups — Mindshare')
@section('box-style', 'wide')

@section('content')
<div class="page-card" style="max-width: 960px; margin: 0 auto; padding: 32px; border-radius: 30px;">
    <div class="screen-title" style="text-align:left; margin-bottom: 6px;">Study Groups</div>
    <p style="color: var(--muted); margin-top: 0; margin-bottom: 28px;">
        Groups you're already part of, and groups you can still join.
    </p>

    <h3 style="margin-bottom: 12px; color: #1f2937;">My Groups ({{ $myGroups->count() }})</h3>

    @if($myGroups->isEmpty())
        <div class="empty-panel" style="margin-bottom: 28px;">
            You haven't joined any groups yet — browse the list below to get started.
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 28px;">
            @foreach($myGroups as $group)
                <div class="panel">
                    <div class="row" style="align-items: center;">
                        <div>
                            <div style="font-weight:700; font-size:16px;">
                                <a href="{{ route('groups.show', $group->id) }}" style="color:inherit; text-decoration:none;">
                                    {{ $group->name }}
                                </a>
                            </div>
                            @if($group->description)
                                <div style="color: var(--muted); font-size: 13px; margin-top: 4px;">
                                    {{ $group->description }}
                                </div>
                            @endif
                            <span class="status-pill" style="margin-top: 8px; display:inline-block;">
                                {{ $group->pivot->role }}
                            </span>
                        </div>
                        <form method="POST" action="/groups/{{ $group->id }}/leave">
                            @csrf
                            <button type="submit" class="dash-btn">Leave</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <h3 style="margin-bottom: 12px; color: #1f2937;">Browse Groups to Join ({{ $joinableGroups->count() }})</h3>

    @if($joinableGroups->isEmpty())
        <div class="empty-panel">
            No other groups available to join right now.
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($joinableGroups as $group)
                <div class="panel">
                    <div class="row" style="align-items: center;">
                        <div>
                            <div style="font-weight:700; font-size:16px;">{{ $group->name }}</div>
                            @if($group->description)
                                <div style="color: var(--muted); font-size: 13px; margin-top: 4px;">
                                    {{ $group->description }}
                                </div>
                            @endif
                            <div style="color: var(--muted); font-size: 12px; margin-top: 6px;">
                                {{ $group->members_count }} {{ $group->members_count === 1 ? 'member' : 'members' }}
                            </div>
                        </div>
                        @if(auth()->user()->role === \App\Enums\RoleEnum::Student)
                            <form method="POST" action="/groups/{{ $group->id }}/join">
                                @csrf
                                <button type="submit" class="btn">Join</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
