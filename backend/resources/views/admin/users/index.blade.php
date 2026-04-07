@extends('admin.layouts.app')

@section('title', 'Users')
@section('header-title', 'User Management')

@section('content')
<div class="page-header">
    <h1 class="page-title">Users</h1>
    <p class="page-subtitle">Manage registered users on the platform</p>
</div>

<!-- Search -->
<form method="GET" action="{{ route('admin.users.index') }}" class="filters">
    <input type="text" name="search" class="filter-input" placeholder="Search by name, email, or phone..." value="{{ request('search') }}">
    <button type="submit" class="btn btn-primary btn-sm">
        <i class="fas fa-search"></i> Search
    </button>
    @if(request('search'))
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-times"></i> Clear
        </a>
    @endif
</form>

<!-- Users Table -->
<div class="card">
    <div class="card-body" style="padding:0;">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Phone</th>
                        <th>Orders</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="avatar">{{ substr($user->name, 0, 1) }}</div>
                                <div>
                                    <div class="name">{{ $user->name }}</div>
                                    <div class="email">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->phone ?? '—' }}</td>
                        <td>
                            <span style="font-weight:600; color:var(--text-primary);">{{ $user->orders_count }}</span>
                            <span style="color:var(--text-muted);"> orders</span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $user->is_active ? 'active' : 'blocked' }}">
                                {{ $user->is_active ? 'Active' : 'Blocked' }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group">
                                <form action="{{ route('admin.users.toggle', $user) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    @if($user->is_active)
                                        <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Block this user?')">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </form>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user and all their orders? This cannot be undone.')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-users"></i>
                                <h3>No users found</h3>
                                <p>There are no registered users matching your search.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($users->hasPages())
<div class="pagination">
    {{ $users->links() }}
</div>
@endif
@endsection
