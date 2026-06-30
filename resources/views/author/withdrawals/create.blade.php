@extends('layouts.app')

@section('title', 'Request Withdrawal')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-2xl p-6 mb-8 text-white">
        <div class="flex items-center gap-2 mb-2">
            <i class="ti ti-arrow-up-circle text-3xl"></i>
            <h1 class="text-2xl font-bold">Request Withdrawal</h1>
        </div>
        <p class="text-yellow-100">Withdraw your earnings to your preferred payment method</p>
    </div>

    <!-- Balance Card -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Available Balance</p>
                <p class="text-3xl font-bold text-green-600">TSh {{ number_format($balance, 2) }}</p>
            </div>
            <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                <i class="ti ti-wallet text-2xl text-green-600"></i>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-2">Minimum withdrawal: TSh 1,000.00</p>
    </div>

    <!-- Withdrawal Form -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('author.withdrawals.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <!-- Amount -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Amount (TSh) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">TSh</span>
                        <input type="number" name="amount" required min="1000" max="{{ $balance }}"
                               step="100" value="{{ old('amount') }}"
                               class="w-full pl-16 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                               placeholder="0.00">
                    </div>
                    @error('amount')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">Maximum: TSh {{ number_format($balance, 2) }}</p>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Payment Method *</label>
                    <select name="payment_method" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        <option value="">Select Method</option>
                        <option value="mpesa" {{ old('payment_method') == 'mpesa' ? 'selected' : '' }}>📱 M-Pesa</option>
                        <option value="tigopesa" {{ old('payment_method') == 'tigopesa' ? 'selected' : '' }}>📱 Tigo Pesa</option>
                        <option value="halopesa" {{ old('payment_method') == 'halopesa' ? 'selected' : '' }}>📱 Halo Pesa</option>
                        <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>🏦 Bank Transfer</option>
                    </select>
                    @error('payment_method')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Details -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Account Details *</label>
                    <input type="text" name="account_details" required 
                           value="{{ old('account_details') }}"
                           placeholder="Phone number or bank account details"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    @error('account_details')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">
                        For M-Pesa/Tigo/Halo: Enter phone number (e.g., 0712345678)
                        <br>For Bank: Enter account number and bank name
                    </p>
                </div>

                <!-- Info Box -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start gap-2">
                        <i class="ti ti-info-circle text-yellow-600 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-yellow-800">Important</p>
                            <ul class="text-xs text-yellow-700 space-y-1 mt-1">
                                <li>• Withdrawals are processed within 24-48 hours</li>
                                <li>• Minimum withdrawal amount is TSh 1,000.00</li>
                                <li>• A small processing fee may apply</li>
                                <li>• Ensure your account details are correct</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-yellow-500 to-orange-500 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold">
                        <i class="ti ti-send"></i> Submit Withdrawal
                    </button>
                    <a href="{{ route('author.withdrawals.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection