@extends('layouts.app')

@section('title', 'Subscription History')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        
        <div class="mb-6">
            <a href="{{ route('user.subscription.index') }}" class="text-purple-300 hover:text-purple-200 transition inline-flex items-center gap-1">
                <i class="ti ti-arrow-left"></i> Back to Subscription
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="ti ti-history"></i> Subscription History
                </h2>
            </div>
            <div class="p-6">
                @if($history->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">End</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $entry)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-sm font-semibold capitalize">{{ $entry->plan }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $entry->starts_at?->format('M d, Y') ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $entry->ends_at?->format('M d, Y') ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm font-medium">TSh {{ number_format($entry->amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        @if($entry->status === 'active') bg-green-100 text-green-700
                                        @elseif($entry->status === 'cancelled') bg-gray-100 text-gray-700
                                        @else bg-yellow-100 text-yellow-700 @endif">
                                        {{ ucfirst($entry->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-6">{{ $history->links() }}</div>
                @else
                    <p class="text-gray-500 text-center py-8">No subscription history found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection