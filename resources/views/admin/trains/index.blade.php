@extends('layouts.app')
@section('title', 'Trains')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Trains</h1>
        <a href="{{ route('admin.trains.create') }}" class="bg-slate-900 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-800">+ New train</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Seats</th>
                    <th class="px-4 py-3">Trips</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($trains as $train)
                    <tr>
                        <td class="px-4 py-3 font-mono">{{ $train->code }}</td>
                        <td class="px-4 py-3">{{ $train->name }}</td>
                        <td class="px-4 py-3">{{ $train->total_seats }}</td>
                        <td class="px-4 py-3">{{ $train->trips_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.trains.edit', $train) }}" class="text-slate-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.trains.destroy', $train) }}" class="inline" onsubmit="return confirm('Delete this train and its trips?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-600 hover:underline ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">No trains yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $trains->links() }}</div>
@endsection
