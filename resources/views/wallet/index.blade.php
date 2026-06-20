@extends('layouts.app')

@section('title', 'My Wallet')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">💰 My Wallet</h1>
        <p class="text-gray-500 mt-1">Manage your funds and transactions</p>
    </div>
    
    <!-- Main Balance Card -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl shadow-xl p-6 mb-8 text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-purple-200 text-sm">Available Balance</p>
                <p class="text-4xl font-bold mt-1">{{ displayPrice($balance) }}</p>
                <p class="text-purple-200 text-xs mt-1">Ready to spend</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('payment.methods') }}" class="bg-white text-purple-600 px-4 py-2 rounded-lg font-semibold hover:bg-purple-50 transition shadow-md">
                    <i class="ti ti-plus"></i> Add Funds
                </a>
                @if($balance >= config('wallet.withdrawal.min_amount', 5000))
                <button onclick="openWithdrawModal()" class="bg-purple-800 text-white px-4 py-2 rounded-lg font-semibold hover:bg-purple-900 transition shadow-md">
                    <i class="ti ti-arrow-up"></i> Withdraw
                </button>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <p class="text-gray-500 text-sm">Total Deposited</p>
            <p class="text-2xl font-bold text-green-600">{{ displayPrice($deposits) }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <p class="text-gray-500 text-sm">Total Spent</p>
            <p class="text-2xl font-bold text-red-600">{{ displayPrice($purchases) }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <p class="text-gray-500 text-sm">Total Withdrawn</p>
            <p class="text-2xl font-bold text-orange-600">{{ displayPrice($withdrawals) }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <p class="text-gray-500 text-sm">Pending Withdrawal</p>
            <p class="text-2xl font-bold text-yellow-600">{{ displayPrice($pendingWithdrawals) }}</p>
        </div>
    </div>
    
    <!-- Available to Withdraw Card -->
    <div class="bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl p-4 mb-8 text-white">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-green-100 text-sm">Available to Withdraw</p>
                <p class="text-2xl font-bold">{{ displayPrice($availableToWithdraw) }}</p>
                <p class="text-green-100 text-xs mt-1">After pending withdrawals</p>
            </div>
            <i class="ti ti-wallet text-4xl text-green-200"></i>
        </div>
    </div>
    
   
    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4 mb-8">
        <div class="flex items-start gap-3">
            <i class="ti ti-info-circle text-blue-500 text-xl"></i>
            <div class="text-sm text-blue-800">
               
                <p>• Minimum withdrawal: {{ displayPrice(config('wallet.withdrawal.min_amount', 5000)) }}</p>
             
                <p>• Contact support for any payment issues</p>
            </div>
        </div>
    </div>
    
    <!-- Transaction History Section -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center flex-wrap gap-4">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="ti ti-history text-purple-600"></i>
                Transaction History
            </h2>
            <div class="flex flex-wrap gap-2">
                <button onclick="filterTransactions('all')" class="filter-btn px-3 py-1 rounded-lg text-sm bg-purple-600 text-white">All</button>
                <button onclick="filterTransactions('credit')" class="filter-btn px-3 py-1 rounded-lg text-sm bg-gray-200 text-gray-700">Deposits</button>
                <button onclick="filterTransactions('debit')" class="filter-btn px-3 py-1 rounded-lg text-sm bg-gray-200 text-gray-700">Payments</button>
                <button onclick="filterTransactions('withdrawal')" class="filter-btn px-3 py-1 rounded-lg text-sm bg-gray-200 text-gray-700">Withdrawals</button>
                <button onclick="filterTransactions('commission')" class="filter-btn px-3 py-1 rounded-lg text-sm bg-gray-200 text-gray-700">Commissions</button>
                <a href="{{ route('wallet.export') }}" class="px-3 py-1 rounded-lg text-sm bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                    <i class="ti ti-download"></i> Export
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Balance</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Invoice</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="transactions-table">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50 transaction-row" data-type="{{ $tx->type }}">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $tx->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">{{ $tx->description }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $tx->reference }}</p>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-semibold {{ $tx->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $tx->type === 'credit' ? '+' : '-' }} {{ displayPrice($tx->amount) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600">{{ displayPrice($tx->balance_after) }}</td>
                        <td class="px-6 py-4">
                            @if($tx->status === 'completed')
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✅ Completed</span>
                            @elseif($tx->status === 'pending')
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⏳ Pending</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">❌ Failed</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('invoices.transaction', $tx->id) }}" class="text-blue-600 hover:text-blue-800" title="Download Invoice" target="_blank">
                                <i class="ti ti-file-invoice"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="ti ti-wallet text-4xl mb-2 block"></i>
                            <p>No transactions yet</p>
                            <a href="{{ route('payment.methods') }}" class="text-purple-600 hover:text-purple-700 text-sm mt-2 inline-block">Add Funds →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $transactions->links() }}
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div id="withdrawModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-md w-full mx-auto overflow-hidden shadow-2xl">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-5 text-white">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold">Withdraw Funds</h3>
                <button onclick="closeWithdrawModal()" class="text-white/80 hover:text-white">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('wallet.withdraw') }}" class="p-6">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Amount (TSh)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">TSh</span>
                    <input type="number" name="amount" step="0.01" required 
                           min="{{ config('wallet.withdrawal.min_amount', 5000) }}"
                           max="{{ $balance }}"
                           class="w-full pl-14 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500"
                           placeholder="0.00">
                </div>
                <p class="text-xs text-gray-500 mt-1">Min: {{ displayPrice(config('wallet.withdrawal.min_amount', 5000)) }} | Max: {{ displayPrice($balance) }}</p>
                @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                <select name="payment_method" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 bg-white">
                    <option value="">Select Method</option>
                    <option value="mpesa">📱 M-Pesa</option>
                    <option value="bank">🏦 Bank Transfer</option>
                </select>
                @error('payment_method') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div id="mpesa_fields" style="display: none;">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" name="phone" value="{{ auth()->user()->mpesa_phone }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500"
                           placeholder="0712 345 678">
                    <p class="text-xs text-gray-500 mt-1">Enter the M-Pesa registered number</p>
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            
            <div id="bank_fields" style="display: none;">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ auth()->user()->bank_name }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500"
                           placeholder="CRDB, NMB, NBC">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                    <input type="text" name="account_number" value="{{ auth()->user()->bank_account_number }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500"
                           placeholder="0123456789">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Holder Name</label>
                    <input type="text" name="account_name" value="{{ auth()->user()->bank_account_name }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500"
                           placeholder="Full name on account">
                </div>
            </div>
            
            <div class="bg-yellow-50 rounded-lg p-3 mb-4 text-sm text-yellow-800">
                <i class="ti ti-alert-circle"></i> 
            </div>
            
            <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition">
                Submit Withdrawal Request
            </button>
        </form>
    </div>
</div>

<script>
function openWithdrawModal() {
    const modal = document.getElementById('withdrawModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeWithdrawModal() {
    const modal = document.getElementById('withdrawModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

// Show/hide fields based on payment method
const paymentMethodSelect = document.querySelector('select[name="payment_method"]');
if (paymentMethodSelect) {
    paymentMethodSelect.addEventListener('change', function() {
        const mpesaFields = document.getElementById('mpesa_fields');
        const bankFields = document.getElementById('bank_fields');
        
        if (this.value === 'mpesa') {
            if (mpesaFields) mpesaFields.style.display = 'block';
            if (bankFields) bankFields.style.display = 'none';
        } else if (this.value === 'bank') {
            if (mpesaFields) mpesaFields.style.display = 'none';
            if (bankFields) bankFields.style.display = 'block';
        } else {
            if (mpesaFields) mpesaFields.style.display = 'none';
            if (bankFields) bankFields.style.display = 'none';
        }
    });
}

// Filter transactions
function filterTransactions(type) {
    const rows = document.querySelectorAll('.transaction-row');
    const buttons = document.querySelectorAll('.filter-btn');
    
    buttons.forEach(btn => {
        btn.classList.remove('bg-purple-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    if (event && event.target) {
        event.target.classList.remove('bg-gray-200', 'text-gray-700');
        event.target.classList.add('bg-purple-600', 'text-white');
    }
    
    rows.forEach(row => {
        if (type === 'all') {
            row.style.display = '';
        } else {
            const rowType = row.getAttribute('data-type');
            if (rowType === type) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}

// Close modal on outside click
const modal = document.getElementById('withdrawModal');
if (modal) {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeWithdrawModal();
        }
    });
}

// Pre-fill phone number if user has saved M-Pesa number
const savedMpesaPhone = '{{ auth()->user()->mpesa_phone }}';
if (savedMpesaPhone) {
    const phoneInput = document.querySelector('#mpesa_fields input[name="phone"]');
    if (phoneInput && !phoneInput.value) {
        phoneInput.value = savedMpesaPhone;
    }
}
</script>

<style>
.rotate-180 {
    transform: rotate(180deg);
}
.transaction-row {
    transition: background-color 0.2s ease;
}
.filter-btn {
    transition: all 0.2s ease;
    cursor: pointer;
}
</style>
@endsection