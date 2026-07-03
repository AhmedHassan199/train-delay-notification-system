<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReportDelayRequest;
use App\Http\Requests\Admin\TripRequest;
use App\Models\Train;
use App\Models\Trip;
use App\Services\TripDelayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TripController extends Controller
{
    public function index(): View
    {
        $trips = Trip::with('train')
            ->withCount(['bookings as confirmed_bookings_count' => fn ($q) => $q->where('status', 'confirmed')])
            ->orderBy('departure_time')
            ->paginate(15);

        return view('admin.trips.index', compact('trips'));
    }

    public function create(): View
    {
        return view('admin.trips.create', [
            'trip' => new Trip(['status' => 'on_time']),
            'trains' => Train::orderBy('code')->get(),
        ]);
    }

    public function store(TripRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $train = Train::findOrFail($data['train_id']);

        $data['available_seats'] = $train->total_seats;
        Trip::create($data);

        return redirect()->route('admin.trips.index')->with('status', 'Trip created.');
    }

    public function show(Trip $trip): View
    {
        $trip->load('train');
        $events = $trip->delayEvents()->latest('reported_at')->paginate(15);
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

    public function update(TripRequest $request, Trip $trip): RedirectResponse
    {
        $trip->update($request->validated());

        return redirect()->route('admin.trips.index')->with('status', 'Trip updated.');
    }

    public function destroy(Trip $trip): RedirectResponse
    {
        $trip->delete();

        return redirect()->route('admin.trips.index')->with('status', 'Trip deleted.');
    }

    public function delay(ReportDelayRequest $request, Trip $trip, TripDelayService $delays): RedirectResponse
    {
        $notified = $delays->applyDelay($trip, (int) $request->validated()['delay_minutes'], 'admin');

        return redirect()->route('admin.trips.show', $trip)
            ->with('status', "Delay applied. Queued SMS for {$notified} passenger(s).");
    }
}
