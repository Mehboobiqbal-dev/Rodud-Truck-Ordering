<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * List all orders for the authenticated user.
     */
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'orders' => $orders,
        ]);
    }

    /**
     * Create a new order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pickup_location' => 'required|string|max:500',
            'delivery_location' => 'required|string|max:500',
            'cargo_size' => 'required|string|max:100',
            'cargo_weight' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:1000',
            'pickup_datetime' => 'required|date|after:now',
            'delivery_datetime' => 'required|date|after:pickup_datetime',
        ]);

        $order = $request->user()->orders()->create([
            'pickup_location' => $request->pickup_location,
            'delivery_location' => $request->delivery_location,
            'cargo_size' => $request->cargo_size,
            'cargo_weight' => $request->cargo_weight,
            'notes' => $request->notes,
            'pickup_datetime' => $request->pickup_datetime,
            'delivery_datetime' => $request->delivery_datetime,
            'status' => 'pending',
        ]);

        // Notify all admins about the new order
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewOrderNotification($order));
        }

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order,
        ], 201);
    }

    /**
     * Show a specific order.
     */
    public function show(Request $request, Order $order)
    {
        // Ensure the user owns this order
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'order' => $order->load('user'),
        ]);
    }

    /**
     * Update an existing order (only if pending).
     */
    public function update(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Cannot update order that is already in progress or delivered'], 422);
        }

        $request->validate([
            'pickup_location' => 'sometimes|string|max:500',
            'delivery_location' => 'sometimes|string|max:500',
            'cargo_size' => 'sometimes|string|max:100',
            'cargo_weight' => 'sometimes|numeric|min:0.01',
            'notes' => 'nullable|string|max:1000',
            'pickup_datetime' => 'sometimes|date|after:now',
            'delivery_datetime' => 'sometimes|date|after:pickup_datetime',
        ]);

        $order->update($request->only([
            'pickup_location', 'delivery_location', 'cargo_size',
            'cargo_weight', 'notes', 'pickup_datetime', 'delivery_datetime',
        ]));

        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order->fresh(),
        ]);
    }

    /**
     * Delete an order (only if pending).
     */
    public function destroy(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Cannot delete order that is already in progress or delivered'], 422);
        }

        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully',
        ]);
    }
}
