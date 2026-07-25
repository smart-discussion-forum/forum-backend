@extends('layouts.app')

@section('title', 'Participation Marks')

@section('content')
<style>
    .part-hero {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 22px;
    }
    .part-kicker {
        color: var(--accent-strong);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin: 0 0 6px;
    }
    .part-title {
        margin: 0;
        text-align: left;
        color: var(--text);
        font-size: 28px;
    }
    .part-subtitle {
        margin: 8px 0 0;
        color: var(--muted);
    }
    .part-metrics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 14px;
        margin-bottom: 26px;
    }
    .part-metric {
        background: linear-gradient(180deg, rgba(255,255,255,0.92), rgba(233,240,247,0.88));
        border: 1px solid rgba(79, 124, 168, 0.18);
        border-radius: 16px;
        padding: 16px 18px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }
    .part-metric span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 8px;
    }
    .part-metric strong {
        display: block;
        color: var(--text);
        font-size: 1.7rem;
        line-height: 1;
    }
    .part-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .part-row {
        display: grid;
        grid-template-columns: 52px minmax(180px, 1.4fr) 1fr 110px 100px;
        gap: 14px;
        align-items: center;
        background: rgba(255,255,255,0.78);
        border: 1px solid rgba(148, 163, 184, 0.22);
        border-radius: 16px;
        padding: 14px 16px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .part-row:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.1);
    }
    .part-rank {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-weight: 700;
        color: white;
        background: linear-gradient(135deg, #58779e, #355172);
    }
    .part-rank.gold { background: linear-gradient(135deg, #d4a017, #a67c00); }
    .part-rank.silver { background: linear-gradient(135deg, #9aa4b2, #6b7280); }
    .part-rank.bronze { background: linear-gradient(135deg, #c2783b, #8f4e1c); }
    .part-user strong {
        display: block;
        color: var(--text);
        font-size: 15px;
    }
    .part-user span {
        color: var(--muted);
        font-size: 12px;
    }
    .part-bar-wrap {
        background: rgba(79, 124, 168, 0.12);
        border-radius: 999px;
        height: 10px;
        overflow: hidden;
    }
    .part-bar {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #4f7ca8, #2f5f84);
    }
    .part-score {
        font-weight: 700;
        color: var(--accent-strong);
        font-size: 1.05rem;
        text-align: right;
    }
    .part-status {
        display: inline-flex;
        justify-content: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }
    .part-status.active {
        background: rgba(34, 197, 94, 0.14);
        color: #15803d;
    }
    .part-status.inactive {
        background: rgba(100, 116, 139, 0.14);
        color: #475569;
    }
    .part-empty {
        text-align: center;
        padding: 36px 18px;
        color: var(--muted);
        border: 1px dashed rgba(148, 163, 184, 0.35);
        border-radius: 16px;
        background: rgba(255,255,255,0.55);
    }
    .part-sort {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }
    .part-sort a {
        text-decoration: none;
        color: var(--muted);
        background: rgba(255,255,255,0.7);
        border: 1px solid rgba(148, 163, 184, 0.25);
        border-radius: 999px;
        padding: 7px 12px;
        font-size: 12px;
        font-weight: 700;
    }
    .part-sort a.active {
        color: white;
        background: linear-gradient(135deg, #58779e, #355172);
        border-color: transparent;
    }
    @media (max-width: 860px) {
        .part-row {
            grid-template-columns: 44px 1fr;
            grid-template-areas:
                "rank user"
                "bar bar"
                "score status";
        }
        .part-rank { grid-area: rank; }
        .part-user { grid-area: user; }
        .part-bar-wrap { grid-area: bar; }
        .part-score { grid-area: score; text-align: left; }
        .part-status { grid-area: status; justify-self: end; }
    }
</style>

<div class="page-card" style="max-width:1100px; margin:30px auto; padding:30px;">
    <div class="part-hero">
        <div>
            <p class="part-kicker">Student participation</p>
            <h2 class="part-title">{{ $group_name }}</h2>
            <p class="part-subtitle">
                Marks for students in this group · created by
                <span style="color:var(--text); font-weight:600;">{{ $created_by }}</span>
            </p>
        </div>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/dashboard') }}" class="dash-btn">Back</a>
    </div>

    <div class="part-metrics">
        <div class="part-metric">
            <span>Students</span>
            <strong>{{ $student_count }}</strong>
        </div>
        <div class="part-metric">
            <span>Topics</span>
            <strong>{{ $topic_count }}</strong>
        </div>
        <div class="part-metric">
            <span>Discussion posts</span>
            <strong>{{ $post_count }}</strong>
        </div>
        <div class="part-metric">
            <span>Average mark</span>
            <strong>{{ number_format($average_score, 1) }}</strong>
        </div>
        <div class="part-metric">
            <span>Top mark</span>
            <strong>{{ number_format($top_score, 1) }}</strong>
        </div>
    </div>

    <div class="part-sort">
        <a href="?sort_by=participation_score&sort_order={{ $sort_by === 'participation_score' && $sort_order === 'desc' ? 'asc' : 'desc' }}"
           class="{{ $sort_by === 'participation_score' ? 'active' : '' }}">Score</a>
        <a href="?sort_by=post_count&sort_order={{ $sort_by === 'post_count' && $sort_order === 'desc' ? 'asc' : 'desc' }}"
           class="{{ $sort_by === 'post_count' ? 'active' : '' }}">Posts</a>
        <a href="?sort_by=name&sort_order={{ $sort_by === 'name' && $sort_order === 'asc' ? 'desc' : 'asc' }}"
           class="{{ $sort_by === 'name' ? 'active' : '' }}">Name</a>
        <a href="?sort_by=activity_status&sort_order={{ $sort_by === 'activity_status' && $sort_order === 'asc' ? 'desc' : 'asc' }}"
           class="{{ $sort_by === 'activity_status' ? 'active' : '' }}">Activity</a>
    </div>

    <div class="part-list">
        @forelse($participation_rows as $index => $row)
            @php
                $rank = $index + 1;
                $rankClass = match ($rank) {
                    1 => 'gold',
                    2 => 'silver',
                    3 => 'bronze',
                    default => '',
                };
                $barWidth = max(4, min(100, (float) $row['participation_score']));
            @endphp
            <div class="part-row">
                <div class="part-rank {{ $rankClass }}">{{ $rank }}</div>
                <div class="part-user">
                    <strong>{{ $row['name'] }}</strong>
                    <span>{{ $row['email'] }} · {{ $row['post_count'] }} posts</span>
                </div>
                <div class="part-bar-wrap" title="{{ number_format($row['participation_score'], 2) }} / 100">
                    <div class="part-bar" style="width: {{ $barWidth }}%;"></div>
                </div>
                <div class="part-score">{{ number_format($row['participation_score'], 1) }}</div>
                <span class="part-status {{ strtolower($row['activity_status']) }}">{{ $row['activity_status'] }}</span>
            </div>
        @empty
            <div class="part-empty">No student participation yet. Marks appear when students post in topics.</div>
        @endforelse
    </div>
</div>
@endsection
