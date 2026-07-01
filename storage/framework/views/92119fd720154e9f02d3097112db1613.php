<?php $__env->startSection('content'); ?>
    <div style="text-align:center; margin-top:60px;">
        <div style="font-size:32px; font-weight:bold;">MINDSHARE<br>DISCUSSION FORUM</div>
        <p style="font-size:20px; margin:20px 0;">Welcome</p>
        <div style="margin-bottom: 30px;">
            <a href="/rules" class="btn" style="font-size:16px; padding:14px 24px;">View Rules</a>
        </div>
    </div>
    <div style="display:flex; justify-content:space-between; max-width:600px; margin:40px auto 0;">
        <a href="/login" class="btn" style="font-size:16px; padding:14px 30px;">Login</a>
        <a href="/register" class="btn" style="font-size:16px; padding:14px 30px;">Register</a>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dashboard\Mindshare\resources\views/welcome.blade.php ENDPATH**/ ?>