<?php

namespace App\Channels;

use App\Models\SmsMessage;
use App\Sms\Exceptions\AllSmsProvidersFailedException;
use App\Sms\SmsGateway;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function __construct(private SmsGateway $gateway)
    {
    }

    public function send(object $notifiable, Notification $notification): void
    {

        $payload = $notification->toSms($notifiable);
        $body = is_array($payload) ? ($payload['body'] ?? '') : (string) $payload;
        $meta = is_array($payload) ? $payload : [];

        $to = $notifiable->routeNotificationFor('sms', $notification)
            ?? ($notifiable->phone ?? null);

        if (! $to) {
            return;
        }

        $base = [
            'to' => $to,
            'body' => $body,
            'user_id' => $meta['user_id'] ?? ($notifiable->id ?? null),
            'trip_id' => $meta['trip_id'] ?? null,
            'booking_id' => $meta['booking_id'] ?? null,
        ];

        try {
            $result = $this->gateway->send($to, $body);

            SmsMessage::create($base + [
                'provider' => $result->provider,
                'attempts' => $result->attempts,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (AllSmsProvidersFailedException $e) {

            SmsMessage::create($base + [
                'provider' => null,
                'attempts' => $e->attempts,
                'status' => 'failed',
                'error' => $e->getPrevious()?->getMessage() ?? $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
