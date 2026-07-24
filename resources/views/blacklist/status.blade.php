@extends('layouts.app')

@section('title', 'Blacklist Status')

@section('content')
<div class="page-card" style="max-width:600px; margin:30px auto; padding:32px;">
    <div class="screen-title" style="text-align:left; margin-bottom:20px; font-size:24px;">Account Status</div>

    @if($activeEntry)
        <div class="panel" style="padding:20px; border-left:4px solid #dc2626; margin-bottom:24px;">
            <div style="font-weight:700; color:#dc2626; margin-bottom:8px;">Your account is currently blacklisted</div>
            <div style="color:var(--text); font-size:14px; margin-bottom:10px;">{{ $activeEntry->Reason }}</div>
            <div style="color:var(--muted); font-size:13px;">
                Blacklisted {{ $activeEntry->Blacklisted_at?->diffForHumans() }}
                @if($activeEntry->Expires_at)
                    · Access restored {{ $activeEntry->Expires_at->diffForHumans() }}
                @endif
            </div>
        </div>
        <p style="color:var(--muted); font-size:13px; margin-bottom:24px;">
            While blacklisted you can browse the app, but can't post topics, replies, or messages.
            If you think this was a mistake, contact your lecturer or an admin.
        </p>
    @else
        <div class="empty-panel" style="margin-bottom:24px;">
            You're in good standing — no active blacklist on your account.
        </div>
    @endif

    @if($entries->isNotEmpty())
        <h3 style="color:var(--text); font-weight:700; font-size:1rem; margin-bottom:12px;">History</h3>
        <div style="display:flex; flex-direction:column; gap:10px;">
            @foreach($entries as $entry)
                <div class="panel" style="padding:14px 16px;">
                    <div style="color:var(--text); font-size:13px;">{{ $entry->Reason }}</div>
                    <div style="color:var(--muted); font-size:12px; margin-top:4px;">
                        {{ $entry->Blacklisted_at?->format('M j, Y') }}
                        @if($entry->Expires_at)
                            – {{ $entry->Expires_at->format('M j, Y') }}
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