@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    
    <div class="mb-6">
        <a href="{{ route('admin.payments.index') }}" class="text-purple-600 hover:text-purple-700">
            <i class="ti ti-arrow-left"></i> Back to Payments
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h1 class="text-white font-bold text-xl">Payment Details</h1>
        </div>
        
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-500 text-sm">Transaction ID</p>
                    <p class="font-mono text-gray-800">{{ $payment->reference ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Date</p>
                    <p class="text-gray-800">{{ $payment->created_at->format('F d, Y H:i:s') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">User</p>
                    <p class="font-semibold text-gray-800">{{ $payment->user->full_name ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-500">{{ $payment->user->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Amount</p>
                    <p class="text-2xl font-bold text-purple-600">TSh {{ number_format($payment->amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Payment Method</p>
                    <p class="capitalize text-gray-800">{{ $payment->method ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Status</p>
                    <span class="px-2 py-1 rounded-full text-xs 
                        {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $payment->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Type</p>
                    <p>{{ $payment->payable_type === 'App\\Models\\Book' ? 'Book Purchase' : 'Wallet Deposit' }}</p>
                </div>
            </div>
            
            @if($payment->status === 'pending' && $payment->payable_type !== 'App\\Models\\Book')
            <div class="mt-6 pt-6 border-t">
                <form action="{{ route('admin.payments.approve-deposit', $payment->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition">
                        Approve This Deposit
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection