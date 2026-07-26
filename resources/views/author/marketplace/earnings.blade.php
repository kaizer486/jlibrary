@extends('layouts.author')

@section('title', 'Earnings')
@section('page-title', 'Earnings')

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200">
            <p class="text-sm text-gray-500">Total Sales</p>
            <p class="text-2xl font-bold text-blue-600">TSh {{ number_format($totalSales, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200">
            <p class="text-sm text-gray-500">Total Royalties (10%)</p>
            <p class="text-2xl font-bold text-green-600">TSh {{ number_format($totalRoyalties, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200">
            <p class="text-sm text-gray-500">Withdrawn</p>
            <p class="text-2xl font-bold text-orange-600">TSh {{ number_format($totalWithdrawn, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200">
            <p class="text-sm text-gray-500">Available Balance</p>
            <p class="text-2xl font-bold text-purple-600">TSh {{ number_format($availableBalance, 2) }}</p>
            @if($pendingWithdrawal > 0)
                <p class="text-xs text-yellow-600 mt-1">Pending: TSh {{ number_format($pendingWithdrawal, 2) }}</p>
            @endif
        </div>
    </div>
    
    <!-- Monthly Earnings Chart -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
        <h3 class="font-semibold text-gray-800 mb-4">📊 Monthly Earnings</h3>
        <div class="h-64 flex items-end gap-2">
            @foreach($months as $index => $month)
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-purple-100 rounded-t transition-all hover:bg-purple-200 relative group" 
                         style="height: {{ $monthlyEarnings[$index] > 0 ? ($monthlyEarnings[$index] / max($monthlyEarnings) * 100) : 5 }}%; min-height: 5px;">
                        <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                            TSh {{ number_format($monthlyEarnings[$index], 0) }}
                        </div>
                    </div>
                    <span class="text-xs text-gray-500">{{ $month }}</span>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- Actions -->
    <div class="flex gap-3">
        <a href="{{ route('author.withdrawals.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="ti ti-wallet"></i> Request Withdrawal
        </a>
        <a href="{{ route('author.withdrawals.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="ti ti-history"></i> Withdrawal History
        </a>
    </div>
</div>
@endsection