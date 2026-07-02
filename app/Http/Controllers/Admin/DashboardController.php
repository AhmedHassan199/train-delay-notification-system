<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\SmsMessage;
use App\Models\Train;
use App\Models\Trip;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'trains' => Train::count(),
            'trips' => Trip::count(),
            'delayed' => Trip::where('status', 'delayed')->count(),
            'bookings' => Booking::where('status', 'confirmed')->count(),
            'sms_sent' => SmsMessage::where('status', 'sent')->count(),
            'sms_failed' => SmsMessage::where('status', 'failed')->count(),
        ];

        $recentDelays = Trip::with('train')
            ->where('status', 'delayed')
            ->orderByDesc('last_reading_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentDelays'));
    }
}
