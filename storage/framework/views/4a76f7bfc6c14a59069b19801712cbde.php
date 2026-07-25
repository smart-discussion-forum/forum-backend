<?php $__env->startSection('content'); ?>
    <div class="page-card" style="max-width:900px; margin:30px auto; padding:30px;">
        <div style="display:flex; justify-content:space-between; gap:16px; align-items:center; margin-bottom:8px;">
            <div>
                <div class="screen-title" style="margin-bottom:6px;"><?php echo e($group->name); ?> Topics</div>
                <p style="color:var(--muted); margin:0;">Choose a topic to open its discussion.</p>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end;">
                <a href="<?php echo e(route('chat', ['group' => $group->id])); ?>" class="dash-btn">Back to Chat</a>
                <a href="/groups/<?php echo e($group->id); ?>/topics/create" class="dash-btn">New Topic</a>
            </div>
        </div>

        <div class="topic-list" style="margin-top:22px;">
            <?php $__empty_1 = true; $__currentLoopData = $topicSummaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $summary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php ($topic = $summary['topic']); ?>
                <a href="/groups/<?php echo e($group->id); ?>/topics/<?php echo e($topic->id); ?>" class="topic-card">
                    <div class="topic-card-title"><?php echo e($topic->title); ?></div>
                    <div class="topic-card-meta">
                        <?php echo e($topic->creator?->name ?? 'Unknown author'); ?>

                        <?php if($topic->category): ?>
                            <span class="topic-dot">•</span><?php echo e($topic->category); ?>

                        <?php endif; ?>
                        <span class="topic-dot">•</span><?php echo e($summary['post_count']); ?> <?php echo e(Str::plural('reply', $summary['post_count'])); ?>

                    </div>
                    <?php if($summary['latest_post']): ?>
                        <div style="color:var(--muted); margin-top:8px; font-size:14px;">
                            Latest reply: <?php echo e(Str::limit($summary['latest_post']->content, 120)); ?>

                        </div>
                    <?php endif; ?>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="empty-panel">No topics yet. Create the first topic for this group.</div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\PILOT\Desktop\forum-backend\resources\views/topics/group-index.blade.php ENDPATH**/ ?>