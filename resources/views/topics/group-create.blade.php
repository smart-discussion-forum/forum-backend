@extends('layouts.app')
@section('content')
    <div class="auth-card" style="max-width:600px; margin:24px auto; padding:28px;">
        <div class="screen-title" style="color:var(--text); margin-bottom:8px;">New Topic in {{ $group->name }}</div>
        <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin-bottom:18px;">
            <a href="/groups/{{ $group->id }}/topics" class="dash-btn">Back to Topics</a>
            <a href="/chat?group={{ $group->id }}" class="dash-btn">Back to Chat</a>
        </div>
        <form method="POST" action="/groups/{{ $group->id }}/topics">
            @csrf
            <label>Title:</label>
            <input type="text" name="title" value="{{ old('title') }}">
            @error('title') <div class="error">{{ $message }}</div> @enderror

            <label>Category:</label>
            <input type="text" name="category" value="{{ old('category') }}">
            @error('category') <div class="error">{{ $message }}</div> @enderror

            <div style="text-align:right; margin-top:10px;">
                <button type="submit" class="btn">Save</button>
            </div>
        </form>
    </div>
@endsection