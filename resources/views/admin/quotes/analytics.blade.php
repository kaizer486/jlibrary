@extends('layouts.admin')

@section('title', 'Quote Analytics')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.quotes.index') }}" class="text-purple-600 hover:text-purple-700">
            <i class="ti ti-arrow-left"></i> Back to Quotes
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h1 class="text-xl font-bold text-white">📊 Quote Analytics</h1>
            <p class="text-purple-100 text-sm">Performance metrics for this quote</p>
        </div>
        
        <div class="p-6">
            <!-- Quote Display -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-6 mb-6 text-center">
                <i class="ti ti-quote text-3xl text-purple-300 mb-2 block"></i>
                <p class="text-gray-700 text-lg italic">"{{ $quote->quote_text }}"</p>
                <p class="text-gray-500 mt-3">— {{ $quote->author ?? 'Anonymous' }}</p>
            </div>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 rounded-xl p-4 text-center">
                    <i class="ti ti-eye text-blue-500 text-2xl mb-2 block"></i>
                    <p class="text-2xl font-bold text-blue-700">{{ number_format($quote->views_count) }}</p>
                    <p class="text-xs text-gray-500">Total Views</p>
                </div>
                <div class="bg-red-50 rounded-xl p-4 text-center">
                    <i class="ti ti-heart text-red-500 text-2xl mb-2 block"></i>
                    <p class="text-2xl font-bold text-red-700">{{ number_format($quote->saves_count) }}</p>
                    <p class="text-xs text-gray-500">Saves</p>
                </div>
                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <i class="ti ti-share text-green-500 text-2xl mb-2 block"></i>
                    <p class="text-2xl font-bold text-green-700">{{ number_format($quote->shares_count) }}</p>
                    <p class="text-xs text-gray-500">Shares</p>
                </div>
            </div>
            
            <!-- Engagement Rate -->
            <div class="bg-gray-50 rounded-xl p-4">
                <h3 class="font-semibold text-gray-800 mb-2">Engagement Rate</h3>
                @php
                    $totalInteractions = $quote->saves_count + $quote->shares_count;
                    $engagementRate = $quote->views_count > 0 ? round(($totalInteractions / $quote->views_count) * 100, 1) : 0;
                @endphp
                <div class="flex items-center gap-3">
                    <div class="flex-1 bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-3 rounded-full" style="width: {{ min($engagementRate, 100) }}%"></div>
                    </div>
                    <span class="text-sm font-semibold text-purple-600">{{ $engagementRate }}%</span>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ $totalInteractions }} total interactions out of {{ number_format($quote->views_count) }} views</p>
            </div>
            
            <!-- Quote Info -->
            <div class="mt-6 pt-6 border-t">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Category:</span>
                        <span class="ml-2 font-medium">{{ ucfirst($quote->category) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Status:</span>
                        <span class="ml-2">{!! $quote->status_badge !!}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Created:</span>
                        <span class="ml-2">{{ $quote->created_at->format('M d, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Last Updated:</span>
                        <span class="ml-2">{{ $quote->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection