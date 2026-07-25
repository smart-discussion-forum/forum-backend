@extends('layouts.app')

@section('content')
    <div class="page-card" style="max-width:900px; margin:30px auto; padding:30px;">
        <div style="display:flex; justify-content:space-between; gap:16px; align-items:center; margin-bottom:8px;">
            <div>
                <div class="screen-title" style="margin-bottom:6px;">{{ $group->name }} Topics</div>
                <p style="color:var(--muted); margin:0;">Choose a topic to open its discussion.</p>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end;">
                <a href="{{ route('chat', ['group' => $group->id]) }}" class="dash-btn">Back to Chat</a>
                <a href="/groups/{{ $group->id }}/topics/create" class="dash-btn">New Topic</a>
            </div>
    <div class="page-card" style="max-width:1180px; margin:24px auto; padding:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div class="screen-title" style="margin:0; color:var(--text);">{{ $group->name }} — Topics</div>
            <a href="/chat?group={{ $group->id }}" class="dash-btn">Back to Group Chat</a>
        </div>
        <p style="text-align:center; color:var(--muted); margin-top:0; margin-bottom:22px;">Browse topic summaries, see the latest discussion snippet, and jump into a thread.</p>
        <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin-bottom:18px;">
            @if(in_array(auth()->user()->role, [\App\Enums\RoleEnum::Lecturer, \App\Enums\RoleEnum::Admin], true))
                <a href="/groups/{{ $group->id }}/topics/create" class="dash-btn">New topic</a>
            @endif
        </div>

        <div class="topic-list" style="margin-top:22px;">
            @forelse($topicSummaries as $summary)
                @php($topic = $summary['topic'])
                <a href="/groups/{{ $group->id }}/topics/{{ $topic->id }}" class="topic-card">
                    <div class="topic-card-title">{{ $topic->title }}</div>
                    <div class="topic-card-meta">
                        {{ $topic->creator?->name ?? 'Unknown author' }}
                        @if($topic->category)
                            <span class="topic-dot">•</span>{{ $topic->category }}
                        @endif
                        <span class="topic-dot">•</span>{{ $summary['post_count'] }} {{ Str::plural('reply', $summary['post_count']) }}
                    </div>
                    @if($summary['latest_post'])
                        <div style="color:var(--muted); margin-top:8px; font-size:14px;">
                            Latest reply: {{ Str::limit($summary['latest_post']->content, 120) }}
                        </div>
                    @endif
                </a>
            @empty
                <div class="empty-panel">No topics yet. Create the first topic for this group.</div>
            @endforelse
        </div>
    </div>
@endsection
