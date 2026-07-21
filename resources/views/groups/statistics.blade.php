@extends('layouts.app')

@section('title', 'Group Statistics')

@section('content')
<div class="page-card" style="max-width:1100px; margin:30px auto; padding:30px;">

    <h2 class="screen-title" style="color:var(--text); text-align:left; margin-bottom:4px;">
        {{ $group_name }}
    </h2>
    <p style="color:var(--muted); margin-bottom:24px;">
        Created by <span style="color:var(--text); font-weight:600;">{{ $created_by }}</span>
    </p>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap:16px; margin-bottom:28px;">
        <div class="page-card" style="margin:0; padding:18px; text-align:center;">
            <p style="color:var(--muted); font-size:0.875rem; margin:0 0 6px;">Members</p>
            <p style="color:var(--text); font-size:1.75rem; font-weight:700; margin:0;">{{ $member_count }}</p>
        </div>
        <div class="page-card" style="margin:0; padding:18px; text-align:center;">
            <p style="color:var(--muted); font-size:0.875rem; margin:0 0 6px;">Topics</p>
            <p style="color:var(--text); font-size:1.75rem; font-weight:700; margin:0;">{{ $topic_count }}</p>
        </div>
        <div class="page-card" style="margin:0; padding:18px; text-align:center;">
            <p style="color:var(--muted); font-size:0.875rem; margin:0 0 6px;">Posts</p>
            <p style="color:var(--text); font-size:1.75rem; font-weight:700; margin:0;">{{ $post_count }}</p>
        </div>
        <div class="page-card" style="margin:0; padding:18px; text-align:center;">
            <p style="color:var(--muted); font-size:0.875rem; margin:0 0 6px;">Messages</p>
            <p style="color:var(--text); font-size:1.75rem; font-weight:700; margin:0;">{{ $message_count }}</p>
        </div>
    </div>

    <form method="GET" action="{{ route('groups.statistics', $selected_group_id) }}" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin-bottom:24px;">
        <label for="group_id" style="font-weight:600; color:var(--text);">Filter by group</label>
        <select name="group_id" id="group_id" class="form-control" style="min-width:220px;">
            @foreach($visible_groups as $groupOption)
                <option value="{{ $groupOption->id }}" {{ $selected_group_id == $groupOption->id ? 'selected' : '' }}>
                    {{ $groupOption->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="dash-btn">Apply</button>
    </form>

    <h3 style="color:var(--text); font-weight:600; font-size:1.125rem; margin-bottom:12px;">Members by role</h3>
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members_by_role as $role => $count)
                    <tr>
                        <td>{{ $role }}</td>
                        <td>{{ $count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="color:var(--muted);">No members yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h3 style="color:var(--text); font-weight:600; font-size:1.125rem; margin:24px 0 12px;">Participation stats</h3>
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>
                        <a href="?group_id={{ $selected_group_id }}&sort_by=name&sort_order={{ $sort_by === 'name' && $sort_order === 'asc' ? 'desc' : 'asc' }}" style="color:inherit; text-decoration:none; cursor:pointer;">
                            User {{ $sort_by === 'name' ? ($sort_order === 'asc' ? '▲' : '▼') : '' }}
                        </a>
                    </th>
                    <th>
                        <a href="?group_id={{ $selected_group_id }}&sort_by=post_count&sort_order={{ $sort_by === 'post_count' && $sort_order === 'asc' ? 'desc' : 'asc' }}" style="color:inherit; text-decoration:none; cursor:pointer;">
                            Post count {{ $sort_by === 'post_count' ? ($sort_order === 'asc' ? '▲' : '▼') : '' }}
                        </a>
                    </th>
                    <th>
                        <a href="?group_id={{ $selected_group_id }}&sort_by=participation_score&sort_order={{ $sort_by === 'participation_score' && $sort_order === 'asc' ? 'desc' : 'asc' }}" style="color:inherit; text-decoration:none; cursor:pointer;">
                            Participation score {{ $sort_by === 'participation_score' ? ($sort_order === 'asc' ? '▲' : '▼') : '' }}
                        </a>
                    </th>
                    <th>
                        <a href="?group_id={{ $selected_group_id }}&sort_by=activity_status&sort_order={{ $sort_by === 'activity_status' && $sort_order === 'asc' ? 'desc' : 'asc' }}" style="color:inherit; text-decoration:none; cursor:pointer;">
                            Activity status {{ $sort_by === 'activity_status' ? ($sort_order === 'asc' ? '▲' : '▼') : '' }}
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($participation_rows as $row)
                    <tr>
                        <td>{{ $row['name'] }}</td>
                        <td>{{ $row['post_count'] }}</td>
                        <td>{{ number_format($row['participation_score'], 2) }}</td>
                        <td>{{ $row['activity_status'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="color:var(--muted);">No participation data yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:24px;">
        <a href="{{ route('groups.index') }}" class="dash-btn">Back to groups</a>
    </div>

</div>
@endsection
