<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Notifications\SupportMessageNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    /**
     * Submit a support message from the user.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $user = $request->user();

        // Store the message in the database
        $supportMessage = Message::create([
            'user_id' => $user->id,
            'sender_type' => 'user',
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        // Notify all admins about the support message
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new SupportMessageNotification($user, $request->subject, $request->message));
        }

        return response()->json([
            'message' => 'Support request submitted successfully.',
            'data' => $supportMessage,
        ], 201);
    }
}

