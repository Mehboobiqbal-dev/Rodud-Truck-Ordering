@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('header-title', 'Overview')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Welcome banner -->
    <div class="relative bg-slate-900 border border-slate-800 p-6 rounded-2xl sm:p-10 mb-8 overflow-hidden shadow-2xl">
        <div class="absolute right-0 top-0 -mt-4 mr-16 pointer-events-none hidden xl:block" aria-hidden="true">
            <svg width="319" height="198" xmlnsXlink="http://www.w3.org/1999/xlink">
                <defs>
                    <path id="welcome-a" d="M64 0l64 128-64-20-64 20z" />
                    <path id="welcome-e" d="M40 0l40 80-40-12.5L0 80z" />
                    <path id="welcome-g" d="M40 0l40 80-40-12.5L0 80z" />
                    <linearGradient x1="50%" y1="0%" x2="50%" y2="100%" id="welcome-b">
                        <stop stop-color="#4f46e5" offset="0%" />
                        <stop stop-color="#3b82f6" offset="100%" />
                    </linearGradient>
                </defs>
                <g fill="none" fill-rule="evenodd">
                    <g transform="rotate(64 36.592 105.604)">
                        <mask id="welcome-d" fill="#fff"><use href="#welcome-a" /></mask>
                        <use fill="url(#welcome-b)" href="#welcome-a" />
                        <path fill="url(#welcome-c)" mask="url(#welcome-d)" d="M64-24h80v152H64z" />
                    </g>
                    <g transform="rotate(-51 91.324 -105.372)">
                        <mask id="welcome-f" fill="#fff"><use href="#welcome-e" /></mask>
                        <use fill="url(#welcome-b)" href="#welcome-e" />
                        <path fill="url(#welcome-c)" mask="url(#welcome-f)" d="M40.333-15.147h50v95h-50z" />
                    </g>
                    <g transform="rotate(44 61.546 392.623)">
                        <mask id="welcome-h" fill="#fff"><use href="#welcome-g" /></mask>
                        <use fill="url(#welcome-b)" href="#welcome-g" />
                        <path fill="url(#welcome-c)" mask="url(#welcome-h)" d="M40.333-15.147h50v95h-50z" />
                    </g>
                </g>
            </svg>
        </div>
        <div class="relative">
            <h1 class="text-2xl md:text-3xl text-white font-bold mb-1">Good afternoon, Admin. 👋</h1>
            <p class="text-slate-400">Here is what's happening with your logistics today:</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Orders -->
        <div class="flex flex-col bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg shadow-black/20 hover:border-indigo-500/50 transition-colors duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-slate-400 text-sm font-semibold uppercase tracking-wider">Total Orders</h3>
                <div class="p-2 bg-indigo-500/10 rounded-lg">
                    <i class="fas fa-boxes-stacked text-indigo-500 text-lg"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-white mb-2">{{ $total_orders }}</div>
            <div class="flex items-center text-sm">
                <span class="text-emerald-500 font-medium">All time volume</span>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="flex flex-col bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg shadow-black/20 hover:border-amber-500/50 transition-colors duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-slate-400 text-sm font-semibold uppercase tracking-wider">Pending</h3>
                <div class="p-2 bg-amber-500/10 rounded-lg">
                    <i class="fas fa-clock text-amber-500 text-lg"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-white mb-2">{{ $pending_orders }}</div>
            <div class="flex items-center text-sm">
                <span class="text-slate-400 font-medium">Awaiting processing</span>
            </div>
        </div>

        <!-- In Progress -->
        <div class="flex flex-col bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg shadow-black/20 hover:border-sky-500/50 transition-colors duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-slate-400 text-sm font-semibold uppercase tracking-wider">In Transit</h3>
                <div class="p-2 bg-sky-500/10 rounded-lg">
                    <i class="fas fa-truck-moving text-sky-500 text-lg"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-white mb-2">{{ $in_progress_orders }}</div>
            <div class="flex items-center text-sm">
                <span class="text-sky-500 font-medium">Currently active</span>
            </div>
        </div>

        <!-- Delivered -->
        <div class="flex flex-col bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg shadow-black/20 hover:border-emerald-500/50 transition-colors duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-slate-400 text-sm font-semibold uppercase tracking-wider">Delivered</h3>
                <div class="p-2 bg-emerald-500/10 rounded-lg">
                    <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-white mb-2">{{ $delivered_orders }}</div>
            <div class="flex items-center text-sm">
                <span class="text-emerald-500 font-medium">Successfully completed</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Recent Orders Table -->
        <div class="col-span-1 xl:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl shadow-lg shadow-black/20">
            <div class="px-6 py-5 border-b border-slate-800 flex items-center justify-between">
                <h2 class="text-lg font-bold text-white font-sans">Recent Orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-indigo-500 hover:text-indigo-400 transition-colors">View All &rarr;</a>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800 bg-slate-900/50">ID</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800 bg-slate-900/50">Customer</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800 bg-slate-900/50">Locations</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800 bg-slate-900/50">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800 bg-slate-900/50 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50 text-sm">
                        @forelse($recent_orders as $order)
                        <tr class="hover:bg-slate-800/25 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-white">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-9 h-9 rounded-full shrink-0 bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold shadow-md">
                                        {{ substr($order->user->name, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-white">{{ $order->user->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $order->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col space-y-1">
                                    <div class="flex items-center text-xs text-slate-300">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500 mr-2 shrink-0"></div>
                                        <span class="truncate max-w-[150px]">{{ $order->pickup_location }}</span>
                                    </div>
                                    <div class="w-0.5 h-3 bg-slate-700 ml-[3px]"></div>
                                    <div class="flex items-center text-xs text-slate-300">
                                        <div class="w-2 h-2 rounded-full bg-rose-500 mr-2 shrink-0"></div>
                                        <span class="truncate max-w-[150px]">{{ $order->delivery_location }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                        'in_progress' => 'bg-sky-500/10 text-sky-500 border-sky-500/20',
                                        'delivered' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                                    ];
                                    $colorClass = $statusColors[$order->status] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/20';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $colorClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center justify-center w-8 h-8 rounded bg-slate-800 text-slate-400 hover:text-white hover:bg-indigo-600 transition-colors shadow-sm">
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <i class="fas fa-inbox text-4xl mb-4 text-slate-700"></i>
                                <p class="text-sm font-medium">No recent orders found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions Sidebar -->
        <div class="col-span-1 space-y-8">
            <div class="bg-gradient-to-br from-indigo-900/50 to-slate-900 border border-indigo-500/20 rounded-2xl p-6 shadow-lg">
                <h3 class="text-lg font-bold text-white mb-2">Registered Users</h3>
                <div class="flex items-end mb-4">
                    <span class="text-5xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-cyan-400">{{ $total_users }}</span>
                    <span class="text-slate-400 ml-2 mb-1">active accounts</span>
                </div>
                <a href="{{ route('admin.users.index') }}" class="mt-4 w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition">
                    Manage Users
                </a>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg shadow-black/20">
                <h3 class="text-lg font-bold text-white mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.orders.index') }}?status=pending" class="flex items-center p-3 text-sm font-medium rounded-xl bg-slate-800 text-amber-500 hover:bg-amber-500/10 border border-transparent hover:border-amber-500/20 transition-all border">
                        <div class="mr-3 p-1.5 rounded-lg bg-slate-900/50">
                            <i class="fas fa-clock w-4 text-center"></i>
                        </div>
                        Review Pending Orders
                        <span class="ml-auto bg-amber-500 text-white text-xs py-0.5 px-2 rounded-full">{{ $pending_orders }}</span>
                    </a>
                    <a href="{{ route('admin.orders.index') }}?status=in_progress" class="flex items-center p-3 text-sm font-medium rounded-xl bg-slate-800 text-sky-500 hover:bg-sky-500/10 border border-transparent hover:border-sky-500/20 transition-all border">
                        <div class="mr-3 p-1.5 rounded-lg bg-slate-900/50">
                            <i class="fas fa-truck w-4 text-center"></i>
                        </div>
                        Track Active Shipments
                        <span class="ml-auto bg-sky-500 text-white text-xs py-0.5 px-2 rounded-full">{{ $in_progress_orders }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
