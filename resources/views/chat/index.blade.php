@extends('layouts.app')

@section('content')
<div class="page-card" style="max-width:900px; margin:30px auto; padding:30px;">
    <div class="screen-title">Group Chat</div>
    <div style="display:flex; gap:20px;">

        <div style="width:250px; border-right:1px solid var(--border); display:flex; flex-direction:column;">
            <h3 style="margin-bottom:15px;">Your Groups</h3>
            <div style="flex:1;">
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
            @if(auth()->user()->role !== \App\Enums\RoleEnum::Admin)
            <a href="{{ route('groups.index') }}" class="dash-btn" style="display:inline-block; margin-top:10px;">Browse Groups</a>
            @endif
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
           <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
    <input type="text" id="message-input" placeholder="Type a message..."
        style="flex:1; min-width:200px; padding:10px; border-radius:8px; border:1px solid var(--border); background:var(--bg-1); color:white;" disabled>
    <div id="exclude-dropdown" style="position:relative; opacity:0.6; pointer-events:none;">
        <button type="button" id="exclude-toggle" onclick="toggleExcludeDropdown()"
            style="padding:8px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg-1); color:white; font-size:12px; cursor:pointer; white-space:nowrap;">
            Exclude members ▾
        </button>
        <div id="exclude-menu" style="display:none; position:absolute; bottom:calc(100% + 6px); right:0; z-index:20; min-width:180px; max-width:240px; padding:8px; border-radius:8px; border:1px solid var(--border); background:var(--bg-1); box-shadow:0 8px 24px rgba(0,0,0,0.35);">
            <div style="font-size:12px; color:var(--muted); margin-bottom:6px;">Exclude members</div>
            <div id="exclude-checkboxes" style="display:flex; flex-direction:column; gap:6px; max-height:160px; overflow-y:auto; font-size:12px; color:white;">
                <span style="color:var(--muted);">Select a group first</span>
            </div>
        </div>
    </div>
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
const groupsData = @json($groupsData);
let currentGroupId = null;
let currentGroupMembers = [];
let echoChannel = null;
const messageIds = new Set();
let tempMessageCounter = 0;

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: @json(env('REVERB_APP_KEY')),
    wsHost: @json(env('REVERB_HOST', 'localhost')),
    wsPort: @json((int) (env('REVERB_PORT') ?: 8080)),
    wssPort: @json((int) (env('REVERB_PORT') ?: 8080)),
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

    const excludedIds = Array.isArray(msg.excluded_user_ids) ? msg.excluded_user_ids : [];
    if (excludedIds.includes(authUserId)) {
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

function setExcludeDropdownEnabled(enabled) {
    const dropdown = document.getElementById('exclude-dropdown');
    dropdown.style.opacity = enabled ? '1' : '0.6';
    dropdown.style.pointerEvents = enabled ? 'auto' : 'none';
    if (!enabled) {
        closeExcludeDropdown();
    }
}

function updateExcludeToggleLabel() {
    const count = getSelectedExcludedUserIds().length;
    const toggle = document.getElementById('exclude-toggle');
    toggle.textContent = count > 0 ? `Exclude members (${count}) ▾` : 'Exclude members ▾';
}

function toggleExcludeDropdown() {
    const menu = document.getElementById('exclude-menu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

function closeExcludeDropdown() {
    document.getElementById('exclude-menu').style.display = 'none';
}

function getSelectedExcludedUserIds() {
    return Array.from(document.querySelectorAll('#exclude-checkboxes input[type="checkbox"]:checked'))
        .map(checkbox => Number(checkbox.value))
        .filter(Boolean);
}

function populateMemberOptions(groupId) {
    const container = document.getElementById('exclude-checkboxes');
    container.innerHTML = '';
    setExcludeDropdownEnabled(false);
    updateExcludeToggleLabel();

    const group = groupsData.find(item => Number(item.id) === Number(groupId));
    currentGroupMembers = group?.members || [];

    const members = currentGroupMembers.filter(member => Number(member.id) !== authUserId);
    if (members.length === 0) {
        container.innerHTML = '<span style="color:var(--muted);">No other members</span>';
        updateExcludeToggleLabel();
        return;
    }

    members.forEach(member => {
        const label = document.createElement('label');
        label.style.display = 'flex';
        label.style.alignItems = 'center';
        label.style.gap = '6px';
        label.style.cursor = 'pointer';

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.value = String(member.id);
        checkbox.className = 'exclude-member-checkbox';
        checkbox.addEventListener('change', updateExcludeToggleLabel);

        const name = document.createElement('span');
        name.textContent = member.name;

        label.appendChild(checkbox);
        label.appendChild(name);
        container.appendChild(label);
    });

    setExcludeDropdownEnabled(true);
    updateExcludeToggleLabel();
}

document.addEventListener('click', (event) => {
    const dropdown = document.getElementById('exclude-dropdown');
    if (!dropdown.contains(event.target)) {
        closeExcludeDropdown();
    }
});

function openGroup(groupId, groupName) {
    currentGroupId = groupId;
    currentGroupMembers = [];
    document.getElementById('chat-header').innerText = groupName;
    document.getElementById('message-input').disabled = false;
    document.getElementById('send-btn').disabled = false;
    setExcludeDropdownEnabled(false);
    document.getElementById('exclude-checkboxes').innerHTML = '<span style="color:var(--muted);">Loading...</span>';
    updateExcludeToggleLabel();
    const topicsLink = document.getElementById('topics-link');
    topicsLink.href = '/groups/' + groupId + '/topics';
    topicsLink.style.display = 'inline-block';

    document.querySelectorAll('.group-item').forEach(el => {
        el.style.background = el.dataset.groupId == groupId ? 'var(--accent-soft)' : '';
    });

    loadMessages(groupId);
    populateMemberOptions(groupId);
    subscribeToGroup(groupId);
}

function sendMessage() {
    const input = document.getElementById('message-input');
    const content = input.value.trim();
    if (!content || !currentGroupId) return;

    input.disabled = true;
    document.getElementById('send-btn').disabled = true;

    const excludedUserIds = getSelectedExcludedUserIds();

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
            excluded_user_ids: excludedUserIds,
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