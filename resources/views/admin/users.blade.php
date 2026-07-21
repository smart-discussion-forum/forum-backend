@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
<div class="page-card" style="max-width:1080px; margin:30px auto; padding:30px;">

    <h2 class="screen-title" style="color:var(--text); text-align:left; margin-bottom:20px;">
        Manage Users
    </h2>

    @if (session('status'))
        <p style="color:#16a34a; margin-bottom:16px;">{{ session('status') }}</p>
    @endif

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Warnings</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role->value }}</td>
                        <td>
                            <span style="{{ $user->status?->value === 'Blacklisted' ? 'color:#dc2626; font-weight:600;' : 'color:var(--text);' }}">
                                {{ $user->status?->value ?? 'Active' }}
                            </span>
                        </td>
                        <td>{{ $user->warnings_count }}</td>
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
                        <td colspan="6" style="color:var(--muted);">No users yet.</td>
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
