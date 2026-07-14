<?php $__env->startSection('content'); ?>
    <div class="page-card" style="max-width:1180px; margin:24px auto; padding:24px;">
        <div class="screen-title" style="margin-bottom:8px; color:var(--text);">Topics</div>
        <p style="text-align:center; color:var(--muted); margin-top:0; margin-bottom:22px;">Browse topic summaries, see the latest discussion snippet, and jump into a thread.</p>
        <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin-bottom:18px;">
            <a href="/topics/create" class="dash-btn">New topic</a>
            <a href="/discussions" class="dash-btn">View Discussions</a>
        </div>

        <div class="table-card">
            <table>
                <tr>
                    <th>Topic</th>
                    <th>Category</th>
                    <th>Created By</th>
                    <th>Replies</th>
                    <th>Latest Discussion</th>
                    <th>Action</th>
                </tr>
                <?php $__empty_1 = true; $__currentLoopData = $topicSummaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $summary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($summary['topic']->title); ?></td>
                        <td><?php echo e($summary['topic']->category ?? 'General'); ?></td>
                        <td><?php echo e($summary['topic']->user?->name ?? 'Unknown author'); ?></td>
                        <td><?php echo e($summary['post_count']); ?></td>
                        <td>
                            <?php if($summary['latest_post']): ?>
                                <?php echo e(\Illuminate\Support\Str::limit($summary['latest_post']->content, 80)); ?>

                            <?php else: ?>
                                No replies yet
                            <?php endif; ?>
                        </td>
                        <td><a href="/discussions/<?php echo e($summary['topic']->id); ?>" class="dash-btn">View Discussion</a></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6">No topics yet.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dell/forum-backend-development/resources/views/topics/index.blade.php ENDPATH**/ ?>