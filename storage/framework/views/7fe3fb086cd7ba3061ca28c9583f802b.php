<?php $__env->startSection('content'); ?>
    <div class="screen-title">Create Topic</div>
    <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin-bottom:18px;">
        <a href="/topics" class="dash-btn">Back to Discussions</a>
        <a href="/dashboard" class="dash-btn">Dashboard</a>
    </div>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dell/forum-backend-AGABA-TRIAL/resources/views/topics/create.blade.php ENDPATH**/ ?>