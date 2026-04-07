<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::pending()->count(),
            'in_progress_orders' => Order::inProgress()->count(),
            'delivered_orders' => Order::delivered()->count(),
            'total_users' => User::where('role', 'user')->count(),
            'recent_orders' => Order::with('user')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
        ];

        return view('admin.dashboard', $stats);
    }
}
