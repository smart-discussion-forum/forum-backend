@extends('layouts.app')
@section('content')
    @php
        $currentUserId = auth()->id();
    @endphp

    <div class="page-card" style="max-width:1320px; margin:24px auto; padding:18px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; padding:0 4px;">
            <div class="screen-title" style="margin:0; text-align:left; color:var(--text);">{{ $group->name }}</div>
            <a href="/chat?group={{ $group->id }}" class="dash-btn">Back to Group Chat</a>
        </div>
        <div class="discussion-shell">
            <aside class="discussion-sidebar">
                <div class="discussion-sidebar-header">
                    <div>
                        <div class="screen-title" style="margin-bottom:6px; text-align:left; color:var(--text);">Topics</div>
                        <div class="sidebar-copy">Open a topic thread to see the conversation.</div>
                    </div>
                    <a href="/groups/{{ $group->id }}/topics/create" class="chat-btn">New topic</a>
                </div>

                <div class="topic-list">
                    @forelse($topics as $topicItem)
                        <a href="/groups/{{ $group->id }}/topics/{{ $topicItem->id }}" class="topic-card {{ $topic->id === $topicItem->id ? 'active' : '' }}">
                            <div class="topic-card-title">{{ $topicItem->title }}</div>
                            <div class="topic-card-meta">
                                {{ $topicItem->creator?->name ?? 'Unknown author' }}
                                @if($topicItem->category)
                                    <span class="topic-dot">•</span>{{ $topicItem->category }}
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="empty-panel">No topics yet. Create the first discussion to get started.</div>
                    @endforelse
                </div>
            </aside>

            <section class="discussion-conversation">
                <div class="conversation-header">
                    <div>
                        <div class="conversation-title">{{ $topic->title }}</div>
                        <div class="conversation-subtitle">
                            Started by {{ $topic->creator?->name ?? 'Unknown author' }}
                            @if($topic->category)
                                <span class="topic-dot">•</span>{{ $topic->category }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="chat-thread" id="chat-thread">
                    @forelse($posts as $post)
                        <div class="chat-row {{ $post->user_id === $currentUserId ? 'mine' : '' }}" data-post-id="{{ $post->id }}">
                            <div class="chat-bubble">
                                <div class="chat-meta">{{ $post->user_id === $currentUserId ? 'You' : ($post->user?->name ?? 'Unknown user') }}</div>
                                <div class="chat-text">{{ $post->content }}</div>
                                <div class="chat-time">
                                    {{ optional($post->created_at)->format('M j, Y g:i A') ?? '' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-panel">No discussion yet. Be the first to reply.</div>
                    @endforelse
                </div>

                <div class="reply-form">
                    <textarea id="reply-content" placeholder="Type your reply..."></textarea>
                    <div class="reply-actions">
                        <button type="button" class="chat-btn" id="reply-send-btn">Send</button>
                    </div>
                </div>
            </section>
        </div>
    </div>

<script>
(function () {
    if (window.__topicThreadBound) return;
    window.__topicThreadBound = true;

    const csrfToken = @json(csrf_token());
    const token = @json(session('api_token'));
    const authUserId = Number(@json(auth()->id()));
    const currentGroupId = Number(@json($group->id));
    const currentTopicId = Number(@json($topic->id));
    const postIds = new Set(@json($posts->pluck('id')->values()));
    const postsUrl = '/groups/' + currentGroupId + '/topics/' + currentTopicId + '/posts';
    const apiPostsUrl = '/api/topics/' + currentTopicId + '/posts';

    const textarea = document.getElementById('reply-content');
    const button = document.getElementById('reply-send-btn');
    let sending = false;

    function formatTime(value) {
        if (!value) return '';
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return '';
        return date.toLocaleString();
    }

    function appendPost(post) {
        if (!post || post.id == null || postIds.has(post.id)) return;
        if (post.topic_id != null && Number(post.topic_id) !== currentTopicId) return;

        postIds.add(post.id);

        const thread = document.getElementById('chat-thread');
        const emptyPanel = thread.querySelector('.empty-panel');
        if (emptyPanel) emptyPanel.remove();

        const userId = Number(post.user?.id ?? post.user_id);
        const isMine = userId === authUserId;
        const name = isMine ? 'You' : (post.user?.name || 'Unknown user');

        const row = document.createElement('div');
        row.className = 'chat-row' + (isMine ? ' mine' : '');
        row.dataset.postId = String(post.id);
        row.innerHTML = `
            <div class="chat-bubble">
                <div class="chat-meta"></div>
                <div class="chat-text"></div>
                <div class="chat-time"></div>
            </div>
        `;
        row.querySelector('.chat-meta').textContent = name;
        row.querySelector('.chat-text').textContent = post.content;
        row.querySelector('.chat-time').textContent = formatTime(post.created_at);

        thread.appendChild(row);
        thread.scrollTop = thread.scrollHeight;
    }

    function loadPosts() {
        const headers = { Accept: 'application/json' };
        if (token) headers.Authorization = 'Bearer ' + token;

        fetch(apiPostsUrl, { headers })
            .then(res => res.json())
            .then(posts => {
                if (!Array.isArray(posts)) return;
                posts.forEach(appendPost);
            })
            .catch(() => {});
    }

    function sendReply() {
        if (sending) return;

        const content = textarea.value.trim();
        if (!content) return;

        sending = true;
        button.disabled = true;
        textarea.disabled = true;

        fetch(postsUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ content }),
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.success || !data.post) {
                throw new Error(typeof data.message === 'string' ? data.message : 'Failed to send reply.');
            }
            appendPost(data.post);
            textarea.value = '';
        })
        .catch((err) => {
            alert(err.message || 'Failed to send reply.');
        })
        .finally(() => {
            sending = false;
            button.disabled = false;
            textarea.disabled = false;
            textarea.focus();
        });
    }

    button.addEventListener('click', sendReply);
    textarea.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendReply();
        }
    });

    setInterval(loadPosts, 3000);
    document.getElementById('chat-thread').scrollTop = document.getElementById('chat-thread').scrollHeight;

    function initRealtime() {
        if (typeof Pusher === 'undefined' || typeof Echo === 'undefined') return;

        try {
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
                        Authorization: token ? ('Bearer ' + token) : '',
                        Accept: 'application/json',
                    },
                },
            });

            window.Echo.private('group.' + currentGroupId)
                .listen('.post.created', (event) => appendPost(event));
        } catch (err) {
            console.warn('Topic realtime unavailable:', err);
        }
    }

    const pusherScript = document.createElement('script');
    pusherScript.src = 'https://js.pusher.com/8.2.0/pusher.min.js';
    pusherScript.onload = function () {
        const echoScript = document.createElement('script');
        echoScript.src = 'https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js';
        echoScript.onload = initRealtime;
        document.body.appendChild(echoScript);
    };
    document.body.appendChild(pusherScript);
})();
</script>
@endsection
