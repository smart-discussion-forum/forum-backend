@extends('layouts.app')

@section('header')
    <h2 class="screen-title" style="color:var(--text);">Recommended for you</h2>
@endsection

@section('content')
<div class="page-card" style="max-width:1100px; margin:30px auto; padding:30px; border-radius:18px; box-shadow:0 18px 40px rgba(15,23,42,0.08);">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:24px;">
        <div>
            <h2 style="margin:0 0 8px; color:var(--text);">Recommended for you</h2>
            <p style="margin:0; color:var(--muted);">
                Personalized topics from your groups and the hottest conversations across the platform.
            </p>
        </div>
        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <div class="table-card" style="padding:10px 14px; min-width:140px; text-align:center;">
                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:var(--muted);">Personalized</div>
                <div style="font-size:20px; font-weight:700; color:var(--text);">{{ $personalized->count() }}</div>
            </div>
            <div class="table-card" style="padding:10px 14px; min-width:140px; text-align:center;">
                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:var(--muted);">Trending</div>
                <div style="font-size:20px; font-weight:700; color:var(--text);">{{ $trending->count() }}</div>
            </div>
        </div>
    </div>

    @php
        $showPersonalized = $personalized->isNotEmpty();
        $showTrending = $trending->isNotEmpty();
    @endphp

    @if (! $showPersonalized && ! $showTrending)
        <div class="table-card" style="padding:30px; text-align:center;">
            <p style="color:var(--muted); margin:0;">
                No recommendations yet. Join a few groups and check back once there's more discussion to draw from.
            </p>
        </div>
    @else
        @if ($showPersonalized)
            <h3 style="margin:0 0 12px; color:var(--text);">Personalized suggestions</h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:16px; margin-bottom:24px;">
                @foreach ($personalized as $item)
                    <div class="table-card" style="padding:18px; display:flex; flex-direction:column; gap:10px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
                            <strong style="color:var(--text);">{{ $item->topic->title }}</strong>
                            <span style="padding:4px 8px; border-radius:999px; background:#eef2ff; color:#4338ca; font-size:12px; font-weight:700;">
                                {{ $item->topic->category ?? 'General' }}
                            </span>
                        </div>
                        <div style="color:var(--muted); font-size:14px;">{{ $item->reason ?? '—' }}</div>
                        <div style="display:flex; justify-content:space-between; align-items:center; color:var(--muted); font-size:13px;">
                            <span>{{ $item->topic->posts_count }} posts</span>
                            <a href="/discussions/{{ $item->topic->id }}" class="dash-btn" style="padding:8px 12px;">Open</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($showTrending)
            <h3 style="margin:0 0 12px; color:var(--text);">Trending this week</h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:16px;">
                @foreach ($trending as $item)
                    <div class="table-card" style="padding:18px; display:flex; flex-direction:column; gap:10px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
                            <strong style="color:var(--text);">{{ $item->topic->title }}</strong>
                            <span style="padding:4px 8px; border-radius:999px; background:#ecfeff; color:#0f766e; font-size:12px; font-weight:700;">
                                {{ $item->topic->category ?? 'General' }}
                            </span>
                        </div>
                        <div style="color:var(--muted); font-size:14px;">{{ $item->reason ?? '—' }}</div>
                        <div style="display:flex; justify-content:space-between; align-items:center; color:var(--muted); font-size:13px;">
                            <span>{{ $item->topic->recent_posts_count ?? 0 }} recent posts</span>
                            <a href="/discussions/{{ $item->topic->id }}" class="dash-btn" style="padding:8px 12px;">Open</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
@endsection
