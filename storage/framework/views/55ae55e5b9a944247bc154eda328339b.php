<?php $__env->startSection('title', 'Manage Groups'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-card" style="max-width:980px; margin:30px auto; padding:30px;">

    <h2 class="screen-title" style="color:var(--text); text-align:left; margin-bottom:20px;">
        Manage Groups
    </h2>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Created by</th>
                    <th>Members</th>
                    <th>Topics</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($group->name); ?></td>
                        <td><?php echo e($group->creator?->name); ?></td>
                        <td><?php echo e($group->members_count); ?></td>
                        <td><?php echo e($group->topics_count); ?></td>
                        <td class="quiz-actions">
                            <a href="<?php echo e(route('groups.statistics', $group->id)); ?>" class="dash-btn" style="padding:6px 12px; font-size:0.8rem;">Stats</a>
                            <a href="<?php echo e(route('groups.edit', $group->id)); ?>" class="dash-btn" style="padding:6px 12px; font-size:0.8rem;">Edit</a>
                            <form method="POST" action="<?php echo e(route('groups.destroy', $group->id)); ?>" style="display:inline;" onsubmit="return confirm('Delete this group? This cannot be undone.');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="dash-btn" style="padding:6px 12px; font-size:0.8rem; color:#dc2626;">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" style="color:var(--muted);">No groups yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dell/forum-backend-development/resources/views/groups/manage.blade.php ENDPATH**/ ?>