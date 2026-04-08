<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Order;
use App\Models\User;
use App\Notifications\UserMessageNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Get all messages for the authenticated user (both sent and received).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Get messages for this user (admin messages sent to them and their support messages)
        $messages = Message::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'messages' => $messages,
        ]);
    }

    /**
     * Get unread message count for the authenticated user.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = Message::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'unread_count' => $count,
        ]);
    }

    /**
     * Mark a specific message as read.
     */
    public function markAsRead(Request $request, Message $message): JsonResponse
    {
        // Authorization: user can only mark their own messages as read
        if ($message->user_id !== $request->user()->id) {
            return response()->json([
                'error' => 'Unauthorized',
            ], 403);
        }

        $message->markAsRead();

        return response()->json([
            'message' => 'Message marked as read.',
        ]);
    }

    /**
     * Mark all messages as read for the authenticated user.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        Message::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => 'All messages marked as read.',
        ]);
    }
}
