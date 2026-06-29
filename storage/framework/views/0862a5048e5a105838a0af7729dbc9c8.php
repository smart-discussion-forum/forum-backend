<?php $__env->startSection('content'); ?>
    <div class="screen-title">Quizzes</div>
    <table>
        <tr><th>Title</th><th>Start</th><th>Status</th></tr>
        <?php $__empty_1 = true; $__currentLoopData = $quizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><a href="/quizzes/<?php echo e($quiz->id); ?>"><?php echo e($quiz->title); ?></a></td>
                <td><?php echo e($quiz->start_time->format('d M H:i')); ?></td>
                <td><?php echo e($quiz->status); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="3">No quizzes yet.</td></tr>
        <?php endif; ?>
    </table>
    <a href="/quizzes/create" class="btn">Create Quiz (lecturer)</a>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dashboard\Mindshare\resources\views/quizzes/index.blade.php ENDPATH**/ ?>