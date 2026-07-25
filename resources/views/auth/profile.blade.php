@extends('layouts.app')
@section('content')
    <div class="page-card" style="max-width:860px; margin:24px auto; padding:28px;">
        <div class="screen-title" style="color:var(--text); margin-bottom:8px;">Profile</div>
        <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin-bottom:18px;">
            <a href="/dashboard" class="dash-btn">Dashboard</a>
            @if($user->role !== \App\Enums\RoleEnum::Admin)
                <a href="/chat" class="dash-btn">Group Chat</a>
            @endif
            <a href="/quizzes" class="dash-btn">Quizzes</a>
        </div>

        <div class="panel" style="max-width:720px; margin:0 auto 20px;">
            <form method="POST" action="/profile">
                @csrf
                <label>Name:</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}">
                @error('name') <div class="error">{{ $message }}</div> @enderror

                <label>Email:</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}">
                @error('email') <div class="error">{{ $message }}</div> @enderror

                <label>Role:</label>
                <input type="text" value="{{ ucfirst($user->role->value) }}" readonly>

                <label>Status:</label>
                <input type="text" value="{{ ucfirst($user->status->value) }}" readonly>

                <div style="text-align:right; margin-top:10px;">
                    <button type="submit" class="btn">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
@endsection