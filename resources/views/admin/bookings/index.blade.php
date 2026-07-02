@extends('layouts.app')
@section('title', 'Bookings')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Bookings</h1>
        <form method="GET" class="flex items-center gap-2">
            <select name="trip_id" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">All trips</option>
                @foreach ($trips as $trip)
                    <option value="{{ $trip->id }}" @selected($tripId == $trip->id)>
                        {{ $trip->train->code }} · {{ $trip->origin }} → {{ $trip->destination }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3">Ref</th>
                    <th class="px-4 py-3">Passenger</th>
                    <th class="px-4 py-3">Phone</th>
                    <th class="px-4 py-3">Trip</th>
                    <th class="px-4 py-3">Seats</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($bookings as $booking)
                    <tr>
                        <td class="px-4 py-3 font-mono">{{ $booking->reference }}</td>
                        <td class="px-4 py-3">{{ $booking->user->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $booking->user->phone }}</td>
                        <td class="px-4 py-3">{{ $booking->trip->train->code }} · {{ $booking->trip->origin }} → {{ $booking->trip->destination }}</td>
                        <td class="px-4 py-3">{{ $booking->seats }}</td>
                        <td class="px-4 py-3">{{ $booking->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">No bookings.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $bookings->links() }}</div>
@endsection
