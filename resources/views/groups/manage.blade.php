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
                            @if(auth()->user()->role === \App\Enums\RoleEnum::Admin)
                                <a href="{{ route('admin.statistics.show', $group->id) }}" class="dash-btn" style="padding:6px 12px; font-size:0.8rem;">Stats</a>
                            @else
                                <a href="{{ route('groups.statistics', $group->id) }}" class="dash-btn" style="padding:6px 12px; font-size:0.8rem;">Stats</a>
                            @endif
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

<style>
    @media (max-width: 640px) {
        .table-card table thead th:nth-child(2),
        .table-card table thead th:nth-child(3),
        .table-card table thead th:nth-child(4),
        .table-card table tbody td:nth-child(2),
        .table-card table tbody td:nth-child(3),
        .table-card table tbody td:nth-child(4) {
            display: none;
        }
        .table-card table {
            min-width: 0;
        }
        .table-card table th:first-child,
        .table-card table td:first-child {
            width: 100%;
        }
    }
</style>
@endsection
