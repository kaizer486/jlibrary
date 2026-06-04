@extends('layouts.admin')

@section('title', 'Request Withdrawal')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('institution.withdrawals.index') }}" class="text-purple-600 hover:text-purple-700">
            <i class="ti ti-arrow-left"></i> Back to Withdrawals
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
            <h1 class="text-xl font-bold text-white">💰 Request Withdrawal</h1>
            <p class="text-green-100 text-sm">Withdraw your institution's earnings</p>
        </div>
        
        <div class="p-6">
            <!-- Balance Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Available Balance:</span>
                    <span class="text-2xl font-bold text-green-600">TSh {{ number_format($wallet->balance, 2) }}</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-gray-600">Minimum Withdrawal:</span>
                    <span class="font-semibold">TSh {{ number_format($minWithdrawal, 2) }}</span>
                </div>
            </div>
            
            <form method="POST" action="{{ route('institution.withdrawals.store') }}">
                @csrf
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">TSh</span>
                            <input type="number" name="amount" step="0.01" required 
                                   class="w-full pl-14 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                   placeholder="0.00">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Minimum: TSh {{ number_format($minWithdrawal, 2) }}</p>
                        @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Payment Method <span class="text-red-500">*</span>
                        </label>
                        <select name="payment_method" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white">
                            <option value="">Select Method</option>
                            <option value="bank">🏦 Bank Transfer</option>
                            <option value="mpesa">📱 M-Pesa</option>
                            <option value="tigopesa">📱 Tigo Pesa</option>
                            <option value="halopesa">📱 HaloPesa</option>
                        </select>
                        @error('payment_method') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Account Details <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="account_details" required 
                               placeholder="e.g., 0712345678 (M-Pesa) or Bank Account Number"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <p class="text-xs text-gray-400 mt-1">Enter phone number for mobile money or account number for bank transfer</p>
                        @error('account_details') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                  placeholder="Any additional information..."></textarea>
                    </div>
                </div>
                
                <div class="flex gap-3 mt-8 pt-6 border-t">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold">
                        Submit Withdrawal Request
                    </button>
                    <a href="{{ route('institution.withdrawals.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection