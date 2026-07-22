<?php $__env->startSection('content'); ?>
    <div class="auth-card" style="max-width:480px; margin:48px auto; padding:30px;">
        <div class="screen-title" style="margin-bottom:6px; font-size:26px;">Registration</div>
        <p style="text-align:center; color:var(--muted); margin-top:0; margin-bottom:22px;">Create your account and join a discussion group.</p>
        <form method="POST" action="/register">
            <?php echo csrf_field(); ?>
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo e(old('name')); ?>">
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo e(old('email')); ?>">
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <label>Password:</label>
            <input type="password" name="password">
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <label>I am a:</label>
           <select name="role">
                <option value="student">Student</option>
                <option value="Lecturer">Lecturer</option>
                <option value="Admin">Admin</option>
            </select>

            <label><input type="checkbox" name="accepted_terms" value="1" style="width:auto;" <?php if(old('accepted_terms')): echo 'checked'; endif; ?>> Accept Rules</label>
            <?php $__errorArgs = ['accepted_terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <div style="text-align:right; margin-top:10px;">
                <button type="submit" class="btn">Register</button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\LENOVO\Documents\forum-backend\resources\views/auth/register.blade.php ENDPATH**/ ?>