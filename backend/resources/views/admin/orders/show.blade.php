@extends('admin.layouts.app')

@section('title', 'Order #' . str_pad($order->id, 5, '0', STR_PAD_LEFT))
@section('header-title', 'Order Details')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-white font-bold inline-flex items-center">
                Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                @php
                    $statusStyles = [
                        'pending' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                        'in_progress' => 'bg-sky-500/10 text-sky-500 border-sky-500/20',
                        'delivered' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                    ];
                    $badgeStyle = $statusStyles[$order->status] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/20';
                @endphp
                <span class="ml-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $badgeStyle }}">
                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                </span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">Submitted on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center px-4 py-2 border border-slate-700 rounded-lg shadow-sm text-sm font-medium text-slate-300 bg-slate-800 hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 focus:ring-offset-slate-900 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Back to Orders
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Main Details Column -->
        <div class="col-span-1 xl:col-span-2 space-y-8">
            <!-- Logistics Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-lg shadow-black/20 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-800 flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center mr-3">
                        <i class="fas fa-route text-indigo-500"></i>
                    </div>
                    <h2 class="text-lg font-bold text-white">Shipping Details</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 flex items-center">
                                <i class="fas fa-arrow-up text-emerald-500 mr-2"></i> Pickup Location
                            </p>
                            <p class="text-sm font-medium text-white pl-5">{{ $order->pickup_location }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 flex items-center">
                                <i class="fas fa-arrow-down text-rose-500 mr-2"></i> Delivery Location
                            </p>
                            <p class="text-sm font-medium text-white pl-5">{{ $order->delivery_location }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 flex items-center">
                                <i class="fas fa-box text-indigo-500 mr-2"></i> Cargo Size
                            </p>
                            <p class="text-sm font-medium text-white pl-6">{{ $order->cargo_size }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 flex items-center">
                                <i class="fas fa-weight-hanging text-amber-500 mr-2"></i> Cargo Weight
                            </p>
                            <p class="text-sm font-medium text-white pl-6">{{ $order->cargo_weight }} kg</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 flex items-center">
                                <i class="fas fa-calendar-plus text-sky-500 mr-2"></i> Scheduled Pickup
                            </p>
                            <p class="text-sm font-medium text-white pl-5">{{ $order->pickup_datetime->format('F d, Y') }} <span class="text-slate-500 ml-1">{{ $order->pickup_datetime->format('h:i A') }}</span></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 flex items-center">
                                <i class="fas fa-calendar-check text-emerald-500 mr-2"></i> Estimated Delivery
                            </p>
                            <p class="text-sm font-medium text-white pl-5">{{ $order->delivery_datetime->format('F d, Y') }} <span class="text-slate-500 ml-1">{{ $order->delivery_datetime->format('h:i A') }}</span></p>
                        </div>
                    </div>

                    @if($order->notes)
                    <div class="mt-8 pt-6 border-t border-slate-800">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2 flex items-center">
                            <i class="fas fa-sticky-note text-slate-500 mr-2"></i> Additional Notes
                        </p>
                        <div class="bg-slate-800/50 border border-slate-700/50 rounded-xl p-4">
                            <p class="text-sm text-slate-300 leading-relaxed">{{ $order->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Send Message -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-lg shadow-black/20 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-800 flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-pink-500/10 flex items-center justify-center mr-3">
                        <i class="fas fa-envelope text-pink-500"></i>
                    </div>
                    <h2 class="text-lg font-bold text-white">Send Message to Customer</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.orders.sendMessage', $order) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-400 mb-2">Subject</label>
                            <input type="text" name="subject" value="Order Update - #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}" 
                                class="w-full pl-3 pr-3 py-2.5 border border-slate-700 rounded-xl leading-5 bg-slate-800 text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors">
                        </div>
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-slate-400 mb-2">Message</label>
                            <textarea name="message" rows="4" placeholder="Write your message to the customer..."
                                class="w-full pl-3 pr-3 py-2.5 border border-slate-700 rounded-xl leading-5 bg-slate-800 text-slate-300 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors resize-y"></textarea>
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-slate-900 transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i> Send Email
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-span-1 space-y-8">
            <!-- Customer Info -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-lg shadow-black/20 overflow-hidden text-center p-6">
                <div class="mx-auto w-20 h-20 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-3xl shadow-lg mb-4 border-4 border-slate-800">
                    {{ substr($order->user->name, 0, 1) }}
                </div>
                <h3 class="text-xl font-bold text-white mb-1">{{ $order->user->name }}</h3>
                <p class="text-sm text-slate-400 mb-1 flex items-center justify-center gap-2">
                    <i class="fas fa-envelope text-slate-500"></i> {{ $order->user->email }}
                </p>
                @if($order->user->phone)
                <p class="text-sm text-slate-400 flex items-center justify-center gap-2">
                    <i class="fas fa-phone text-slate-500"></i> {{ $order->user->phone }}
                </p>
                @endif
            </div>

            <!-- Update Status -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-lg shadow-black/20 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-800 flex items-center">
                    <h3 class="text-lg font-bold text-white">Update Status</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-4 relative">
                            <select name="status" class="w-full pl-4 pr-10 py-3 bg-slate-800 border border-slate-700 text-slate-300 rounded-xl leading-5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors appearance-none font-semibold">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="in_progress" {{ $order->status === 'in_progress' ? 'selected' : '' }}>🚛 In Progress</option>
                                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>✅ Delivered</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                        <button type="submit" class="w-full flex justify-center items-center px-4 py-3 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-slate-900 transition-colors">
                            <i class="fas fa-sync-alt mr-2"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-lg shadow-black/20 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-800 flex items-center">
                    <h3 class="text-lg font-bold text-white">Timeline</h3>
                </div>
                <div class="p-6">
                    <div class="relative space-y-6">
                        <!-- Vertical line -->
                        <div class="absolute left-4 top-2 bottom-2 w-0.5 bg-slate-800"></div>

                        <!-- Created -->
                        <div class="relative flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center shrink-0 z-10 shadow-sm mt-0.5">
                                <i class="fas fa-plus text-[10px] text-emerald-500"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">Order Created</h4>
                                <p class="text-xs text-slate-400 mt-1">{{ $order->created_at->format('M d, Y') }} &bull; {{ $order->created_at->format('h:i A') }}</p>
                            </div>
                        </div>

                        <!-- In Progress -->
                        @if($order->status !== 'pending')
                        <div class="relative flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full bg-sky-500/20 border border-sky-500/30 flex items-center justify-center shrink-0 z-10 shadow-sm mt-0.5">
                                <i class="fas fa-truck text-[10px] text-sky-500"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">Status Updated to In Progress</h4>
                                <p class="text-xs text-slate-400 mt-1">{{ $order->updated_at->format('M d, Y') }} &bull; {{ $order->updated_at->format('h:i A') }}</p>
                            </div>
                        </div>
                        @else
                        <!-- Future state -->
                        <div class="relative flex items-start gap-4 opacity-40">
                            <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center shrink-0 z-10 mt-0.5">
                                <i class="fas fa-truck text-[10px] text-slate-500"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-300">In Progress</h4>
                                <p class="text-xs text-slate-500 mt-1">Pending update</p>
                            </div>
                        </div>
                        @endif

                        <!-- Delivered -->
                        @if($order->status === 'delivered')
                        <div class="relative flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center shrink-0 z-10 shadow-sm mt-0.5">
                                <i class="fas fa-check text-[10px] text-emerald-500"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">Order Delivered</h4>
                                <p class="text-xs text-slate-400 mt-1">{{ $order->updated_at->format('M d, Y') }} &bull; {{ $order->updated_at->format('h:i A') }}</p>
                            </div>
                        </div>
                        @else
                        <!-- Future state -->
                        <div class="relative flex items-start gap-4 opacity-40">
                            <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center shrink-0 z-10 mt-0.5">
                                <i class="fas fa-check text-[10px] text-slate-500"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-300">Delivered</h4>
                                <p class="text-xs text-slate-500 mt-1">Pending delivery</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
