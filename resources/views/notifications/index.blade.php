@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="page-card" style="max-width:700px; margin:30px auto; padding:32px;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:20px;">
        <div class="screen-title" style="text-align:left; margin:0; font-size:24px;">Notifications</div>
        <form method="POST" action="{{ route('notifications.read-all') }}" style="margin:0;">
            @csrf
            <button type="submit" class="dash-btn" style="margin:0;">Mark all read</button>
        </form>
    </div>

    @if (session('status'))
        <p style="color:#16a34a; margin-bottom:16px;">{{ session('status') }}</p>
    @endif

    <div style="display:flex; flex-direction:column; gap:10px;">
        @forelse($notifications as $n)
            <div class="panel notif-page-item {{ $n->read_at ? '' : 'unread' }}" data-id="{{ $n->id }}"
                 style="padding:14px 16px; display:flex; justify-content:space-between; align-items:flex-start; gap:12px; {{ $n->read_at ? '' : 'border-left:3px solid var(--accent-strong);' }}">
                <div>
                    <div style="color:var(--text); font-size:14px;">{{ $n->data['message'] ?? 'New notification' }}</div>
                    <div style="color:var(--muted); font-size:12px; margin-top:4px;">{{ $n->created_at->diffForHumans() }}</div>
                </div>
                @unless($n->read_at)
                    <form method="POST" action="{{ route('notifications.read', $n->id) }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="status-pill" style="white-space:nowrap; cursor:pointer; border:none;">Mark read</button>
                    </form>
                @endunless
            </div>
        @empty
            <div class="empty-panel">You don't have any notifications yet.</div>
        @endforelse
    </div>

    <div style="margin-top:20px;">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
