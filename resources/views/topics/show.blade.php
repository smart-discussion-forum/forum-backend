@extends('layouts.app')
@section('content')
    <div class="screen-title">{{ $topic->title }}</div>
    <div class="panel">
        @forelse($posts as $post)
            <p><strong>{{ $post->user->name }}:</strong> {{ $post->content }}</p>
        @empty
            <p>No discussion yet — be the first to reply.</p>
        @endforelse
    </div>
    <form method="POST" action="/topics/{{ $topic->id }}/posts">
        @csrf
        <textarea name="content" placeholder="Type response..."></textarea>
        <div style="text-align:right; margin-top:10px;">
            <button type="submit">Send</button>
        </div>
    </form>
@endsection
