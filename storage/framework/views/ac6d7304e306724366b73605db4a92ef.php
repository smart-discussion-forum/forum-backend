<?php $__env->startSection('content'); ?>
    <div class="page-card" style="max-width:980px; margin:30px auto; padding:30px;">
        <div class="screen-title" style="color:var(--text); margin-bottom:10px;">Dashboard</div>
        <p style="color:var(--muted); text-align:center; margin:0 0 24px;">Choose a section to continue.</p>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:16px;">
            <a href="/chat" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Group Chat</a>
            <a href="/quizzes" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Quizzes</a>
            <a href="/profile" class="dash-btn" style="margin:0; text-align:center; padding:18px 16px;">Profile</a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\LENOVO\Documents\forum-backend\resources\views/dashboard.blade.php ENDPATH**/ ?>