@extends('layouts.app')
@section('content')
    <div class="screen-title">Discussion forum</div>
    <table>
        <tr><th>TOPIC</th></tr>
        @forelse($topics as $topic)
            <tr><td><a href="/topics/{{ $topic->id }}">{{ $topic->title }}</a></td></tr>
        @empty
            <tr><td>No topics yet.</td></tr>
        @endforelse
    </table>
    <div style="text-align:right;">
        <a href="/topics/create" class="btn">create topic</a>
    </div>
@endsection
