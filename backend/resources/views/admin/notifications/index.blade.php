@extends('admin.layouts.app')

@section('title', 'Notifications')
@section('header-title', 'Notifications')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-white font-bold inline-flex items-center">
                Notifications
            </h1>
            <p class="text-sm text-slate-400 mt-1">View and manage your recent alerts and events</p>
        </div>
    </div>

    <!-- Notifications List Box -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-lg shadow-black/20 overflow-hidden">
        @if($notifications->count() > 0)
            <div class="divide-y divide-slate-800">
                @foreach($notifications as $notification)
                    <div class="px-6 py-5 flex items-start gap-4 transition-colors hover:bg-slate-800/50 {{ $notification->unread() ? 'bg-indigo-500/5' : '' }}">
                        <!-- Icon -->
                        <div class="w-10 h-10 rounded-full shrink-0 flex items-center justify-center {{ $notification->unread() ? 'bg-indigo-500/20 text-indigo-400' : 'bg-slate-800 text-slate-500' }}">
                            <i class="fas fa-bell"></i>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0 pr-4">
                            <h3 class="text-sm font-semibold {{ $notification->unread() ? 'text-white' : 'text-slate-300' }}">
                                @if(isset($notification->data['order_id']))
                                    New Truck Order #{{ $notification->data['order_id'] }} by {{ $notification->data['user_name'] ?? 'User' }}
                                @else
                                    System Notification
                                @endif
                            </h3>
                            <p class="text-xs text-slate-400 mt-1 line-clamp-2">
                                @if(isset($notification->data['pickup_location']))
                                    Pickup: {{ $notification->data['pickup_location'] }} → Delivery: {{ $notification->data['delivery_location'] }}
                                @else
                                    You have a new update.
                                @endif
                            </p>
                            <span class="block text-[11px] text-slate-500 mt-2 font-medium">
                                <i class="far fa-clock mr-1"></i> {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col items-end gap-2 shrink-0">
                            @if(isset($notification->data['order_id']))
                                <a href="{{ route('admin.orders.show', $notification->data['order_id']) }}" class="text-xs font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                                    View Order
                                </a>
                            @endif
                            @if($notification->unread())
                                <form action="{{ route('admin.notifications.markRead', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs font-medium text-slate-400 hover:text-white transition-colors flex items-center gap-1">
                                        <i class="fas fa-check"></i> Mark as Read
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="px-6 py-4 border-t border-slate-800">
                {{ $notifications->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <i class="far fa-bell-slash"></i>
                <h3 class="text-white">All caught up!</h3>
                <p>You have no notifications at this time.</p>
            </div>
        @endif
    </div>
</div>
@endsection
