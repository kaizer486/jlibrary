@extends('layouts.author')

@section('title', 'Earnings')
@section('page-title', 'My Earnings')

@section('content')
<div class="space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">Earnings Overview</h2>
    
    <!-- Total Earnings Card -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="stat-card bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Earnings</p>
                    <p class="text-3xl font-bold text-purple-600">TSh {{ number_format($totalEarnings ?? 0, 2) }}</p>
                </div>
                <div class="stat-icon bg-purple-100 text-purple-600">
                    <i class="ti ti-coin"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Chart -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Monthly Earnings</h3>
        <div class="h-48 flex items-end gap-2">
            @foreach($monthlyEarnings ?? [] as $index => $amount)
            <div class="flex-1 flex flex-col items-center gap-1">
                <div class="w-full bg-purple-200 rounded-t relative group" style="height: {{ max($amount > 0 ? ($amount / max($monthlyEarnings) * 100) : 5, 5) }}%">
                    <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-0.5 rounded opacity-0 group-hover:opacity-100 transition">
                        TSh {{ number_format($amount, 0) }}
                    </div>
                </div>
                <span class="text-xs text-gray-500">{{ ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][$index] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Earnings -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Recent Transactions</h3>
        <div class="space-y-3">
            @forelse($recentEarnings ?? [] as $earning)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">{{ $earning->listing->title ?? 'Book Sale' }}</p>
                    <p class="text-xs text-gray-500">{{ $earning->created_at->diffForHumans() }}</p>
                </div>
                <span class="font-semibold text-green-600">+TSh {{ number_format($earning->seller_earnings ?? $earning->amount, 2) }}</span>
            </div>
            @empty
            <p class="text-center text-gray-500 py-4">No earnings yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection