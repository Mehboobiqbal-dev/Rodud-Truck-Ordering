<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Order;
use App\Models\User;
use App\Notifications\UserMessageNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminMessageController extends Controller
{
    /**
     * Send a message from admin to user (related to an order).
     */
    public function sendToUser(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'order_id' => 'nullable|exists:orders,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $user = User::findOrFail($request->user_id);
        $order = $request->order_id ? Order::findOrFail($request->order_id) : null;

        // Store the message in the database
        $message = Message::create([
            'user_id' => $user->id,
            'order_id' => $order?->id,
            'sender_type' => 'admin',
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        // Send in-app notification
        $user->notify(new UserMessageNotification(
            $message,
            $request->subject,
            $request->message
        ));

        // Optionally send email
        if ($request->boolean('send_email', true)) {
            Mail::raw($request->message, function ($mail) use ($user, $request) {
                $mail->to($user->email)
                     ->subject($request->subject);
            });
        }

        return response()->json([
            'message' => 'Message sent to ' . $user->name . ' successfully.',
            'data' => $message,
        ], 201);
    }

    /**
     * Get all messages sent to a specific user (for admin panel).
     */
    public function getUserMessages(Request $request, User $user): JsonResponse
    {
        $messages = Message::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'messages' => $messages,
        ]);
    }

    /**
     * Get all support messages received from users.
     */
    public function getSupportMessages(Request $request): JsonResponse
    {
        $messages = Message::where('sender_type', 'user')
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->get();

        return response()->json([
            'messages' => $messages,
        ]);
    }
}
