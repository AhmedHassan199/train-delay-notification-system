<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Trip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{

    public function index(Request $request): View
    {
        $bookings = $request->user()->bookings()
            ->with('trip.train')
            ->latest()
            ->get();

        return view('bookings.index', compact('bookings'));
    }

    public function store(Request $request, Trip $trip): RedirectResponse
    {
        $data = $request->validate([
            'seats' => ['required', 'integer', 'min:1', 'max:6'],
        ]);

        try {
            DB::transaction(function () use ($request, $trip, $data) {

                $locked = Trip::whereKey($trip->id)->lockForUpdate()->first();

                if ($locked->available_seats < $data['seats']) {
                    abort(422, 'Not enough seats available.');
                }

                $locked->decrement('available_seats', $data['seats']);

                Booking::create([
                    'user_id' => $request->user()->id,
                    'trip_id' => $locked->id,
                    'reference' => Booking::generateReference(),
                    'seats' => $data['seats'],
                    'status' => 'confirmed',
                ]);
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Sorry, we could not complete your booking (not enough seats).');
        }

        return redirect()->route('bookings.index')
            ->with('status', 'Booking confirmed! We will SMS you if this train is delayed.');
    }
}
