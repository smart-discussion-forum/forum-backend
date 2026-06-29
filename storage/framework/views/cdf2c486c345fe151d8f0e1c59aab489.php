<?php $__env->startSection('content'); ?>
    <div class="screen-title">Dashboard</div>
    <div style="display:flex; flex-direction:column; gap:12px; max-width:50%; margin:0 auto;">
        <a href="/topics" class="dash-btn">Discussions</a>
        <a href="/quizzes" class="dash-btn">Quiz</a>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dashboard\Mindshare\resources\views/dashboard.blade.php ENDPATH**/ ?>