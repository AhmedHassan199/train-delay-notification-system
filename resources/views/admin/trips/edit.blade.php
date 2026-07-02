@extends('layouts.app')
@section('title', 'Edit Trip')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Edit trip</h1>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.trips.update', $trip) }}">
            @csrf @method('PUT')
            @include('admin.trips._form')
        </form>
    </div>
@endsection
