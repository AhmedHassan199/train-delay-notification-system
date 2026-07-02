@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

    <div class="grid gap-4 grid-cols-2 md:grid-cols-6">
        @foreach ([
            ['Trains', $stats['trains'], 'text-slate-800'],
            ['Trips', $stats['trips'], 'text-slate-800'],
            ['Delayed', $stats['delayed'], 'text-rose-600'],
            ['Bookings', $stats['bookings'], 'text-slate-800'],
            ['SMS sent', $stats['sms_sent'], 'text-emerald-600'],
            ['SMS failed', $stats['sms_failed'], 'text-rose-600'],
        ] as [$label, $value, $color])
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div class="text-3xl font-bold {{ $color }}">{{ $value }}</div>
                <div class="text-sm text-slate-500 mt-1">{{ $label }}</div>
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        <h2 class="text-lg font-semibold mb-3">Currently delayed</h2>
        @if ($recentDelays->isEmpty())
            <p class="text-slate-500">No delayed trips.</p>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 divide-y">
                @foreach ($recentDelays as $trip)
                    <a href="{{ route('admin.trips.show', $trip) }}" class="flex items-center justify-between p-4 hover:bg-slate-50">
                        <div>
                            <div class="font-medium">{{ $trip->origin }} → {{ $trip->destination }}</div>
                            <div class="text-sm text-slate-500">{{ $trip->train->code }} · +{{ $trip->delay_minutes }} min</div>
                        </div>
                        <div class="text-sm text-rose-600">
                            new arrival {{ optional($trip->effectiveArrival())->format('H:i') }}
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <div class="mt-8">
        <a href="{{ route('admin.simulator.index') }}"
           class="flex items-center justify-between bg-slate-900 text-white rounded-xl p-5 hover:bg-slate-800 transition">
            <div>
                <div class="font-semibold text-lg">🛰 Open the Sensor Simulator</div>
                <div class="text-slate-300 text-sm mt-1">Play the trackside sensor — pick a trip, send distance + speed, and watch the backend compute the delay and notify passengers.</div>
                <div class="text-slate-400 text-xs mt-2">SMS providers (failover order): <span class="font-mono">{{ implode(' → ', config('services.sms.providers')) }}</span></div>
            </div>
            <span class="text-2xl ml-4">→</span>
        </a>
    </div>
@endsection
