<?php $__env->startSection('content'); ?>
    <div class="page-card" style="max-width:1100px; margin:24px auto; padding:24px;">
        <div class="screen-title" style="color:var(--text); margin-bottom:8px;">Quizzes</div>
        <p style="text-align:center; color:var(--muted); margin-top:0; margin-bottom:22px;">View scheduled quizzes, their status, and announcements.</p>
        <div class="table-card">
            <table>
        <tr><th>Title</th><th>Start</th><th>Status</th><th>Actions</th></tr>
        <?php $__empty_1 = true; $__currentLoopData = $quizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><a href="/quizzes/<?php echo e($quiz->id); ?>"><?php echo e($quiz->title); ?></a></td>
                <td><?php echo e($quiz->start_time->format('d M H:i')); ?></td>
                <td>
                    <span class="status-pill"><?php echo e($quiz->announced_at ? 'Announced' : ucfirst($quiz->status)); ?></span>
                </td>
                <td class="quiz-actions">
                    <?php if(auth()->user()->role === 'lecturer' && !$quiz->announced_at): ?>
                        <form method="POST" action="/quizzes/<?php echo e($quiz->id); ?>/announce" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="chat-btn">Announce</button>
                        </form>
                    <?php elseif($quiz->announced_at): ?>
                        <span class="sidebar-copy">Sent <?php echo e($quiz->announced_at->format('d M H:i')); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4">No quizzes yet.</td></tr>
        <?php endif; ?>
    </table>
    <?php if(auth()->user()->role === 'lecturer'): ?>
        <div style="display:flex; justify-content:flex-end; margin-top:18px;">
            <a href="/quizzes/create" class="btn">Create Quiz</a>
        </div>
    <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dell/forum-backend-development/resources/views/quizzes/index.blade.php ENDPATH**/ ?>