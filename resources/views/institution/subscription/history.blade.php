@extends('layouts.librarian')

@section('title', 'Subscription History')

@section('content')
<div class="max-w-6xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('institution.subscription.index') }}" class="text-purple-600 hover:text-purple-700 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Subscription
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="ti ti-history"></i> Full Subscription History
            </h2>
        </div>
        <div class="p-6">
            @if($history->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $entry)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 text-sm font-semibold capitalize">
                                {{ $entry->plan->name ?? $entry->plan ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $entry->start_date ? $entry->start_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $entry->end_date ? $entry->end_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">TSh {{ number_format($entry->amount ?? 0, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ strtoupper($entry->payment_method ?? 'N/A') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                    @if($entry->status === 'active') bg-green-100 text-green-700
                                    @elseif($entry->status === 'cancelled') bg-gray-100 text-gray-700
                                    @elseif($entry->status === 'expired') bg-red-100 text-red-700
                                    @else bg-yellow-100 text-yellow-700 @endif">
                                    {{ ucfirst($entry->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="mt-6">
                    {{ $history->links() }}
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No subscription history found.</p>
            @endif
        </div>
    </div>
</div>
@endsection