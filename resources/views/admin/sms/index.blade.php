@extends('layouts.app')
@section('title', 'SMS Outbox')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">SMS Outbox</h1>
            <p class="text-slate-500 text-sm">Failover chain: <span class="font-mono">{{ implode(' → ', config('services.sms.providers')) }}</span></p>
        </div>
        <div class="flex gap-2 text-sm">
            <a href="{{ route('admin.sms.index') }}" class="px-3 py-1.5 rounded-lg {{ !$status ? 'bg-slate-900 text-white' : 'bg-white border border-slate-300' }}">All</a>
            <a href="{{ route('admin.sms.index', ['status' => 'sent']) }}" class="px-3 py-1.5 rounded-lg {{ $status === 'sent' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-300' }}">Sent</a>
            <a href="{{ route('admin.sms.index', ['status' => 'failed']) }}" class="px-3 py-1.5 rounded-lg {{ $status === 'failed' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-300' }}">Failed</a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3">When</th>
                    <th class="px-4 py-3">To</th>
                    <th class="px-4 py-3">Message</th>
                    <th class="px-4 py-3">Provider</th>
                    <th class="px-4 py-3">Tries</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($messages as $msg)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $msg->created_at->format('d M H:i:s') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $msg->to }}</td>
                        <td class="px-4 py-3 text-slate-600 max-w-md">{{ $msg->body }}</td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs px-2 py-0.5 rounded bg-slate-100">{{ $msg->provider ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $msg->attempts }}</td>
                        <td class="px-4 py-3">
                            @if ($msg->status === 'sent')
                                <span class="text-emerald-600 font-medium">sent</span>
                            @else
                                <span class="text-rose-600 font-medium" title="{{ $msg->error }}">failed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">No messages.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $messages->links() }}</div>
@endsection
