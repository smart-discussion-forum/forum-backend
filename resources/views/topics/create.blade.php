@extends('layouts.app')
@section('content')
    <div class="screen-title">Create Topic</div>
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
