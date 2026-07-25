@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
<div class="page-card" style="max-width:1080px; margin:30px auto; padding:30px;">

    <h2 class="screen-title" style="color:var(--text); text-align:left; margin-bottom:20px;">
        Manage Users
    </h2>

    @if (session('status'))
        <p style="color:#16a34a; margin-bottom:16px;">{{ session('status') }}</p>
    @endif
    @if (session('error'))
        <p style="color:#dc2626; margin-bottom:16px;">{{ session('error') }}</p>
    @endif

    @php
        $filters = [
            'all' => 'All',
            'active' => 'Active',
            'blacklisted' => 'Blacklisted',
            'warned' => 'Warned',
        ];
        $currentFilter = $filter ?? 'all';
        $currentSearch = $search ?? '';
    @endphp

    <form action="{{ route('admin.users.index') }}" method="GET" style="display:flex; gap:10px; margin-bottom:16px;">
        <input type="hidden" name="filter" value="{{ $currentFilter }}">
        <input
            type="text"
            name="search"
            value="{{ $currentSearch }}"
            placeholder="Search by name or email..."
            style="flex:1; padding:8px 14px; border-radius:10px; border:1px solid var(--line); background:rgba(255,255,255,0.9); color:var(--text); font-size:0.9rem;"
        >
        <button type="submit" class="dash-btn" style="padding:8px 16px; font-size:0.85rem;">
            Search
        </button>
        @if($currentSearch !== '')
            <a href="{{ route('admin.users.index', ['filter' => $currentFilter]) }}" class="dash-btn" style="padding:8px 16px; font-size:0.85rem;">
                Clear
            </a>
        @endif
    </form>

    <div style="display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap;">
        @foreach($filters as $key => $label)
            <a href="{{ route('admin.users.index', ['filter' => $key, 'search' => $currentSearch]) }}"
               class="dash-btn"
               style="padding:6px 14px; font-size:0.85rem; {{ $currentFilter === $key ? 'background:var(--accent-strong); color:#fff;' : '' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Warnings</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role->value }}</td>
                        <td>
                            <span style="{{ $user->status?->value === 'Blacklisted' ? 'color:#dc2626; font-weight:600;' : 'color:var(--text);' }}">
                                {{ $user->status?->value ?? 'Active' }}
                            </span>
                        </td>
                        <td>{{ $user->warnings_count }}</td>
                        <td>
                            <a href="{{ route('admin.users.show', $user->id) }}" class="dash-btn" style="padding:6px 12px; font-size:0.8rem;">
                                Manage
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="color:var(--muted);">
                            @if($currentSearch !== '')
                                No users match "{{ $currentSearch }}".
                            @else
                                No users match this filter.
                            @endif
                        </td>
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
        .table-card table thead th:nth-child(5),
        .table-card table tbody td:nth-child(2),
        .table-card table tbody td:nth-child(3),
        .table-card table tbody td:nth-child(4),
        .table-card table tbody td:nth-child(5) {
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
