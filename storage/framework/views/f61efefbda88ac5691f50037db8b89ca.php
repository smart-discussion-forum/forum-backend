
<?php $__env->startSection('content'); ?>
    <?php
        $selectedTopic = $topic ?? $activeTopic ?? null;
        $currentUserId = auth()->id();
    ?>

    <div class="page-card" style="max-width:1320px; margin:24px auto; padding:18px;">
        <div class="discussion-shell">
            <aside class="discussion-sidebar">
                <div class="discussion-sidebar-header">
                    <div>
                        <div class="screen-title" style="margin-bottom:6px; text-align:left; color:var(--text);">Discussions</div>
                        <div class="sidebar-copy">Open a topic thread to see the conversation.</div>
                    </div>
                    <a href="/topics/create" class="chat-btn">New topic</a>
                </div>

                <div class="topic-list">
                    <?php $__empty_1 = true; $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topicItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="/discussions/<?php echo e($topicItem->id); ?>" class="topic-card <?php echo e($selectedTopic && $selectedTopic->id === $topicItem->id ? 'active' : ''); ?>">
                            <div class="topic-card-title"><?php echo e($topicItem->title); ?></div>
                            <div class="topic-card-meta">
                                <?php echo e($topicItem->user?->name ?? 'Unknown author'); ?>

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
                <?php if($selectedTopic): ?>
                    <div class="conversation-header">
                        <div>
                            <div class="conversation-title"><?php echo e($selectedTopic->title); ?></div>
                            <div class="conversation-subtitle">
                                Started by <?php echo e($selectedTopic->user?->name ?? 'Unknown author'); ?>

                                <?php if($selectedTopic->category): ?>
                                    <span class="topic-dot">•</span><?php echo e($selectedTopic->category); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="chat-thread">
                        <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="chat-row <?php echo e($post->user_id === $currentUserId ? 'mine' : ''); ?>">
                                <div class="chat-bubble">
                                    <div class="chat-meta"><?php echo e($post->user?->name ?? 'Unknown user'); ?></div>
                                    <div class="chat-text"><?php echo e($post->content); ?></div>
                                    <div class="chat-time">
                                        <?php echo e(optional($post->created_at)->format('M j, Y g:i A') ?? ''); ?>

                                    </div>
                                    <div class="chat-actions">
                                        <form method="POST" action="/topics/<?php echo e($selectedTopic->id); ?>/posts/<?php echo e($post->id); ?>/reaction">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="reaction-btn <?php echo e(in_array($post->id, $reactedPostIds ?? []) ? 'active' : ''); ?>">
                                                <?php echo e(in_array($post->id, $reactedPostIds ?? []) ? 'Unlike' : 'Like'); ?>

                                                <?php if(isset($post->reactions_count)): ?>
                                                    (<?php echo e($post->reactions_count); ?>)
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="empty-panel">No discussion yet. Be the first to reply.</div>
                        <?php endif; ?>
                    </div>

                    <form method="POST" action="/topics/<?php echo e($selectedTopic->id); ?>/posts" class="reply-form">
                        <?php echo csrf_field(); ?>
                        <textarea name="content" placeholder="Type your reply..."></textarea>
                        <div class="reply-actions">
                            <button type="submit" class="chat-btn">Send</button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="empty-conversation">
                        <div class="conversation-title">Select a topic</div>
                        <p>Pick a discussion from the left to open the chat thread, or start a new topic.</p>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dell/forum-backend-development/resources/views/discussions/index.blade.php ENDPATH**/ ?>