@extends('layouts.app')

@section('title', 'Purchase Successful')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen flex items-center justify-center py-8">
    <div class="container mx-auto px-4 max-w-2xl">

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-6 text-center">
                <i class="ti ti-check-circle text-6xl text-white mb-2 block"></i>
                <h1 class="text-3xl font-bold text-white">Purchase Successful!</h1>
                <p class="text-green-100 mt-1">Thank you for your purchase</p>
            </div>

            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Book</span>
                        <span class="font-semibold">{{ $payment->book->title }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Amount Paid</span>
                        <span class="font-semibold text-green-600">TSh {{ number_format($payment->amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Payment Method</span>
                        <span class="font-semibold capitalize">{{ $payment->payment_method }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Transaction ID</span>
                        <span class="font-semibold text-sm">{{ $payment->transaction_id }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Date</span>
                        <span class="font-semibold">{{ $payment->created_at->format('F d, Y h:i A') }}</span>
                    </div>
                </div>

                <div class="flex gap-3 mt-6 pt-6 border-t">
                    <a href="{{ route('library.public.show', [$payment->book->institution_id, $payment->book->id]) }}" 
                       class="flex-1 bg-purple-600 text-white px-4 py-3 rounded-lg hover:bg-purple-700 transition text-center font-semibold">
                        <i class="ti ti-book"></i> Read Book
                    </a>
                    <a href="{{ route('library.purchase.history') }}" 
                       class="flex-1 border border-gray-300 px-4 py-3 rounded-lg hover:bg-gray-50 transition text-center font-semibold">
                        <i class="ti ti-history"></i> My Purchases
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('dashboard') }}" class="text-purple-300 hover:text-purple-200 transition inline-flex items-center gap-2">
                <i class="ti ti-dashboard"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection