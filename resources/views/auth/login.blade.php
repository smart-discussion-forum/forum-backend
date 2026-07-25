@extends('layouts.app')
@section('content')
    <div class="auth-card" style="max-width:420px; margin:58px auto; padding:30px;">
        <div class="screen-title" style="margin-bottom:6px; font-size:26px;">Login</div>
        <p style="text-align:center; color:var(--muted); margin-top:0; margin-bottom:22px;">Access your discussions, quizzes, and profile.</p>

        @if (session('status'))
            <div style="background:#ecfdf5; color:#065f46; padding:10px 14px; border-radius:8px; margin-bottom:16px; text-align:center;">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <label>Email:</label>
            <input type="email" name="email" value="{{ old('email') }}">
            <label>Password:</label>
            <input type="password" name="password">
            @error('email') <div class="error">{{ $message }}</div> @enderror
            <div style="text-align:right; margin-top:6px;">
                <a href="{{ route('password.request') }}" style="color:var(--accent); font-size:14px;">Forgot Password?</a>
            </div>
            <div style="text-align:right; margin-top:10px;">
                <button type="submit" class="btn">Login</button>
            </div>
        </form>
    </div>
@endsection
