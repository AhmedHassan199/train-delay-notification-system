@extends('layouts.guest')
@section('title', 'Register')

@section('content')
    <h2 class="text-xl font-semibold mb-6">Create your account</h2>

    @if ($errors->any())
        <div class="mb-4 rounded bg-rose-100 border border-rose-300 text-rose-700 px-3 py-2 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Full name</label>
            <input name="name" type="text" value="{{ old('name') }}" required autofocus
                   class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-slate-900 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input name="email" type="email" value="{{ old('email') }}" required
                   class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-slate-900 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Phone <span class="text-slate-400">(for delay SMS)</span></label>
            <input name="phone" type="text" value="{{ old('phone') }}" placeholder="+20100..." required
                   class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-slate-900 outline-none">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input name="password" type="password" required
                       class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-slate-900 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Confirm</label>
                <input name="password_confirmation" type="password" required
                       class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-slate-900 outline-none">
            </div>
        </div>
        <button class="w-full bg-slate-900 text-white rounded-lg py-2.5 font-medium hover:bg-slate-800">Register</button>
    </form>

    <p class="text-sm text-slate-500 mt-6 text-center">
        Already have an account? <a href="{{ route('login') }}" class="text-slate-900 font-medium underline">Log in</a>
    </p>
@endsection
