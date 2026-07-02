<?php

namespace App\Notifications;

use App\Channels\SmsChannel;
use App\Models\Booking;
use App\Models\Trip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TripDelayedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [30, 120];

    public function __construct(
        public Trip $trip,
        public ?Booking $booking = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return [SmsChannel::class];
    }

    public function toSms(object $notifiable): array
    {
        $trip = $this->trip->loadMissing('train');
        $code = $trip->train->code ?? 'Train';
        $newDeparture = optional($trip->effectiveDeparture())->format('D d M, H:i');
        $newArrival = optional($trip->effectiveArrival())->format('H:i');
        $ref = $this->booking?->reference;

        $body = "Train {$code} to {$trip->destination} is DELAYED by {$trip->delay_minutes} min. "
            ."New departure: {$newDeparture} (arrives ~{$newArrival})."
            .($ref ? " Booking {$ref}." : '')
            .' Please arrive at the new time.';

        return [
            'body' => $body,
            'trip_id' => $trip->id,
            'booking_id' => $this->booking?->id,
            'user_id' => $notifiable->id ?? null,
        ];
    }
}
