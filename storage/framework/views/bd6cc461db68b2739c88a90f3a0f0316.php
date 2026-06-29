<?php $__env->startSection('content'); ?>
    <div class="screen-title">Create Topic</div>
    <form method="POST" action="/topics">
        <?php echo csrf_field(); ?>
        <label>Title:</label>
        <input type="text" name="title">
        <label>Category:</label>
        <input type="text" name="category">
        <div style="text-align:right; margin-top:10px;">
            <button type="submit" class="btn">Save</button>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dashboard\Mindshare\resources\views/topics/create.blade.php ENDPATH**/ ?>