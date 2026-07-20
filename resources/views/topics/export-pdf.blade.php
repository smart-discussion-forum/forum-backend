<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $topic->title }}</title>
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
    <h1>{{ $topic->title }}</h1>
    <div class="meta">Exported on {{ now()->format('d M Y, H:i') }}</div>

    @forelse ($posts as $post)
        <div class="post">
            <span class="post-author">{{ $post->user->name ?? 'Unknown user' }}</span>
            <span class="post-date">{{ $post->created_at->format('d M Y, H:i') }}</span>
            <div class="post-content">{{ $post->content }}</div>
        </div>
    @empty
        <p>No discussion yet.</p>
    @endforelse
</body>
</html>
