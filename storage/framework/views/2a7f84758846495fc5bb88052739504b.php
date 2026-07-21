
<?php $__env->startSection('content'); ?>
    <div class="page-card" style="max-width:860px; margin:24px auto; padding:28px;">
        <div class="screen-title" style="color:var(--text); margin-bottom:8px;">Profile</div>
        <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin-bottom:18px;">
            <a href="/dashboard" class="dash-btn">Dashboard</a>
            <a href="/chat" class="dash-btn">Group Chat</a>
            <a href="/quizzes" class="dash-btn">Quizzes</a>
        </div>

        <div class="panel" style="max-width:720px; margin:0 auto 20px;">
            <form method="POST" action="/profile">
                <?php echo csrf_field(); ?>
                <label>Name:</label>
                <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                <label>Email:</label>
                <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>">
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                <label>Role:</label>
                <input type="text" value="<?php echo e(ucfirst($user->role->value)); ?>" readonly>

                <label>Status:</label>
                <input type="text" value="<?php echo e(ucfirst($user->status->value)); ?>" readonly>

                <div style="text-align:right; margin-top:10px;">
                    <button type="submit" class="btn">Update Profile</button>
                </div>
            </form>
        </div>

        <div class="panel" style="max-width:720px; margin:0 auto;">
            <strong>Change Password</strong>
            <form method="POST" action="/profile/password" style="margin-top:10px;">
                <?php echo csrf_field(); ?>
                <label>Current Password:</label>
                <input type="password" name="current_password">
                <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                <label>New Password:</label>
                <input type="password" name="password">
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                <label>Confirm New Password:</label>
                <input type="password" name="password_confirmation">

                <div style="text-align:right; margin-top:10px;">
                    <button type="submit" class="btn">Update Password</button>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/noerine/Smart-Discussion-Forum/forum-backend/resources/views/auth/profile.blade.php ENDPATH**/ ?>