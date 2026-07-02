@extends('layouts.guest')
@section('title', 'Log in')

@section('content')
    <h2 class="text-xl font-semibold mb-6">Log in</h2>

    @if ($errors->any())
        <div class="mb-4 rounded bg-rose-100 border border-rose-300 text-rose-700 px-3 py-2 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input name="email" type="email" value="{{ old('email') }}" required autofocus
                   class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-slate-900 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <input name="password" type="password" required
                   class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-slate-900 outline-none">
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" class="rounded border-slate-300"> Remember me
        </label>
        <button class="w-full bg-slate-900 text-white rounded-lg py-2.5 font-medium hover:bg-slate-800">Log in</button>
    </form>

    <p class="text-sm text-slate-500 mt-6 text-center">
        No account? <a href="{{ route('register') }}" class="text-slate-900 font-medium underline">Register</a>
    </p>
@endsection
