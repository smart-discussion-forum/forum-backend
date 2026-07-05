@extends('layouts.app')
@section('content')
    <div class="auth-card" style="max-width:480px; margin:48px auto; padding:30px;">
        <div class="screen-title" style="margin-bottom:6px; font-size:26px;">Registration</div>
        <p style="text-align:center; color:var(--muted); margin-top:0; margin-bottom:22px;">Create your account to join topics and quizzes.</p>
        <form method="POST" action="/register">
            @csrf
            <label>Name:</label>
            <input type="text" name="name">
            @error('name') <div class="error">{{ $message }}</div> @enderror

            <label>Email:</label>
            <input type="email" name="email">
            @error('email') <div class="error">{{ $message }}</div> @enderror

            <label>Password:</label>
            <input type="password" name="password">
            @error('password') <div class="error">{{ $message }}</div> @enderror

            <label>I am a:</label>
            <select name="role">
                <option value="student">Student</option>
                <option value="teacher">Lecturer</option>
                <option value="admin">Admin</option>
            </select>

            <label><input type="checkbox" name="accepted_terms" value="1" style="width:auto;"> Accept Rules</label>
            @error('accepted_terms') <div class="error">{{ $message }}</div> @enderror

            <div style="text-align:right; margin-top:10px;">
                <button type="submit" class="btn">Register</button>
            </div>
        </form>
    </div>
@endsection
