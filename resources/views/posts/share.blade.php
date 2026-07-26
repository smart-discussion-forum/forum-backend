<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $post->user->name }} on {{ $post->topic->title }} — Mindshare</title>

    <meta property="og:title" content="{{ $post->user->name }} on {{ $post->topic->title }}">
    <meta property="og:description" content="{{ $post->shareExcerpt }}">
    <meta property="og:url" content="{{ $post->shareUrl }}">
    <meta property="og:type" content="article">
    <meta name="twitter:card" content="summary">

    <style>
        body { font-family: Arial, sans-serif; background:#0b1020; color:#eef2f7; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
        .card { background: rgba(238,242,247,0.95); color:#101827; border-radius:24px; padding:32px; max-width:480px; box-shadow:0 24px 60px rgba(0,0,0,0.35); }
        .meta { color:#5b6476; font-size:13px; margin-bottom:12px; }
        .content { font-size:16px; line-height:1.5; margin-bottom:24px; }
        .share-row { display:flex; gap:10px; flex-wrap:wrap; }
        .share-btn { padding:10px 16px; border-radius:999px; text-decoration:none; font-size:13px; font-weight:600; color:#fff; border:none; cursor:pointer; font-family:inherit; }
        .tw { background:#000; }
        .wa { background:#25D366; }
        .fb { background:#1877F2; }
        .li { background:#0A66C2; }
        .tg { background:#26A5E4; }
        .copy { background:#5b6476; }
        .copy.copied { background:#067647; }
        .cta { display:block; margin-top:18px; text-align:center; font-size:13px; color:#4f7ca8; }
    </style>
</head>
<body>
    <div class="card">
        <div class="meta">{{ $post->user->name }} · {{ $post->topic->title }}</div>
        <div class="content">{{ $post->shareExcerpt }}</div>
        <div class="share-row">
            <a class="share-btn tw" target="_blank" rel="noopener"
               href="https://twitter.com/intent/tweet?url={{ urlencode($post->shareUrl) }}&text={{ urlencode($post->shareExcerpt) }}">
                Share on X
            </a>
            <a class="share-btn wa" target="_blank" rel="noopener"
               href="https://wa.me/?text={{ urlencode($post->shareExcerpt.' '.$post->shareUrl) }}">
                Share on WhatsApp
            </a>            
            <a class="share-btn li" target="_blank" rel="noopener"
               href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($post->shareUrl) }}">
                Share on LinkedIn
            </a>
            <a class="share-btn tg" target="_blank" rel="noopener"
               href="https://t.me/share/url?url={{ urlencode($post->shareUrl) }}&text={{ urlencode($post->shareExcerpt) }}">
                Share on Telegram
            </a>
            <button type="button" class="share-btn copy" id="copyLinkBtn" data-url="{{ $post->shareUrl }}">
                Copy Link
            </button>
        </div>
        <a class="cta" href="{{ route('login') }}">Join the discussion on Mindshare &rarr;</a>
    </div>

    <script>
        document.getElementById('copyLinkBtn').addEventListener('click', function () {
            const btn = this;
            navigator.clipboard.writeText(btn.dataset.url).then(function () {
                const original = btn.textContent;
                btn.textContent = 'Copied!';
                btn.classList.add('copied');
                setTimeout(function () {
                    btn.textContent = original;
                    btn.classList.remove('copied');
                }, 1500);
            });
        });
    </script>
</body>
</html>
