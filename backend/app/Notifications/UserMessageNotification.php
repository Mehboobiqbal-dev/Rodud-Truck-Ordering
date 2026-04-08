<?php

namespace App\Notifications;


use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Message $message,
        protected string $subject,
        protected string $messageText
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'admin_message',
            'order_id' => $this->message->order?->id,
            'message_id' => $this->message->id,
            'subject' => $this->subject,
            'message' => $this->messageText,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
