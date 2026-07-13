<?php $__env->startSection('title', 'Group Statistics'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-card" style="max-width:980px; margin:30px auto; padding:30px;">

    <h2 class="screen-title" style="color:var(--text); text-align:left; margin-bottom:4px;">
        <?php echo e($group_name); ?>

    </h2>
    <p style="color:var(--muted); margin-bottom:24px;">
        Created by <span style="color:var(--text); font-weight:600;"><?php echo e($created_by); ?></span>
    </p>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap:16px; margin-bottom:28px;">
        <div class="page-card" style="margin:0; padding:18px; text-align:center;">
            <p style="color:var(--muted); font-size:0.875rem; margin:0 0 6px;">Members</p>
            <p style="color:var(--text); font-size:1.75rem; font-weight:700; margin:0;"><?php echo e($member_count); ?></p>
        </div>
        <div class="page-card" style="margin:0; padding:18px; text-align:center;">
            <p style="color:var(--muted); font-size:0.875rem; margin:0 0 6px;">Topics</p>
            <p style="color:var(--text); font-size:1.75rem; font-weight:700; margin:0;"><?php echo e($topic_count); ?></p>
        </div>
        <div class="page-card" style="margin:0; padding:18px; text-align:center;">
            <p style="color:var(--muted); font-size:0.875rem; margin:0 0 6px;">Posts</p>
            <p style="color:var(--text); font-size:1.75rem; font-weight:700; margin:0;"><?php echo e($post_count); ?></p>
        </div>
        <div class="page-card" style="margin:0; padding:18px; text-align:center;">
            <p style="color:var(--muted); font-size:0.875rem; margin:0 0 6px;">Messages</p>
            <p style="color:var(--text); font-size:1.75rem; font-weight:700; margin:0;"><?php echo e($message_count); ?></p>
        </div>
    </div>

    <h3 style="color:var(--text); font-weight:600; font-size:1.125rem; margin-bottom:12px;">Members by role</h3>
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $members_by_role; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($role); ?></td>
                        <td><?php echo e($count); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="2" style="color:var(--muted);">No members yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top:24px;">
        <a href="<?php echo e(route('groups.index')); ?>" class="dash-btn">Back to groups</a>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dell/forum-backend-development/resources/views/groups/statistics.blade.php ENDPATH**/ ?>