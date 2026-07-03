<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\View\View;

class TripController extends Controller
{

    public function index(): View
    {
        $trips = Trip::with('train')
            ->whereIn('status', ['on_time', 'delayed'])
            ->orderBy('departure_time')
            ->paginate(10);

        return view('trips.index', compact('trips'));
    }
}
