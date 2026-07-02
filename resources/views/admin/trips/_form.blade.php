@php
    $fmt = fn ($v) => $v ? \Illuminate\Support\Carbon::parse($v)->format('Y-m-d\TH:i') : '';
@endphp

@if ($errors->any())
    <div class="mb-4 rounded bg-rose-100 border border-rose-300 text-rose-700 px-3 py-2 text-sm">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium mb-1">Train</label>
        <select name="train_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
            @foreach ($trains as $train)
                <option value="{{ $train->id }}" @selected(old('train_id', $trip->train_id) == $train->id)>
                    {{ $train->code }} — {{ $train->name }} ({{ $train->total_seats }} seats)
                </option>
            @endforeach
        </select>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Origin</label>
            <input name="origin" value="{{ old('origin', $trip->origin) }}" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Destination</label>
            <input name="destination" value="{{ old('destination', $trip->destination) }}" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Departure time</label>
            <input name="departure_time" type="datetime-local" value="{{ old('departure_time', $fmt($trip->departure_time)) }}" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Arrival time</label>
            <input name="arrival_time" type="datetime-local" value="{{ old('arrival_time', $fmt($trip->arrival_time)) }}" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Route distance (km) <span class="text-slate-400">optional</span></label>
        <input name="route_distance_km" type="number" step="0.01" min="0" value="{{ old('route_distance_km', $trip->route_distance_km) }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2">
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button class="bg-slate-900 text-white rounded-lg px-5 py-2 font-medium hover:bg-slate-800">Save</button>
    <a href="{{ route('admin.trips.index') }}" class="px-5 py-2 text-slate-600 hover:underline">Cancel</a>
</div>
