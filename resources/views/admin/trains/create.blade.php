@extends('layouts.app')
@section('title', 'New Train')

@section('content')
    <h1 class="text-2xl font-bold mb-6">New train</h1>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-lg">
        <form method="POST" action="{{ route('admin.trains.store') }}">
            @csrf
            @include('admin.trains._form')
        </form>
    </div>
@endsection
