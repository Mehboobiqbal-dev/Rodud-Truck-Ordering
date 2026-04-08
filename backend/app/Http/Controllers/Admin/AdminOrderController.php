<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\OrderStatusNotification;
use App\Notifications\UserMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by location or user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pickup_location', 'LIKE', "%{$search}%")
                  ->orWhere('delivery_location', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(15)->appends($request->query());

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,delivered',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        if ($oldStatus !== $newStatus) {
            $order->update(['status' => $newStatus]);
            $order->user->notify(new OrderStatusNotification($order, $oldStatus, $newStatus));
        }

        return redirect()->back()->with('success', 'Order status updated to ' . ucfirst(str_replace('_', ' ', $request->status)));
    }

    public function sendMessage(Request $request, Order $order)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $order->load('user');

        // Trigger in-app notification to the user
        $order->user->notify(new UserMessageNotification($order, $request->subject, $request->message));

        // Send email to the user
        Mail::raw($request->message, function ($mail) use ($order, $request) {
            $mail->to($order->user->email)
                 ->subject($request->subject);
        });

        return redirect()->back()->with('success', 'Message sent to ' . $order->user->name . ' successfully.');
    }
}

