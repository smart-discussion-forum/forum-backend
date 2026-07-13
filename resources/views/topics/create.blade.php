@extends('layouts.app')
@section('content')
    <div class="screen-title">Create Topic</div>
    <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin-bottom:18px;">
        <a href="/topics" class="dash-btn">Back to Discussions</a>
        <a href="/dashboard" class="dash-btn">Dashboard</a>
    </div>
    <form method="POST" action="/topics">
        @csrf
        <label>Title:</label>
        <input type="text" name="title" value="{{ old('title') }}">
        @error('title') <div class="error">{{ $message }}</div> @enderror

        <label>Category:</label>
        <input type="text" name="category" value="{{ old('category') }}">
        @error('category') <div class="error">{{ $message }}</div> @enderror

        <label>Group:</label>
        <select name="group_id">
            <option value="">-- Select a group --</option>
            @foreach(auth()->user()->groups as $group)
                <option value="{{ $group->id }}" @selected(old('group_id') == $group->id)>{{ $group->name }}</option>
            @endforeach
        </select>
        @error('group_id') <div class="error">{{ $message }}</div> @enderror

        <div style="text-align:right; margin-top:10px;">
            <button type="submit" class="btn">Save</button>
        </div>
    </form>
@endsection