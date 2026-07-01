<?php $__env->startSection('content'); ?>
    <div class="screen-title">Discussion forum</div>
    <table>
        <tr><th>TOPIC</th></tr>
        <?php $__empty_1 = true; $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr><td><a href="/topics/<?php echo e($topic->id); ?>"><?php echo e($topic->title); ?></a></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td>No topics yet.</td></tr>
        <?php endif; ?>
    </table>
    <div style="text-align:right;">
        <a href="/topics/create" class="btn">create topic</a>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dashboard\Mindshare\resources\views/topics/index.blade.php ENDPATH**/ ?>