@extends('layouts.app')
@section('content')
    <div class="auth-card" style="max-width:480px; margin:48px auto; padding:30px;">
        <div class="screen-title" style="margin-bottom:6px; font-size:26px;">Registration</div>
        <p style="text-align:center; color:var(--muted); margin-top:0; margin-bottom:22px;">Create your account and join a discussion group.</p>
        <form method="POST" action="/register">
            @csrf
            <label>Name:</label>
            <input type="text" name="name" value="{{ old('name') }}">
            @error('name') <div class="error">{{ $message }}</div> @enderror

            <label>Email:</label>
            <input type="email" name="email" value="{{ old('email') }}">
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

            <label>Choose your groups:</label>
            <div style="border:1px solid #d8dbe2; border-radius:8px; padding:10px; background:#fff;">
                <details>
                    <summary style="cursor:pointer; font-weight:600;">Select groups</summary>
                    <div style="margin-top:8px; display:grid; gap:6px; max-height:180px; overflow:auto;">
                        @foreach($groups as $group)
                            <label style="display:flex; align-items:center; gap:8px;">
                                <input type="checkbox" name="group_ids[]" value="{{ $group->id }}" @checked(in_array($group->id, old('group_ids', [])))>
                                <span>{{ $group->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </details>
            </div>
            @error('group_ids') <div class="error">{{ $message }}</div> @enderror

            <label><input type="checkbox" name="accepted_terms" value="1" style="width:auto;" @checked(old('accepted_terms'))> Accept Rules</label>
            @error('accepted_terms') <div class="error">{{ $message }}</div> @enderror

            <div style="text-align:right; margin-top:10px;">
                <button type="submit" class="btn">Register</button>
            </div>
        </form>
    </div>
@endsection
