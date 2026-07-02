<?php

namespace App\Jobs;

use App\Models\Trip;
use App\Notifications\TripDelayedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyTripPassengers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $chunkSize = 500;

    public function __construct(public Trip $trip)
    {
    }

    public function handle(): void
    {
        $this->trip->confirmedBookings()
            ->with('user')
            ->chunkById($this->chunkSize, function ($bookings) {
                foreach ($bookings as $booking) {
                    $user = $booking->user;
                    if (! $user || ! $user->phone) {
                        continue;
                    }

                    $user->notify(new TripDelayedNotification($this->trip, $booking));
                }
            });
    }
}
