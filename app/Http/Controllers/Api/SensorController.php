<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SensorReadingRequest;
use App\Models\Trip;
use App\Services\TripDelayService;
use Illuminate\Http\JsonResponse;

class SensorController extends Controller
{
    public function __construct(private TripDelayService $delays)
    {
    }

    public function reading(SensorReadingRequest $request, Trip $trip): JsonResponse
    {
        $data = $request->validated();

        $result = $this->delays->processSensorReading(
            $trip,
            (float) $data['distance_remaining_km'],
            (float) $data['current_speed_kmh'],
        );

        return response()->json([
            'trip_id' => $trip->id,
            'computed_eta' => $result['expected_arrival'],
            'delay_minutes' => $result['delay_minutes'],
            'notified' => $result['notified'],
            'passengers_notified' => $result['passengers_notified'],
        ], 202);
    }
}
