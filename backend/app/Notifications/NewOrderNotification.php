<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Channels\TwilioChannel;
use App\Notifications\Messages\TwilioMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;   // ← REQUIRED
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue  // ← FIXED
{
    use Queueable;

    public function __construct(
        protected Order $order
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];

        if (config('services.twilio.auth_sid') && $notifiable->phone) {
            $channels[] = TwilioChannel::class;
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Truck Order #' . $this->order->id)
            ->greeting('Hello Admin!')
            ->line('A new truck order has been submitted.')
            ->line('**Order Details:**')
            ->line('Pickup: ' . $this->order->pickup_location)
            ->line('Delivery: ' . $this->order->delivery_location)
            ->line('Cargo: ' . $this->order->cargo_size . ' (' . $this->order->cargo_weight . ' kg)')
            ->line('Pickup Date: ' . $this->order->pickup_datetime->format('M d, Y H:i'))
            ->action('View Order', url('/admin/orders/' . $this->order->id))
            ->line('Please review and process this order.');
    }

    public function toTwilio(object $notifiable): ?TwilioMessage
    {
        return new TwilioMessage(
            "New Order #{$this->order->id}: {$this->order->pickup_location} → {$this->order->delivery_location}. "
            . "Cargo: {$this->order->cargo_size} ({$this->order->cargo_weight}kg). "
            . "Reply ACCEPT or DECLINE."
        );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'user_name' => $this->order->user->name,
            'pickup_location' => $this->order->pickup_location,
            'delivery_location' => $this->order->delivery_location,
            'status' => $this->order->status,
        ];
    }
}