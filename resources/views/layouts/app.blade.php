<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mindshare Discussion Forum')</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background: #f0f0f0;
    margin: 0;
    padding: 0;
        }
    .navbar {
        background: #c0b6b6;
        padding: 12px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .navbar .nav-links {
        display: flex;
        gap: 50px;
    }
    .navbar a {
        color: white;
        text-decoration: none;
        font-weight: bold;
        font-size: 16px;
    }
    .navbar a:hover {
        text-decoration: underline;
    }
        .nav-logout {
            background: #b00;
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 3px;
            cursor: pointer;
        }
            .screen-box {
            background: #bdb8b8;
            padding: 30px;
            box-sizing: border-box;
        }
        .screen-box.wide {
            margin: 0;
            border-radius: 0;
            width: 100%;
            min-height: calc(100vh - 60px);
        }
        .screen-box.centered {
            margin: 60px auto;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 90%;
            max-width: 460px;
        }
        .screen-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 18px;
        }
        input[type=text], input[type=email], input[type=password], input[type=number],
        input[type=datetime-local], textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #999;
            box-sizing: border-box;
        }
        .btn{
            background: #686363;
            color: #fff;
            border: none;
            padding: 10px 20px;
            text-decoration: none;
            display: inline-block;
            margin: 50px;
            cursor: pointer;
            border-radius: 20px;
        }
        .dash-btn {
            background: #686363;
            color: #020202;
            border: none;
            padding: 10px 20px;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            cursor: pointer;
            border-radius: 12px;
        }
        .row { display:flex; justify-content:space-between; gap:10px; }
        .panel { background:white; padding:10px; max-height:220px; overflow-y:auto; margin-bottom:10px; }
        table { width:100%; border-collapse:collapse; background:white; margin-bottom:10px; }
        table, th, td { border:1px solid #999; padding:6px; text-align:left; }
        .error { color:#b00; font-size:13px; margin-bottom:8px; }
        .success { color:#060; font-size:13px; margin-bottom:8px; }
        label { font-size: 13px; }
    </style>
</head>
<body>
<div class="navbar">
    @auth
        <div class="nav-links">
            <a href="/dashboard">Dashboard</a>
            <a href="/topics">Discussions</a>
            <a href="/quizzes">Quiz</a>
        </div>
        <form method="POST" action="/logout" style="display:inline;">
            @csrf
            <button type="submit" class="nav-logout">Logout</button>
        </form>
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
    @yield('content')
    </div>
</body>
</html>
