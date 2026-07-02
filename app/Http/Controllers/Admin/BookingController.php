<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $tripId = $request->integer('trip_id') ?: null;

        $bookings = Booking::with(['user', 'trip.train'])
            ->when($tripId, fn ($q) => $q->where('trip_id', $tripId))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $trips = Trip::with('train')->orderBy('departure_time')->get();

        return view('admin.bookings.index', compact('bookings', 'trips', 'tripId'));
    }
}
