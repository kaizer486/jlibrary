@extends('layouts.institution')

@section('title', 'Request Withdrawal')

@section('content')

@php
    // ==========================================
    // SECURITY CHECKS
    // ==========================================
    
    // Check if user belongs to an institution
    if (!auth()->user()->institution_id) {
        abort(403, 'You do not belong to any institution.');
    }
    
    // Check if institution exists
    if (!isset($institution) || !$institution) {
        abort(404, 'Institution not found.');
    }
    
    // Check if user has access to this institution
    if (auth()->user()->institution_id != $institution->id) {
        abort(403, 'You do not have access to this institution.');
    }
    
    // Check if user has permission to create withdrawals
    if (!auth()->user()->can('create', App\Models\WithdrawalRequest::class)) {
        abort(403, 'You do not have permission to request withdrawals.');
    }
    
    // Get wallet balance with null safety
    $balance = $wallet?->balance ?? 0;
    $minWithdrawal = $minWithdrawal ?? 0;
@endphp

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('institution.withdrawals.index') }}" class="text-purple-600 hover:text-purple-700 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Withdrawals
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
            <h1 class="text-xl font-bold text-white"> Request Withdrawal</h1>
            <p class="text-green-100 text-sm">Withdraw your institution's earnings</p>
        </div>
        
        <div class="p-6">
            <!-- ========================================== -->
            <!-- BALANCE INFO                                -->
            <!-- ========================================== -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <span class="text-gray-600 text-sm">Available Balance:</span>
                        <p class="text-2xl font-bold text-green-600">TSh {{ number_format($balance, 2) }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 text-sm">Minimum Withdrawal:</span>
                        <p class="text-2xl font-bold text-blue-600">TSh {{ number_format($minWithdrawal, 2) }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 text-sm">Maximum Withdrawal:</span>
                        <p class="text-2xl font-bold text-red-600">TSh {{ number_format($balance, 2) }}</p>
                    </div>
                </div>
            </div>
            
            <!-- ========================================== -->
            <!-- QUICK AMOUNT BUTTONS                       -->
            <!-- ========================================== -->
            @if($balance > 0)
            <div class="flex gap-3 mb-4 flex-wrap">
                <button type="button" onclick="setAmount({{ $minWithdrawal }})" class="text-sm bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-lg transition">
                    Min ({{ number_format($minWithdrawal, 0) }})
                </button>
                <button type="button" onclick="setAmount({{ $balance }})" class="text-sm bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded-lg transition">
                    Max ({{ number_format($balance, 0) }})
                </button>
                <button type="button" onclick="setAmount({{ $balance / 2 }})" class="text-sm bg-purple-100 hover:bg-purple-200 text-purple-700 px-3 py-1 rounded-lg transition">
                    50% ({{ number_format($balance / 2, 0) }})
                </button>
                <button type="button" onclick="setAmount({{ $balance * 0.25 }})" class="text-sm bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-3 py-1 rounded-lg transition">
                    25% ({{ number_format($balance * 0.25, 0) }})
                </button>
            </div>
            @endif
            
            <!-- ========================================== -->
            <!-- AMOUNT ERROR MESSAGE                       -->
            <!-- ========================================== -->
            <div id="amountError" class="hidden text-red-500 text-sm mt-1 mb-3"></div>
            
            <!-- ========================================== -->
            <!-- WITHDRAWAL FORM                            -->
            <!-- ========================================== -->
            <form method="POST" action="{{ route('institution.withdrawals.store') }}">
                @csrf
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">TSh</span>
                            <input type="number" name="amount" id="amount" step="0.01" required 
                                   min="{{ $minWithdrawal }}" 
                                   max="{{ $balance }}"
                                   class="w-full pl-14 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="0.00">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Minimum: TSh {{ number_format($minWithdrawal, 2) }} | Maximum: TSh {{ number_format($balance, 2) }}</p>
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
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <p class="text-xs text-gray-400 mt-1">Enter phone number for mobile money or account number for bank transfer</p>
                        @error('account_details') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                  placeholder="Any additional information..."></textarea>
                        @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                
                <div class="flex gap-3 mt-8 pt-6 border-t border-gray-200">
                    <button type="submit" id="submitBtn" class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="ti ti-send"></i> Submit Withdrawal Request
                    </button>
                    <a href="{{ route('institution.withdrawals.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- SCRIPTS FOR VALIDATION & QUICK AMOUNTS     -->
<!-- ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const errorDiv = document.getElementById('amountError');
    const submitBtn = document.getElementById('submitBtn');
    const min = {{ $minWithdrawal }};
    const max = {{ $balance }};
    
    // ==========================================
    // SET AMOUNT FUNCTION (for quick buttons)
    // ==========================================
    window.setAmount = function(value) {
        if (amountInput) {
            const roundedValue = Math.round(value * 100) / 100;
            amountInput.value = roundedValue;
            validateAmount(roundedValue);
            amountInput.focus();
        }
    };
    
    // ==========================================
    // VALIDATE AMOUNT FUNCTION
    // ==========================================
    function validateAmount(value) {
        const numValue = parseFloat(value);
        
        if (isNaN(numValue) || numValue <= 0) {
            errorDiv.textContent = ' Please enter a valid amount';
            errorDiv.classList.remove('hidden');
            submitBtn.disabled = true;
            return false;
        }
        
        if (numValue < min) {
            errorDiv.textContent = ' Minimum withdrawal is TSh ' + min.toFixed(2);
            errorDiv.classList.remove('hidden');
            submitBtn.disabled = true;
            return false;
        }
        
        if (numValue > max) {
            errorDiv.textContent = ' Amount exceeds available balance of TSh ' + max.toFixed(2);
            errorDiv.classList.remove('hidden');
            submitBtn.disabled = true;
            return false;
        }
        
        errorDiv.classList.add('hidden');
        submitBtn.disabled = false;
        return true;
    }
    
    // ==========================================
    // INPUT EVENT LISTENER
    // ==========================================
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            const value = parseFloat(this.value);
            validateAmount(value);
        });
        
        amountInput.addEventListener('blur', function() {
            const value = parseFloat(this.value);
            if (!isNaN(value) && value > 0) {
                const roundedValue = Math.round(value * 100) / 100;
                if (roundedValue !== value) {
                    this.value = roundedValue;
                    validateAmount(roundedValue);
                }
            }
        });
    }
});
</script>

@endsection