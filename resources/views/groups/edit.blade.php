@extends('layouts.app')

@section('title', 'Edit Group')

@section('content')
<div class="auth-card" style="max-width:520px; margin:30px auto; padding:30px;">

    <h2 class="screen-title">Edit Group</h2>

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('groups.update', $group->id) }}">
        @csrf
        @method('PUT')

        <label for="name">Group name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $group->name) }}" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4">{{ old('description', $group->description) }}</textarea>

        <div class="row" style="margin-top:16px;">
            <a href="{{ route('groups.manage') }}" class="dash-btn">Cancel</a>
            <button type="submit" class="btn">Save changes</button>
        </div>
    </form>

</div>
@endsection
