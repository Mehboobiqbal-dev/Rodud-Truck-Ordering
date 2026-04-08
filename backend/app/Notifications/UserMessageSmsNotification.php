<?php

namespace App\Notifications;

use App\Models\User;
use App\Notifications\Channels\TwilioChannel;
use App\Notifications\Messages\TwilioMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserMessageSmsNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected User $user,
        protected string $subject,
        protected string $messageText
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        if (config('services.twilio.auth_sid') && $notifiable->phone) {
            $channels[] = TwilioChannel::class;
        }
        
        return $channels;
    }

    public function toTwilio(object $notifiable): ?TwilioMessage
    {
        return new TwilioMessage(
            "New admin message: {$this->subject}. "
            . substr($this->messageText, 0, 80) . (strlen($this->messageText) > 80 ? '...' : '')
        );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'admin_message',
            'subject' => $this->subject,
            'message' => $this->messageText,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
