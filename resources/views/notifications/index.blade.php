@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="page-card" style="max-width:700px; margin:30px auto; padding:32px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div class="screen-title" style="text-align:left; margin:0; font-size:24px;">Notifications</div>
        <button type="button" id="pageMarkAllRead" class="dash-btn" style="margin:0;">Mark all read</button>
    </div>

    <div style="display:flex; flex-direction:column; gap:10px;">
        @forelse($notifications as $n)
            <div class="panel notif-page-item {{ $n->read_at ? '' : 'unread' }}" data-id="{{ $n->id }}"
                 style="padding:14px 16px; display:flex; justify-content:space-between; align-items:flex-start; gap:12px; {{ $n->read_at ? '' : 'border-left:3px solid var(--accent-strong);' }}">
                <div>
                    <div style="color:var(--text); font-size:14px;">{{ $n->data['message'] ?? 'New notification' }}</div>
                    <div style="color:var(--muted); font-size:12px; margin-top:4px;">{{ $n->created_at->diffForHumans() }}</div>
                </div>
                @unless($n->read_at)
                    <span class="status-pill" style="white-space:nowrap;">Unread</span>
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

@push('scripts')
<script>
    const pageNotifToken = @json(session('api_token'));

    document.querySelectorAll('.notif-page-item.unread').forEach(function(item) {
        item.addEventListener('click', function() {
            fetch('/api/notifications/' + item.dataset.id + '/read', {
                method: 'POST',
                headers: { Authorization: 'Bearer ' + pageNotifToken, Accept: 'application/json' },
            }).then(() => {
                item.classList.remove('unread');
                item.style.borderLeft = 'none';
                const pill = item.querySelector('.status-pill');
                if (pill) pill.remove();
            });
        });
    });

    document.getElementById('pageMarkAllRead')?.addEventListener('click', function() {
        fetch('/api/notifications/read-all', {
            method: 'POST',
            headers: { Authorization: 'Bearer ' + pageNotifToken, Accept: 'application/json' },
        }).then(() => {
            document.querySelectorAll('.notif-page-item.unread').forEach(function(item) {
                item.classList.remove('unread');
                item.style.borderLeft = 'none';
                const pill = item.querySelector('.status-pill');
                if (pill) pill.remove();
            });
        });
    });
</script>
@endpush