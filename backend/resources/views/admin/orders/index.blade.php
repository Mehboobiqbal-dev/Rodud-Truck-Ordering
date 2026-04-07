@extends('admin.layouts.app')

@section('title', 'Orders')
@section('header-title', 'Order Management')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-white font-bold">Orders <span class="text-slate-400 font-medium">({{ $orders->total() ?? 0 }})</span></h1>
            <p class="text-sm text-slate-400 mt-1">Manage and track all truck shipping requests</p>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-lg shadow-black/20 p-5 mb-8">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-col sm:flex-row gap-4 items-end">
            <!-- Search -->
            <div class="w-full sm:w-1/2 lg:w-1/3">
                <label for="search" class="block text-sm font-medium text-slate-400 mb-2">Search Orders</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-500"></i>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="block w-full pl-10 pr-3 py-2.5 border border-slate-700 rounded-xl leading-5 bg-slate-800 text-slate-300 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors"
                        placeholder="Search by location, name, or email...">
                </div>
            </div>

            <!-- Status Filter -->
            <div class="w-full sm:w-1/4">
                <label for="status" class="block text-sm font-medium text-slate-400 mb-2">Filter by Status</label>
                <div class="relative">
                    <select name="status" id="status" onchange="this.form.submit()"
                        class="block w-full pl-3 pr-10 py-2.5 bg-slate-800 border border-slate-700 text-slate-300 rounded-xl leading-5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors appearance-none">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>🚛 In Progress</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>✅ Delivered</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 ml-auto w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-none justify-center inline-flex items-center px-4 py-2.5 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-slate-900 transition-colors">
                    Filter
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.orders.index') }}" class="flex-1 sm:flex-none justify-center inline-flex items-center px-4 py-2.5 border border-slate-700 rounded-xl shadow-sm text-sm font-medium text-slate-300 bg-slate-800 hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 focus:ring-offset-slate-900 transition-colors">
                        <i class="fas fa-times mr-2"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-lg shadow-black/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800 bg-slate-900/50">Order Details</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800 bg-slate-900/50">Logistics</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800 bg-slate-900/50">Dates</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800 bg-slate-900/50">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800 bg-slate-900/50 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50 text-sm">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-800/25 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full shrink-0 bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold shadow-md">
                                    {{ substr($order->user->name, 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-semibold text-white">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</p>
                                    <p class="text-sm font-medium text-slate-300">{{ $order->user->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $order->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col space-y-1 mb-2">
                                <div class="flex items-start text-xs text-slate-300">
                                    <i class="fas fa-arrow-up text-emerald-500 mr-2 mt-0.5 w-3 text-center shrink-0"></i>
                                    <span class="truncate max-w-[200px]" title="{{ $order->pickup_location }}">{{ Str::limit($order->pickup_location, 30) }}</span>
                                </div>
                                <div class="flex items-start text-xs text-slate-300">
                                    <i class="fas fa-arrow-down text-rose-500 mr-2 mt-0.5 w-3 text-center shrink-0"></i>
                                    <span class="truncate max-w-[200px]" title="{{ $order->delivery_location }}">{{ Str::limit($order->delivery_location, 30) }}</span>
                                </div>
                            </div>
                            <div class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-800 text-slate-300 border border-slate-700">
                                <i class="fas fa-box text-slate-400 mr-1.5"></i> {{ $order->cargo_size }} &bull; {{ $order->cargo_weight }} kg
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-xs text-slate-300">
                                <span class="text-slate-500 w-12 inline-block">Pickup:</span> {{ $order->pickup_datetime->format('M d, Y H:i') }}
                            </div>
                            <div class="text-xs text-slate-300 mt-1">
                                <span class="text-slate-500 w-12 inline-block">Deliver:</span> {{ $order->delivery_datetime->format('M d, Y H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="w-full max-w-[150px]">
                                @csrf
                                @method('PATCH')
                                <div class="relative">
                                    @php
                                        $statusStyles = [
                                            'pending' => 'text-amber-500 border-amber-500/30 bg-amber-500/10 focus:ring-amber-500',
                                            'in_progress' => 'text-sky-500 border-sky-500/30 bg-sky-500/10 focus:ring-sky-500',
                                            'delivered' => 'text-emerald-500 border-emerald-500/30 bg-emerald-500/10 focus:ring-emerald-500',
                                        ];
                                        $style = $statusStyles[$order->status] ?? 'text-slate-400 border-slate-700 bg-slate-800 focus:ring-slate-500';
                                    @endphp
                                    <select name="status" onchange="this.form.submit()"
                                        class="block w-full pl-3 pr-8 py-1.5 text-xs font-semibold rounded-full border leading-5 focus:outline-none focus:ring-2 transition-colors appearance-none cursor-pointer {{ $style }}">
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ $order->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
                                        <i class="fas fa-chevron-down text-[10px]"></i>
                                    </div>
                                </div>
                            </form>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center px-3 py-1.5 border border-slate-700 rounded-lg shadow-sm text-xs font-medium text-white bg-slate-800 hover:bg-indigo-600 hover:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-slate-900 transition-colors">
                                <i class="fas fa-eye mr-2 text-[10px]"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 rounded-full bg-slate-800/50 flex items-center justify-center mx-auto mb-4 border border-slate-700/50">
                                <i class="fas fa-box-open text-2xl text-slate-500"></i>
                            </div>
                            <h3 class="text-lg font-medium text-white mb-1">No orders found</h3>
                            <p class="text-sm text-slate-400">There are no orders matching your current filters.</p>
                            @if(request('search') || request('status'))
                                <a href="{{ route('admin.orders.index') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-slate-700 rounded-lg text-sm font-medium text-slate-300 bg-slate-800 hover:bg-slate-700 transition-colors">
                                    Clear Filters
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if(method_exists($orders, 'hasPages') && $orders->hasPages())
        <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/80 mt-auto">
            {{ $orders->links('pagination::tailwind') }}
        </div>
        @endif
    </div>
</div>

<style>
/* For overriding Laravel's default Tailwind pagination colors if needed */
nav[role="navigation"] button, 
nav[role="navigation"] a, 
nav[role="navigation"] span {
    background-color: #1e293b !important;
    border-color: #334155 !important;
    color: #94a3b8 !important;
}
nav[role="navigation"] [aria-current="page"] span {
    background-color: #4f46e5 !important;
    border-color: #4f46e5 !important;
    color: white !important;
}
nav[role="navigation"] a:hover {
    background-color: #334155 !important;
    color: white !important;
}
</style>
@endsection
