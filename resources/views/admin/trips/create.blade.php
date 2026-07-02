@extends('layouts.app')
@section('title', 'New Trip')

@section('content')
    <h1 class="text-2xl font-bold mb-6">New trip</h1>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.trips.store') }}">
            @csrf
            @include('admin.trips._form')
        </form>
    </div>
@endsection
