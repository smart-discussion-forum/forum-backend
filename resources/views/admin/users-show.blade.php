@extends('layouts.app')

@section('title', 'Manage ' . $user->name)

@section('content')
<div class="page-card" style="max-width:900px; margin:30px auto; padding:32px;">

    <a href="{{ route('admin.users.index') }}" style="color:var(--muted); font-size:13px; text-decoration:none;">&larr; Back to all users</a>

    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin:16px 0 24px; flex-wrap:wrap; gap:12px;">
        <div>
            <div class="screen-title" style="text-align:left; margin:0; font-size:24px;">{{ $user->name }}</div>
            <div style="color:var(--muted); font-size:14px; margin-top:4px;">{{ $user->email }} &middot; {{ $user->role->value }}</div>
        </div>
        <span class="status-pill" style="{{ $user->status?->value === 'Blacklisted' ? 'background:#dc2626; color:#fff;' : '' }}">
            {{ $user->status?->value ?? 'Active' }}
        </span>
    </div>

    @if (session('status'))
        <p style="color:#16a34a; margin-bottom:16px;">{{ session('status') }}</p>
    @endif
    @if (session('error'))
        <p style="color:#dc2626; margin-bottom:16px;">{{ session('error') }}</p>
    @endif

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(120px, 1fr)); gap:12px; margin-bottom:28px;">
        <div class="panel" style="padding:16px; text-align:center;">
            <div style="font-size:22px; font-weight:800; color:var(--text);">{{ $user->created_groups_count }}</div>
            <div style="color:var(--muted); font-size:12px; margin-top:4px;">Groups Created</div>
        </div>
        <div class="panel" style="padding:16px; text-align:center;">
            <div style="font-size:22px; font-weight:800; color:var(--text);">{{ $user->groups_count }}</div>
            <div style="color:var(--muted); font-size:12px; margin-top:4px;">Groups Joined</div>
        </div>
        <div class="panel" style="padding:16px; text-align:center;">
            <div style="font-size:22px; font-weight:800; color:var(--text);">{{ $user->topics_count }}</div>
            <div style="color:var(--muted); font-size:12px; margin-top:4px;">Topics</div>
        </div>
        <div class="panel" style="padding:16px; text-align:center;">
            <div style="font-size:22px; font-weight:800; color:var(--text);">{{ $user->posts_count }}</div>
            <div style="color:var(--muted); font-size:12px; margin-top:4px;">Posts</div>
        </div>
        <div class="panel" style="padding:16px; text-align:center;">
            <div style="font-size:22px; font-weight:800; color:var(--text);">{{ $user->sent_messages_count }}</div>
            <div style="color:var(--muted); font-size:12px; margin-top:4px;">Messages</div>
        </div>
        <div class="panel" style="padding:16px; text-align:center;">
            <div style="font-size:22px; font-weight:800; color:var(--text);">{{ $user->warnings_count }}</div>
            <div style="color:var(--muted); font-size:12px; margin-top:4px;">Warnings</div>
        </div>
    </div>

    @if($createdGroups->isNotEmpty() || $joinedGroups->isNotEmpty())
        <h3 style="color:var(--text); font-weight:700; font-size:1rem; margin-bottom:12px;">Groups</h3>
        <div style="display:flex; flex-direction:column; gap:8px; margin-bottom:28px;">
            @foreach($createdGroups as $group)
                <div class="panel" style="padding:12px 16px; display:flex; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                    <span>{{ $group->name }} <span class="status-pill" style="margin-left:6px;">Creator</span></span>
                    <span style="color:var(--muted); font-size:12px;">{{ $group->members_count }} members &middot; {{ $group->topics_count }} topics</span>
                </div>
            @endforeach
            @foreach($joinedGroups as $group)
                <div class="panel" style="padding:12px 16px; display:flex; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                    <span>{{ $group->name }} <span class="status-pill" style="margin-left:6px;">{{ ucfirst($group->pivot->role ?? 'Member') }}</span></span>
                    <span style="color:var(--muted); font-size:12px;">{{ $group->members_count }} members &middot; {{ $group->topics_count }} topics</span>
                </div>
            @endforeach
        </div>
    @endif

    <h3 style="color:var(--text); font-weight:700; font-size:1rem; margin-bottom:12px;">Actions</h3>
    <div style="display:flex; flex-direction:column; gap:16px; margin-bottom:28px;">

        <form method="POST" action="{{ route('admin.users.warn', $user->id) }}" class="panel" style="padding:16px;">
            @csrf
            <label style="display:block; font-weight:600; margin-bottom:8px; color:var(--text); font-size:14px;">Issue a warning</label>
            <textarea name="reason" required rows="2" placeholder="Reason for this warning..."
                style="width:100%; border-radius:8px; border:1px solid var(--border); padding:10px; font-family:inherit; margin-bottom:10px; box-sizing:border-box;"></textarea>
            <button type="submit" class="dash-btn" @disabled($user->status?->value === 'Blacklisted')>Issue Warning</button>
        </form>

        @if ($user->status?->value === 'Blacklisted')
            <form method="POST" action="{{ route('admin.users.reinstate', $user->id) }}" class="panel" style="padding:16px;">
                @csrf
                <label style="display:block; font-weight:600; margin-bottom:8px; color:var(--text); font-size:14px;">Currently blacklisted</label>
                @if($activeBlacklistEntry)
                    <p style="color:var(--muted); font-size:13px; margin-bottom:10px;">
                        {{ $activeBlacklistEntry->Reason }} &mdash; access restored {{ $activeBlacklistEntry->Expires_at?->diffForHumans() }}
                    </p>
                @endif
                <button type="submit" class="dash-btn">Reinstate</button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.users.blacklist', $user->id) }}" class="panel" style="padding:16px;">
                @csrf
                <label style="display:block; font-weight:600; margin-bottom:8px; color:var(--text); font-size:14px;">Blacklist this user</label>
                <textarea name="reason" rows="2" placeholder="Reason for blacklisting (optional)..."
                    style="width:100%; border-radius:8px; border:1px solid var(--border); padding:10px; font-family:inherit; margin-bottom:10px; box-sizing:border-box;"></textarea>
                <label style="display:block; font-size:13px; color:var(--muted); margin-bottom:6px;">Duration in days (leave blank for the default)</label>
                <input type="number" name="duration_days" min="1" max="365" placeholder="e.g. 30"
                    style="width:140px; border-radius:8px; border:1px solid var(--border); padding:8px; margin-bottom:12px;">
                <div>
                    <button type="submit" class="dash-btn" style="color:#dc2626;">Blacklist User</button>
                </div>
            </form>
        @endif
    </div>

    @if($warnings->isNotEmpty())
        <h3 style="color:var(--text); font-weight:700; font-size:1rem; margin-bottom:12px;">Warning history</h3>
        <div style="display:flex; flex-direction:column; gap:8px; margin-bottom:28px;">
            @foreach($warnings as $warning)
                <div class="panel" style="padding:12px 16px;">
                    <div style="color:var(--text); font-size:13px;">{{ $warning->Reason }}</div>
                    <div style="color:var(--muted); font-size:12px; margin-top:4px;">{{ $warning->Issued_at?->diffForHumans() }}</div>
                </div>
            @endforeach
        </div>
    @endif

    @if($blacklistEntries->isNotEmpty())
        <h3 style="color:var(--text); font-weight:700; font-size:1rem; margin-bottom:12px;">Blacklist history</h3>
        <div style="display:flex; flex-direction:column; gap:8px;">
            @foreach($blacklistEntries as $entry)
                <div class="panel" style="padding:12px 16px;">
                    <div style="color:var(--text); font-size:13px;">{{ $entry->Reason }}</div>
                    <div style="color:var(--muted); font-size:12px; margin-top:4px;">
                        {{ $entry->Blacklisted_at?->format('M j, Y') }}
                        @if($entry->Expires_at)
                            &ndash; {{ $entry->Expires_at->format('M j, Y') }}
                        @endif
                        @if($entry->isActive())
                            <span class="status-pill" style="margin-left:6px;">Active</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
