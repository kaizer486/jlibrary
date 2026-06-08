@extends('layouts.admin')

@section('title', 'Quote Analytics')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('institution.quotes.index') }}" class="text-purple-600 hover:text-purple-700">
            <i class="ti ti-arrow-left"></i> Back to Quotes
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <h1 class="text-xl font-bold text-white">📊 Quote Analytics</h1>
            <p class="text-blue-100 text-sm">Performance metrics for this quote</p>
        </div>
        
        <div class="p-6">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 mb-6 text-center">
                <i class="ti ti-quote text-3xl text-blue-500 mb-2 block"></i>
                <p class="text-gray-700 text-lg italic">"{{ $quote->quote_text }}"</p>
                <p class="text-gray-500 mt-3">— {{ $quote->author ?? 'Anonymous' }}</p>
            </div>
            
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
            
            <div class="bg-gray-50 rounded-xl p-4">
                <h3 class="font-semibold text-gray-800 mb-2">Engagement Rate</h3>
                @php
                    $totalInteractions = $quote->saves_count + $quote->shares_count;
                    $engagementRate = $quote->views_count > 0 ? round(($totalInteractions / $quote->views_count) * 100, 1) : 0;
                @endphp
                <div class="flex items-center gap-3">
                    <div class="flex-1 bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full" style="width: {{ min($engagementRate, 100) }}%"></div>
                    </div>
                    <span class="text-sm font-semibold text-blue-600">{{ $engagementRate }}%</span>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ $totalInteractions }} total interactions out of {{ number_format($quote->views_count) }} views</p>
            </div>
        </div>
    </div>
</div>
@endsection