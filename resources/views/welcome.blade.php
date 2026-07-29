@extends('layouts.app')
@section('content')
    <div class="welcome-card" style="max-width:820px; margin:44px auto; padding:44px; border-radius:34px; text-align:center;">
        <div style="display:inline-flex; align-items:center; gap:14px; margin-bottom:18px; color:#f8fbff; font-size:15px; letter-spacing:0.16em; text-transform:uppercase;">
            <img src="{{ asset('images/logo.png') }}" alt="Mindshare logo" style="width:48px; height:48px; object-fit:contain; border-radius:12px;">
            Mindshare Discussion Forum
        </div>
        <div style="height:1px; background:linear-gradient(90deg, transparent, rgba(255,255,255,0.26), transparent); margin:0 auto 24px; max-width:620px;"></div>
        <div class="screen-title" style="margin:0; color:#f8fbff; font-size:46px; line-height:1.05;">Welcome</div>
        <p style="color:rgba(248,251,255,0.82); font-size:18px; margin:18px auto 30px; max-width:540px;">A clean space for topics, conversations, quizzes, and profile management.</p>
        <div style="display:flex; justify-content:center; gap:14px; flex-wrap:wrap;">
            <a href="/rules" class="btn" style="font-size:15px; padding:13px 22px;">View Rules</a>
            <a href="/login" class="dash-btn" style="font-size:15px; padding:13px 22px;">Login</a>
            <a href="/register" class="dash-btn" style="font-size:15px; padding:13px 22px;">Register</a>
        </div>
    </div>
@endsection
