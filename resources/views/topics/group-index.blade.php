@extends('layouts.app')
@section('content')
    <div class="page-card" style="max-width:1180px; margin:24px auto; padding:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div class="screen-title" style="margin:0; color:var(--text);">{{ $group->name }} — Topics</div>
            <a href="/chat?group={{ $group->id }}" class="dash-btn">Back to Group Chat</a>
        </div>
        <p style="text-align:center; color:var(--muted); margin-top:0; margin-bottom:22px;">Browse topic summaries, see the latest discussion snippet, and jump into a thread.</p>
        <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin-bottom:18px;">
            <a href="/groups/{{ $group->id }}/topics/create" class="dash-btn">New topic</a>
        </div>

        <div class="table-card">
            <table>
                <tr>
                    <th>Topic</th>
                    <th>Category</th>
                    <th>Created By</th>
                    <th>Replies</th>
                    <th>Latest Discussion</th>
                    <th>Action</th>
                </tr>
                @forelse($topicSummaries as $summary)
                    <tr>
                        <td>{{ $summary['topic']->title }}</td>
                        <td>{{ $summary['topic']->category ?? 'General' }}</td>
                        <td>{{ $summary['topic']->creator?->name ?? 'Unknown author' }}</td>
                        <td>{{ $summary['post_count'] }}</td>
                        <td>
                            @if($summary['latest_post'])
                                {{ \Illuminate\Support\Str::limit($summary['latest_post']->content, 80) }}
                            @else
                                No replies yet
                            @endif
                        </td>
                        <td><a href="/groups/{{ $group->id }}/topics/{{ $summary['topic']->id }}" class="dash-btn">Open Thread</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6">No topics yet.</td></tr>
                @endforelse
            </table>
        </div>
    </div>
@endsection