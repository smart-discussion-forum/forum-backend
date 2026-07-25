@extends('layouts.app')

@section('title', 'Group Statistics')

@section('content')
<div class="page-card" style="max-width:1100px; margin:30px auto; padding:30px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-end; gap:16px; flex-wrap:wrap; margin-bottom:22px;">
        <div>
            <h2 class="screen-title" style="color:var(--text); text-align:left; margin:0 0 8px;">Group Statistics</h2>
            <p style="color:var(--muted); margin:0;">
                Overall activity for each group — posts, topics, and weekly engagement.
            </p>
        </div>
        <a href="/dashboard" class="dash-btn">Back</a>
    </div>

    <div style="display:flex; flex-direction:column; gap:16px;">
        @forelse($groups as $group)
            <div style="border:1px solid rgba(148,163,184,0.22); border-radius:16px; background:rgba(255,255,255,0.72); padding:20px 22px;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap; margin-bottom:16px;">
                    <div>
                        <h3 style="margin:0 0 4px; color:var(--text); font-size:1.2rem;">{{ $group['name'] }}</h3>
                        <p style="margin:0; color:var(--muted); font-size:0.875rem;">
                            Created by {{ $group['created_by'] ?? 'Unknown' }}
                        </p>
                    </div>
                    <a href="{{ route('admin.statistics.show', $group['id']) }}" class="dash-btn" style="padding:8px 14px; font-size:0.875rem;">
                        View details
                    </a>
                </div>

                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:12px; margin-bottom:14px;">
                    <div style="padding:12px 14px; border-radius:12px; background:rgba(233,240,247,0.7);">
                        <span style="display:block; color:var(--muted); font-size:12px; font-weight:600; margin-bottom:6px;">Members</span>
                        <strong style="color:var(--text); font-size:1.35rem;">{{ $group['member_count'] }}</strong>
                    </div>
                    <div style="padding:12px 14px; border-radius:12px; background:rgba(233,240,247,0.7);">
                        <span style="display:block; color:var(--muted); font-size:12px; font-weight:600; margin-bottom:6px;">Topics</span>
                        <strong style="color:var(--text); font-size:1.35rem;">{{ $group['topic_count'] }}</strong>
                    </div>
                    <div style="padding:12px 14px; border-radius:12px; background:rgba(233,240,247,0.7);">
                        <span style="display:block; color:var(--muted); font-size:12px; font-weight:600; margin-bottom:6px;">Total posts</span>
                        <strong style="color:var(--text); font-size:1.35rem;">{{ $group['total_posts'] }}</strong>
                    </div>
                    <div style="padding:12px 14px; border-radius:12px; background:rgba(233,240,247,0.7);">
                        <span style="display:block; color:var(--muted); font-size:12px; font-weight:600; margin-bottom:6px;">Posts this week</span>
                        <strong style="color:var(--text); font-size:1.35rem;">{{ $group['posts_this_week'] }}</strong>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:12px;">
                    <p style="margin:0; color:var(--muted); font-size:0.9rem;">
                        <span style="font-weight:600; color:var(--text);">Most active topic:</span>
                        @if($group['most_active_topic'])
                            {{ $group['most_active_topic']['title'] }}
                            ({{ $group['most_active_topic']['posts_count'] }} posts)
                        @else
                            None yet
                        @endif
                    </p>
                    <p style="margin:0; color:var(--muted); font-size:0.9rem;">
                        <span style="font-weight:600; color:var(--text);">Least active topic:</span>
                        @if($group['least_active_topic'])
                            {{ $group['least_active_topic']['title'] }}
                            ({{ $group['least_active_topic']['posts_count'] }} posts)
                        @else
                            None yet
                        @endif
                    </p>
                </div>
            </div>
        @empty
            <div style="text-align:center; padding:36px 18px; color:var(--muted); border:1px dashed rgba(148,163,184,0.35); border-radius:16px;">
                No groups yet. Create a group to start tracking statistics.
            </div>
        @endforelse
    </div>
</div>
@endsection
