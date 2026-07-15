@extends('layouts.super-admin')

@section('title', 'Payment Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="ti ti-receipt text-purple-600"></i>
                Payment Details
            </h1>
            <p class="text-gray-500 text-sm mt-1">View payment information and transaction details</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('super-admin.payments.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition text-sm flex items-center gap-2">
                <i class="ti ti-arrow-left"></i> Back to Payments
            </a>
            <a href="{{ route('invoices.payment', $payment->id) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition text-sm flex items-center gap-2" target="_blank">
                <i class="ti ti-file-invoice"></i> Download Invoice
            </a>
        </div>
    </div>

    <!-- Payment Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Status Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 
            {{ $payment->status === 'completed' ? 'border-green-500' : '' }}
            {{ $payment->status === 'pending' ? 'border-yellow-500' : '' }}
            {{ $payment->status === 'failed' ? 'border-red-500' : '' }}
            {{ $payment->status === 'refunded' ? 'border-gray-500' : '' }}">
            <p class="text-sm text-gray-500">Status</p>
            <p class="text-xl font-bold mt-1">
                @if($payment->status === 'completed')
                    <span class="text-green-600">✅ Completed</span>
                @elseif($payment->status === 'pending')
                    <span class="text-yellow-600">⏳ Pending</span>
                @elseif($payment->status === 'refunded')
                    <span class="text-gray-600">🔄 Refunded</span>
                @else
                    <span class="text-red-600">❌ Failed</span>
                @endif
            </p>
        </div>

        <!-- Amount Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <p class="text-sm text-gray-500">Amount</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">
                TSh {{ number_format($payment->amount, 2) }}
            </p>
            <p class="text-xs text-gray-500 mt-1">{{ $payment->currency ?? 'TZS' }}</p>
        </div>

        <!-- Date Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-500">Date</p>
            <p class="text-xl font-bold text-gray-800 mt-1">
                {{ $payment->created_at->format('M d, Y') }}
            </p>
            <p class="text-xs text-gray-500 mt-1">{{ $payment->created_at->format('H:i:s') }}</p>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Left Column -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ti ti-user text-purple-600"></i>
                User Information
            </h3>
            
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Full Name</p>
                    <p class="font-medium text-gray-800">{{ $payment->user->full_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-medium text-gray-800">{{ $payment->user->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">User ID</p>
                    <p class="font-medium text-gray-800">{{ $payment->user_id }}</p>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ti ti-info-circle text-purple-600"></i>
                Payment Information
            </h3>
            
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Reference</p>
                    <p class="font-mono font-medium text-gray-800 text-sm">{{ $payment->reference ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Method</p>
                    <p class="font-medium text-gray-800">{{ ucfirst($payment->method ?? 'N/A') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Type</p>
                    <p class="font-medium text-gray-800">
                        @if($payment->payable_type === 'App\\Models\\Book')
                            📚 Book Purchase
                        @elseif($payment->payable_type === 'App\\Models\\User')
                            💰 Wallet Deposit
                        @else
                            {{ $payment->payable_type ?? 'N/A' }}
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Transaction ID</p>
                    <p class="font-mono font-medium text-gray-800 text-sm">{{ $payment->transaction_id ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Info -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="ti ti-details text-purple-600"></i>
            Additional Information
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Payable Type</p>
                <p class="font-medium text-gray-800">{{ $payment->payable_type ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Payable ID</p>
                <p class="font-medium text-gray-800">{{ $payment->payable_id ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Created At</p>
                <p class="font-medium text-gray-800">{{ $payment->created_at->format('F d, Y H:i:s') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Last Updated</p>
                <p class="font-medium text-gray-800">{{ $payment->updated_at->format('F d, Y H:i:s') }}</p>
            </div>
        </div>

        @if($payment->metadata)
        <div class="mt-4">
            <p class="text-sm text-gray-500 mb-2">Metadata</p>
            <pre class="bg-gray-50 p-4 rounded-lg text-sm overflow-x-auto">{{ json_encode($payment->metadata, JSON_PRETTY_PRINT) }}</pre>
        </div>
        @endif
    </div>

    <!-- Actions -->
    <div class="mt-6 flex items-center gap-3">
        <a href="{{ route('super-admin.payments.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition text-sm">
            <i class="ti ti-arrow-left"></i> Back
        </a>
        
        @if($payment->status === 'pending')
        <form action="#" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition text-sm">
                <i class="ti ti-check"></i> Mark as Completed
            </button>
        </form>
        <form action="#" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition text-sm">
                <i class="ti ti-x"></i> Mark as Failed
            </button>
        </form>
        @endif
        
        @if($payment->status === 'completed')
        <form action="#" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to refund this payment?');">
            @csrf
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition text-sm">
                <i class="ti ti-rotate"></i> Refund
            </button>
        </form>
        @endif
    </div>
</div>
@endsection