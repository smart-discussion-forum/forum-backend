<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo e($topic->title); ?> — Thread Export</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            margin: 0;
            padding: 24px 28px;
            line-height: 1.5;
        }

        .document-header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 14px;
            margin-bottom: 22px;
        }

        .group-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6366f1;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .topic-title {
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 8px 0;
            color: #111827;
        }

        .topic-meta {
            font-size: 10px;
            color: #6b7280;
        }

        .topic-meta span {
            margin-right: 12px;
        }

        .thread-summary {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 20px;
            font-size: 10px;
            color: #374151;
        }

        .thread-summary strong {
            color: #111827;
        }

        .thread {
            width: 100%;
        }

        .message {
            margin-bottom: 14px;
            page-break-inside: avoid;
        }

        .message-header {
            margin-bottom: 4px;
        }

        .message-number {
            display: inline-block;
            background: #e0e7ff;
            color: #4338ca;
            font-size: 9px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 10px;
            margin-right: 6px;
        }

        .message-author {
            font-weight: bold;
            color: #111827;
            font-size: 11px;
        }

        .message-date {
            color: #9ca3af;
            font-size: 9px;
            margin-left: 6px;
        }

        .message-bubble {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-left: 3px solid #6366f1;
            border-radius: 0 8px 8px 8px;
            padding: 10px 12px;
            color: #1f2937;
        }

        .message-bubble.opener {
            border-left-color: #059669;
            background: #f0fdf4;
        }

        .message-content {
            margin: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .empty-thread {
            text-align: center;
            color: #9ca3af;
            padding: 30px 0;
            font-style: italic;
        }

        .document-footer {
            margin-top: 28px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="document-header">
        <div class="group-label"><?php echo e($group->name ?? 'Discussion Group'); ?></div>
        <h1 class="topic-title"><?php echo e($topic->title); ?></h1>
        <div class="topic-meta">
            <span>Started by <?php echo e($topic->creator?->name ?? 'Unknown author'); ?></span>
            <?php if($topic->category): ?>
                <span>Category: <?php echo e($topic->category); ?></span>
            <?php endif; ?>
            <span>Created <?php echo e(optional($topic->created_at)->format('d M Y, H:i') ?? '—'); ?></span>
        </div>
    </div>

    <div class="thread-summary">
        <strong><?php echo e($posts->count()); ?></strong>
        <?php echo e($posts->count() === 1 ? 'reply' : 'replies'); ?>

        in this thread
        &nbsp;·&nbsp;
        Exported on <?php echo e(now()->format('d M Y, H:i')); ?>

    </div>

    <div class="thread">
        <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="message">
                <div class="message-header">
                    <span class="message-number">#<?php echo e($index + 1); ?></span>
                    <span class="message-author"><?php echo e($post->user?->name ?? 'Unknown user'); ?></span>
                    <span class="message-date"><?php echo e(optional($post->created_at)->format('d M Y, H:i') ?? ''); ?></span>
                </div>
                <div class="message-bubble <?php echo e($index === 0 ? 'opener' : ''); ?>">
                    <p class="message-content"><?php echo e($post->content); ?></p>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="empty-thread">No replies in this thread yet.</div>
        <?php endif; ?>
    </div>

    <div class="document-footer">
        Forum thread export · <?php echo e($topic->title); ?> · <?php echo e($group->name ?? 'Group'); ?>

    </div>
</body>
</html>
<?php /**PATH C:\Users\PILOT\Desktop\forum-backend\resources\views/topics/export-pdf.blade.php ENDPATH**/ ?>