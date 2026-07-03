@extends('layouts.app')
@section('title', 'Trips')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Trips</h1>
        <a href="{{ route('admin.trips.create') }}" class="bg-slate-900 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-800">+ New trip</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3">Route</th>
                    <th class="px-4 py-3">Train</th>
                    <th class="px-4 py-3">Departure</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Seats</th>
                    <th class="px-4 py-3">Booked</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($trips as $trip)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $trip->origin }} → {{ $trip->destination }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $trip->train->code }}</td>
                        <td class="px-4 py-3">{{ $trip->departure_time->format('d M, H:i') }}</td>
                        <td class="px-4 py-3">@include('partials.status-badge', ['trip' => $trip])</td>
                        <td class="px-4 py-3">{{ $trip->available_seats }}</td>
                        <td class="px-4 py-3">{{ $trip->confirmed_bookings_count }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.trips.show', $trip) }}" class="text-sky-600 hover:underline">Delay</a>
                            <a href="{{ route('admin.trips.edit', $trip) }}" class="text-slate-600 hover:underline ml-2">Edit</a>
                            <form method="POST" action="{{ route('admin.trips.destroy', $trip) }}" class="inline" onsubmit="return confirm('Delete this trip?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-600 hover:underline ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-slate-400">No trips yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $trips->links() }}</div>
@endsection
