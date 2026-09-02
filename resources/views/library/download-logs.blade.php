@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">📥 Download History</h1>
    
    <!-- Download Limit Card -->
    @php
        $status = auth()->user()->getDownloadLimitStatus();
    @endphp
    
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-200">
        <h2 class="text-lg font-semibold mb-2">Today's Download Usage</h2>
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Used: {{ $status['used'] }} of {{ $status['limit'] }}</span>
                    <span>{{ $status['progress'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="h-3 rounded-full transition-all duration-500 
                        {{ $status['color'] === 'red' ? 'bg-red-500' : ($status['color'] === 'orange' ? 'bg-orange-500' : 'bg-green-500') }}"
                        style="width: {{ $status['progress'] }}%">
                    </div>
                </div>
            </div>
            <div class="text-center">
                <span class="text-3xl font-bold text-{{ $status['color'] }}-600">{{ $status['remaining'] }}</span>
                <p class="text-xs text-gray-500">Remaining</p>
            </div>
        </div>
        <p class="text-sm mt-3 text-{{ $status['color'] }}-600">{{ $status['message'] }}</p>
    </div>
    
    <!-- Download History -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Book</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($downloads as $download)
                    <tr class="border-b">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <i class="ti ti-file-text text-blue-500"></i>
                                {{ $download->book->title ?? 'Unknown' }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $download->downloaded_at->format('M d, Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-8 text-center text-gray-500">
                            No downloads yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $downloads->links() }}
    </div>
</div>
@endsection