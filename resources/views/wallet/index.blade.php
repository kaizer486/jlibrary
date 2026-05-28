@extends('layouts.app')

@section('title', 'My Wallet')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">My Wallet</h1>
        <p class="text-gray-500">Manage your funds and view transaction history</p>
    </div>
    
    <!-- Stats Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Available Balance Card -->
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-green-100 text-sm">Available Balance</p>
                    <p class="text-3xl font-bold mt-1">TSh {{ number_format($balance, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="ti ti-wallet text-2xl text-white"></i>
                </div>
            </div>
            <div class="flex gap-3 mt-4">
                <a href="{{ route('payment.methods') }}" class="flex-1 bg-white/20 hover:bg-white/30 text-white text-center py-2 rounded-xl text-sm transition">
                    <i class="ti ti-plus"></i> Add Funds
                </a>
                <button onclick="showWithdrawModal()" class="flex-1 bg-white/20 hover:bg-white/30 text-white text-center py-2 rounded-xl text-sm transition">
                    <i class="ti ti-arrow-up"></i> Withdraw
                </button>
            </div>
            <div class="mt-3 pt-3 border-t border-white/20 text-xs text-green-100 flex justify-between">
                <span>Secured by M-Pesa & Stripe</span>
                <span>ID: {{ substr(auth()->id(), 0, 8) }}</span>
            </div>
        </div>
        
        <!-- Total Deposits Card -->
        <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Total Deposits</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">TSh {{ number_format($totalDeposits ?? 0, 2) }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="ti ti-arrow-down-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Withdrawn Card -->
        <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Total Withdrawn</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">TSh {{ number_format($totalWithdrawn ?? 0, 2) }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="ti ti-arrow-up-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Pending Withdrawals Card -->
        <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Pending Withdrawals</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">TSh {{ number_format($pendingWithdrawals ?? 0, 2) }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="ti ti-clock text-amber-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Available to withdraw: TSh {{ number_format($availableToWithdraw ?? 0, 2) }}</p>
        </div>
    </div>
    
    <!-- Two Column Layout -->
    <div class="grid lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Add Funds -->
        <div class="lg:col-span-1">
            
            <!-- Add Funds Card -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden sticky top-24">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                    <h2 class="text-white font-bold text-lg flex items-center gap-2">
                        <i class="ti ti-plus-circle"></i> Add Funds
                    </h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('wallet.topup') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount (TSh)</label>
                            <input type="number" name="amount" required min="1000" max="1000000" id="topup-amount"
                                   placeholder="Enter amount"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        
                        <div class="grid grid-cols-4 gap-2 mb-5">
                            <button type="button" onclick="setTopupAmount(1000)" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition">1k</button>
                            <button type="button" onclick="setTopupAmount(5000)" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition">5k</button>
                            <button type="button" onclick="setTopupAmount(10000)" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition">10k</button>
                            <button type="button" onclick="setTopupAmount(20000)" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition">20k</button>
                        </div>
                        
                        <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition">
                            <i class="ti ti-arrow-right"></i> Continue to Payment
                        </button>
                    </form>
                    
                    <!-- Payment Logos -->
                    <div class="mt-6 pt-4 border-t text-center">
                        <p class="text-xs text-gray-400 mb-2">Secure payments powered by</p>
                        <div class="flex justify-center gap-4">
                            <span class="text-sm font-semibold text-green-600">M-Pesa</span>
                            <span class="text-sm font-semibold text-blue-600">TigoPesa</span>
                            <span class="text-sm font-semibold text-red-600">HaloPesa</span>
                            <span class="text-sm font-semibold text-purple-600">Visa/Mastercard</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-3">🔒 256-bit SSL Encrypted</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Transaction History -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="border-b px-6 py-4 bg-gray-50">
                    <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                        <i class="ti ti-history"></i> Transaction History
                    </h2>
                </div>
                
                <!-- Filter Tabs -->
                <div class="flex gap-2 px-6 py-3 border-b bg-white">
                    <button onclick="filterTransactions('all')" class="filter-btn active-filter px-4 py-1.5 rounded-full text-sm font-medium bg-purple-600 text-white transition">All</button>
                    <button onclick="filterTransactions('credit')" class="filter-btn px-4 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Deposits</button>
                    <button onclick="filterTransactions('debit')" class="filter-btn px-4 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Purchases</button>
                    <button onclick="filterTransactions('withdrawal')" class="filter-btn px-4 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Withdrawals</button>
                </div>
                
                <div class="divide-y" id="transactions-list">
                    @forelse($allTransactions as $tx)
                    <div class="transaction-item p-4 flex justify-between items-center hover:bg-gray-50 transition" data-type="{{ $tx->type ?? ($tx->status === 'withdrawal' ? 'debit' : $tx->type) }}">
                        <div class="flex items-center gap-3">
                            @if(($tx->type ?? '') === 'credit')
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="ti ti-arrow-down-circle text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Deposit via {{ ucfirst($tx->method ?? 'wallet') }}</p>
                                    <p class="text-xs text-gray-400">{{ $tx->created_at->format('M d, Y • h:i A') }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $tx->reference }}</p>
                                </div>
                            @elseif(($tx->type ?? '') === 'debit')
                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                    <i class="ti ti-arrow-up-circle text-red-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $tx->description ?? 'Purchase' }}</p>
                                    <p class="text-xs text-gray-400">{{ $tx->created_at->format('M d, Y • h:i A') }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $tx->reference }}</p>
                                </div>
                            @else
                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="ti ti-receipt text-gray-500"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Transaction</p>
                                    <p class="text-xs text-gray-400">{{ $tx->created_at->format('M d, Y') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="text-right">
                            @if(($tx->type ?? '') === 'credit')
                                <p class="font-bold text-green-600">+TSh {{ number_format($tx->amount, 2) }}</p>
                            @else
                                <p class="font-bold text-red-600">-TSh {{ number_format($tx->amount, 2) }}</p>
                            @endif
                            <span class="text-xs px-2 py-0.5 rounded-full 
                                @if(($tx->status ?? 'completed') === 'completed') bg-green-100 text-green-700
                                @elseif(($tx->status ?? '') === 'pending') bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($tx->status ?? 'completed') }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center text-gray-500">
                        <i class="ti ti-receipt text-5xl mb-3 block"></i>
                        <p>No transactions yet</p>
                        <a href="{{ route('payment.methods') }}" class="inline-block mt-3 text-purple-600 hover:underline">Add funds to get started →</a>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Recent Book Purchases Section -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden mt-6">
                <div class="border-b px-6 py-4 bg-gray-50">
                    <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                        <i class="ti ti-books"></i> Recent Book Purchases
                    </h2>
                </div>
                <div class="divide-y">
                    @php
                        $recentPurchases = \App\Models\UserBook::where('user_id', auth()->id())
                            ->whereNotNull('purchased_at')
                            ->with('book')
                            ->latest('purchased_at')
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @forelse($recentPurchases as $purchase)
                    <div class="p-4 flex justify-between items-center hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="ti ti-book text-purple-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $purchase->book->title ?? 'Unknown Book' }}</p>
                               <p class="text-xs text-gray-400">Purchased {{ \Carbon\Carbon::parse($purchase->purchased_at)->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <a href="{{ route('library.read', $purchase->book) }}" class="text-purple-600 text-sm hover:underline">Read Now →</a>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-500">
                        <i class="ti ti-bookmark text-4xl mb-2 block"></i>
                        <p>No book purchases yet</p>
                        <a href="{{ route('library.index') }}" class="inline-block mt-2 text-purple-600 hover:underline">Browse Library →</a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div id="withdrawModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl max-w-md w-full mx-4 overflow-hidden shadow-2xl">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-4 text-white">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold">Withdraw Funds</h3>
                <button onclick="closeWithdrawModal()" class="text-white/80 hover:text-white">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <form action="{{ route('wallet.withdraw') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount (TSh)</label>
                    <input type="number" name="amount" required min="100" max="{{ $availableToWithdraw }}"
                           placeholder="Enter amount"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                    <p class="text-xs text-gray-500 mt-1">Min: TSh 100 | Max: TSh {{ number_format($availableToWithdraw, 2) }}</p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Withdrawal Method</label>
                    <select name="method" id="withdraw-method-select" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500" required>
                        <option value="">Select method</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="bank">Bank Transfer</option>
                    </select>
                </div>
                
                <div id="withdraw-mpesa-fields" class="mb-4" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">M-Pesa Phone Number</label>
                    <input type="tel" name="phone" placeholder="0712 345 678"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                </div>
                
                <div id="withdraw-bank-fields" class="mb-4" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                    <input type="text" name="bank_name" placeholder="e.g., CRDB, NMB, NBC"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl mb-3">
                    
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                    <input type="text" name="account_number" placeholder="Your bank account number"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl mb-3">
                    
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Holder Name</label>
                    <input type="text" name="account_name" placeholder="Name on account"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                </div>
                
                <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition">
                    <i class="ti ti-send"></i> Request Withdrawal
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function setTopupAmount(amount) {
    document.getElementById('topup-amount').value = amount;
}

function showWithdrawModal() {
    document.getElementById('withdrawModal').classList.remove('hidden');
    document.getElementById('withdrawModal').classList.add('flex');
}

function closeWithdrawModal() {
    document.getElementById('withdrawModal').classList.add('hidden');
    document.getElementById('withdrawModal').classList.remove('flex');
}

// Toggle withdrawal method fields
document.getElementById('withdraw-method-select')?.addEventListener('change', function() {
    const mpesaFields = document.getElementById('withdraw-mpesa-fields');
    const bankFields = document.getElementById('withdraw-bank-fields');
    
    if (this.value === 'mpesa') {
        mpesaFields.style.display = 'block';
        bankFields.style.display = 'none';
    } else if (this.value === 'bank') {
        mpesaFields.style.display = 'none';
        bankFields.style.display = 'block';
    } else {
        mpesaFields.style.display = 'none';
        bankFields.style.display = 'none';
    }
});

function filterTransactions(type) {
    const items = document.querySelectorAll('.transaction-item');
    const buttons = document.querySelectorAll('.filter-btn');
    
    buttons.forEach(btn => {
        btn.classList.remove('bg-purple-600', 'text-white');
        btn.classList.add('bg-gray-100', 'text-gray-700');
    });
    
    event.target.classList.remove('bg-gray-100', 'text-gray-700');
    event.target.classList.add('bg-purple-600', 'text-white');
    
    items.forEach(item => {
        if (type === 'all') {
            item.style.display = 'flex';
        } else {
            if (item.dataset.type === type) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        }
    });
}

// Close modal on click outside
document.getElementById('withdrawModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeWithdrawModal();
    }
});
</script>

@endsection