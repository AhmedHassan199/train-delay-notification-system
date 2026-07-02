@extends('layouts.app')
@section('title', 'Edit Train')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Edit train</h1>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-lg">
        <form method="POST" action="{{ route('admin.trains.update', $train) }}">
            @csrf @method('PUT')
            @include('admin.trains._form')
        </form>
    </div>
@endsection
