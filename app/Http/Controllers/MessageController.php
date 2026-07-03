<?php

namespace App\Http\Controllers;

use App\Models\SmsMessage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{

    public function index(Request $request): View
    {
        $messages = SmsMessage::with('trip.train')
            ->where(fn ($q) => $q
                ->where('user_id', $request->user()->id)
                ->orWhere('to', $request->user()->phone))
            ->latest()
            ->paginate(15);

        return view('messages.index', compact('messages'));
    }
}
