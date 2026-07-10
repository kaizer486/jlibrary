@extends('layouts.app')

@section('title', 'My Wallet')

@section('content')

<!-- ========================================== -->
<!-- LIGHT BISQUE BACKGROUND                    -->
<!-- ========================================== -->
<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div class="relative z-10 container mx-auto px-4 py-8 max-w-7xl">
    
    <!-- ========================================== -->
    <!-- HEADER                                     -->
    <!-- ========================================== -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
            <span class="bg-gradient-to-br from-orange-500 to-amber-500 p-2.5 rounded-xl shadow-lg shadow-orange-500/20">
                <i class="ti ti-wallet text-white text-2xl"></i>
            </span>
            My Wallet
        </h1>
        <p class="text-slate-600 mt-1">Manage your funds and transactions</p>
    </div>
    
    <!-- ========================================== -->
    <!-- MAIN BALANCE CARD                          -->
    <!-- ========================================== -->
    <div class="bg-gradient-to-r from-orange-600 to-amber-600 rounded-2xl shadow-lg border-2 border-orange-400/30 p-6 mb-8 text-white">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
            <div>
                <p class="text-orange-100 text-sm">Available Balance</p>
                <p class="text-4xl font-bold mt-1">{{ displayPrice($balance) }}</p>
                <p class="text-orange-100 text-xs mt-1">Ready to spend</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('payment.methods') }}" class="bg-white text-orange-600 px-4 py-2.5 rounded-xl font-semibold hover:shadow-lg transition border-2 border-white/30">
                    <i class="ti ti-plus"></i> Add Funds
                </a>
                @if($balance >= config('wallet.withdrawal.min_amount', 5000))
                <button onclick="openWithdrawModal()" class="bg-orange-800 text-white px-4 py-2.5 rounded-xl font-semibold hover:bg-orange-900 transition border-2 border-orange-700/30">
                    <i class="ti ti-arrow-up"></i> Withdraw
                </button>
                @endif
            </div>
        </div>
    </div>
    
    <!-- ========================================== -->
    <!-- STATS CARDS                                -->
    <!-- ========================================== -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-4 shadow-md border-2 border-slate-200/80 hover:shadow-lg transition">
            <p class="text-slate-500 text-sm">Total Deposited</p>
            <p class="text-2xl font-bold text-orange-600">{{ displayPrice($deposits) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-md border-2 border-slate-200/80 hover:shadow-lg transition">
            <p class="text-slate-500 text-sm">Total Spent</p>
            <p class="text-2xl font-bold text-amber-600">{{ displayPrice($purchases) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-md border-2 border-slate-200/80 hover:shadow-lg transition">
            <p class="text-slate-500 text-sm">Total Withdrawn</p>
            <p class="text-2xl font-bold text-orange-600">{{ displayPrice($withdrawals) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-md border-2 border-slate-200/80 hover:shadow-lg transition">
            <p class="text-slate-500 text-sm">Pending Withdrawal</p>
            <p class="text-2xl font-bold text-amber-600">{{ displayPrice($pendingWithdrawals) }}</p>
        </div>
    </div>
    
    <!-- ========================================== -->
    <!-- AVAILABLE TO WITHDRAW CARD                 -->
    <!-- ========================================== -->
    <div class="bg-gradient-to-r from-orange-500 to-amber-500 rounded-xl p-4 mb-8 text-white shadow-lg border-2 border-orange-400/30">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-orange-100 text-sm">Available to Withdraw</p>
                <p class="text-2xl font-bold">{{ displayPrice($availableToWithdraw) }}</p>
                <p class="text-orange-100 text-xs mt-1">After pending withdrawals</p>
            </div>
            <i class="ti ti-wallet text-4xl text-orange-200"></i>
        </div>
    </div>
    
    <!-- ========================================== -->
    <!-- INFO BOX                                   -->
    <!-- ========================================== -->
    <div class="bg-blue-50 border-2 border-blue-200/80 rounded-xl p-4 mb-8 shadow-sm">
        <div class="flex items-start gap-3">
            <i class="ti ti-info-circle text-blue-500 text-xl mt-0.5"></i>
            <div class="text-sm text-blue-800">
                <p>• Minimum withdrawal: {{ displayPrice(config('wallet.withdrawal.min_amount', 5000)) }}</p>
                <p>• Contact support for any payment issues</p>
            </div>
        </div>
    </div>
    
    <!-- ========================================== -->
    <!-- TRANSACTION HISTORY CARD                   -->
    <!-- ========================================== -->
    <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 overflow-hidden">
        <div class="px-6 py-4 border-b-2 border-slate-200 flex justify-between items-center flex-wrap gap-4 bg-orange-50/60">
            <h2 class="text-xl font-semibold text-slate-800 flex items-center gap-2">
                <i class="ti ti-history text-orange-500"></i>
                Transaction History
            </h2>
            <div class="flex flex-wrap gap-2">
                <button onclick="filterTransactions('all')" class="filter-btn px-3 py-1.5 rounded-lg text-sm font-medium bg-orange-600 text-white border-2 border-orange-400/30">All</button>
                <button onclick="filterTransactions('credit')" class="filter-btn px-3 py-1.5 rounded-lg text-sm font-medium bg-white text-slate-700 border-2 border-slate-200/80 hover:bg-orange-50 transition">Deposits</button>
                <button onclick="filterTransactions('debit')" class="filter-btn px-3 py-1.5 rounded-lg text-sm font-medium bg-white text-slate-700 border-2 border-slate-200/80 hover:bg-orange-50 transition">Payments</button>
                <button onclick="filterTransactions('withdrawal')" class="filter-btn px-3 py-1.5 rounded-lg text-sm font-medium bg-white text-slate-700 border-2 border-slate-200/80 hover:bg-orange-50 transition">Withdrawals</button>
                <button onclick="filterTransactions('commission')" class="filter-btn px-3 py-1.5 rounded-lg text-sm font-medium bg-white text-slate-700 border-2 border-slate-200/80 hover:bg-orange-50 transition">Commissions</button>
                <a href="{{ route('wallet.export') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium bg-white text-slate-700 border-2 border-slate-200/80 hover:bg-orange-50 transition">
                    <i class="ti ti-download"></i> Export
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-orange-50/40 border-b-2 border-slate-200">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-medium text-slate-500 uppercase">Date</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-slate-500 uppercase">Description</th>
                        <th class="text-right px-6 py-3 text-xs font-medium text-slate-500 uppercase">Amount</th>
                        <th class="text-right px-6 py-3 text-xs font-medium text-slate-500 uppercase">Balance</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-slate-500 uppercase">Invoice</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200" id="transactions-table">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-orange-50/50 transition transaction-row" data-type="{{ $tx->type }}">
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $tx->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800">{{ $tx->description }}</p>
                            <p class="text-xs text-slate-400 font-mono">{{ $tx->reference }}</p>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-semibold {{ $tx->type === 'credit' ? 'text-orange-600' : 'text-red-600' }}">
                                {{ $tx->type === 'credit' ? '+' : '-' }} {{ displayPrice($tx->amount) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-slate-600">{{ displayPrice($tx->balance_after) }}</td>
                        <td class="px-6 py-4">
                            @if($tx->status === 'completed')
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700 border border-orange-200">✅ Completed</span>
                            @elseif($tx->status === 'pending')
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 border border-yellow-200">⏳ Pending</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 border border-red-200">❌ Failed</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('invoices.transaction', $tx->id) }}" class="text-blue-600 hover:text-blue-800 transition" title="Download Invoice" target="_blank">
                                <i class="ti ti-file-invoice"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <i class="ti ti-wallet text-4xl mb-2 block text-orange-400/30"></i>
                            <p>No transactions yet</p>
                            <a href="{{ route('payment.methods') }}" class="text-orange-600 hover:text-orange-700 text-sm mt-2 inline-block font-medium">Add Funds →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t-2 border-slate-200/60 bg-white">
            {{ $transactions->links() }}
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- WITHDRAW MODAL                             -->
<!-- ========================================== -->
<div id="withdrawModal" class="fixed inset-0 bg-black/30 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white/95 backdrop-blur-sm rounded-2xl max-w-md w-full mx-auto overflow-hidden shadow-2xl border border-white/60">
        <div class="bg-gradient-to-r from-orange-600 to-amber-600 p-5 text-white border-b-2 border-orange-400/30">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold flex items-center gap-2">
                    <i class="ti ti-arrow-up-circle"></i> Withdraw Funds
                </h3>
                <button onclick="closeWithdrawModal()" class="text-white/80 hover:text-white transition">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('wallet.withdraw') }}" class="p-6">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Amount (TSh)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500 font-medium">TSh</span>
                    <input type="number" name="amount" step="0.01" required 
                           min="{{ config('wallet.withdrawal.min_amount', 5000) }}"
                           max="{{ $balance }}"
                           class="w-full pl-14 pr-4 py-3 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800"
                           placeholder="0.00">
                </div>
                <p class="text-xs text-slate-500 mt-1">Min: {{ displayPrice(config('wallet.withdrawal.min_amount', 5000)) }} | Max: {{ displayPrice($balance) }}</p>
                @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Payment Method</label>
                <select name="payment_method" required class="w-full px-4 py-3 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800">
                    <option value="">Select Method</option>
                    <option value="mpesa">📱 M-Pesa</option>
                    <option value="bank">🏦 Bank Transfer</option>
                </select>
                @error('payment_method') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div id="mpesa_fields" style="display: none;">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Phone Number</label>
                    <input type="tel" name="phone" value="{{ auth()->user()->mpesa_phone }}" class="w-full px-4 py-3 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800"
                           placeholder="0712 345 678">
                    <p class="text-xs text-slate-500 mt-1">Enter the M-Pesa registered number</p>
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            
            <div id="bank_fields" style="display: none;">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ auth()->user()->bank_name }}" class="w-full px-4 py-3 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800"
                           placeholder="CRDB, NMB, NBC">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Account Number</label>
                    <input type="text" name="account_number" value="{{ auth()->user()->bank_account_number }}" class="w-full px-4 py-3 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800"
                           placeholder="0123456789">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Account Holder Name</label>
                    <input type="text" name="account_name" value="{{ auth()->user()->bank_account_name }}" class="w-full px-4 py-3 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800"
                           placeholder="Full name on account">
                </div>
            </div>
            
            <div class="bg-amber-50 border-2 border-amber-200/80 rounded-xl p-3 mb-4 text-sm text-amber-800 shadow-sm">
                <i class="ti ti-alert-circle"></i> Withdrawals are processed within 24-48 hours
            </div>
            
            <button type="submit" class="w-full bg-gradient-to-r from-orange-600 to-amber-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-orange-600/25 transition border-2 border-orange-400/30">
                <i class="ti ti-send"></i> Submit Withdrawal Request
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
        document.body.style.overflow = 'hidden';
    }
}

function closeWithdrawModal() {
    const modal = document.getElementById('withdrawModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
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
        btn.classList.remove('bg-orange-600', 'text-white', 'border-orange-400/30');
        btn.classList.add('bg-white', 'text-slate-700', 'border-slate-200/80');
    });
    
    if (event && event.target) {
        event.target.classList.remove('bg-white', 'text-slate-700', 'border-slate-200/80');
        event.target.classList.add('bg-orange-600', 'text-white', 'border-orange-400/30');
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