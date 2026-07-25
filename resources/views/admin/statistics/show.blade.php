@extends('layouts.app')

@section('title', $stats['name'] . ' · Statistics')

@section('content')
<div class="page-card" style="max-width:1100px; margin:30px auto; padding:30px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-end; gap:16px; flex-wrap:wrap; margin-bottom:22px;">
        <div>
            <p style="color:var(--accent-strong); font-size:12px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; margin:0 0 6px;">
                Group overview
            </p>
            <h2 class="screen-title" style="color:var(--text); text-align:left; margin:0;">{{ $stats['name'] }}</h2>
            <p style="color:var(--muted); margin:8px 0 0;">
                Created by
                <span style="color:var(--text); font-weight:600;">{{ $stats['created_by'] ?? 'Unknown' }}</span>
                @if($stats['description'])
                    · {{ $stats['description'] }}
                @endif
            </p>
        </div>
        <a href="{{ route('admin.statistics.index') }}" class="dash-btn">All groups</a>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap:14px; margin-bottom:26px;">
        <div style="background:linear-gradient(180deg, rgba(255,255,255,0.92), rgba(233,240,247,0.88)); border:1px solid rgba(79,124,168,0.18); border-radius:16px; padding:16px 18px;">
            <span style="display:block; color:var(--muted); font-size:12px; font-weight:600; margin-bottom:8px;">Members</span>
            <strong style="display:block; color:var(--text); font-size:1.7rem; line-height:1;">{{ $stats['member_count'] }}</strong>
        </div>
        <div style="background:linear-gradient(180deg, rgba(255,255,255,0.92), rgba(233,240,247,0.88)); border:1px solid rgba(79,124,168,0.18); border-radius:16px; padding:16px 18px;">
            <span style="display:block; color:var(--muted); font-size:12px; font-weight:600; margin-bottom:8px;">Topics</span>
            <strong style="display:block; color:var(--text); font-size:1.7rem; line-height:1;">{{ $stats['topic_count'] }}</strong>
        </div>
        <div style="background:linear-gradient(180deg, rgba(255,255,255,0.92), rgba(233,240,247,0.88)); border:1px solid rgba(79,124,168,0.18); border-radius:16px; padding:16px 18px;">
            <span style="display:block; color:var(--muted); font-size:12px; font-weight:600; margin-bottom:8px;">Total posts</span>
            <strong style="display:block; color:var(--text); font-size:1.7rem; line-height:1;">{{ $stats['total_posts'] }}</strong>
        </div>
        <div style="background:linear-gradient(180deg, rgba(255,255,255,0.92), rgba(233,240,247,0.88)); border:1px solid rgba(79,124,168,0.18); border-radius:16px; padding:16px 18px;">
            <span style="display:block; color:var(--muted); font-size:12px; font-weight:600; margin-bottom:8px;">Posts this week</span>
            <strong style="display:block; color:var(--text); font-size:1.7rem; line-height:1;">{{ $stats['posts_this_week'] }}</strong>
        </div>
        <div style="background:linear-gradient(180deg, rgba(255,255,255,0.92), rgba(233,240,247,0.88)); border:1px solid rgba(79,124,168,0.18); border-radius:16px; padding:16px 18px;">
            <span style="display:block; color:var(--muted); font-size:12px; font-weight:600; margin-bottom:8px;">Chat messages</span>
            <strong style="display:block; color:var(--text); font-size:1.7rem; line-height:1;">{{ $stats['message_count'] }}</strong>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:16px; margin-bottom:28px;">
        <div style="border:1px solid rgba(148,163,184,0.22); border-radius:16px; padding:18px; background:rgba(255,255,255,0.65);">
            <p style="margin:0 0 8px; color:var(--muted); font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em;">Most active topic</p>
            @if($stats['most_active_topic'])
                <p style="margin:0; color:var(--text); font-weight:700; font-size:1.1rem;">{{ $stats['most_active_topic']['title'] }}</p>
                <p style="margin:6px 0 0; color:var(--muted);">{{ $stats['most_active_topic']['posts_count'] }} posts</p>
            @else
                <p style="margin:0; color:var(--muted);">No topics yet.</p>
            @endif
        </div>
        <div style="border:1px solid rgba(148,163,184,0.22); border-radius:16px; padding:18px; background:rgba(255,255,255,0.65);">
            <p style="margin:0 0 8px; color:var(--muted); font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em;">Least active topic</p>
            @if($stats['least_active_topic'])
                <p style="margin:0; color:var(--text); font-weight:700; font-size:1.1rem;">{{ $stats['least_active_topic']['title'] }}</p>
                <p style="margin:6px 0 0; color:var(--muted);">{{ $stats['least_active_topic']['posts_count'] }} posts</p>
            @else
                <p style="margin:0; color:var(--muted);">No topics yet.</p>
            @endif
        </div>
    </div>

    <h3 style="color:var(--text); font-weight:600; font-size:1.125rem; margin:0 0 12px;">All topics</h3>
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Topic</th>
                    <th>Category</th>
                    <th>Posts</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['topics'] as $topic)
                    <tr>
                        <td>{{ $topic['title'] }}</td>
                        <td>{{ $topic['category'] ?? '—' }}</td>
                        <td>{{ $topic['posts_count'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="color:var(--muted);">No topics in this group yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
