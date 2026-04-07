@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('header-title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Overview of your truck ordering operations</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card accent">
        <div class="stat-icon"><i class="fas fa-boxes-stacked"></i></div>
        <div class="stat-value">{{ $total_orders }}</div>
        <div class="stat-label">Total Orders</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-value">{{ $pending_orders }}</div>
        <div class="stat-label">Pending Orders</div>
    </div>
    <div class="stat-card info">
        <div class="stat-icon"><i class="fas fa-truck-moving"></i></div>
        <div class="stat-value">{{ $in_progress_orders }}</div>
        <div class="stat-label">In Progress</div>
    </div>
    <div class="stat-card success">
        <div class="stat-icon"><i class="fas fa-circle-check"></i></div>
        <div class="stat-value">{{ $delivered_orders }}</div>
        <div class="stat-label">Delivered</div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Recent Orders</h3>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
            View All <i class="fas fa-arrow-right" style="font-size:11px;"></i>
        </a>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Pickup</th>
                        <th>Delivery</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_orders as $order)
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
                        <td>{{ Str::limit($order->pickup_location, 25) }}</td>
                        <td>{{ Str::limit($order->delivery_location, 25) }}</td>
                        <td>
                            <span class="badge badge-{{ $order->status }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h3>No orders yet</h3>
                                <p>Orders will appear here once users start submitting them.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-top:24px;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Registered Users</h3>
        </div>
        <div class="card-body" style="text-align:center; padding:40px;">
            <div style="font-size:48px; font-weight:800; background:linear-gradient(135deg, var(--accent), #a78bfa); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">
                {{ $total_users }}
            </div>
            <p style="color:var(--text-secondary); font-size:14px; margin-top:8px;">Active users on the platform</p>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Quick Actions</h3>
        </div>
        <div class="card-body" style="display:flex; flex-direction:column; gap:12px;">
            <a href="{{ route('admin.orders.index') }}?status=pending" class="btn btn-warning" style="justify-content:center;">
                <i class="fas fa-clock"></i> Review Pending Orders ({{ $pending_orders }})
            </a>
            <a href="{{ route('admin.orders.index') }}?status=in_progress" class="btn btn-info" style="justify-content:center;">
                <i class="fas fa-truck"></i> Track In-Progress ({{ $in_progress_orders }})
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary" style="justify-content:center;">
                <i class="fas fa-users"></i> Manage Users
            </a>
        </div>
    </div>
</div>
@endsection
