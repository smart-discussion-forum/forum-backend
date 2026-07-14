<?php $__env->startSection('title', 'Study Groups — Mindshare'); ?>
<?php $__env->startSection('box-style', 'wide'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-card" style="max-width: 960px; margin: 0 auto; padding: 32px; border-radius: 30px;">
    <div class="screen-title" style="text-align:left; margin-bottom: 6px;">Study Groups</div>
    <p style="color: var(--muted); margin-top: 0; margin-bottom: 28px;">
        Groups you're already part of, and groups you can still join.
    </p>

    <h3 style="margin-bottom: 12px; color: #1f2937;">My Groups (<?php echo e($myGroups->count()); ?>)</h3>

    <?php if($myGroups->isEmpty()): ?>
        <div class="empty-panel" style="margin-bottom: 28px;">
            You haven't joined any groups yet — browse the list below to get started.
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 28px;">
            <?php $__currentLoopData = $myGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="panel">
                    <div class="row" style="align-items: center;">
                        <div>
                            <div style="font-weight:700; font-size:16px;">
                                <a href="<?php echo e(route('groups.show', $group->id)); ?>" style="color:inherit; text-decoration:none;">
                                    <?php echo e($group->name); ?>

                                </a>
                            </div>
                            <?php if($group->description): ?>
                                <div style="color: var(--muted); font-size: 13px; margin-top: 4px;">
                                    <?php echo e($group->description); ?>

                                </div>
                            <?php endif; ?>
                            <span class="status-pill" style="margin-top: 8px; display:inline-block;">
                                <?php echo e($group->pivot->role); ?>

                            </span>
                        </div>
                        <form method="POST" action="/groups/<?php echo e($group->id); ?>/leave">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="dash-btn">Leave</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <h3 style="margin-bottom: 12px; color: #1f2937;">Browse Groups to Join (<?php echo e($joinableGroups->count()); ?>)</h3>

    <?php if($joinableGroups->isEmpty()): ?>
        <div class="empty-panel">
            No other groups available to join right now.
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php $__currentLoopData = $joinableGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="panel">
                    <div class="row" style="align-items: center;">
                        <div>
                            <div style="font-weight:700; font-size:16px;"><?php echo e($group->name); ?></div>
                            <?php if($group->description): ?>
                                <div style="color: var(--muted); font-size: 13px; margin-top: 4px;">
                                    <?php echo e($group->description); ?>

                                </div>
                            <?php endif; ?>
                            <div style="color: var(--muted); font-size: 12px; margin-top: 6px;">
                                <?php echo e($group->members_count); ?> <?php echo e($group->members_count === 1 ? 'member' : 'members'); ?>

                            </div>
                        </div>
                        <?php if(auth()->user()->role === \App\Enums\RoleEnum::Student): ?>
                            <form method="POST" action="/groups/<?php echo e($group->id); ?>/join">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn">Join</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dell/forum-backend-development/resources/views/groups/index.blade.php ENDPATH**/ ?>