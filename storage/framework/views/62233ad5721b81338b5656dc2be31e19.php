<?php $__env->startSection('content'); ?>
    <div class="auth-card" style="max-width:600px; margin:24px auto; padding:28px;">
        <div class="screen-title" style="color:var(--text); margin-bottom:8px;">New Topic in <?php echo e($group->name); ?></div>
        <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin-bottom:18px;">
            <a href="/groups/<?php echo e($group->id); ?>/topics" class="dash-btn">Back to Topics</a>
            <a href="/chat?group=<?php echo e($group->id); ?>" class="dash-btn">Back to Chat</a>
        </div>
        <form method="POST" action="/groups/<?php echo e($group->id); ?>/topics">
            <?php echo csrf_field(); ?>
            <label>Title:</label>
            <input type="text" name="title" value="<?php echo e(old('title')); ?>">
            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <label>Category:</label>
            <input type="text" name="category" value="<?php echo e(old('category')); ?>">
            <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <div style="text-align:right; margin-top:10px;">
                <button type="submit" class="btn">Save</button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/noerine/Smart-Discussion-Forum/forum-backend/resources/views/topics/group-create.blade.php ENDPATH**/ ?>