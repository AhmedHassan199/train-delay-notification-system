@extends('layouts.app')
@section('title', 'My Messages')

@section('content')
    <h1 class="text-2xl font-bold mb-2">My messages</h1>
    <p class="text-slate-500 text-sm mb-6">SMS sent to {{ auth()->user()->phone }} (demo mode — messages are recorded here).</p>

    @if ($messages->isEmpty())
        <p class="text-slate-500">No messages yet.</p>
    @else
        <div class="space-y-3">
            @foreach ($messages as $msg)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex gap-3">
                    <div class="text-2xl">📩</div>
                    <div class="flex-1">
                        <div class="text-sm text-slate-800">{{ $msg->body }}</div>
                        <div class="text-xs text-slate-400 mt-2 flex gap-3">
                            <span>{{ $msg->created_at->format('D d M, H:i') }}</span>
                            @if ($msg->provider)<span>via {{ $msg->provider }}</span>@endif
                            <span class="{{ $msg->status === 'sent' ? 'text-emerald-600' : 'text-rose-600' }}">{{ $msg->status }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
