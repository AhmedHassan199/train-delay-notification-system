<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Services\TripDelayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function __construct(private TripDelayService $delays)
    {
    }

    public function reading(Request $request, Trip $trip): JsonResponse
    {
        $data = $request->validate([
            'distance_remaining_km' => ['required', 'numeric', 'min:0'],
            'current_speed_kmh' => ['required', 'numeric', 'min:0'],
        ]);

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
