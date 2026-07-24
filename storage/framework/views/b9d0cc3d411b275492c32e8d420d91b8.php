<?php $__env->startSection('content'); ?>
<div class="page-card" style="max-width:600px; margin:40px auto; padding:24px;">
    <h2 style="margin-bottom:16px;">Create a New Group</h2>

    <?php if($errors->any()): ?>
        <div style="background:#fee; border:1px solid #f99; padding:12px; border-radius:6px; margin-bottom:16px;">
            <ul style="margin:0; padding-left:18px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('groups.store')); ?>">
        <?php echo csrf_field(); ?>

        <div style="margin-bottom:16px;">
            <label for="name" style="display:block; font-weight:600; margin-bottom:6px;">Group Name</label>
            <input type="text" id="name" name="name" value="<?php echo e(old('name')); ?>" required maxlength="100"
                   style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
        </div>

        <div style="margin-bottom:16px;">
            <label for="description" style="display:block; font-weight:600; margin-bottom:6px;">Description</label>
            <textarea id="description" name="description" rows="4"
                      style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;"><?php echo e(old('description')); ?></textarea>
        </div>

        <button type="submit" class="dash-btn" style="padding:10px 18px;">Create Group</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\LENOVO\Documents\forum-backend\resources\views/groups/create.blade.php ENDPATH**/ ?>