@extends('layouts.app')
@section('title', 'Trips')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Available trips</h1>
        <span class="text-sm text-slate-500">{{ $trips->count() }} trips</span>
    </div>

    @if ($trips->isEmpty())
        <p class="text-slate-500">No trips scheduled right now.</p>
    @else
        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($trips as $trip)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-lg font-semibold">{{ $trip->origin }} → {{ $trip->destination }}</div>
                            <div class="text-sm text-slate-500">{{ $trip->train->name }} · {{ $trip->train->code }}</div>
                        </div>
                        @include('partials.status-badge', ['trip' => $trip])
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <div class="text-slate-400">Departure</div>
                            <div class="font-medium">
                                @if ($trip->isDelayed())
                                    <span class="line-through text-slate-400">{{ $trip->departure_time->format('H:i') }}</span>
                                    <span class="text-rose-600">{{ $trip->effectiveDeparture()->format('H:i') }}</span>
                                @else
                                    {{ $trip->departure_time->format('D d M, H:i') }}
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="text-slate-400">Arrival</div>
                            <div class="font-medium">
                                @if ($trip->isDelayed())
                                    <span class="line-through text-slate-400">{{ $trip->arrival_time->format('H:i') }}</span>
                                    <span class="text-rose-600">{{ $trip->effectiveArrival()->format('H:i') }}</span>
                                @else
                                    {{ $trip->arrival_time->format('H:i') }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-sm {{ $trip->available_seats > 0 ? 'text-slate-500' : 'text-rose-600' }}">
                            {{ $trip->available_seats }} seats left
                        </span>
                        @if ($trip->available_seats > 0)
                            <form method="POST" action="{{ route('bookings.store', $trip) }}" class="flex items-center gap-2">
                                @csrf
                                <input type="number" name="seats" value="1" min="1" max="6"
                                       class="w-16 rounded-lg border border-slate-300 px-2 py-1.5 text-sm">
                                <button class="bg-slate-900 text-white rounded-lg px-4 py-1.5 text-sm font-medium hover:bg-slate-800">Book</button>
                            </form>
                        @else
                            <span class="text-sm text-slate-400">Sold out</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
