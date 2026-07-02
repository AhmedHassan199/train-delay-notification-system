<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Train Station') · {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen">
@auth
    @php $u = auth()->user(); @endphp
    <nav class="bg-slate-900 text-slate-100">
        <div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ url('/') }}" class="font-bold text-white flex items-center gap-2">
                    <span>🚆</span> {{ config('app.name') }}
                </a>
                @if ($u->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white {{ request()->routeIs('admin.dashboard') ? 'text-white font-semibold' : 'text-slate-300' }}">Dashboard</a>
                    <a href="{{ route('admin.trains.index') }}" class="hover:text-white {{ request()->routeIs('admin.trains.*') ? 'text-white font-semibold' : 'text-slate-300' }}">Trains</a>
                    <a href="{{ route('admin.trips.index') }}" class="hover:text-white {{ request()->routeIs('admin.trips.*') ? 'text-white font-semibold' : 'text-slate-300' }}">Trips</a>
                    <a href="{{ route('admin.simulator.index') }}" class="hover:text-white {{ request()->routeIs('admin.simulator.*') ? 'text-white font-semibold' : 'text-slate-300' }}">🛰 Sensor</a>
                    <a href="{{ route('admin.bookings.index') }}" class="hover:text-white {{ request()->routeIs('admin.bookings.*') ? 'text-white font-semibold' : 'text-slate-300' }}">Bookings</a>
                    <a href="{{ route('admin.sms.index') }}" class="hover:text-white {{ request()->routeIs('admin.sms.*') ? 'text-white font-semibold' : 'text-slate-300' }}">SMS Outbox</a>
                @else
                    <a href="{{ route('trips.index') }}" class="hover:text-white {{ request()->routeIs('trips.*') ? 'text-white font-semibold' : 'text-slate-300' }}">Trips</a>
                    <a href="{{ route('bookings.index') }}" class="hover:text-white {{ request()->routeIs('bookings.*') ? 'text-white font-semibold' : 'text-slate-300' }}">My Bookings</a>
                    <a href="{{ route('messages.index') }}" class="hover:text-white {{ request()->routeIs('messages.*') ? 'text-white font-semibold' : 'text-slate-300' }}">My Messages</a>
                @endif
            </div>
            <div class="flex items-center gap-4 text-sm">
                <span class="text-slate-400">{{ $u->name }}@if($u->isAdmin()) <span class="ml-1 px-1.5 py-0.5 rounded bg-amber-500 text-xs text-white">admin</span>@endif</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-slate-300 hover:text-white">Logout</button>
                </form>
            </div>
        </div>
    </nav>
@endauth

<main class="max-w-6xl mx-auto px-4 py-8">
    @if (session('status'))
        <div class="mb-6 rounded-lg bg-emerald-100 border border-emerald-300 text-emerald-800 px-4 py-3">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-lg bg-rose-100 border border-rose-300 text-rose-800 px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>
