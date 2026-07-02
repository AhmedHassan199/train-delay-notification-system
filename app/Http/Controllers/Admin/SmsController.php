<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsMessage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmsController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString() ?: null;

        $messages = SmsMessage::with(['trip.train', 'user'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(40)
            ->withQueryString();

        return view('admin.sms.index', compact('messages', 'status'));
    }
}
