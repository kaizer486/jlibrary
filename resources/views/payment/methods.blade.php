@extends('layouts.app')

@section('title', 'Add Funds')

@section('content')
<!-- Dark Blue Background -->
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('wallet.index') }}" class="text-indigo-300 hover:text-white transition">
                    <i class="ti ti-arrow-left text-2xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white">Add Funds</h1>
                    <p class="text-indigo-200 text-sm">Add money to your wallet securely</p>
                </div>
            </div>
        </div>

        <!-- Insufficient Balance Alert -->
        @if(isset($suggestedAmount) && $suggestedAmount > 0)
        <div class="bg-gradient-to-r from-amber-500/20 to-orange-500/20 border-l-4 border-amber-500 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <i class="ti ti-alert-circle text-amber-400 text-xl"></i>
                <div>
                    <p class="text-amber-300 font-semibold">Complete Your Purchase</p>
                    <p class="text-sm text-amber-200">You need TSh {{ number_format($suggestedAmount, 2) }} more to complete your book purchase.</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Grid -->
        <div class="grid lg:grid-cols-3 gap-6">
            
            <!-- LEFT COLUMN -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Current Balance Card -->
                <div class="bg-white rounded-2xl p-6 shadow-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">CURRENT BALANCE</p>
                            <p class="text-4xl font-bold text-gray-900 mt-1 wallet-balance">TSh {{ number_format(auth()->user()->wallet_balance ?? 0, 2) }}</p>
                            <p class="text-gray-400 text-xs mt-1">Available to spend</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 rounded-xl flex items-center justify-center shadow-md">
                            <i class="ti ti-wallet text-white text-xl"></i>
                        </div>
                    </div>
                    @php
                        $totalSpent = $totalSpent ?? 0;
                        $totalDeposits = $totalDeposits ?? 0;
                        $spentPercentage = $totalDeposits > 0 ? ($totalSpent / $totalDeposits) * 100 : 0;
                    @endphp
                    <div class="mt-4 pt-3 border-t border-gray-100">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Total Deposited: <span class="text-gray-800 font-semibold">TSh {{ number_format($totalDeposits, 2) }}</span></span>
                            <span>Spent: <span class="text-gray-800 font-semibold">{{ round($spentPercentage) }}%</span></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-pink-400 via-purple-500 to-indigo-500 h-2 rounded-full" style="width: {{ $spentPercentage }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Amount Selector -->
                <div class="bg-white rounded-2xl p-6 shadow-lg">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <div class="w-7 h-7 bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 rounded-lg flex items-center justify-center shadow-md">
                            <i class="ti ti-coin text-white text-sm"></i>
                        </div>
                        Select Amount
                    </h2>
                    
                    <div class="grid grid-cols-3 md:grid-cols-6 gap-3 mb-5">
                        <button onclick="setAmount(1000)" class="amount-preset py-2.5 px-3 bg-gray-100 hover:bg-gradient-to-r hover:from-pink-500 hover:via-purple-500 hover:to-indigo-500 text-gray-700 hover:text-white rounded-xl text-sm font-medium transition-all duration-300">TSh 1k</button>
                        <button onclick="setAmount(5000)" class="amount-preset py-2.5 px-3 bg-gray-100 hover:bg-gradient-to-r hover:from-pink-500 hover:via-purple-500 hover:to-indigo-500 text-gray-700 hover:text-white rounded-xl text-sm font-medium transition-all duration-300">TSh 5k</button>
                        <button onclick="setAmount(10000)" class="amount-preset py-2.5 px-3 bg-gray-100 hover:bg-gradient-to-r hover:from-pink-500 hover:via-purple-500 hover:to-indigo-500 text-gray-700 hover:text-white rounded-xl text-sm font-medium transition-all duration-300">TSh 10k</button>
                        <button onclick="setAmount(20000)" class="amount-preset py-2.5 px-3 bg-gray-100 hover:bg-gradient-to-r hover:from-pink-500 hover:via-purple-500 hover:to-indigo-500 text-gray-700 hover:text-white rounded-xl text-sm font-medium transition-all duration-300">TSh 20k</button>
                        <button onclick="setAmount(50000)" class="amount-preset py-2.5 px-3 bg-gray-100 hover:bg-gradient-to-r hover:from-pink-500 hover:via-purple-500 hover:to-indigo-500 text-gray-700 hover:text-white rounded-xl text-sm font-medium transition-all duration-300">TSh 50k</button>
                        <button onclick="setAmount(100000)" class="amount-preset py-2.5 px-3 bg-gray-100 hover:bg-gradient-to-r hover:from-pink-500 hover:via-purple-500 hover:to-indigo-500 text-gray-700 hover:text-white rounded-xl text-sm font-medium transition-all duration-300">TSh 100k</button>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Or enter custom amount</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">TSh</span>
                            <input type="number" id="custom-amount" 
                                   min="100" max="1000000"
                                   placeholder="Enter amount"
                                   class="w-full pl-14 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 text-lg">
                        </div>
                        <p class="text-xs text-gray-500 mt-2" id="amount-warning"></p>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="bg-white rounded-2xl p-6 shadow-lg">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <div class="w-7 h-7 bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 rounded-lg flex items-center justify-center shadow-md">
                            <i class="ti ti-credit-card text-white text-sm"></i>
                        </div>
                        Select Payment Method
                    </h2>
                    
                    <!-- Payment Methods Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                        
                        <!-- M-Pesa -->
                        <div onclick="selectPaymentMethod('mpesa', 'M-Pesa', 100, 500000, false)" 
                             class="payment-card p-4 rounded-xl border-2 border-gray-200 hover:border-green-500 cursor-pointer transition-all duration-300 text-center group bg-white" id="card-mpesa">
                            <div class="w-12 h-12 mx-auto bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-all duration-300 shadow-md">
                                <i class="ti ti-device-mobile text-xl text-white"></i>
                            </div>
                            <h3 class="font-bold text-gray-800 group-hover:text-green-600 transition">M-Pesa</h3>
                            <p class="text-xs text-gray-500">0% fee • Instant</p>
                        </div>
                        
                        <!-- TigoPesa -->
                        <div onclick="selectPaymentMethod('tigopesa', 'TigoPesa', 100, 500000, false)" 
                             class="payment-card p-4 rounded-xl border-2 border-gray-200 hover:border-blue-500 cursor-pointer transition-all duration-300 text-center group bg-white" id="card-tigopesa">
                            <div class="w-12 h-12 mx-auto bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-all duration-300 shadow-md">
                                <i class="ti ti-device-mobile text-xl text-white"></i>
                            </div>
                            <h3 class="font-bold text-gray-800 group-hover:text-blue-600 transition">TigoPesa</h3>
                            <p class="text-xs text-gray-500">0% fee • Instant</p>
                        </div>
                        
                        <!-- HaloPesa -->
                        <div onclick="selectPaymentMethod('halopesa', 'HaloPesa', 100, 500000, false)" 
                             class="payment-card p-4 rounded-xl border-2 border-gray-200 hover:border-orange-500 cursor-pointer transition-all duration-300 text-center group bg-white" id="card-halopesa">
                            <div class="w-12 h-12 mx-auto bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-all duration-300 shadow-md">
                                <i class="ti ti-device-mobile text-xl text-white"></i>
                            </div>
                            <h3 class="font-bold text-gray-800 group-hover:text-orange-600 transition">HaloPesa</h3>
                            <p class="text-xs text-gray-500">0% fee • Instant</p>
                        </div>
                        
                        <!-- Credit Card -->
                        <div onclick="selectPaymentMethod('card', 'Credit/Debit Card', 500, 1000000, false)" 
                             class="payment-card p-4 rounded-xl border-2 border-gray-200 hover:border-purple-500 cursor-pointer transition-all duration-300 text-center group bg-white" id="card-card">
                            <div class="w-12 h-12 mx-auto bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-all duration-300 shadow-md">
                                <i class="ti ti-credit-card text-xl text-white"></i>
                            </div>
                            <h3 class="font-bold text-gray-800 group-hover:text-purple-600 transition">Card</h3>
                            <p class="text-xs text-gray-500">2.5% fee • Instant</p>
                        </div>
                        
                        <!-- Bank Transfer -->
                        <div onclick="selectPaymentMethod('bank', 'Bank Transfer', 1000, 10000000, true)" 
                             class="payment-card p-4 rounded-xl border-2 border-gray-200 hover:border-gray-400 cursor-pointer transition-all duration-300 text-center group bg-white" id="card-bank">
                            <div class="w-12 h-12 mx-auto bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-all duration-300 shadow-md">
                                <i class="ti ti-building-bank text-xl text-white"></i>
                            </div>
                            <h3 class="font-bold text-gray-800 group-hover:text-gray-600 transition">Bank Transfer</h3>
                            <p class="text-xs text-gray-500">0% fee • 1-2 days</p>
                        </div>
                    </div>
                    
                    <!-- Payment Details Panel -->
                    <div id="payment-details-panel" class="hidden mt-6 pt-6 border-t border-gray-200">
                        <h3 class="font-semibold text-gray-800 mb-4">Payment Details</h3>
                        <div id="payment-details-content"></div>
                    </div>
                </div>
            </div>
            
            <!-- RIGHT COLUMN: Sidebar -->
            <div class="space-y-6">
                
                <!-- Quick Info -->
                <div class="bg-white rounded-2xl p-5 shadow-lg">
                    <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <div class="w-6 h-6 bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 rounded-lg flex items-center justify-center shadow-md">
                            <i class="ti ti-info-circle text-white text-xs"></i>
                        </div>
                        Quick Info
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between items-center py-1">
                            <span class="text-gray-500">Minimum deposit:</span>
                            <span class="font-semibold text-gray-800">TSh 100</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-gray-500">Maximum deposit:</span>
                            <span class="font-semibold text-gray-800">TSh 1,000,000</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-gray-500">Processing time:</span>
                            <span class="font-semibold text-green-600">Instant</span>
                        </div>
                        <div class="border-t border-gray-100 pt-2 mt-2">
                            <div class="flex justify-between items-center py-1">
                                <span class="text-gray-500">Mobile money fee:</span>
                                <span class="font-semibold text-green-600">0%</span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="text-gray-500">Card fee:</span>
                                <span class="font-semibold text-amber-600">2.5%</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Transactions -->
                <div class="bg-white rounded-2xl p-5 shadow-lg">
                    <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <div class="w-6 h-6 bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 rounded-lg flex items-center justify-center shadow-md">
                            <i class="ti ti-history text-white text-xs"></i>
                        </div>
                        Recent Activity
                    </h3>
                    <div class="space-y-3">
                        @forelse(($transactions ?? collect())->take(3) as $tx)
                        <div class="flex justify-between items-center text-sm border-b border-gray-100 pb-2 last:border-0">
                            <div>
                                <p class="font-medium text-gray-800">{{ ucfirst($tx->type) }}</p>
                                <p class="text-xs text-gray-400">{{ $tx->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold {{ $tx->type == 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $tx->type == 'credit' ? '+' : '-' }} TSh {{ number_format($tx->amount, 2) }}
                                </p>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500 text-center py-4">No recent transactions</p>
                        @endforelse
                    </div>
                    <a href="{{ route('wallet.index') }}" class="block text-center text-xs text-purple-600 mt-3 hover:text-pink-600 transition font-medium">View All →</a>
                </div>
                
                <!-- Trust Badges -->
                <div class="bg-white rounded-2xl p-5 shadow-lg text-center">
                    <div class="flex justify-center gap-4 mb-3">
                        <span class="text-xs font-semibold text-green-600">M-Pesa</span>
                        <span class="text-gray-400">•</span>
                        <span class="text-xs font-semibold text-blue-600">TigoPesa</span>
                        <span class="text-gray-400">•</span>
                        <span class="text-xs font-semibold text-orange-600">HaloPesa</span>
                        <span class="text-gray-400">•</span>
                        <span class="text-xs font-semibold text-purple-600">Visa/MC</span>
                    </div>
                    <div class="w-8 h-8 bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 rounded-full flex items-center justify-center mx-auto mb-2 shadow-md">
                        <i class="ti ti-lock text-white text-sm"></i>
                    </div>
                    <p class="text-xs text-gray-600">256-bit SSL Encrypted</p>
                    <p class="text-xs text-gray-400 mt-1">Your payment is secure and protected</p>
                </div>
            </div>
        </div>
        
        <!-- Saved Payment Details - Collapsible -->
        <div class="mt-6 bg-white rounded-2xl shadow-lg overflow-hidden">
            <button onclick="toggleSavedDetails()" class="w-full px-6 py-4 bg-gray-50 hover:bg-gray-100 transition-all duration-300 flex justify-between items-center">
                <span class="font-semibold text-gray-700 flex items-center gap-2">
                    <div class="w-6 h-6 bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 rounded-lg flex items-center justify-center shadow-md">
                        <i class="ti ti-device-floppy text-white text-xs"></i>
                    </div>
                    Saved Payment Details
                </span>
                <i id="toggle-icon" class="ti ti-chevron-down text-gray-500 transition-transform duration-300"></i>
            </button>
            <div id="saved-details-content" class="hidden p-6 border-t border-gray-200">
                <form id="paymentDetailsForm" class="grid md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">M-Pesa Number</label>
                        <input type="tel" name="mpesa_phone" value="{{ auth()->user()->mpesa_phone }}" 
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900"
                               placeholder="0712 345 678">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">TigoPesa Number</label>
                        <input type="tel" name="tigopesa_phone" value="{{ auth()->user()->tigopesa_phone }}" 
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900"
                               placeholder="0712 345 678">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">HaloPesa Number</label>
                        <input type="tel" name="halopesa_phone" value="{{ auth()->user()->halopesa_phone }}" 
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900"
                               placeholder="0712 345 678">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                        <input type="text" name="bank_name" value="{{ auth()->user()->bank_name }}" 
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900"
                               placeholder="CRDB, NMB, NBC">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                        <input type="text" name="bank_account_number" value="{{ auth()->user()->bank_account_number }}" 
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Holder</label>
                        <input type="text" name="bank_account_name" value="{{ auth()->user()->bank_account_name }}" 
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900">
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" class="bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 text-white px-6 py-2 rounded-lg hover:from-pink-600 hover:via-purple-600 hover:to-indigo-600 transition-all duration-300 shadow-md">
                            <i class="ti ti-device-floppy"></i> Save Details
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-md w-full mx-auto overflow-hidden shadow-2xl">
        <div class="bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 p-5 text-white">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold" id="modalTitle">Complete Payment</h3>
                <button onclick="closePaymentModal()" class="text-white/80 hover:text-white">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6" id="paymentModalContent">
            <div class="text-center py-8">
                <i class="ti ti-loader-2 animate-spin text-3xl text-pink-500"></i>
                <p class="text-gray-500 mt-2">Loading...</p>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-md w-full mx-auto overflow-hidden shadow-2xl text-center">
        <div class="p-6">
            <div class="w-20 h-20 bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-md">
                <i class="ti ti-circle-check text-4xl text-white"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Payment Successful! 🎉</h3>
            <p class="text-gray-600 mb-2" id="successAmount"></p>
            <p class="text-sm text-gray-500 mb-4" id="successBalance"></p>
            <div class="flex gap-3">
                <button onclick="closeSuccessAndGoToWallet()" class="flex-1 bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 text-white py-3 rounded-xl font-semibold hover:from-pink-600 hover:via-purple-600 hover:to-indigo-600 transition-all duration-300 shadow-md">
                    Go to Wallet
                </button>
                <button onclick="closeSuccessAndStay()" class="flex-1 border border-purple-400 text-purple-600 py-3 rounded-xl font-semibold hover:bg-purple-50 transition-all duration-300">
                    Add More
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.payment-card.selected {
    border-color: #a855f7 !important;
    background-color: #faf5ff !important;
    box-shadow: 0 4px 12px rgba(168, 85, 247, 0.15);
}
.amount-preset:active {
    transform: scale(0.97);
}
</style>

<script>
// ========== VARIABLES ==========
let selectedGateway = null;
let selectedGatewayName = null;
let selectedMinAmount = null;
let selectedMaxAmount = null;
let requiresApproval = false;

let suggestedAmount = {{ isset($suggestedAmount) ? $suggestedAmount : 0 }};

// ========== AMOUNT SELECTOR ==========
function setAmount(amount) {
    document.getElementById('custom-amount').value = amount;
    validateAmount();
}

function validateAmount() {
    const amount = parseFloat(document.getElementById('custom-amount').value);
    const warning = document.getElementById('amount-warning');
    
    if (isNaN(amount)) {
        warning.innerHTML = '';
        return false;
    }
    
    if (amount < 100) {
        warning.innerHTML = '⚠️ Minimum amount is TSh 100';
        warning.classList.add('text-red-500');
        return false;
    }
    
    if (amount > 1000000) {
        warning.innerHTML = '⚠️ Maximum amount is TSh 1,000,000';
        warning.classList.add('text-red-500');
        return false;
    }
    
    warning.innerHTML = '✓ Valid amount';
    warning.classList.remove('text-red-500');
    warning.classList.add('text-green-600');
    return true;
}

document.getElementById('custom-amount')?.addEventListener('input', validateAmount);

// ========== PAYMENT METHOD SELECTION ==========
function selectPaymentMethod(gateway, name, minAmount, maxAmount, requiresApprovalFlag) {
    document.querySelectorAll('.payment-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    const card = document.getElementById(`card-${gateway}`);
    if (card) card.classList.add('selected');
    
    selectedGateway = gateway;
    selectedGatewayName = name;
    selectedMinAmount = minAmount;
    selectedMaxAmount = maxAmount;
    requiresApproval = requiresApprovalFlag;
    
    showPaymentDetailsPanel();
}

function showPaymentDetailsPanel() {
    const panel = document.getElementById('payment-details-panel');
    const content = document.getElementById('payment-details-content');
    const amount = parseFloat(document.getElementById('custom-amount').value) || selectedMinAmount;
    
    let fee = 0;
    let total = amount;
    let feeText = '0% fee';
    
    if (selectedGateway === 'card') {
        fee = amount * 0.025;
        total = amount + fee;
        feeText = '2.5% processing fee';
    }
    
    let phoneField = '';
    if (selectedGateway === 'mpesa' || selectedGateway === 'tigopesa' || selectedGateway === 'halopesa') {
        let savedPhone = '';
        if (selectedGateway === 'mpesa') savedPhone = '{{ auth()->user()->mpesa_phone ?? '' }}';
        if (selectedGateway === 'tigopesa') savedPhone = '{{ auth()->user()->tigopesa_phone ?? '' }}';
        if (selectedGateway === 'halopesa') savedPhone = '{{ auth()->user()->halopesa_phone ?? '' }}';
        
        phoneField = `
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="tel" id="phoneNumber" placeholder="0712 345 678" 
                       value="${savedPhone}"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 text-gray-900">
                <p class="text-xs text-gray-500 mt-1">Enter the number registered with ${selectedGatewayName}</p>
            </div>
        `;
    }
    
    content.innerHTML = `
        ${phoneField}
        
        <div class="bg-gray-50 rounded-xl p-4 mb-4">
            <div class="flex justify-between mb-2">
                <span class="text-gray-500">Amount</span>
                <span class="font-semibold text-gray-800">TSh ${amount.toLocaleString()}</span>
            </div>
            ${fee > 0 ? `
            <div class="flex justify-between mb-2">
                <span class="text-gray-500">Fee (${feeText})</span>
                <span class="text-amber-600">+ TSh ${fee.toLocaleString()}</span>
            </div>
            ` : ''}
            <div class="border-t border-gray-200 pt-2 mt-2">
                <div class="flex justify-between">
                    <span class="font-bold text-gray-800">Total to Pay</span>
                    <span class="font-bold text-pink-600 text-lg">TSh ${total.toLocaleString()}</span>
                </div>
            </div>
        </div>
        
        <div class="mb-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="save-method" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                <span class="text-sm text-gray-700">Save this payment method for future</span>
            </label>
        </div>
        
        <button onclick="processPayment()" 
                class="w-full bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 text-white py-3 rounded-xl font-semibold hover:from-pink-600 hover:via-purple-600 hover:to-indigo-600 transition-all duration-300 shadow-md">
            <i class="ti ti-arrow-right"></i> Pay TSh ${total.toLocaleString()}
        </button>
    `;
    
    panel.classList.remove('hidden');
}

// ========== PROCESS PAYMENT ==========
function processPayment() {
    const amount = parseFloat(document.getElementById('custom-amount').value);
    const phone = document.getElementById('phoneNumber')?.value;
    
    if (!amount || amount < selectedMinAmount || amount > selectedMaxAmount) {
        alert(`Please enter a valid amount between TSh ${selectedMinAmount.toLocaleString()} and TSh ${selectedMaxAmount.toLocaleString()}`);
        return;
    }
    
    if ((selectedGateway === 'mpesa' || selectedGateway === 'tigopesa' || selectedGateway === 'halopesa') && !phone) {
        alert('Please enter your phone number');
        return;
    }
    
    const modal = document.getElementById('paymentModal');
    const content = document.getElementById('paymentModalContent');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    content.innerHTML = `
        <div class="text-center py-8">
            <i class="ti ti-loader-2 animate-spin text-3xl text-pink-500"></i>
            <p class="text-gray-500 mt-2">Processing ${selectedGatewayName} payment...</p>
        </div>
    `;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/payment/initiate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            gateway: selectedGateway,
            amount: amount,
            phone: phone
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (requiresApproval || selectedGateway === 'bank') {
                content.innerHTML = `
                    <div class="text-center py-4">
                        <i class="ti ti-building-bank text-5xl text-green-500 mb-3"></i>
                        <h4 class="text-xl font-bold text-gray-800 mb-2">Bank Transfer Instructions</h4>
                        <div class="bg-gray-50 rounded-lg p-4 text-left mb-4">
                            <p class="font-semibold text-gray-800">Bank: ${data.bank_details?.bank_name || 'CRDB Bank'}</p>
                            <p class="text-gray-600">Account Name: ${data.bank_details?.account_name || 'JLIBRARY LTD'}</p>
                            <p class="text-gray-600">Account Number: ${data.bank_details?.account_number || '01-1234567890'}</p>
                            <p class="text-gray-500 text-sm mt-2">Reference: ${data.payment_id}</p>
                        </div>
                        <button onclick="closePaymentModal(); location.reload();" class="w-full bg-green-600 text-white py-2 rounded-lg">
                            I've Made the Transfer
                        </button>
                    </div>
                `;
            } else if (data.client_secret) {
                content.innerHTML = `<div class="text-center py-4"><p class="text-gray-600">Redirecting to secure payment...</p></div>`;
                setTimeout(() => {
                    window.location.href = `/stripe/checkout?client_secret=${data.client_secret}&payment_id=${data.payment_id}`;
                }, 1000);
            } else {
                content.innerHTML = `
                    <div class="text-center py-4">
                        <i class="ti ti-circle-check text-5xl text-green-500 mb-3"></i>
                        <p class="text-gray-800 mb-4">STK Push Sent!</p>
                        <p class="text-sm text-gray-600 mb-4">Check your phone and enter PIN to complete.</p>
                        <div id="payment-status" class="text-sm text-gray-500 mb-3">Waiting for confirmation...</div>
                    </div>
                `;
                
                let attempts = 0;
                const interval = setInterval(() => {
                    attempts++;
                    const statusDiv = document.getElementById('payment-status');
                    if (statusDiv) statusDiv.innerHTML = `Checking... (${attempts})`;
                    
                    fetch(`/payment/status/${data.payment_id}`)
                        .then(res => res.json())
                        .then(statusData => {
                            if (statusData.status === 'completed') {
                                clearInterval(interval);
                                if (statusDiv) statusDiv.innerHTML = '✅ Payment confirmed!';
                                showSuccessModal(amount);
                            } else if (attempts > 15) {
                                clearInterval(interval);
                                if (statusDiv) statusDiv.innerHTML = '⏰ Still waiting? Please check your phone.';
                            }
                        });
                }, 3000);
            }
        } else {
            content.innerHTML = `
                <div class="text-center py-8">
                    <i class="ti ti-circle-x text-3xl text-red-500"></i>
                    <p class="text-red-600 mt-2">${data.message}</p>
                    <button onclick="closePaymentModal()" class="mt-4 text-purple-600">Close</button>
                </div>
            `;
        }
    })
    .catch(error => {
        content.innerHTML = `
            <div class="text-center py-8">
                <i class="ti ti-circle-x text-3xl text-red-500"></i>
                <p class="text-gray-500 mt-2">Network error. Please try again.</p>
                <button onclick="closePaymentModal()" class="mt-4 text-purple-600">Close</button>
            </div>
        `;
    });
}

function showSuccessModal(amount) {
    closePaymentModal();
    const modal = document.getElementById('successModal');
    document.getElementById('successAmount').innerHTML = `TSh ${amount.toLocaleString()} added!`;
    document.getElementById('successBalance').innerHTML = `Your wallet has been updated`;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    setTimeout(() => {
        location.reload();
    }, 3000);
}

function closeSuccessAndGoToWallet() {
    window.location.href = '/wallet';
}

function closeSuccessAndStay() {
    document.getElementById('successModal').classList.add('hidden');
    location.reload();
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function toggleSavedDetails() {
    const content = document.getElementById('saved-details-content');
    const icon = document.getElementById('toggle-icon');
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}

// Save payment details
document.getElementById('paymentDetailsForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('{{ route("payment.save-details") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Payment details saved successfully!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving details');
    });
});

if (suggestedAmount > 0) {
    setAmount(suggestedAmount);
}
</script>
@endsection