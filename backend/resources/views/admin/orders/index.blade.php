@extends('admin.layouts.app')

@section('title', 'Orders')
@section('header-title', 'Order Management')

@section('content')
<div class="page-header">
    <h1 class="page-title">Orders</h1>
    <p class="page-subtitle">Manage all truck shipping orders</p>
</div>

<!-- Filters -->
<form method="GET" action="{{ route('admin.orders.index') }}" class="filters">
    <input type="text" name="search" class="filter-input" placeholder="Search by location, name, or email..." value="{{ request('search') }}">
    <select name="status" class="filter-select" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">
        <i class="fas fa-search"></i> Search
    </button>
    @if(request('search') || request('status'))
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-times"></i> Clear
        </a>
    @endif
</form>

<!-- Orders Table -->
<div class="card">
    <div class="card-body" style="padding:0;">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Pickup Location</th>
                        <th>Delivery Location</th>
                        <th>Cargo</th>
                        <th>Pickup Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td style="font-weight:600; color:var(--text-primary);">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="user-cell">
                                <div class="avatar">{{ substr($order->user->name, 0, 1) }}</div>
                                <div>
                                    <div class="name">{{ $order->user->name }}</div>
                                    <div class="email">{{ $order->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ Str::limit($order->pickup_location, 30) }}</td>
                        <td>{{ Str::limit($order->delivery_location, 30) }}</td>
                        <td>
                            <div style="font-weight:500; color:var(--text-primary);">{{ $order->cargo_size }}</div>
                            <div style="font-size:11px; color:var(--text-muted);">{{ $order->cargo_weight }} kg</div>
                        </td>
                        <td>{{ $order->pickup_datetime->format('M d, Y') }}</td>
                        <td>
                            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="status-form">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                    <option value="in_progress" {{ $order->status === 'in_progress' ? 'selected' : '' }}>🚛 In Progress</option>
                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>✅ Delivered</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="fas fa-truck"></i>
                                <h3>No orders found</h3>
                                <p>There are no orders matching your search criteria.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
@if($orders->hasPages())
<div class="pagination">
    {{ $orders->links() }}
</div>
@endif
@endsection
