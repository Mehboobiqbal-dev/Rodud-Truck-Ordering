<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Twilio\Rest\Client;

class TwilioChannel
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.auth_sid'),
            config('services.twilio.auth_token')
        );
    }

    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toTwilio($notifiable);

        if (is_null($message)) {
            return;
        }

        if (!$notifiable->phone_number) {
            return;
        }

        $this->client->messages->create(
            $notifiable->phone_number,
            [
                'from' => config('services.twilio.phone_number'),
                'body' => $message->content,
            ]
        );
    }
}
