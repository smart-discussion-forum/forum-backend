@extends('layouts.app')
@section('content')
    <div class="page-card" style="max-width:1180px; margin:24px auto; padding:24px;">
        <div class="screen-title" style="margin-bottom:8px; color:var(--text);">Topics</div>
        <p style="text-align:center; color:var(--muted); margin-top:0; margin-bottom:22px;">Browse topic summaries, see the latest discussion snippet, and jump into a thread.</p>
        <form method="GET" action="/topics/search" style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap; margin-bottom:18px;">
    <input type="text" name="keyword" placeholder="Search by keyword..." value="{{ $keyword ?? '' }}" style="flex:1; min-width:200px; max-width:300px;">
    <input type="text" name="category" placeholder="Filter by category..." value="{{ $category ?? '' }}" style="flex:1; min-width:180px; max-width:260px;">
    <button type="submit" class="dash-btn">Search</button>
    @if(($keyword ?? null) || ($category ?? null))
        <a href="/topics" class="dash-btn">Clear</a>
    @endif
</form>
        <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin-bottom:18px;">
            <a href="/topics/create" class="dash-btn">New topic</a>
            <a href="/discussions" class="dash-btn">View Discussions</a>
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
                        <td><a href="/discussions/{{ $summary['topic']->id }}" class="dash-btn">View Discussion</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6">No topics yet.</td></tr>
                @endforelse
            </table>
        </div>
    </div>
@endsection
