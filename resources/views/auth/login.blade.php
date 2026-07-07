@extends('layouts.app')
@section('content')
    <div class="auth-card" style="max-width:420px; margin:58px auto; padding:30px;">
        <div class="screen-title" style="margin-bottom:6px; font-size:26px;">Login</div>
        <p style="text-align:center; color:var(--muted); margin-top:0; margin-bottom:22px;">Access your discussions, quizzes, and profile.</p>
        <form method="POST" action="/login">
            @csrf
            <label>Email:</label>
            <input type="email" name="email">
            <label>Password:</label>
            <input type="password" name="password">
            @error('email') <div class="error">{{ $message }}</div> @enderror
            <div style="text-align:right; margin-top:10px;">
                <button type="submit" class="btn">Login</button>
            </div>
        </form>
    </div>
@endsection
