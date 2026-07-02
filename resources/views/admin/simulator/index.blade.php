@extends('layouts.app')
@section('title', 'Sensor Simulator')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">🛰 Sensor Simulator</h1>
        <p class="text-slate-500 text-sm">Act as the trackside sensor: pick a trip, enter distance + speed, and send a reading. This runs the <span class="font-medium">exact same pipeline</span> as the real <span class="font-mono">POST /api/trips/&#123;id&#125;/reading</span> endpoint.</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            @if ($errors->any())
                <div class="mb-4 rounded bg-rose-100 border border-rose-300 text-rose-700 px-3 py-2 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.simulator.send') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Trip</label>
                    <select name="trip_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
                        @foreach ($trips as $trip)
                            <option value="{{ $trip->id }}"
                                @selected(session('sim_trip_id', old('trip_id')) == $trip->id)>
                                {{ $trip->train->code }} · {{ $trip->origin }} → {{ $trip->destination }}
                                (arrives {{ $trip->arrival_time->format('D H:i') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Distance remaining (km)</label>
                        <input id="distance" name="distance_remaining_km" type="number" step="0.1" min="0"
                               value="{{ old('distance_remaining_km', 250) }}" required
                               class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Current speed (km/h)</label>
                        <input id="speed" name="current_speed_kmh" type="number" step="0.1" min="0"
                               value="{{ old('current_speed_kmh', 50) }}" required
                               class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    </div>
                </div>

                                <div class="text-sm text-slate-500">
                    Estimated travel time from this reading:
                    <span id="eta-preview" class="font-semibold text-slate-700">—</span>
                </div>

                <div class="flex flex-wrap gap-2">
                    <span class="text-xs text-slate-400 self-center">Quick speed:</span>
                    @foreach ([120, 90, 60, 40, 20, 0] as $s)
                        <button type="button" onclick="document.getElementById('speed').value={{ $s }};updateEta()"
                                class="text-xs px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200">{{ $s }} km/h</button>
                    @endforeach
                </div>

                <button class="w-full bg-slate-900 text-white rounded-lg py-2.5 font-medium hover:bg-slate-800">
                    📡 Send reading to backend
                </button>
            </form>
        </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="font-semibold mb-3">Last reading result</h2>
            @if ($result)
                <div class="space-y-3 text-sm">
                    <div class="text-slate-500">{{ $result['trip'] }}</div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-lg bg-slate-50 p-3">
                            <div class="text-slate-400 text-xs">Sensor sent</div>
                            <div class="font-medium">{{ $result['distance'] }} km @ {{ $result['speed'] }} km/h</div>
                        </div>
                        <div class="rounded-lg bg-slate-50 p-3">
                            <div class="text-slate-400 text-xs">Backend computed delay</div>
                            <div class="font-bold text-lg {{ $result['delay_minutes'] > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $result['delay_minutes'] }} min
                            </div>
                        </div>
                        <div class="rounded-lg bg-slate-50 p-3">
                            <div class="text-slate-400 text-xs">Scheduled arrival</div>
                            <div class="font-medium">{{ $result['scheduled_arrival'] }}</div>
                        </div>
                        <div class="rounded-lg bg-slate-50 p-3">
                            <div class="text-slate-400 text-xs">New computed arrival (ETA)</div>
                            <div class="font-medium {{ $result['delay_minutes'] > 0 ? 'text-rose-600' : '' }}">{{ $result['computed_eta'] ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="rounded-lg p-3 {{ $result['notified'] ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-50' }}">
                        @if ($result['notified'])
                            <div class="text-emerald-700 font-semibold">✓ SMS queued for {{ $result['passengers_notified'] }} passenger(s)</div>
                            <div class="text-slate-500 text-xs mt-1">Check the <a href="{{ route('admin.sms.index') }}" class="underline">SMS Outbox</a> (queue worker must be running).</div>
                        @else
                            <div class="text-slate-600 font-medium">No SMS sent</div>
                            <div class="text-slate-500 text-xs mt-1">
                                @if ($result['delay_minutes'] == 0)
                                    Train is on time for this reading.
                                @else
                                    Delay unchanged since last notification (deduped) — passengers were already told.
                                @endif
                            </div>
                        @endif
                    </div>

                    <a href="{{ route('admin.trips.show', $result['trip_id']) }}" class="text-sky-600 hover:underline text-sm">View trip &amp; readings log →</a>
                </div>
            @else
                <p class="text-slate-400 text-sm">Send a reading to see the computed ETA, delay, and who got notified.</p>
            @endif
        </div>
    </div>

    <script>
        function updateEta() {
            const d = parseFloat(document.getElementById('distance').value);
            const s = parseFloat(document.getElementById('speed').value);
            const el = document.getElementById('eta-preview');
            if (!s || s <= 0 || isNaN(d)) { el.textContent = s === 0 ? 'stopped (∞)' : '—'; return; }
            const mins = Math.round((d / s) * 60);
            const h = Math.floor(mins / 60), m = mins % 60;
            el.textContent = (h ? h + 'h ' : '') + m + 'm  (' + mins + ' min)';
        }
        document.getElementById('distance').addEventListener('input', updateEta);
        document.getElementById('speed').addEventListener('input', updateEta);
        updateEta();
    </script>
@endsection
