<?php $__env->startSection('content'); ?>
    <div class="auth-card" style="max-width:900px; margin:24px auto; padding:28px;">
        <div class="screen-title" style="color:var(--text); margin-bottom:8px;">Quiz configuration</div>
        <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin-bottom:18px;">
            <a href="/quizzes" class="dash-btn">Back to Quizzes</a>
            <a href="/dashboard" class="dash-btn">Dashboard</a>
            <a href="/topics" class="dash-btn">Topics</a>
        </div>
        <form method="POST" action="/quizzes">
            <?php echo csrf_field(); ?>
            <label>Quiz Title:</label>
            <input type="text" name="title">
            <label>Date:</label>
            <input type="datetime-local" name="start_time">
            <label>Duration (minutes):</label>
            <input type="number" name="duration_minutes">
            <label>Category:</label>
            <select name="target_category">
                <option value="">All</option>
                <option value="year1">Year 1</option>
                <option value="year2">Year 2</option>
                <option value="year3">Year 3</option>
            </select>
            <label>Questions (one per line — format: question | optionA,optionB,optionC | correct option index 0-2):</label>
            <textarea name="raw_questions" rows="4" placeholder="2+2=? | 3,4,5 | 1"></textarea>
            <div style="text-align:right; margin-top:10px;">
                <button type="submit" class="btn">Save</button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dell/forum-backend-development/resources/views/quizzes/create.blade.php ENDPATH**/ ?>