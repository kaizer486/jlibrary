@extends('layouts.app')

@section('title', 'Earnings - Seller')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">💰 Earnings</h1>
        <a href="{{ route('seller.dashboard') }}" class="text-purple-600 hover:text-purple-700">
            <i class="ti ti-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Total Earnings</p>
            <p class="text-2xl font-bold text-green-600">TSh {{ number_format($totalEarnings ?? 0, 2) }}</p>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500">Pending Earnings</p>
            <p class="text-2xl font-bold text-yellow-600">TSh {{ number_format($pendingEarnings ?? 0, 2) }}</p>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-blue-500">
            <p class="text-sm text-gray-500">Monthly Earnings</p>
            <p class="text-2xl font-bold text-blue-600">TSh {{ number_format($monthlyEarnings ?? 0, 2) }}</p>
        </div>
    </div>

    @if(isset($monthlyData) && count($monthlyData) > 0)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Monthly Earnings Overview</h3>
            <div class="space-y-3">
                @foreach($monthlyData as $data)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ $data['month'] }}</span>
                            <span class="text-gray-800 font-medium">TSh {{ number_format($data['total'], 2) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full bg-purple-500" style="width: {{ $data['percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Recent Transactions -->
    @if(isset($transactions) && $transactions->count() > 0)
        <div class="mt-8 bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h3 class="font-semibold text-gray-800">Recent Transactions</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($transactions as $transaction)
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition">
                        <div>
                            <p class="font-medium text-gray-800">{{ $transaction->description ?? 'Order #' . $transaction->order_id }}</p>
                            <p class="text-xs text-gray-400">{{ $transaction->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <span class="font-semibold {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type === 'credit' ? '+' : '-' }} TSh {{ number_format($transaction->amount, 2) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection