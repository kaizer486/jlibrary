@extends('layouts.author')

@section('title', 'Request Withdrawal')
@section('page-title', 'Request Withdrawal')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Request Withdrawal</h2>
        
        <div class="bg-purple-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-purple-700">Available Balance: <span class="font-bold text-lg">TSh {{ number_format($availableBalance ?? 0, 2) }}</span></p>
        </div>

        <form action="{{ route('author.withdrawals.store') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (TSh)</label>
                    <input type="number" name="amount" min="1000" max="{{ $availableBalance ?? 0 }}" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('amount')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select name="payment_method" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="mpesa">M-Pesa</option>
                        <option value="tigopesa">Tigo Pesa</option>
                        <option value="halopesa">Halo Pesa</option>
                        <option value="bank">Bank Transfer</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Details (Phone/Account Number)</label>
                    <input type="text" name="payment_details" required placeholder="e.g. 0712345678"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('payment_details')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full btn-author py-3 justify-center">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>
@endsection