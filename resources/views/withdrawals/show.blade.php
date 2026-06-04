@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="mb-6">
        <a href="{{ route('withdrawals.index') }}" class="text-purple-600 hover:text-purple-700">
            <i class="ti ti-arrow-left"></i> Back to Withdrawals
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h1 class="text-xl font-bold text-white">Withdrawal Details</h1>
            <p class="text-purple-200 text-sm">Reference: #WD-{{ $withdrawal->id }}</p>
        </div>
        
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-gray-400 uppercase">Amount</p>
                    <p class="text-2xl font-bold text-red-600">TSh {{ number_format($withdrawal->amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase">Status</p>
                    @if($withdrawal->status === 'pending')
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⏳ Pending</span>
                    @elseif($withdrawal->status === 'processing')
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">🔄 Processing</span>
                    @elseif($withdrawal->status === 'completed')
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✅ Completed</span>
                    @elseif($withdrawal->status === 'rejected')
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">❌ Rejected</span>
                    @else
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Cancelled</span>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase">Requested On</p>
                    <p class="text-gray-800">{{ $withdrawal->created_at->format('F d, Y h:i A') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase">Payment Method</p>
                    <p class="text-gray-800">{{ strtoupper($withdrawal->payment_method) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase">Account Details</p>
                    <p class="text-gray-800">{{ $withdrawal->account_details }}</p>
                </div>
                @if($withdrawal->notes)
                <div>
                    <p class="text-xs text-gray-400 uppercase">Notes</p>
                    <p class="text-gray-800">{{ $withdrawal->notes }}</p>
                </div>
                @endif
                @if($withdrawal->rejection_reason)
                <div class="md:col-span-2">
                    <p class="text-xs text-gray-400 uppercase">Rejection Reason</p>
                    <p class="text-red-600">{{ $withdrawal->rejection_reason }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection