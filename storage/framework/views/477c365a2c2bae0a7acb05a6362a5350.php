<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo e($topic->title); ?></title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }
        h1 {
            font-size: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }
        .meta {
            color: #666;
            font-size: 11px;
            margin-bottom: 20px;
        }
        .post {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .post-author {
            font-weight: bold;
        }
        .post-date {
            color: #888;
            font-size: 10px;
            margin-left: 8px;
        }
        .post-content {
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <h1><?php echo e($topic->title); ?></h1>
    <div class="meta">Exported on <?php echo e(now()->format('d M Y, H:i')); ?></div>

    <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="post">
            <span class="post-author"><?php echo e($post->user->name); ?></span>
            <span class="post-date"><?php echo e($post->created_at->format('d M Y, H:i')); ?></span>
            <div class="post-content"><?php echo e($post->content); ?></div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p>No discussion yet.</p>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\Users\PILOT\Desktop\forum-backend\resources\views/topics/export-pdf.blade.php ENDPATH**/ ?>