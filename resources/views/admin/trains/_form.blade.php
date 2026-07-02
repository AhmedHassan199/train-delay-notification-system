@if ($errors->any())
    <div class="mb-4 rounded bg-rose-100 border border-rose-300 text-rose-700 px-3 py-2 text-sm">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input name="name" value="{{ old('name', $train->name) }}" required
               class="w-full rounded-lg border border-slate-300 px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Code (train number)</label>
        <input name="code" value="{{ old('code', $train->code) }}" required
               class="w-full rounded-lg border border-slate-300 px-3 py-2 font-mono">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Total seats</label>
        <input name="total_seats" type="number" min="1" value="{{ old('total_seats', $train->total_seats ?? 200) }}" required
               class="w-full rounded-lg border border-slate-300 px-3 py-2">
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button class="bg-slate-900 text-white rounded-lg px-5 py-2 font-medium hover:bg-slate-800">Save</button>
    <a href="{{ route('admin.trains.index') }}" class="px-5 py-2 text-slate-600 hover:underline">Cancel</a>
</div>
