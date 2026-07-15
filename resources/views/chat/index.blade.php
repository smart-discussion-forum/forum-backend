@extends('layouts.app')

@section('content')
<div class="page-card" style="max-width:900px; margin:30px auto; padding:30px;">
    <div class="screen-title">Group Chat</div>
    <div style="display:flex; gap:20px;">

        <div style="width:250px; border-right:1px solid var(--border);">
            <h3 style="margin-bottom:15px;">Your Groups</h3>
            @forelse($groups as $group)
            <div onclick='openGroup({{ $group->id }}, @json($group->name))'
                style="padding:10px; cursor:pointer; border-radius:8px;"
                class="dash-btn group-item"
                data-group-id="{{ $group->id }}">
                {{ $group->name }}
            </div>
            @empty
            <p style="color:var(--muted); font-size:14px;">You are not in any groups yet.</p>
            @endforelse
        </div>

        <div style="flex:1;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <div id="chat-header" style="font-weight:bold;">
                Select a group to chat
            </div>
            <a id="topics-link" href="#" class="dash-btn" style="display:none;">Topics</a>
        </div>
            <div id="messages" style="height:400px; overflow-y:auto; border:1px solid var(--border); border-radius:8px; padding:15px; margin-bottom:15px;">
            </div>
            <div style="display:flex; gap:10px;">
                <input type="text" id="message-input" placeholder="Type a message..."
                    style="flex:1; padding:10px; border-radius:8px; border:1px solid var(--border); background:var(--bg-1); color:white;"
                    disabled>
                <button onclick="sendMessage()" class="btn" id="send-btn" disabled>Send</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>
<script>
const authUserId = Number(@json(auth()->id()));
const token = @json(session('api_token'));
let currentGroupId = null;
let echoChannel = null;
const messageIds = new Set();
let pollingTimer = null;
let tempMessageCounter = 0;

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: @json(env('REVERB_APP_KEY')),
    wsHost: @json(env('REVERB_HOST', 'localhost')),
    wsPort: {{ env('REVERB_PORT', 8080) }},
    wssPort: {{ env('REVERB_PORT', 8080) }},
    forceTLS: @json(env('REVERB_SCHEME', 'http') === 'https'),
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            Authorization: 'Bearer ' + token,
            Accept: 'application/json',
        },
    },
});

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function renderMessage(msg) {
    const isMe = String(msg.sender_id) === String(authUserId);
    const senderName = isMe ? 'You' : (msg.sender?.name || 'Unknown');
    return `<div data-message-id="${escapeHtml(String(msg.id))}" style="text-align:${isMe ? 'right' : 'left'}; margin-bottom:10px;">
        <div style="font-size:12px; color:var(--muted); margin-bottom:4px;">${escapeHtml(senderName)}</div>
        <span style="background:${isMe ? '#0084ff' : '#25D366'}; padding:8px 12px; border-radius:8px; display:inline-block; color:white;">
            ${escapeHtml(msg.content)}
        </span>
    </div>`;
}

function removeMessageById(id) {
    messageIds.delete(id);
    const el = document.querySelector(`[data-message-id="${CSS.escape(String(id))}"]`);
    if (el) el.remove();
}

function appendMessage(msg) {
    if (!msg || typeof msg.content !== 'string' || msg.content.trim() === '' || messageIds.has(msg.id)) {
        return;
    }

    messageIds.add(msg.id);
    const container = document.getElementById('messages');
    container.insertAdjacentHTML('beforeend', renderMessage(msg));
    container.scrollTop = container.scrollHeight;
}

function loadMessages(groupId) {
    fetch('/api/messages/group/' + groupId, {
        headers: {
            Authorization: 'Bearer ' + token,
            Accept: 'application/json',
        },
    })
    .then(res => res.json())
    .then(messages => {
        const container = document.getElementById('messages');
        container.innerHTML = '';
        messageIds.clear();
        messages.forEach(msg => appendMessage(msg));
    });
}

function subscribeToGroup(groupId) {
    if (echoChannel) {
        window.Echo.leave('group.' + currentGroupId);
        echoChannel = null;
    }

    echoChannel = window.Echo.private('group.' + groupId)
        .listen('.message.sent', (e) => {
            const eventData = e?.message ? e.message : e;
            if (groupId === currentGroupId) {
                appendMessage(eventData);
            }
        });
}

function openGroup(groupId, groupName) {
    currentGroupId = groupId;
    document.getElementById('chat-header').innerText = groupName;
    document.getElementById('message-input').disabled = false;
    document.getElementById('send-btn').disabled = false;
    const topicsLink = document.getElementById('topics-link');
    topicsLink.href = '/groups/' + groupId + '/topics';
    topicsLink.style.display = 'inline-block';

    document.querySelectorAll('.group-item').forEach(el => {
        el.style.background = el.dataset.groupId == groupId ? 'var(--accent-soft)' : '';
    });

    loadMessages(groupId);
    subscribeToGroup(groupId);

    if (pollingTimer) {
        clearInterval(pollingTimer);
    }

    pollingTimer = setInterval(() => {
        if (currentGroupId) {
            loadMessages(currentGroupId);
        }
    }, 3000);
}

function sendMessage() {
    const input = document.getElementById('message-input');
    const content = input.value.trim();
    if (!content || !currentGroupId) return;

    input.disabled = true;
    document.getElementById('send-btn').disabled = true;

    const tempMessage = {
        id: `temp-${Date.now()}-${tempMessageCounter++}`,
        sender_id: authUserId,
        content: content,
        sender: { name: 'You' },
    };

    appendMessage(tempMessage);
    input.value = '';

    fetch('/api/messages/send', {
        method: 'POST',
        headers: {
            Authorization: 'Bearer ' + token,
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            group_id: currentGroupId,
            content: content,
        }),
    })
    .then(async res => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success || !data.message) {
            throw new Error(typeof data.message === 'string' ? data.message : 'Failed to send message.');
        }
        // Replace optimistic "You" bubble with the saved message (same bubble, real id)
        removeMessageById(tempMessage.id);
        appendMessage(data.message);
    })
    .catch((err) => {
        removeMessageById(tempMessage.id);
        input.value = content;
        alert(err.message || 'Failed to send message.');
    })
    .finally(() => {
        input.disabled = false;
        document.getElementById('send-btn').disabled = false;
        input.focus();
    });
}

document.getElementById('message-input').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') sendMessage();
});
const urlParams = new URLSearchParams(window.location.search);
const initialGroupId = urlParams.get('group');
if (initialGroupId) {
    const groupEl = document.querySelector('.group-item[data-group-id="' + initialGroupId + '"]');
    if (groupEl) {
        openGroup(Number(initialGroupId), groupEl.textContent.trim());
    }
}
</script>
@endpush
