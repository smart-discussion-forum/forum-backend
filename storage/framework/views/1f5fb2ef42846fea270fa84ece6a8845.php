<?php $__env->startSection('content'); ?>
    <div class="screen-title"><?php echo e($topic->title); ?></div>

    <div style="background:white; padding:10px; max-height:220px; overflow-y:auto; margin-bottom:10px;">
        <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <p><strong><?php echo e($post->user->name); ?>:</strong> <?php echo e($post->content); ?></p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p>No discussion yet — be the first to reply.</p>
        <?php endif; ?>
    </div>

    <form method="POST" action="/topics/<?php echo e($topic->id); ?>/posts">
        <?php echo csrf_field(); ?>
        <textarea name="content" placeholder="Type response..."></textarea>
        <div style="text-align:right; margin-top:10px;">
            <button type="submit">Send</button>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dashboard\Mindshare\resources\views/topics/show.blade.php ENDPATH**/ ?>