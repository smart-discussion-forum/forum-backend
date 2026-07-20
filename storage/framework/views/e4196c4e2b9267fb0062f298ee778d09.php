<?php $__env->startSection('content'); ?>
    <?php
        $currentUserId = auth()->id();
    ?>

    <div class="page-card" style="max-width:1320px; margin:24px auto; padding:18px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; padding:0 4px;">
            <div class="screen-title" style="margin:0; text-align:left; color:var(--text);"><?php echo e($group->name); ?></div>
            <a href="/chat?group=<?php echo e($group->id); ?>" class="dash-btn">Back to Group Chat</a>
        </div>
        <div class="discussion-shell">
            <aside class="discussion-sidebar">
                <div class="discussion-sidebar-header">
                    <div>
                        <div class="screen-title" style="margin-bottom:6px; text-align:left; color:var(--text);">Topics</div>
                        <div class="sidebar-copy">Open a topic thread to see the conversation.</div>
                    </div>
                    <a href="/groups/<?php echo e($group->id); ?>/topics/create" class="chat-btn">New topic</a>
                </div>

                <div class="topic-list">
                    <?php $__empty_1 = true; $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topicItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="/groups/<?php echo e($group->id); ?>/topics/<?php echo e($topicItem->id); ?>" class="topic-card <?php echo e($topic->id === $topicItem->id ? 'active' : ''); ?>">
                            <div class="topic-card-title"><?php echo e($topicItem->title); ?></div>
                            <div class="topic-card-meta">
                                <?php echo e($topicItem->creator?->name ?? 'Unknown author'); ?>

                                <?php if($topicItem->category): ?>
                                    <span class="topic-dot">•</span><?php echo e($topicItem->category); ?>

                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="empty-panel">No topics yet. Create the first discussion to get started.</div>
                    <?php endif; ?>
                </div>
            </aside>

            <section class="discussion-conversation">
                <div class="conversation-header">
                    <div>
                        <div class="conversation-title"><?php echo e($topic->title); ?></div>
                        <div class="conversation-subtitle">
                            Started by <?php echo e($topic->creator?->name ?? 'Unknown author'); ?>

                            <?php if($topic->category): ?>
                                <span class="topic-dot">•</span><?php echo e($topic->category); ?>

                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="chat-thread" id="chat-thread">
                    <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="chat-row <?php echo e($post->user_id === $currentUserId ? 'mine' : ''); ?>" data-post-id="<?php echo e($post->id); ?>">
                            <div class="chat-bubble">
                                <div class="chat-meta"><?php echo e($post->user_id === $currentUserId ? 'You' : ($post->user?->name ?? 'Unknown user')); ?></div>
                                <div class="chat-text"><?php echo e($post->content); ?></div>
                                <div class="chat-time">
                                    <?php echo e(optional($post->created_at)->format('M j, Y g:i A') ?? ''); ?>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="empty-panel">No discussion yet. Be the first to reply.</div>
                    <?php endif; ?>
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

    const csrfToken = <?php echo json_encode(csrf_token(), 15, 512) ?>;
    const token = <?php echo json_encode(session('api_token'), 15, 512) ?>;
    const authUserId = Number(<?php echo json_encode(auth()->id(), 15, 512) ?>);
    const currentGroupId = Number(<?php echo json_encode($group->id, 15, 512) ?>);
    const currentTopicId = Number(<?php echo json_encode($topic->id, 15, 512) ?>);
    const postIds = new Set(<?php echo json_encode($posts->pluck('id')->values(), 15, 512) ?>);
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
                key: <?php echo json_encode(env('REVERB_APP_KEY'), 15, 512) ?>,
                wsHost: <?php echo json_encode(env('REVERB_HOST', 'localhost'), 512) ?>,
                wsPort: <?php echo e(env('REVERB_PORT', 8080)); ?>,
                wssPort: <?php echo e(env('REVERB_PORT', 8080)); ?>,
                forceTLS: <?php echo json_encode(env('REVERB_SCHEME', 'http') === 'https', 512) ?>,
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\LENOVO\Documents\forum-backend\resources\views/discussions/group-show.blade.php ENDPATH**/ ?>