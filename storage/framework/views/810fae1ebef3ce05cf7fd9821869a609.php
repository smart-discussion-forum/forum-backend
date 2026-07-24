<?php $__env->startSection('header'); ?>
    <h2 class="screen-title" style="color:var(--text);">Lecturer Dashboard</h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $firstGroup = $myGroups->first();
?>
<div class="page-card" style="max-width:980px; margin:30px auto; padding:30px;">

    <p style="color:var(--muted); margin-bottom:24px;">
        Welcome back, <?php echo e(auth()->user()->name); ?>. You're signed in as
        <span style="color:var(--text); font-weight:600;">Lecturer</span>.
    </p>

    <div class="page-card" style="margin:0 0 24px; padding:18px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h3 style="color:var(--text); font-weight:600; font-size:1.125rem; margin:0;">Your groups</h3>
            <a href="<?php echo e(route('groups.create')); ?>" class="dash-btn" style="margin:0; padding:8px 14px; font-size:0.875rem;">+ New group</a>
        </div>
        <ul style="list-style:none; padding:0; margin:0;">
            <?php $__empty_1 = true; $__currentLoopData = $myGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li style="padding:10px 0; border-top:1px solid var(--muted); display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; align-items:center;">
                    <span style="color:var(--text); font-weight:600;"><?php echo e($group->name); ?></span>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <a href="/groups/<?php echo e($group->id); ?>/topics/create" class="dash-btn" style="margin:0; padding:6px 12px; font-size:0.8rem;">Create topic</a>
                        <a href="<?php echo e(route('groups.statistics', $group->id)); ?>" class="dash-btn" style="margin:0; padding:6px 12px; font-size:0.8rem;">Participation</a>
                        <a href="/groups/<?php echo e($group->id); ?>/topics" class="dash-btn" style="margin:0; padding:6px 12px; font-size:0.8rem;">View topics</a>
                    </div>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li style="padding:8px 0; color:var(--muted);">No groups yet. Create a group first, then you can add topics and view participation.</li>
            <?php endif; ?>
        </ul>
    </div>

    <h3 style="color:var(--text); font-weight:600; font-size:1.125rem; margin-bottom:12px;">Lecturer actions</h3>
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:16px;">
        <a href="<?php echo e(route('quizzes.create')); ?>" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Schedule a quiz</a>

        <?php if($firstGroup): ?>
            <a href="/groups/<?php echo e($firstGroup->id); ?>/topics/create" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Create Topics</a>
            <a href="<?php echo e(route('groups.statistics', $firstGroup->id)); ?>" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Participation</a>
        <?php else: ?>
            <a href="<?php echo e(route('groups.create')); ?>" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Create a group first</a>
        <?php endif; ?>

        <a href="<?php echo e(route('chat')); ?>" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Group Chat / Topics</a>
        <a href="<?php echo e(route('recommendations.index')); ?>" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Recommended topics</a>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\LENOVO\Documents\forum-backend\resources\views/dashboard/lecturer.blade.php ENDPATH**/ ?>