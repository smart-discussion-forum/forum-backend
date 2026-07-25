@extends('layouts.app')
@section('content')
    <div class="auth-card" style="max-width:420px; margin:58px auto; padding:30px;">
        <div class="screen-title" style="margin-bottom:6px; font-size:26px;">Reset Password</div>
        <p style="text-align:center; color:var(--muted); margin-top:0; margin-bottom:22px;">
            Choose a new password for your account.
        </p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <label>Email:</label>
            <input type="email" name="email" value="{{ old('email', $email) }}" required autofocus>
            @error('email') <div class="error">{{ $message }}</div> @enderror

            <label>New Password:</label>
            <input type="password" name="password" required>
            @error('password') <div class="error">{{ $message }}</div> @enderror

            <label>Confirm New Password:</label>
            <input type="password" name="password_confirmation" required>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px;">
                <a href="{{ route('login') }}" style="color:var(--accent); font-size:14px;">Back to Login</a>
                <button type="submit" class="btn">Reset Password</button>
            </div>
        </form>
    </div>
@endsection
