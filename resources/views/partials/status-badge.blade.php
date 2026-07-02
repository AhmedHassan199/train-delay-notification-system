@php
    $map = [
        'on_time'   => ['On time', 'bg-emerald-100 text-emerald-700'],
        'delayed'   => ['Delayed', 'bg-rose-100 text-rose-700'],
        'cancelled' => ['Cancelled', 'bg-slate-200 text-slate-600'],
        'departed'  => ['Departed', 'bg-sky-100 text-sky-700'],
        'arrived'   => ['Arrived', 'bg-slate-100 text-slate-600'],
    ];
    [$label, $classes] = $map[$trip->status] ?? [$trip->status, 'bg-slate-100 text-slate-600'];
@endphp
<span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $classes }}">
    {{ $label }}@if ($trip->isDelayed() && $trip->delay_minutes) · +{{ $trip->delay_minutes }}m @endif
</span>
