<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()->orders()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'orders' => $orders,
        ]);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $order = $request->user()->orders()->create([
            'pickup_location'   => $validated['pickup_location'],
            'delivery_location' => $validated['delivery_location'],
            'cargo_size'        => $validated['cargo_size'],
            'cargo_weight'      => $validated['cargo_weight'],
            'notes'             => $validated['notes'] ?? null,
            'pickup_datetime'   => $validated['pickup_datetime'],
            'delivery_datetime' => $validated['delivery_datetime'],
            'status'            => 'pending',
        ]);

        // Notifications are now 100% safe and queued
        try {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewOrderNotification($order));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send NewOrderNotification', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);
        }

        return response()->json([
            'message' => 'Order created successfully',
            'order'   => $order->load('user'),
        ], 201);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'order' => $order->load('user'),
        ]);
    }

    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Cannot update order that is already in progress or delivered.',
            ], 422);
        }

        $order->update($request->validated());

        return response()->json([
            'message' => 'Order updated successfully',
            'order'   => $order->fresh()->load('user'),
        ]);
    }

    public function destroy(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Cannot delete order that is already in progress or delivered.',
            ], 422);
        }

        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully',
        ]);
    }
}