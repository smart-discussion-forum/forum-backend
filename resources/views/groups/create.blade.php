@extends('layouts.app')

@section('content')
<div class="page-card" style="max-width:600px; margin:40px auto; padding:24px;">
    <h2 style="margin-bottom:16px;">Create a New Group</h2>

    @if ($errors->any())
        <div style="background:#fee; border:1px solid #f99; padding:12px; border-radius:6px; margin-bottom:16px;">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('groups.store') }}">
        @csrf

        <div style="margin-bottom:16px;">
            <label for="name" style="display:block; font-weight:600; margin-bottom:6px;">Group Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="100"
                   style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
        </div>

        <div style="margin-bottom:16px;">
            <label for="description" style="display:block; font-weight:600; margin-bottom:6px;">Description</label>
            <textarea id="description" name="description" rows="4"
                      style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="dash-btn" style="padding:10px 18px;">Create Group</button>
    </form>
</div>
@endsection
