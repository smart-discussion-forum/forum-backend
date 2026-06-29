@extends('layouts.app')
@section('box-style', 'centered')
@section('content')
    <div class="screen-title">Registration</div>
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

        <label><input type="checkbox" name="accepted_terms" value="1" style="width:auto;"> Accept Rules</label>
        @error('accepted_terms') <div class="error">{{ $message }}</div> @enderror

        <div style="text-align:right; margin-top:10px;">
            <button type="submit" class="btn">Register</button>
        </div>
    </form>
@endsection
