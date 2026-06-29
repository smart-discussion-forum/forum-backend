<?php $__env->startSection('box-style', 'centered'); ?>
<?php $__env->startSection('content'); ?>
    <div class="screen-title">Login</div>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dashboard\Mindshare\resources\views/auth/login.blade.php ENDPATH**/ ?>