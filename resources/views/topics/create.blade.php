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
        <input type="text" name="title">
        <label>Category:</label>
        <input type="text" name="category">
        <div style="text-align:right; margin-top:10px;">
            <button type="submit" class="btn">Save</button>
        </div>
    </form>
@endsection
