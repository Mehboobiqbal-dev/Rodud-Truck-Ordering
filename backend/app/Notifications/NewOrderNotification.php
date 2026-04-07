<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Order $order
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
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
