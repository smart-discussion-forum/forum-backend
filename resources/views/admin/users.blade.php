@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
<div class="page-card" style="max-width:1100px; margin:30px auto; padding:30px;">

    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:20px;">
        <h2 class="screen-title" style="color:var(--text); text-align:left; margin:0;">
            Manage Users
        </h2>
        <form method="POST" action="{{ route('admin.users.run-inactivity-check') }}" style="margin:0;">
            @csrf
            <button type="submit" class="dash-btn" style="padding:8px 14px; font-size:0.875rem;">
                Run inactivity check
            </button>
        </form>
    </div>

    @if (session('status'))
        <p style="color:#16a34a; margin-bottom:16px;">{{ session('status') }}</p>
    @endif

    <div style="margin:0 0 22px; padding:16px 18px; border-radius:16px; border:1px solid rgba(148,163,184,0.22); background:rgba(255,255,255,0.65);">
        <p style="margin:0 0 8px; color:var(--text); font-weight:700;">Automatic inactivity policy</p>
        <p style="margin:0; color:var(--muted); font-size:0.925rem; line-height:1.55;">
            Students only (lecturers and admins are excluded). Students who do not communicate get
            <strong style="color:var(--text);">warning 1 after {{ $moderation['first_warning_days'] }} day(s)</strong>,
            <strong style="color:var(--text);">warning 2 after {{ $moderation['second_warning_days'] }} more day(s)</strong>,
            then are
            <strong style="color:var(--text);">blacklisted for {{ $moderation['blacklist_duration_days'] }} day(s)</strong>
            if they stay inactive
            <strong style="color:var(--text);">{{ $moderation['blacklist_after_days'] }} day(s) after the second warning</strong>.
            This runs daily automatically; use the button above to run it now.
        </p>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Last active</th>
                    <th>Status</th>
                    <th>Manual</th>
                    <th>Auto</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role->value }}</td>
                        <td style="color:var(--muted); white-space:nowrap;">
                            {{ $user->last_active ? $user->last_active->diffForHumans() : 'Never' }}
                        </td>
                        <td>
                            <span style="{{ $user->status?->value === 'Blacklisted' ? 'color:#dc2626; font-weight:600;' : 'color:var(--text);' }}">
                                {{ $user->status?->value ?? 'Active' }}
                            </span>
                        </td>
                        <td>{{ $user->manual_warnings_count }}</td>
                        <td>{{ $user->auto_warnings_count }}</td>
                        <td class="quiz-actions">
                            <form method="POST" action="{{ route('admin.users.warn', $user->id) }}" style="display:inline;"
                                  onsubmit="return promptWarningReason(this);">
                                @csrf
                                <input type="hidden" name="reason" value="">
                                <button type="submit" class="dash-btn" style="padding:6px 12px; font-size:0.8rem;" @disabled($user->status?->value === 'Blacklisted')>
                                    Warn
                                </button>
                            </form>

                            @if ($user->status?->value === 'Blacklisted')
                                <form method="POST" action="{{ route('admin.users.reinstate', $user->id) }}" style="display:inline;"
                                      onsubmit="return confirm('Reinstate {{ $user->name }}?');">
                                    @csrf
                                    <button type="submit" class="dash-btn" style="padding:6px 12px; font-size:0.8rem;">
                                        Reinstate
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.users.blacklist', $user->id) }}" style="display:inline;"
                                      onsubmit="return confirm('Blacklist {{ $user->name }}? They will be blocked from posting, creating topics, and messaging.');">
                                    @csrf
                                    <button type="submit" class="dash-btn" style="padding:6px 12px; font-size:0.8rem; color:#dc2626;">
                                        Blacklist
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="color:var(--muted);">No users yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<script>
    function promptWarningReason(form) {
        const reason = prompt('Reason for this warning:');
        if (!reason) {
            return false;
        }
        form.querySelector('input[name="reason"]').value = reason;
        return true;
    }
</script>
@endsection
