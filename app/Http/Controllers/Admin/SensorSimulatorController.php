<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Services\TripDelayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SensorSimulatorController extends Controller
{
    public function index(): View
    {
        $trips = Trip::with('train')->orderBy('departure_time')->get();

        return view('admin.simulator.index', [
            'trips' => $trips,
            'result' => session('sim_result'),
        ]);
    }

    public function send(Request $request, TripDelayService $delays): RedirectResponse
    {
        $data = $request->validate([
            'trip_id' => ['required', 'exists:trips,id'],
            'distance_remaining_km' => ['required', 'numeric', 'min:0'],
            'current_speed_kmh' => ['required', 'numeric', 'min:0'],
        ]);

        $trip = Trip::with('train')->findOrFail($data['trip_id']);

        $out = $delays->processSensorReading(
            $trip,
            (float) $data['distance_remaining_km'],
            (float) $data['current_speed_kmh'],
        );

        $trip->refresh();
        $result = [
            'trip' => $trip->train->code.' · '.$trip->origin.' → '.$trip->destination,
            'trip_id' => $trip->id,
            'distance' => $data['distance_remaining_km'],
            'speed' => $data['current_speed_kmh'],
            'scheduled_arrival' => $trip->arrival_time->format('D d M, H:i'),
            'computed_eta' => $out['expected_arrival'] ? \Illuminate\Support\Carbon::parse($out['expected_arrival'])->format('D d M, H:i') : null,
            'delay_minutes' => $out['delay_minutes'],
            'notified' => $out['notified'],
            'passengers_notified' => $out['passengers_notified'],
            'status' => $trip->status,
        ];

        return redirect()
            ->route('admin.simulator.index')
            ->with('sim_result', $result)
            ->with('sim_trip_id', $trip->id);
    }
}
