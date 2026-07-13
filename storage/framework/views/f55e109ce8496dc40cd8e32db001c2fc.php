<?php $__env->startSection('content'); ?>
    <div class="auth-card" style="max-width:420px; margin:58px auto; padding:30px;">
        <div class="screen-title" style="margin-bottom:6px; font-size:26px;">Login</div>
        <p style="text-align:center; color:var(--muted); margin-top:0; margin-bottom:22px;">Access your discussions, quizzes, and profile.</p>
        <form method="POST" action="/login">
            <?php echo csrf_field(); ?>
            <label>Email:</label>
            <input type="email" name="email">
            <label>Password:</label>
            <input type="password" name="password">
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <div style="text-align:right; margin-top:10px;">
                <button type="submit" class="btn">Login</button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dell/forum-backend-NEW-AGABA/resources/views/auth/login.blade.php ENDPATH**/ ?>