<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Notifications\UserMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminNotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(15);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back();
    }

    // Messages management
    public function messages(Request $request)
    {
        $query = Message::where('sender_type', 'user')
            ->with(['user', 'order'])
            ->orderBy('created_at', 'desc');

        // Filter by read status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->whereNull('read_at');
            } elseif ($request->status === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        // Search by user name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $messages = $query->paginate(15)->appends($request->query());

        return view('admin.messages.index', compact('messages'));
    }

    public function markMessageRead(Request $request, Message $message)
    {
        if (!$message->read_at) {
            $message->update(['read_at' => now()]);
        }

        return redirect()->back()->with('success', 'Message marked as read.');
    }

    public function reply(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:messages,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $originalMessage = Message::findOrFail($request->message_id);

        // Store the reply in the database
        $reply = Message::create([
            'user_id' => $originalMessage->user_id,
            'order_id' => $originalMessage->order_id,
            'sender_type' => 'admin',
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        // Send in-app notification to user
        $originalMessage->user->notify(new UserMessageNotification(
            $originalMessage->order ?? $originalMessage->user,
            $request->subject,
            $request->message
        ));

        // Send email to user
        Mail::raw($request->message, function ($mail) use ($originalMessage, $request) {
            $mail->to($originalMessage->user->email)
                 ->subject($request->subject);
        });

        return redirect()->back()->with('success', 'Reply sent successfully to ' . $originalMessage->user->name);
    }
}

