@extends('layouts.app')
@section('title', 'Trip · Delay')

@section('content')
    <a href="{{ route('admin.trips.index') }}" class="text-sm text-slate-500 hover:underline">&larr; All trips</a>

    <div class="flex items-start justify-between mt-2 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ $trip->origin }} → {{ $trip->destination }}</h1>
            <p class="text-slate-500">{{ $trip->train->name }} · {{ $trip->train->code }} · {{ $bookingsCount }} confirmed booking(s)</p>
        </div>
        @include('partials.status-badge', ['trip' => $trip])
    </div>

    <div class="grid gap-6 md:grid-cols-2">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 text-sm">
            <h2 class="font-semibold mb-3">Schedule</h2>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <div class="text-slate-400">Departure</div>
                    <div>{{ $trip->departure_time->format('D d M, H:i') }}</div>
                    @if ($trip->expected_departure_time)
                        <div class="text-rose-600">→ {{ $trip->expected_departure_time->format('H:i') }}</div>
                    @endif
                </div>
                <div>
                    <div class="text-slate-400">Arrival</div>
                    <div>{{ $trip->arrival_time->format('D d M, H:i') }}</div>
                    @if ($trip->expected_arrival_time)
                        <div class="text-rose-600">→ {{ $trip->expected_arrival_time->format('H:i') }}</div>
                    @endif
                </div>
            </div>

            <h2 class="font-semibold mt-5 mb-2">Last sensor reading</h2>
            @if ($trip->last_reading_at)
                <div class="text-slate-600">
                    {{ $trip->last_distance_km }} km at {{ $trip->last_speed_kmh }} km/h
                    <span class="text-slate-400">({{ $trip->last_reading_at->diffForHumans() }})</span>
                </div>
            @else
                <div class="text-slate-400">No sensor readings yet.</div>
            @endif
        </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <h2 class="font-semibold mb-3">Report a delay manually</h2>
            <p class="text-sm text-slate-500 mb-4">Applies a delay and queues an SMS to every confirmed passenger — the same code path the sensor triggers.</p>
            <form method="POST" action="{{ route('admin.trips.delay', $trip) }}" class="flex items-end gap-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Delay (minutes)</label>
                    <input name="delay_minutes" type="number" min="0" max="1440" value="30" required
                           class="w-32 rounded-lg border border-slate-300 px-3 py-2">
                </div>
                <button class="bg-rose-600 text-white rounded-lg px-5 py-2 font-medium hover:bg-rose-700">Apply & notify</button>
            </form>
        </div>
    </div>

        <div class="mt-8">
        <h2 class="text-lg font-semibold mb-3">Sensor readings & delay events</h2>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-left">
                    <tr>
                        <th class="px-4 py-3">When</th>
                        <th class="px-4 py-3">Source</th>
                        <th class="px-4 py-3">Distance</th>
                        <th class="px-4 py-3">Speed</th>
                        <th class="px-4 py-3">Computed ETA</th>
                        <th class="px-4 py-3">Delay</th>
                        <th class="px-4 py-3">Notified?</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($events as $e)
                        <tr>
                            <td class="px-4 py-3">{{ $e->reported_at->format('d M H:i:s') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-xs {{ $e->source === 'sensor' ? 'bg-sky-100 text-sky-700' : 'bg-amber-100 text-amber-700' }}">{{ $e->source }}</span>
                            </td>
                            <td class="px-4 py-3">{{ $e->distance_km !== null ? $e->distance_km.' km' : '—' }}</td>
                            <td class="px-4 py-3">{{ $e->speed_kmh !== null ? $e->speed_kmh.' km/h' : '—' }}</td>
                            <td class="px-4 py-3">{{ optional($e->computed_eta)->format('H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 {{ $e->delay_minutes > 0 ? 'text-rose-600 font-medium' : 'text-slate-400' }}">+{{ $e->delay_minutes }}m</td>
                            <td class="px-4 py-3">
                                @if ($e->notified)
                                    <span class="text-emerald-600">✓ SMS sent</span>
                                @else
                                    <span class="text-slate-400">— (deduped)</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-6 text-center text-slate-400">No readings yet. Post to the sensor API or use the manual delay above.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $events->links() }}</div>
    </div>
@endsection
