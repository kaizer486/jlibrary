@extends('layouts.app')

@section('title', 'Add Funds')

@section('content')
<!-- Dark Blue Background -->
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

@php
    use App\Http\Middleware\CurrencyMiddleware;
    $userCurrency = CurrencyMiddleware::getCurrency();
    $exchangeRate = CurrencyMiddleware::getRate();
    $isLocalUser = ($userCurrency === 'TZS');
@endphp

<div class="relative z-10 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        
      <!-- Currency Info Banner -->
        @if(!$isLocalUser)
        <div class="bg-gradient-to-r from-blue-500/20 to-purple-500/20 border-l-4 border-blue-500 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <i class="ti ti-world text-blue-400 text-xl"></i>
                <div>
                    <p class="text-blue-300 font-semibold">International Payment</p>
                    <p class="text-sm text-blue-200">You are viewing prices in {{ $userCurrency }}. Exchange rate: 1 USD = {{ number_format($exchangeRate, 2) }} TZS</p>
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
                            <p class="text-4xl font-bold text-gray-900 mt-1 wallet-balance">{{ displayPrice(auth()->user()->wallet_balance ?? 0) }}</p>
                            <p class="text-gray-400 text-xs mt-1">Available to spend</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 rounded-xl flex items-center justify-center shadow-md">
                            <i class="ti ti-wallet text-white text-xl"></i>
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
                        <button onclick="setAmount(1000)" class="amount-preset py-2.5 px-3 bg-gray-100 hover:bg-gradient-to-r hover:from-pink-500 hover:via-purple-500 hover:to-indigo-500 text-gray-700 hover:text-white rounded-xl text-sm font-medium transition-all duration-300">{{ $isLocalUser ? 'TSh 1k' : '$0.40' }}</button>
                        <button onclick="setAmount(5000)" class="amount-preset py-2.5 px-3 bg-gray-100 hover:bg-gradient-to-r hover:from-pink-500 hover:via-purple-500 hover:to-indigo-500 text-gray-700 hover:text-white rounded-xl text-sm font-medium transition-all duration-300">{{ $isLocalUser ? 'TSh 5k' : '$2.00' }}</button>
                        <button onclick="setAmount(10000)" class="amount-preset py-2.5 px-3 bg-gray-100 hover:bg-gradient-to-r hover:from-pink-500 hover:via-purple-500 hover:to-indigo-500 text-gray-700 hover:text-white rounded-xl text-sm font-medium transition-all duration-300">{{ $isLocalUser ? 'TSh 10k' : '$4.00' }}</button>
                        <button onclick="setAmount(20000)" class="amount-preset py-2.5 px-3 bg-gray-100 hover:bg-gradient-to-r hover:from-pink-500 hover:via-purple-500 hover:to-indigo-500 text-gray-700 hover:text-white rounded-xl text-sm font-medium transition-all duration-300">{{ $isLocalUser ? 'TSh 20k' : '$8.00' }}</button>
                        <button onclick="setAmount(50000)" class="amount-preset py-2.5 px-3 bg-gray-100 hover:bg-gradient-to-r hover:from-pink-500 hover:via-purple-500 hover:to-indigo-500 text-gray-700 hover:text-white rounded-xl text-sm font-medium transition-all duration-300">{{ $isLocalUser ? 'TSh 50k' : '$20.00' }}</button>
                        <button onclick="setAmount(100000)" class="amount-preset py-2.5 px-3 bg-gray-100 hover:bg-gradient-to-r hover:from-pink-500 hover:via-purple-500 hover:to-indigo-500 text-gray-700 hover:text-white rounded-xl text-sm font-medium transition-all duration-300">{{ $isLocalUser ? 'TSh 100k' : '$40.00' }}</button>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Or enter custom amount</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">{{ $isLocalUser ? 'TSh' : '$' }}</span>
                            <input type="number" id="custom-amount" 
                                   min="{{ $isLocalUser ? 100 : 1 }}" 
                                   max="{{ $isLocalUser ? 1000000 : 400 }}"
                                   placeholder="Enter amount"
                                   class="w-full pl-14 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 text-lg">
                        </div>
                        <p class="text-xs text-gray-500 mt-2" id="amount-warning"></p>
                    </div>
                </div>

                <!-- Payment Methods Grid -->
                <div class="bg-white rounded-2xl p-6 shadow-lg">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <div class="w-7 h-7 bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 rounded-lg flex items-center justify-center shadow-md">
                            <i class="ti ti-credit-card text-white text-sm"></i>
                        </div>
                        Select Payment Method
                    </h2>
                    
                    @if($isLocalUser)
                    <!-- LOCAL PAYMENT METHODS -->
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-gray-500 mb-2">Local Payments (TZS)</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <!-- M-Pesa -->
                            <button onclick="openPaymentModal('mpesa', 'M-Pesa', 100, 500000, false)" 
                                    class="payment-card p-4 rounded-xl border-2 border-gray-200 hover:border-green-500 cursor-pointer transition-all duration-300 text-center group bg-white">
                                <div class="w-12 h-12 mx-auto bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-all duration-300 shadow-md">
                                    <i class="ti ti-device-mobile text-xl text-white"></i>
                                </div>
                                <h3 class="font-bold text-gray-800 group-hover:text-green-600 transition">M-Pesa</h3>
                                <p class="text-xs text-gray-500">0% fee • Instant</p>
                            </button>
                            
                            <!-- TigoPesa -->
                            <button onclick="openPaymentModal('tigopesa', 'TigoPesa', 100, 500000, false)" 
                                    class="payment-card p-4 rounded-xl border-2 border-gray-200 hover:border-blue-500 cursor-pointer transition-all duration-300 text-center group bg-white">
                                <div class="w-12 h-12 mx-auto bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-all duration-300 shadow-md">
                                    <i class="ti ti-device-mobile text-xl text-white"></i>
                                </div>
                                <h3 class="font-bold text-gray-800 group-hover:text-blue-600 transition">TigoPesa</h3>
                                <p class="text-xs text-gray-500">0% fee • Instant</p>
                            </button>
                            
                            <!-- HaloPesa -->
                            <button onclick="openPaymentModal('halopesa', 'HaloPesa', 100, 500000, false)" 
                                    class="payment-card p-4 rounded-xl border-2 border-gray-200 hover:border-orange-500 cursor-pointer transition-all duration-300 text-center group bg-white">
                                <div class="w-12 h-12 mx-auto bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-all duration-300 shadow-md">
                                    <i class="ti ti-device-mobile text-xl text-white"></i>
                                </div>
                                <h3 class="font-bold text-gray-800 group-hover:text-orange-600 transition">HaloPesa</h3>
                                <p class="text-xs text-gray-500">0% fee • Instant</p>
                            </button>
                            
                            <!-- Bank Transfer -->
                            <button onclick="openPaymentModal('bank', 'Bank Transfer', 1000, 10000000, true)" 
                                    class="payment-card p-4 rounded-xl border-2 border-gray-200 hover:border-gray-500 cursor-pointer transition-all duration-300 text-center group bg-white">
                                <div class="w-12 h-12 mx-auto bg-gradient-to-br from-gray-500 to-gray-600 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-all duration-300 shadow-md">
                                    <i class="ti ti-building-bank text-xl text-white"></i>
                                </div>
                                <h3 class="font-bold text-gray-800 group-hover:text-gray-600 transition">Bank Transfer</h3>
                                <p class="text-xs text-gray-500">0% fee • 1-2 days</p>
                            </button>
                        </div>
                    </div>
                    
                    <!-- INTERNATIONAL PAYMENT METHODS -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 mb-2">International Payments (USD)</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <!-- Credit Card (Stripe) -->
                            <button onclick="openPaymentModal('card', 'Credit/Debit Card', 500, 1000000, false)" 
                                    class="payment-card p-4 rounded-xl border-2 border-gray-200 hover:border-purple-500 cursor-pointer transition-all duration-300 text-center group bg-white">
                                <div class="w-12 h-12 mx-auto bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-all duration-300 shadow-md">
                                    <i class="ti ti-credit-card text-xl text-white"></i>
                                </div>
                                <h3 class="font-bold text-gray-800 group-hover:text-purple-600 transition">Credit Card</h3>
                                <p class="text-xs text-gray-500">2.5% fee • Instant</p>
                            </button>
                            
                            <!-- Pesapal -->
                            <button onclick="openPaymentModal('pesapal', 'PesaPal', 100, 10000000, false)" 
                                    class="payment-card p-4 rounded-xl border-2 border-gray-200 hover:border-indigo-500 cursor-pointer transition-all duration-300 text-center group bg-white">
                                <div class="w-12 h-12 mx-auto bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-all duration-300 shadow-md">
                                    <i class="ti ti-world text-xl text-white"></i>
                                </div>
                                <h3 class="font-bold text-gray-800 group-hover:text-indigo-600 transition">PesaPal</h3>
                                <p class="text-xs text-gray-500">Card & Mobile Money</p>
                            </button>
                        </div>
                    </div>
                    @else
                    <!-- INTERNATIONAL USERS -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <button onclick="openPaymentModal('card', 'Credit/Debit Card', 1, 400, false)" 
                                class="payment-card p-4 rounded-xl border-2 border-gray-200 hover:border-purple-500 cursor-pointer transition-all duration-300 text-center group bg-white">
                            <div class="w-12 h-12 mx-auto bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-all duration-300 shadow-md">
                                <i class="ti ti-credit-card text-xl text-white"></i>
                            </div>
                            <h3 class="font-bold text-gray-800 group-hover:text-purple-600 transition">Credit Card</h3>
                            <p class="text-xs text-gray-500">2.5% fee • Instant</p>
                        </button>
                        
                        <button onclick="openPaymentModal('pesapal', 'PesaPal', 1, 4000, false)" 
                                class="payment-card p-4 rounded-xl border-2 border-gray-200 hover:border-indigo-500 cursor-pointer transition-all duration-300 text-center group bg-white">
                            <div class="w-12 h-12 mx-auto bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-all duration-300 shadow-md">
                                <i class="ti ti-world text-xl text-white"></i>
                            </div>
                            <h3 class="font-bold text-gray-800 group-hover:text-indigo-600 transition">PesaPal</h3>
                            <p class="text-xs text-gray-500">Card & Mobile Money</p>
                        </button>
                    </div>
                    @endif
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
                            <span class="font-semibold text-gray-800">{{ $isLocalUser ? 'TSh 100' : '$1.00' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-gray-500">Maximum deposit:</span>
                            <span class="font-semibold text-gray-800">{{ $isLocalUser ? 'TSh 1,000,000' : '$400.00' }}</span>
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
                                    {{ $tx->type == 'credit' ? '+' : '-' }} {{ displayPrice($tx->amount) }}
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
            
                        <span class="text-xs font-semibold text-blue-600">TigoPesa</span>
                        
                        <span class="text-xs font-semibold text-orange-600">HaloPesa</span>
                        
                        <span class="text-xs font-semibold text-purple-600">Visa</span>
                    
                        <span class="text-xs font-semibold text-indigo-600">PesaPal</span>
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

<!-- ========================================== -->
<!-- PAYMENT MODAL (POPUP) - Appears when user clicks any payment method -->
<!-- ========================================== -->
<div id="paymentModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-md w-full mx-auto overflow-hidden shadow-2xl animate-fadeInUp">
        <div class="bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 p-5 text-white">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold" id="modalTitle">Complete Payment</h3>
                <button onclick="closePaymentModal()" class="text-white/80 hover:text-white transition">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6" id="paymentModalContent">
            <!-- Dynamic content will be loaded here -->
        </div>
        <div class="p-4 border-t border-gray-100 bg-gray-50">
            <button onclick="closePaymentModal()" class="w-full text-gray-600 hover:text-gray-800 font-medium py-2 transition">
                <i class="ti ti-arrow-left"></i> Back to Payment Methods
            </button>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-md w-full mx-auto overflow-hidden shadow-2xl text-center animate-fadeInUp">
        <div class="p-6">
            <div class="w-20 h-20 bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-md">
                <i class="ti ti-circle-check text-4xl text-white"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Payment Successful! 🎉</h3>
            <p class="text-gray-600 mb-2" id="successAmount"></p>
            <p class="text-sm text-gray-500 mb-4" id="successBalance"></p>
            <div class="flex gap-3">
                <button onclick="closeSuccessAndGoToWallet()" class="flex-1 bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition">
                    Go to Wallet
                </button>
                <button onclick="closeSuccessAndStay()" class="flex-1 border border-purple-400 text-purple-600 py-3 rounded-xl font-semibold hover:bg-purple-50 transition">
                    Add More
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification Container -->
<div id="toast-container" class="fixed bottom-5 right-5 z-50 space-y-2"></div>

<style>
.payment-card {
    transition: all 0.3s ease;
}
.payment-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}
.amount-preset:active {
    transform: scale(0.97);
}
.rotate-180 {
    transform: rotate(180deg);
}
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fadeInUp {
    animation: fadeInUp 0.3s ease-out;
}
@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
.toast-notification {
    animation: slideInRight 0.3s ease-out;
}
</style>
<script>
// ========== VARIABLES ==========
let selectedGateway = null;
let selectedGatewayName = null;
let selectedMinAmount = null;
let selectedMaxAmount = null;
let requiresApproval = false;
const isLocalUser = {{ $isLocalUser ? 'true' : 'false' }};

// ========== TOAST NOTIFICATION ==========
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    
    const icons = {
        success: 'ti-circle-check',
        error: 'ti-circle-x',
        warning: 'ti-alert-circle',
        info: 'ti-info-circle'
    };
    
    const toast = document.createElement('div');
    toast.className = `toast-notification ${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 min-w-[300px]`;
    toast.innerHTML = `
        <i class="ti ${icons[type]} text-xl"></i>
        <span class="flex-1 text-sm">${message}</span>
        <button onclick="this.parentElement.remove()" class="text-white/80 hover:text-white">
            <i class="ti ti-x"></i>
        </button>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        toast.style.transition = 'all 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

// ========== AMOUNT SELECTOR ==========
function setAmount(amount) {
    document.getElementById('custom-amount').value = amount;
    validateAmount();
}

function validateAmount() {
    const amount = parseFloat(document.getElementById('custom-amount').value);
    const warning = document.getElementById('amount-warning');
    const minAmount = isLocalUser ? 100 : 1;
    const maxAmount = isLocalUser ? 1000000 : 400;
    
    if (isNaN(amount) || amount <= 0) {
        warning.innerHTML = '⚠️ Please enter a valid amount';
        warning.classList.add('text-red-500');
        return false;
    }
    
    if (amount < minAmount) {
        warning.innerHTML = `⚠️ Minimum amount is ${isLocalUser ? 'TSh' : '$'} ${minAmount.toLocaleString()}`;
        warning.classList.add('text-red-500');
        return false;
    }
    
    if (amount > maxAmount) {
        warning.innerHTML = `⚠️ Maximum amount is ${isLocalUser ? 'TSh' : '$'} ${maxAmount.toLocaleString()}`;
        warning.classList.add('text-red-500');
        return false;
    }
    
    warning.innerHTML = '✓ Valid amount';
    warning.classList.remove('text-red-500');
    warning.classList.add('text-green-600');
    return true;
}

// ========== OPEN PAYMENT MODAL (POPUP) ==========
function openPaymentModal(gateway, name, minAmount, maxAmount, requiresApprovalFlag) {
    selectedGateway = gateway;
    selectedGatewayName = name;
    selectedMinAmount = minAmount;
    selectedMaxAmount = maxAmount;
    requiresApproval = requiresApprovalFlag;
    
    const amountInput = document.getElementById('custom-amount');
    let amount = parseFloat(amountInput?.value) || minAmount;
    
    if (amount < minAmount) amount = minAmount;
    if (amount > maxAmount) amount = maxAmount;
    
    let fee = 0;
    let total = amount;
    let feeText = '0% fee';
    
    if (gateway === 'card') {
        fee = amount * 0.025;
        total = amount + fee;
        feeText = '2.5% processing fee';
    }
    
    const currencySymbol = isLocalUser ? 'TSh' : '$';
    let modalContent = '';
    
    // ========== PESAPAL ==========
    if (gateway === 'pesapal') {
        modalContent = `
            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-800"><i class="ti ti-info-circle"></i> You will be redirected to PesaPal secure payment page to complete your transaction.</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 mb-4">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500">Amount</span>
                    <span class="font-semibold text-gray-800">${currencySymbol} ${amount.toLocaleString()}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500">Fee</span>
                    <span class="font-semibold text-green-600">${feeText}</span>
                </div>
                <div class="border-t border-gray-200 pt-2 mt-2">
                    <div class="flex justify-between">
                        <span class="font-bold text-gray-800">Total to Pay</span>
                        <span class="font-bold text-purple-600 text-lg">${currencySymbol} ${total.toLocaleString()}</span>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number (Optional)</label>
                <input type="tel" id="phoneNumber" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500" 
                       placeholder="0712 345 678" value="{{ auth()->user()->mpesa_phone }}">
                <p class="text-xs text-gray-500 mt-1">For mobile money payments</p>
            </div>
            <div class="flex gap-3">
                <button onclick="processPayment()" 
                        class="flex-1 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition">
                    <i class="ti ti-world"></i> Pay with PesaPal
                </button>
                <button onclick="closePaymentModal()" 
                        class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-50 transition">
                    <i class="ti ti-arrow-left"></i> Cancel
                </button>
            </div>
        `;
    }
    // ========== CREDIT CARD ==========
    else if (gateway === 'card') {
        modalContent = `
            <div class="mb-4 p-3 bg-yellow-50 rounded-lg">
                <p class="text-sm text-yellow-800"><i class="ti ti-info-circle"></i> You will be redirected to Stripe secure payment page to enter your card details.</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 mb-4">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500">Amount</span>
                    <span class="font-semibold text-gray-800">${currencySymbol} ${amount.toLocaleString()}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500">Fee (${feeText})</span>
                    <span class="font-semibold text-amber-600">+ ${currencySymbol} ${fee.toLocaleString()}</span>
                </div>
                <div class="border-t border-gray-200 pt-2 mt-2">
                    <div class="flex justify-between">
                        <span class="font-bold text-gray-800">Total to Pay</span>
                        <span class="font-bold text-purple-600 text-lg">${currencySymbol} ${total.toLocaleString()}</span>
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <button onclick="processPayment()" 
                        class="flex-1 bg-gradient-to-r from-purple-500 to-purple-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition">
                    <i class="ti ti-credit-card"></i> Pay with Card
                </button>
                <button onclick="closePaymentModal()" 
                        class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-50 transition">
                    <i class="ti ti-arrow-left"></i> Cancel
                </button>
            </div>
        `;
    }
    // ========== BANK TRANSFER (WITH BACK BUTTON) ==========
    else if (gateway === 'bank') {
        modalContent = `
            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-800"><i class="ti ti-info-circle"></i> You will receive bank transfer instructions after confirmation.</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 mb-4">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500">Amount</span>
                    <span class="font-semibold text-gray-800">${currencySymbol} ${amount.toLocaleString()}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500">Fee</span>
                    <span class="font-semibold text-green-600">${feeText}</span>
                </div>
                <div class="border-t border-gray-200 pt-2 mt-2">
                    <div class="flex justify-between">
                        <span class="font-bold text-gray-800">Total to Pay</span>
                        <span class="font-bold text-purple-600 text-lg">${currencySymbol} ${total.toLocaleString()}</span>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                <input type="text" id="bankName" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500" 
                       placeholder="CRDB, NMB, NBC" value="{{ auth()->user()->bank_name }}">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                <input type="text" id="accountNumber" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500" 
                       placeholder="0123456789" value="{{ auth()->user()->bank_account_number }}">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Account Holder Name</label>
                <input type="text" id="accountName" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500" 
                       placeholder="Full name on account" value="{{ auth()->user()->bank_account_name }}">
            </div>
            <div class="flex gap-3">
                <button onclick="processPayment()" 
                        class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition">
                    <i class="ti ti-building-bank"></i> Submit Request
                </button>
                <button onclick="closePaymentModal()" 
                        class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-50 transition">
                    <i class="ti ti-arrow-left"></i> Cancel
                </button>
            </div>
        `;
    }
    // ========== MOBILE MONEY (M-Pesa, TigoPesa, HaloPesa) ==========
    else {
        let savedPhone = '';
        if (gateway === 'mpesa') savedPhone = '{{ auth()->user()->mpesa_phone }}';
        if (gateway === 'tigopesa') savedPhone = '{{ auth()->user()->tigopesa_phone }}';
        if (gateway === 'halopesa') savedPhone = '{{ auth()->user()->halopesa_phone }}';
        
        modalContent = `
            <div class="mb-4 p-3 bg-green-50 rounded-lg">
                <p class="text-sm text-green-800"><i class="ti ti-info-circle"></i> You will receive an STK Push on your phone. Enter your PIN to complete payment.</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 mb-4">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500">Amount</span>
                    <span class="font-semibold text-gray-800">${currencySymbol} ${amount.toLocaleString()}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500">Fee</span>
                    <span class="font-semibold text-green-600">${feeText}</span>
                </div>
                <div class="border-t border-gray-200 pt-2 mt-2">
                    <div class="flex justify-between">
                        <span class="font-bold text-gray-800">Total to Pay</span>
                        <span class="font-bold text-purple-600 text-lg">${currencySymbol} ${total.toLocaleString()}</span>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="tel" id="phoneNumber" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500" 
                       placeholder="0712 345 678" value="${savedPhone}">
                <p class="text-xs text-gray-500 mt-1">Enter the number registered with ${name}</p>
            </div>
            <div class="flex gap-3">
                <button onclick="processPayment()" 
                        class="flex-1 bg-gradient-to-r from-green-500 to-green-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition">
                    <i class="ti ti-device-mobile"></i> Pay with ${name}
                </button>
                <button onclick="closePaymentModal()" 
                        class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-50 transition">
                    <i class="ti ti-arrow-left"></i> Cancel
                </button>
            </div>
        `;
    }
    
    const modal = document.getElementById('paymentModal');
    const content = document.getElementById('paymentModalContent');
    const title = document.getElementById('modalTitle');
    
    if (title) title.innerHTML = `Pay with ${selectedGatewayName}`;
    if (content) content.innerHTML = modalContent;
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

// ========== PROCESS PAYMENT ==========
function processPayment() {
    const amount = parseFloat(document.getElementById('custom-amount').value);
    const phone = document.getElementById('phoneNumber')?.value;
    const bankName = document.getElementById('bankName')?.value;
    const accountNumber = document.getElementById('accountNumber')?.value;
    const accountName = document.getElementById('accountName')?.value;
    
    if (!amount || amount < selectedMinAmount || amount > selectedMaxAmount) {
        showToast(`Please enter a valid amount between ${isLocalUser ? 'TSh' : '$'} ${selectedMinAmount.toLocaleString()} and ${isLocalUser ? 'TSh' : '$'} ${selectedMaxAmount.toLocaleString()}`, 'warning');
        return;
    }
    
    if ((selectedGateway === 'mpesa' || selectedGateway === 'tigopesa' || selectedGateway === 'halopesa' || selectedGateway === 'pesapal') && !phone) {
        showToast('Please enter your phone number', 'warning');
        return;
    }
    
    if (selectedGateway === 'bank' && (!bankName || !accountNumber || !accountName)) {
        showToast('Please fill in your bank details', 'warning');
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     document.querySelector('input[name="_token"]')?.value;
    
    if (!csrfToken) {
        showToast('CSRF token not found. Please refresh the page.', 'error');
        return;
    }
    
    const content = document.getElementById('paymentModalContent');
    if (content) {
        content.innerHTML = `
            <div class="text-center py-8">
                <i class="ti ti-loader-2 animate-spin text-3xl text-purple-500"></i>
                <p class="text-gray-500 mt-2">Processing ${selectedGatewayName} payment...</p>
            </div>
        `;
    }
    
    const requestData = {
        gateway: selectedGateway,
        amount: amount,
        phone: phone,
        bank_details: selectedGateway === 'bank' ? {
            bank_name: bankName,
            account_number: accountNumber,
            account_name: accountName
        } : null
    };
    
    fetch('/payment/initiate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (requiresApproval || selectedGateway === 'bank') {
                if (content) {
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
                }
                showToast('Bank transfer instructions received. Please complete the transfer.', 'info');
            } else if (data.client_secret) {
                if (content) content.innerHTML = `<div class="text-center py-4"><p class="text-gray-600">Redirecting to secure payment...</p></div>`;
                setTimeout(() => {
                    window.location.href = `/stripe/checkout?client_secret=${data.client_secret}&payment_id=${data.payment_id}`;
                }, 1000);
            } else if (data.redirect_url) {
                if (content) content.innerHTML = `<div class="text-center py-4"><p class="text-gray-600">Redirecting to secure payment page...</p></div>`;
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1000);
            } else {
                if (content) {
                    content.innerHTML = `
                        <div class="text-center py-4">
                            <i class="ti ti-circle-check text-5xl text-green-500 mb-3"></i>
                            <p class="text-gray-800 mb-4">STK Push Sent!</p>
                            <p class="text-sm text-gray-600 mb-4">Check your phone and enter PIN to complete.</p>
                            <div id="payment-status" class="text-sm text-gray-500 mb-3">Waiting for confirmation...</div>
                            <button onclick="checkPaymentStatus('${data.payment_id}')" class="text-purple-600 text-sm">Check Status</button>
                        </div>
                    `;
                }
                showToast('STK Push sent to your phone. Please enter your PIN.', 'info');
                
                let attempts = 0;
                const interval = setInterval(() => {
                    attempts++;
                    const statusDiv = document.getElementById('payment-status');
                    if (statusDiv) statusDiv.innerHTML = `Checking... (${attempts}/15)`;
                    
                    fetch(`/payment/status/${data.payment_id}`)
                        .then(res => res.json())
                        .then(statusData => {
                            if (statusData.status === 'completed') {
                                clearInterval(interval);
                                if (statusDiv) statusDiv.innerHTML = '✅ Payment confirmed!';
                                showSuccessModal(amount);
                            } else if (attempts >= 15) {
                                clearInterval(interval);
                                if (statusDiv) statusDiv.innerHTML = '⏰ Still waiting? Please check your phone.';
                            }
                        });
                }, 3000);
            }
        } else {
            if (content) {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <i class="ti ti-circle-x text-3xl text-red-500"></i>
                        <p class="text-red-600 mt-2">${data.message || 'Payment failed'}</p>
                        <button onclick="closePaymentModal()" class="mt-4 text-purple-600">Close</button>
                    </div>
                `;
            }
            showToast(data.message || 'Payment failed. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Payment error:', error);
        if (content) {
            content.innerHTML = `
                <div class="text-center py-8">
                    <i class="ti ti-circle-x text-3xl text-red-500"></i>
                    <p class="text-gray-500 mt-2">Network error. Please try again.</p>
                    <button onclick="closePaymentModal()" class="mt-4 text-purple-600">Close</button>
                </div>
            `;
        }
        showToast('Network error. Please check your connection and try again.', 'error');
    });
}

function checkPaymentStatus(paymentId) {
    fetch(`/payment/status/${paymentId}`)
        .then(res => res.json())
        .then(data => {
            const statusDiv = document.getElementById('payment-status');
            if (data.status === 'completed') {
                if (statusDiv) statusDiv.innerHTML = '✅ Payment confirmed!';
                showSuccessModal(data.amount);
            } else if (data.status === 'failed') {
                if (statusDiv) statusDiv.innerHTML = '❌ Payment failed. Please try again.';
                showToast('Payment failed. Please try again.', 'error');
            } else {
                if (statusDiv) statusDiv.innerHTML = '⏳ Payment pending... Please wait.';
                showToast('Payment is still processing. Please wait...', 'info');
            }
        });
}

function showSuccessModal(amount) {
    closePaymentModal();
    const modal = document.getElementById('successModal');
    if (modal) {
        document.getElementById('successAmount').innerHTML = `${isLocalUser ? 'TSh' : '$'} ${amount.toLocaleString()} added!`;
        document.getElementById('successBalance').innerHTML = `Your wallet has been updated`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        showToast('Payment successful! Money added to your wallet.', 'success');
        
        setTimeout(() => {
            window.location.href = '/wallet';
        }, 3000);
    }
}

function closeSuccessAndGoToWallet() {
    window.location.href = '/wallet';
}

function closeSuccessAndStay() {
    const modal = document.getElementById('successModal');
    if (modal) modal.classList.add('hidden');
    location.reload();
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    const content = document.getElementById('paymentModalContent');
    if (content) {
        content.innerHTML = '<div class="text-center py-8"><i class="ti ti-loader-2 animate-spin text-3xl text-purple-500"></i><p class="text-gray-500 mt-2">Loading...</p></div>';
    }
}

function toggleSavedDetails() {
    const content = document.getElementById('saved-details-content');
    const icon = document.getElementById('toggle-icon');
    if (content && icon) {
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            content.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }
}

document.getElementById('custom-amount')?.addEventListener('input', validateAmount);

document.getElementById('paymentDetailsForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
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
            showToast('Payment details saved successfully!', 'success');
            setTimeout(() => location.reload(), 1500);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error saving details', 'error');
    });
});

@if(isset($suggestedAmount) && $suggestedAmount > 0)
setAmount({{ $suggestedAmount }});
@endif
</script>

@endsection