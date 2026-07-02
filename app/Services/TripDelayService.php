<?php

namespace App\Services;

use App\Jobs\NotifyTripPassengers;
use App\Models\DelayEvent;
use App\Models\Trip;
use Carbon\Carbon;

class TripDelayService
{

    public function processSensorReading(Trip $trip, float $distanceKm, float $speedKmh): array
    {
        $now = Carbon::now();
        $expectedArrival = null;
        $delayMinutes = 0;

        if ($speedKmh > 0) {
            $etaMinutes = ($distanceKm / $speedKmh) * 60;
            $expectedArrival = $now->copy()->addMinutes($etaMinutes);

            $delayMinutes = (int) max(0, round(
                $trip->arrival_time->diffInMinutes($expectedArrival, false)
            ));
        }

        $notified = false;

        if ($speedKmh > 0) {

            $trip->last_distance_km = $distanceKm;
            $trip->last_speed_kmh = $speedKmh;
            $trip->last_reading_at = $now;
            $trip->expected_arrival_time = $expectedArrival;
            $trip->expected_departure_time = $trip->departure_time->copy()->addMinutes($delayMinutes);

            if ($delayMinutes > 0 && $this->shouldNotify($trip, $delayMinutes)) {
                $trip->status = 'delayed';
                $trip->delay_minutes = $delayMinutes;
                $trip->last_notified_delay_minutes = $delayMinutes;
                $trip->save();

                $notified = true;
                $passengers = $this->notifyPassengers($trip);
            } else {

                if ($delayMinutes === 0 && $trip->status === 'delayed') {
                    $trip->status = 'on_time';
                    $trip->delay_minutes = 0;
                    $trip->last_notified_delay_minutes = 0;
                }
                $trip->save();
                $passengers = 0;
            }
        } else {
            $trip->last_distance_km = $distanceKm;
            $trip->last_speed_kmh = $speedKmh;
            $trip->last_reading_at = $now;
            $trip->save();
            $passengers = 0;
        }

        $this->recordEvent($trip, [
            'distance_km' => $distanceKm,
            'speed_kmh' => $speedKmh,
            'computed_eta' => $expectedArrival,
            'delay_minutes' => $delayMinutes,
            'notified' => $notified,
            'source' => 'sensor',
            'payload' => ['distance_remaining_km' => $distanceKm, 'current_speed_kmh' => $speedKmh],
        ]);

        return [
            'delay_minutes' => $delayMinutes,
            'expected_arrival' => $expectedArrival?->toIso8601String(),
            'notified' => $notified,
            'passengers_notified' => $passengers,
        ];
    }

    public function applyDelay(Trip $trip, int $delayMinutes, string $source = 'admin'): int
    {
        $delayMinutes = max(0, $delayMinutes);

        $trip->status = $delayMinutes > 0 ? 'delayed' : 'on_time';
        $trip->delay_minutes = $delayMinutes;
        $trip->expected_departure_time = $trip->departure_time->copy()->addMinutes($delayMinutes);
        $trip->expected_arrival_time = $trip->arrival_time->copy()->addMinutes($delayMinutes);
        $trip->last_notified_delay_minutes = $delayMinutes;
        $trip->save();

        $passengers = $delayMinutes > 0 ? $this->notifyPassengers($trip) : 0;

        $this->recordEvent($trip, [
            'distance_km' => null,
            'speed_kmh' => null,
            'computed_eta' => $trip->expected_arrival_time,
            'delay_minutes' => $delayMinutes,
            'notified' => $passengers > 0,
            'source' => $source,
            'payload' => ['manual_delay_minutes' => $delayMinutes],
        ]);

        return $passengers;
    }

    private function shouldNotify(Trip $trip, int $delayMinutes): bool
    {
        if ($trip->status !== 'delayed') {
            return true;
        }

        $step = (int) config('services.sms.delay_step_minutes', 10);

        return ($delayMinutes - $trip->last_notified_delay_minutes) >= $step;
    }

    private function notifyPassengers(Trip $trip): int
    {
        $targeted = $trip->confirmedBookings()->count();

        if ($targeted > 0) {
            NotifyTripPassengers::dispatch($trip);
        }

        return $targeted;
    }

    private function recordEvent(Trip $trip, array $attributes): void
    {
        DelayEvent::create($attributes + [
            'trip_id' => $trip->id,
            'reported_at' => Carbon::now(),
        ]);
    }
}
