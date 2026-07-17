@extends('layouts.app')

@section('title', 'Group Statistics')

@section('content')
<div class="page-card" style="max-width:980px; margin:30px auto; padding:30px;">

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

    <div style="margin-top:24px;">
        <a href="{{ route('groups.index') }}" class="dash-btn">Back to groups</a>
    </div>

</div>
@endsection
