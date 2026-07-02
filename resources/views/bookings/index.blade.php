@extends('layouts.app')
@section('title', 'My Bookings')

@section('content')
    <h1 class="text-2xl font-bold mb-6">My bookings</h1>

    @if ($bookings->isEmpty())
        <p class="text-slate-500">You have no bookings yet. <a href="{{ route('trips.index') }}" class="underline">Browse trips</a>.</p>
    @else
        <div class="space-y-4">
            @foreach ($bookings as $booking)
                @php $trip = $booking->trip; @endphp
                <div class="bg-white rounded-xl shadow-sm border {{ $trip->isDelayed() ? 'border-rose-300' : 'border-slate-200' }} p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-lg font-semibold">{{ $trip->origin }} → {{ $trip->destination }}</div>
                            <div class="text-sm text-slate-500">
                                {{ $trip->train->name }} · {{ $trip->train->code }} ·
                                Ref <span class="font-mono">{{ $booking->reference }}</span> ·
                                {{ $booking->seats }} seat(s)
                            </div>
                        </div>
                        @include('partials.status-badge', ['trip' => $trip])
                    </div>

                    @if ($trip->isDelayed())
                        <div class="mt-4 rounded-lg bg-rose-50 border border-rose-200 p-3 text-sm">
                            <div class="font-semibold text-rose-700 mb-1">⚠ This train is delayed by {{ $trip->delay_minutes }} minutes</div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="text-slate-500">Departure:</span>
                                    <span class="line-through text-slate-400">{{ $trip->departure_time->format('H:i') }}</span>
                                    <span class="font-semibold text-rose-700">{{ $trip->effectiveDeparture()->format('H:i') }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-500">Arrival:</span>
                                    <span class="line-through text-slate-400">{{ $trip->arrival_time->format('H:i') }}</span>
                                    <span class="font-semibold text-rose-700">{{ $trip->effectiveArrival()->format('H:i') }}</span>
                                </div>
                            </div>
                            <div class="text-rose-600 mt-2">Please arrive at the new time — no need to come early.</div>
                        </div>
                    @else
                        <div class="mt-4 text-sm text-slate-600">
                            Departs {{ $trip->departure_time->format('D d M, H:i') }} · Arrives {{ $trip->arrival_time->format('H:i') }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endsection
