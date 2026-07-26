@extends('layouts.author')

@section('title', 'My Earnings')
@section('page-title', 'My Earnings')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Earnings Overview</h2>
            <p class="text-gray-500">Track your book sales and royalties</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Royalties</p>
                    <p class="text-3xl font-bold text-purple-600">TSh {{ number_format($totalRoyalties ?? 0, 2) }}</p>
                </div>
                <div class="stat-icon bg-purple-100 text-purple-600">
                    <i class="ti ti-coin"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">10% commission on all book sales</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">This Month</p>
                    <p class="text-2xl font-bold text-emerald-600">TSh {{ number_format($monthlyRoyalties[date('n') - 1] ?? 0, 2) }}</p>
                </div>
                <div class="stat-icon bg-emerald-100 text-emerald-600">
                    <i class="ti ti-calendar"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Sales</p>
                    <p class="text-2xl font-bold text-blue-600">{{ count($recentRoyalties ?? []) }}</p>
                </div>
                <div class="stat-icon bg-blue-100 text-blue-600">
                    <i class="ti ti-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Chart -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Monthly Royalties</h3>
        <div class="h-64 flex items-end gap-2">
            @php
                $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                $maxEarning = max($monthlyRoyalties ?? [0]);
            @endphp
            @foreach($monthlyRoyalties ?? [] as $index => $amount)
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-purple-100 rounded-t transition-all hover:bg-purple-200 relative group" 
                         style="height: {{ $maxEarning > 0 && $amount > 0 ? ($amount / $maxEarning * 100) : 5 }}%; min-height: 5px;">
                        <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                            TSh {{ number_format($amount, 0) }}
                        </div>
                    </div>
                    <span class="text-xs text-gray-500">{{ $months[$index] ?? '' }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Recent Sales</h3>
        @if(count($recentRoyalties ?? []) > 0)
            <div class="space-y-3">
                @foreach($recentRoyalties as $royalty)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-800 text-sm">{{ $royalty->payable->title ?? 'Unknown Book' }}</p>
                            <p class="text-xs text-gray-500">Buyer: {{ $royalty->user->full_name ?? 'Guest' }}</p>
                            <p class="text-xs text-gray-400">{{ $royalty->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-green-600">TSh {{ number_format(($royalty->amount ?? 0) * 0.10, 2) }}</p>
                            <p class="text-xs text-gray-400">Sale: TSh {{ number_format($royalty->amount ?? 0, 2) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="ti ti-coin-off text-4xl mb-2 block text-gray-300"></i>
                <p>No sales yet</p>
                <p class="text-sm">Start selling your books to see earnings here</p>
            </div>
        @endif
    </div>
</div>
@endsection