@extends('layouts.app')
@section('box-style', 'centered')
@section('content')
    <div class="screen-title">Login</div>
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
@endsection
