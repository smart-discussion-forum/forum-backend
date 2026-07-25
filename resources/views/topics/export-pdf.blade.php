<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $topic->title }} — Thread Export</title>
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
        <div class="group-label">{{ $group->name ?? 'Discussion Group' }}</div>
        <h1 class="topic-title">{{ $topic->title }}</h1>
        <div class="topic-meta">
            <span>Started by {{ $topic->creator?->name ?? 'Unknown author' }}</span>
            @if($topic->category)
                <span>Category: {{ $topic->category }}</span>
            @endif
            <span>Created {{ optional($topic->created_at)->format('d M Y, H:i') ?? '—' }}</span>
        </div>
    </div>

    <div class="thread-summary">
        <strong>{{ $posts->count() }}</strong>
        {{ $posts->count() === 1 ? 'reply' : 'replies' }}
        in this thread
        &nbsp;·&nbsp;
        Exported on {{ now()->format('d M Y, H:i') }}
    </div>

    <div class="thread">
        @forelse ($posts as $index => $post)
            <div class="message">
                <div class="message-header">
                    <span class="message-number">#{{ $index + 1 }}</span>
                    <span class="message-author">{{ $post->user?->name ?? 'Unknown user' }}</span>
                    <span class="message-date">{{ optional($post->created_at)->format('d M Y, H:i') ?? '' }}</span>
                </div>
                <div class="message-bubble {{ $index === 0 ? 'opener' : '' }}">
                    <p class="message-content">{{ $post->content }}</p>
                </div>
            </div>
        @empty
            <div class="empty-thread">No replies in this thread yet.</div>
        @endforelse
    </div>

    <div class="document-footer">
        Forum thread export · {{ $topic->title }} · {{ $group->name ?? 'Group' }}
    </div>
</body>
</html>
