<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mindshare Discussion Forum')</title>
    <style>
        :root {
            --bg-0: #0b1020;
            --bg-1: #11192d;
            --glass: rgba(238, 242, 247, 0.84);
            --glass-strong: rgba(226, 232, 240, 0.94);
            --line: rgba(148, 163, 184, 0.22);
            --text: #101827;
            --muted: #5b6476;
            --accent: #4f7ca8;
            --accent-strong: #2f5f84;
            --accent-soft: #c9d9e8;
            --shadow: 0 24px 60px rgba(0, 0, 0, 0.26);
        }
        body {
            font-family: Arial, sans-serif;
            background:
                radial-gradient(circle at 15% 20%, rgba(63, 114, 175, 0.45), transparent 24%),
                radial-gradient(circle at 80% 18%, rgba(40, 167, 188, 0.35), transparent 20%),
                radial-gradient(circle at 75% 82%, rgba(145, 92, 182, 0.22), transparent 22%),
                linear-gradient(160deg, #0b1020 0%, #1a2338 55%, #2e3a56 100%);
            margin: 0;
            padding: 0;
            color: var(--text);
        }
    .navbar {
        background: rgba(10, 15, 28, 0.78);
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(18px);
        padding: 14px 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
    }
    .navbar .nav-links {
        display: flex;
        gap: 22px;
        flex-wrap: wrap;
    }
    .navbar .nav-links a.active {
        text-decoration: underline;
    }
    .navbar a {
        color: rgba(255, 255, 255, 0.92);
        text-decoration: none;
        font-weight: bold;
        font-size: 14px;
    }
    .navbar a:hover {
        text-decoration: underline;
    }
        .nav-logout {
            background: linear-gradient(135deg, #58779e, #355172);
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 999px;
            cursor: pointer;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.18);
        }
        .nac-logout:hover {
            opacity: 0.95;
        }
            .screen-box {
            background: transparent;
            padding: 28px;
            box-sizing: border-box;
            min-height: calc(100vh - 68px);
        }
        .screen-box.wide {
            margin: 0;
            border-radius: 0;
            width: 100%;
        }
        .screen-box.centered {
            margin: 34px auto;
            border-radius: 28px;
            width: min(92%, 520px);
        }
        .screen-title {
            text-align: center;
            font-weight: 700;
            margin-bottom: 18px;
            font-size: 28px;
            letter-spacing: 0.02em;
            color: #eff6ff;
        }
        input[type=text], input[type=email], input[type=password], input[type=number],
        input[type=datetime-local], textarea, select {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 12px;
            border: 1px solid rgba(148, 163, 184, 0.28);
            box-sizing: border-box;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.94);
            color: var(--text);
            outline: none;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.6);
        }
        .btn{
            background: linear-gradient(135deg, var(--accent), var(--accent-strong));
            color: #fff;
            border: none;
            padding: 11px 22px;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            border-radius: 999px;
            box-shadow: 0 12px 24px rgba(31, 57, 86, 0.25);
        }
        .dash-btn {
            background: rgba(255, 255, 255, 0.9);
            color: var(--text);
            border: none;
            padding: 11px 18px;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.24);
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.12);
        }
        .row { display:flex; justify-content:space-between; gap:10px; }
        .panel,
        .auth-card,
        .glass-card,
        .discussion-sidebar,
        .discussion-conversation,
        .topic-list,
        .topic-summary-wrap,
        .quiz-table-wrap,
        .hero-card,
        .page-card,
        .welcome-card {
            background: var(--glass);
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
            backdrop-filter: blur(20px);
        }
        .welcome-card {
            background:
                radial-gradient(circle at 20% 20%, rgba(66, 134, 244, 0.34), transparent 28%),
                radial-gradient(circle at 80% 30%, rgba(28, 177, 184, 0.26), transparent 24%),
                radial-gradient(circle at 50% 82%, rgba(115, 86, 178, 0.18), transparent 25%),
                rgba(15, 22, 39, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #f8fbff;
            box-shadow: 0 28px 80px rgba(0, 0, 0, 0.42);
        }
        .welcome-card .screen-title {
            color: #f8fbff;
        }
        .auth-card {
            background: linear-gradient(180deg, rgba(245, 248, 252, 0.96), rgba(229, 236, 245, 0.9));
            color: var(--text);
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.58);
            box-shadow: 0 22px 60px rgba(0, 0, 0, 0.28);
        }
        .auth-card .screen-title,
        .auth-card label {
            color: var(--text);
        }
        .page-card {
            background: rgba(245, 247, 250, 0.92);
            color: var(--text);
            border-radius: 30px;
        }
        .panel {
            padding: 18px;
            border-radius: 22px;
            margin-bottom: 12px;
        }
        .table-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 24px;
            padding: 18px;
            box-shadow: var(--shadow);
            border: 1px solid var(--line);
        }
        table {
            width:100%;
            border-collapse: separate;
            border-spacing: 0;
            background: transparent;
            margin-bottom: 0;
            overflow: hidden;
        }
        table th, table td {
            padding: 14px 12px;
            text-align:left;
            border-bottom: 1px solid rgba(148, 163, 184, 0.18);
        }
        table th {
            color: #1f2937;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        table td {
            color: var(--text);
            font-size: 14px;
        }
        .error { color:#b42318; font-size:13px; margin-bottom:8px; }
        .success { color:#067647; font-size:13px; margin-bottom:8px; }
        label { font-size: 13px; color: var(--muted); font-weight: 600; }
        .discussion-shell {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 20px;
            min-height: calc(100vh - 130px);
        }
        .discussion-sidebar,
        .discussion-conversation {
            border-radius: 28px;
            padding: 20px;
        }
        .discussion-sidebar {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .discussion-sidebar-header,
        .conversation-header,
        .reply-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .sidebar-copy,
        .conversation-subtitle,
        .topic-card-meta,
        .chat-meta {
            color: var(--muted);
            font-size: 13px;
        }
        .topic-list,
        .chat-thread {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .topic-list {
            overflow-y: auto;
            padding-right: 2px;
        }
        .topic-card {
            display: block;
            padding: 14px 16px;
            border-radius: 18px;
            text-decoration: none;
            color: inherit;
            background: rgba(255, 255, 255, 0.78);
            border: 1px solid rgba(148, 163, 184, 0.18);
        }
        .topic-card.active {
            background: linear-gradient(135deg, #1f2a44, #324561);
            color: #fff;
        }
        .topic-card.active .topic-card-meta {
            color: rgba(255, 255, 255, 0.78);
        }
        .topic-card-title,
        .conversation-title {
            font-weight: 700;
            font-size: 16px;
        }
        .topic-dot {
            margin: 0 6px;
        }
        .discussion-conversation {
            display: flex;
            flex-direction: column;
            gap: 16px;
            min-height: 0;
        }
        .chat-thread {
            flex: 1;
            overflow-y: auto;
            padding-right: 6px;
            min-height: 300px;
        }
        .chat-row {
            display: flex;
        }
        .chat-row.mine {
            justify-content: flex-end;
        }
        .chat-bubble {
            max-width: min(58%, 480px);
            padding: 14px 16px;
            border-radius: 18px 18px 18px 6px;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.1);
        }
        .chat-row.mine .chat-bubble {
            background: linear-gradient(135deg, #edf4fb, #dbe7f3);
            border-color: rgba(79, 124, 168, 0.22);
            color: #172033;
            border-radius: 18px 18px 6px 18px;
        }
        .chat-row.mine .chat-meta {
            color: #516170;
        }
        .chat-text {
            white-space: pre-wrap;
            line-height: 1.5;
        }
        .chat-time {
            margin-top: 8px;
            font-size: 12px;
            color: inherit;
            opacity: 0.65;
        }
        .chat-actions {
            margin-top: 10px;
            display: flex;
            justify-content: flex-end;
        }
        .reaction-btn {
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(148, 163, 184, 0.3);
            border-radius: 999px;
            padding: 6px 12px;
            cursor: pointer;
            font-size: 12px;
            color: inherit;
        }
        .reaction-btn.active {
            background: linear-gradient(135deg, var(--accent), var(--accent-strong));
            border-color: transparent;
            color: #fff;
        }
        .reply-form {
            margin-top: auto;
        }
        .reply-form textarea {
            min-height: 110px;
            resize: vertical;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.95);
        }
        .chat-btn {
            background: linear-gradient(135deg, var(--accent), var(--accent-strong));
            color: #fff;
            border: none;
            padding: 10px 18px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 999px;
            margin: 0;
        }
        .empty-panel,
        .empty-conversation {
            padding: 18px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.82);
            color: var(--muted);
        }
        .empty-conversation {
            margin: auto;
            max-width: 520px;
            text-align: center;
        }
        .reply-actions {
            justify-content: flex-end;
            margin-top: 12px;
        }
        .status-pill {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(79, 124, 168, 0.14);
            font-size: 12px;
            color: #20324a;
        }
        .quiz-actions {
            white-space: nowrap;
        }
        @media (max-width: 960px) {
            .discussion-shell {
                grid-template-columns: 1fr;
            }
            .chat-bubble {
                max-width: 100%;
            }
        }
        @media (max-width: 720px) {
            .screen-box {
                padding: 18px;
            }
            .navbar {
                padding: 12px 16px;
                gap: 12px;
                flex-direction: column;
                align-items: flex-start;
            }
            .navbar .nav-links {
                gap: 14px;
            }
            .screen-title {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
<div class="navbar">
        @auth
            <div class="nav-links">
                <a href="/dashboard">Dashboard</a>
                <a href="/topics">Topics</a>
                <a href="/quizzes">Quiz</a>
                <a href="/profile">Profile</a>
            </div>
            <div style="color:white; display:flex; align-items:center; gap:15px;">
                <span>{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role->value) }})</span>
                <form method="POST" action="/logout" style="display:inline;">
                    @csrf
                    <button type="submit" class="nav-logout">Logout</button>
                </form>
            </div>
        @else
        <div class="nav-links">
            <a href="/">Home</a>
            <a href="/login">Login</a>
            <a href="/register">Register</a>
        </div>
    @endauth
</div>
<div class="screen-box @yield('box-style', 'wide')">
    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif
    @yield('content')
    </div>
    @auth
@if(auth()->user()->role->value === 'student')
<div id="quizCountdownBanner" style="display:none; position:fixed; top:0; left:0; right:0; z-index:9999; background:linear-gradient(135deg,#4f7ca8,#2f5f84); color:#fff; text-align:center; padding:12px; font-weight:700;">
    <span id="quizCountdownText"></span>
</div>
<script>
    let quizRedirectTimer = null;
    let quizPollTimer = null;

    function checkUpcomingQuiz() {
        fetch('/quizzes/upcoming-check')
            .then(res => res.json())
            .then(data => {
                const banner = document.getElementById('quizCountdownBanner');
                const text = document.getElementById('quizCountdownText');

                if (!data.upcoming) {
                    banner.style.display = 'none';
                    return;
                }

                const seconds = data.upcoming.seconds_until_start;

                if (seconds <= 30 && seconds > 0) {
                    banner.style.display = 'block';
                    let remaining = seconds;

                    if (quizRedirectTimer) clearInterval(quizRedirectTimer);

                    quizRedirectTimer = setInterval(() => {
                        remaining--;
                        text.textContent = `"${data.upcoming.title}" starts in ${remaining} second${remaining !== 1 ? 's' : ''}...`;

                        if (remaining <= 0) {
                            clearInterval(quizRedirectTimer);
                            window.location.href = '/quizzes/' + data.upcoming.id;
                        }
                    }, 1000);

                    text.textContent = `"${data.upcoming.title}" starts in ${remaining} second${remaining !== 1 ? 's' : ''}...`;
                } else if (seconds <= 0) {
                    window.location.href = '/quizzes/' + data.upcoming.id;
                } else {
                    banner.style.display = 'none';
                }
            })
            .catch(() => {});
    }

    checkUpcomingQuiz();
    quizPollTimer = setInterval(checkUpcomingQuiz, 5000);
</script>
@endif
@endauth
    
    @stack('scripts')
</body>
</html>
