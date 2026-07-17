@extends('layouts.app')

@section('title', 'Manage Groups')

@section('content')
<div class="page-card" style="max-width:980px; margin:30px auto; padding:30px;">

    <h2 class="screen-title" style="color:var(--text); text-align:left; margin-bottom:20px;">
        Manage Groups
    </h2>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Created by</th>
                    <th>Members</th>
                    <th>Topics</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groups as $group)
                    <tr>
                        <td>{{ $group->name }}</td>
                        <td>{{ $group->creator?->name }}</td>
                        <td>{{ $group->members_count }}</td>
                        <td>{{ $group->topics_count }}</td>
                        <td class="quiz-actions">
                            <a href="{{ route('groups.statistics', $group->id) }}" class="dash-btn" style="padding:6px 12px; font-size:0.8rem;">Stats</a>
                            <a href="{{ route('groups.edit', $group->id) }}" class="dash-btn" style="padding:6px 12px; font-size:0.8rem;">Edit</a>
                            <form method="POST" action="{{ route('groups.destroy', $group->id) }}" style="display:inline;" onsubmit="return confirm('Delete this group? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dash-btn" style="padding:6px 12px; font-size:0.8rem; color:#dc2626;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="color:var(--muted);">No groups yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
