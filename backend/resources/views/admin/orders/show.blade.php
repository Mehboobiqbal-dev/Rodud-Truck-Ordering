@extends('admin.layouts.app')

@section('title', 'Order #' . str_pad($order->id, 5, '0', STR_PAD_LEFT))
@section('header-title', 'Order Details')

@section('content')
<div class="page-header" style="display:flex; align-items:center; justify-content:space-between;">
    <div>
        <h1 class="page-title">Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h1>
        <p class="page-subtitle">Submitted on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
    </div>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>
</div>

<div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
    <!-- Order Details -->
    <div>
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <h3 class="card-title">Shipping Details</h3>
                <span class="badge badge-{{ $order->status }}">
                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                </span>
            </div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <label><i class="fas fa-location-dot" style="color:var(--success); margin-right:6px;"></i> Pickup Location</label>
                        <div class="value">{{ $order->pickup_location }}</div>
                    </div>
                    <div class="detail-item">
                        <label><i class="fas fa-flag-checkered" style="color:var(--danger); margin-right:6px;"></i> Delivery Location</label>
                        <div class="value">{{ $order->delivery_location }}</div>
                    </div>
                    <div class="detail-item">
                        <label><i class="fas fa-box" style="color:var(--accent); margin-right:6px;"></i> Cargo Size</label>
                        <div class="value">{{ $order->cargo_size }}</div>
                    </div>
                    <div class="detail-item">
                        <label><i class="fas fa-weight-hanging" style="color:var(--warning); margin-right:6px;"></i> Cargo Weight</label>
                        <div class="value">{{ $order->cargo_weight }} kg</div>
                    </div>
                    <div class="detail-item">
                        <label><i class="fas fa-calendar" style="color:var(--info); margin-right:6px;"></i> Pickup Date & Time</label>
                        <div class="value">{{ $order->pickup_datetime->format('F d, Y — h:i A') }}</div>
                    </div>
                    <div class="detail-item">
                        <label><i class="fas fa-calendar-check" style="color:var(--success); margin-right:6px;"></i> Delivery Date & Time</label>
                        <div class="value">{{ $order->delivery_datetime->format('F d, Y — h:i A') }}</div>
                    </div>
                </div>

                @if($order->notes)
                <div style="margin-top:24px; padding-top:24px; border-top:1px solid var(--border-color);">
                    <div class="detail-item">
                        <label><i class="fas fa-sticky-note" style="color:var(--text-muted); margin-right:6px;"></i> Additional Notes</label>
                        <div class="value" style="line-height:1.7;">{{ $order->notes }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Send Message -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-envelope" style="margin-right:8px; color:var(--accent);"></i> Send Message to Customer</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.sendMessage', $order) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" placeholder="Order Update - #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}" value="Order Update - #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" placeholder="Write your message to the customer..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Email
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Customer Info -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <h3 class="card-title">Customer Info</h3>
            </div>
            <div class="card-body" style="text-align:center;">
                <div class="user-avatar" style="width:64px; height:64px; font-size:24px; margin:0 auto 16px;">
                    {{ substr($order->user->name, 0, 1) }}
                </div>
                <div style="font-weight:700; font-size:16px; margin-bottom:4px;">{{ $order->user->name }}</div>
                <div style="color:var(--text-muted); font-size:13px; margin-bottom:4px;">{{ $order->user->email }}</div>
                @if($order->user->phone)
                <div style="color:var(--text-muted); font-size:13px;">{{ $order->user->phone }}</div>
                @endif
            </div>
        </div>

        <!-- Update Status -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <h3 class="card-title">Update Status</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-group" style="margin-bottom:12px;">
                        <select name="status" class="form-control">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                            <option value="in_progress" {{ $order->status === 'in_progress' ? 'selected' : '' }}>🚛 In Progress</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>✅ Delivered</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                        <i class="fas fa-refresh"></i> Update Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Timeline -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Timeline</h3>
            </div>
            <div class="card-body">
                <div style="display:flex; flex-direction:column; gap:16px; position:relative;">
                    <div style="position:absolute; left:11px; top:24px; bottom:24px; width:2px; background:var(--border-color);"></div>

                    <div style="display:flex; gap:12px; align-items:flex-start; position:relative;">
                        <div style="width:24px; height:24px; border-radius:50%; background:var(--success-bg); display:flex; align-items:center; justify-content:center; flex-shrink:0; z-index:1;">
                            <i class="fas fa-plus" style="font-size:10px; color:var(--success);"></i>
                        </div>
                        <div>
                            <div style="font-size:13px; font-weight:600;">Order Created</div>
                            <div style="font-size:11px; color:var(--text-muted);">{{ $order->created_at->format('M d, Y h:i A') }}</div>
                        </div>
                    </div>

                    @if($order->status !== 'pending')
                    <div style="display:flex; gap:12px; align-items:flex-start; position:relative;">
                        <div style="width:24px; height:24px; border-radius:50%; background:var(--info-bg); display:flex; align-items:center; justify-content:center; flex-shrink:0; z-index:1;">
                            <i class="fas fa-truck" style="font-size:10px; color:var(--info);"></i>
                        </div>
                        <div>
                            <div style="font-size:13px; font-weight:600;">In Progress</div>
                            <div style="font-size:11px; color:var(--text-muted);">{{ $order->updated_at->format('M d, Y h:i A') }}</div>
                        </div>
                    </div>
                    @endif

                    @if($order->status === 'delivered')
                    <div style="display:flex; gap:12px; align-items:flex-start; position:relative;">
                        <div style="width:24px; height:24px; border-radius:50%; background:var(--success-bg); display:flex; align-items:center; justify-content:center; flex-shrink:0; z-index:1;">
                            <i class="fas fa-check" style="font-size:10px; color:var(--success);"></i>
                        </div>
                        <div>
                            <div style="font-size:13px; font-weight:600;">Delivered</div>
                            <div style="font-size:11px; color:var(--text-muted);">{{ $order->updated_at->format('M d, Y h:i A') }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
