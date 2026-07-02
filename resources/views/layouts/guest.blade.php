<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Welcome') · {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <div class="text-4xl">🚆</div>
            <h1 class="text-2xl font-bold text-white mt-2">{{ config('app.name') }}</h1>
            <p class="text-slate-400 text-sm">Delay notifications for passengers</p>
        </div>
        <div class="bg-white text-slate-800 rounded-2xl shadow-xl p-8">
            @yield('content')
        </div>
    </div>
</body>
</html>
