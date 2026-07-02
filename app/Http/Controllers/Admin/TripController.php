<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Train;
use App\Models\Trip;
use App\Services\TripDelayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TripController extends Controller
{
    public function index(): View
    {
        $trips = Trip::with('train')
            ->withCount(['bookings as confirmed_bookings_count' => fn ($q) => $q->where('status', 'confirmed')])
            ->orderBy('departure_time')
            ->get();

        return view('admin.trips.index', compact('trips'));
    }

    public function create(): View
    {
        return view('admin.trips.create', [
            'trip' => new Trip(['status' => 'on_time']),
            'trains' => Train::orderBy('code')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $train = Train::findOrFail($data['train_id']);

        $data['available_seats'] = $train->total_seats;
        Trip::create($data);

        return redirect()->route('admin.trips.index')->with('status', 'Trip created.');
    }

    public function show(Trip $trip): View
    {
        $trip->load('train');
        $events = $trip->delayEvents()->latest('reported_at')->limit(20)->get();
        $bookingsCount = $trip->confirmedBookings()->count();

        return view('admin.trips.show', compact('trip', 'events', 'bookingsCount'));
    }

    public function edit(Trip $trip): View
    {
        return view('admin.trips.edit', [
            'trip' => $trip,
            'trains' => Train::orderBy('code')->get(),
        ]);
    }

    public function update(Request $request, Trip $trip): RedirectResponse
    {
        $trip->update($this->validated($request));

        return redirect()->route('admin.trips.index')->with('status', 'Trip updated.');
    }

    public function destroy(Trip $trip): RedirectResponse
    {
        $trip->delete();

        return redirect()->route('admin.trips.index')->with('status', 'Trip deleted.');
    }

    public function delay(Request $request, Trip $trip, TripDelayService $delays): RedirectResponse
    {
        $data = $request->validate([
            'delay_minutes' => ['required', 'integer', 'min:0', 'max:1440'],
        ]);

        $notified = $delays->applyDelay($trip, (int) $data['delay_minutes'], 'admin');

        return redirect()->route('admin.trips.show', $trip)
            ->with('status', "Delay applied. Queued SMS for {$notified} passenger(s).");
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'train_id' => ['required', 'exists:trains,id'],
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'departure_time' => ['required', 'date'],
            'arrival_time' => ['required', 'date', 'after:departure_time'],
            'route_distance_km' => ['nullable', 'numeric', 'min:0'],
        ]);
    }
}
