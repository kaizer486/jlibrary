@extends('layouts.librarian')

@section('title', 'Subscription Management')

@section('content')

<!-- ========================================== -->
<!-- LIGHT BISQUE BACKGROUND                    -->
<!-- ========================================== -->
<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div style="position: relative; z-index: 10; min-height: 100vh; padding: 1.5rem 0;">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <!-- Header - Balanced -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                    <span class="bg-gradient-to-br from-orange-500 to-amber-500 p-2 rounded-xl shadow-lg shadow-orange-500/20">
                        <i class="ti ti-credit-card text-white text-xl"></i>
                    </span>
                    Subscription Management
                </h1>
                <p class="text-sm text-slate-500 mt-0.5">Manage your institution subscription plan</p>
            </div>
            <a href="{{ route('institution.subscription.history') }}" 
               class="bg-white/70 hover:bg-white/90 text-slate-700 px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-medium border border-slate-200/50 backdrop-blur-sm">
                <i class="ti ti-history"></i> History
            </a>
        </div>

        <!-- Current Subscription Status -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl border-2 border-orange-200/60 shadow-sm p-5 mb-5">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-medium">Current Subscription</p>
                    <div class="flex items-center gap-3 mt-0.5">
                        <h2 class="text-xl font-bold text-slate-800">{{ $stats['plan_label'] }}</h2>
                        <span class="text-sm font-medium {{ $stats['status_color'] }}">
                            {{ $stats['status_label'] }}
                        </span>
                        @if($stats['is_active'])
                            <span class="text-sm text-slate-500">({{ $stats['days_left'] }} days left)</span>
                        @endif
                    </div>
                    @if($stats['expires_at'])
                        <p class="text-xs text-slate-400">Expires: {{ \Carbon\Carbon::parse($stats['expires_at'])->format('M d, Y') }}</p>
                    @endif
                </div>
                @if($stats['is_active'])
                    <div class="bg-emerald-50/80 border-2 border-emerald-200/60 rounded-lg px-4 py-2">
                        <p class="text-sm text-emerald-700 flex items-center gap-2">
                            <i class="ti ti-lock"></i> Plan locked until expiry
                        </p>
                    </div>
                @else
                    <div class="bg-amber-50/80 border-2 border-amber-200/60 rounded-lg px-4 py-2">
                        <p class="text-sm text-amber-700 flex items-center gap-2">
                            <i class="ti ti-alert-triangle"></i> No active plan
                        </p>
                    </div>
                @endif
            </div>
            
            <!-- Progress Bar -->
            @if($stats['is_active'])
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-slate-500 mb-1">
                        <span>Subscription Progress</span>
                        <span>{{ $stats['progress'] }}%</span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-orange-500 to-amber-500 rounded-full transition-all duration-500" style="width: {{ $stats['progress'] }}%;"></div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Plans Title -->
        <div class="mb-4">
            <h3 class="text-base font-semibold text-slate-700">
                @if($stats['is_active'])
                    <span class="text-amber-600">Plan locked until subscription expires</span>
                @else
                    Choose Your Plan
                @endif
            </h3>
        </div>
        
        <!-- Plan Cards - Balanced Size -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach($plans as $plan)
                @php
                    $isCurrentPlan = $stats['plan'] === $plan->slug && $stats['is_active'];
                    $isLocked = $stats['is_active'] && !$isCurrentPlan;
                    $isExpiredPlan = $stats['is_expired'] && $stats['plan'] === $plan->slug;
                    
                    $colors = [
                        'basic' => ['border' => 'border-orange-200/80', 'bg' => 'bg-orange-50/80', 'badge' => 'bg-orange-100 text-orange-700', 'btn' => 'from-orange-600 to-orange-500', 'shadow' => 'shadow-orange-500/20', 'text' => 'text-orange-600', 'light' => 'border-orange-100/60'],
                        'premium' => ['border' => 'border-amber-200/80', 'bg' => 'bg-amber-50/80', 'badge' => 'bg-amber-100 text-amber-700', 'btn' => 'from-amber-600 to-amber-500', 'shadow' => 'shadow-amber-500/20', 'text' => 'text-amber-600', 'light' => 'border-amber-100/60'],
                        'enterprise' => ['border' => 'border-orange-300/80', 'bg' => 'bg-orange-50/80', 'badge' => 'bg-orange-100 text-orange-700', 'btn' => 'from-orange-600 to-amber-600', 'shadow' => 'shadow-orange-500/20', 'text' => 'text-orange-600', 'light' => 'border-orange-100/60'],
                    ];
                    $c = $colors[$plan->slug] ?? $colors['basic'];
                @endphp
                
                <div class="group bg-white/80 backdrop-blur-sm rounded-xl border-2 {{ $c['border'] }} hover:border-orange-300/70 transition-all duration-300 hover:shadow-xl hover:shadow-orange-500/10 overflow-hidden flex flex-col {{ $isCurrentPlan ? 'ring-2 ring-emerald-500' : '' }} {{ $isLocked ? 'opacity-60' : '' }}">
                    
                    <!-- Plan Header -->
                    <div class="px-5 py-3 border-b-2 border-slate-200/60 {{ $c['bg'] }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-lg font-bold text-slate-800">{{ $plan->name }}</h4>
                                <p class="text-xs text-slate-500">{{ $plan->description }}</p>
                            </div>
                            @if($isCurrentPlan)
                                <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full border border-emerald-200/60">Active</span>
                            @elseif($isExpiredPlan)
                                <span class="text-[10px] font-bold bg-red-100 text-red-700 px-2.5 py-1 rounded-full border border-red-200/60">Expired</span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Card Body -->
                    <div class="px-5 py-4 flex-1 flex flex-col">
                        <!-- Price -->
                        <div class="text-center pb-3 border-b-2 border-slate-200/60">
                            <span class="text-2xl font-bold {{ $c['text'] }}">TSh {{ number_format($plan->price) }}</span>
                            <span class="text-sm text-slate-500">/ month</span>
                        </div>
                        
                        <!-- Limits - With light colored borders -->
                        <div class="grid grid-cols-2 gap-2 mt-3">
                            <div class="bg-white/60 rounded-lg p-2.5 text-center border-2 {{ $c['light'] }}">
                                <div class="text-xs text-slate-500 font-medium">👥 Members</div>
                                <div class="text-base font-bold text-slate-700">
                                    {{ $plan->max_users ? 'Up to ' . $plan->max_users : '♾️ Unlimited' }}
                                </div>
                            </div>
                            <div class="bg-white/60 rounded-lg p-2.5 text-center border-2 {{ $c['light'] }}">
                                <div class="text-xs text-slate-500 font-medium">📚 Books</div>
                                <div class="text-base font-bold text-slate-700">
                                    {{ $plan->max_books ? 'Up to ' . $plan->max_books : '♾️ Unlimited' }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Features -->
                        <div class="mt-3 flex-1">
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Features</p>
                            <ul class="grid grid-cols-1 gap-1 mt-1.5">
                                @php
                                    $featureLabels = [
                                        'analytics' => '📊 Analytics',
                                        'download_pdf' => '📥 Download PDF',
                                        'multi_user' => '👥 Multi-user access',
                                        'unlimited_users' => '♾️ Unlimited users',
                                    ];
                                    $displayedFeatures = [];
                                    foreach($plan->features as $key => $value) {
                                        if (is_bool($value) && $value && isset($featureLabels[$key])) {
                                            $displayedFeatures[] = $featureLabels[$key];
                                        }
                                    }
                                    if (isset($plan->features['max_books']) && is_numeric($plan->features['max_books'])) {
                                        $displayedFeatures[] = '📖 ' . $plan->features['max_books'] . ' books';
                                    }
                                @endphp
                                @foreach(array_slice($displayedFeatures, 0, 3) as $feature)
                                    <li class="text-sm text-slate-600 flex items-center gap-2">
                                        <span class="text-emerald-500 text-sm">✓</span>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                                @if(count($displayedFeatures) > 3)
                                    <li class="text-xs text-slate-400">+{{ count($displayedFeatures) - 3 }} more features</li>
                                @endif
                                @if(empty($displayedFeatures))
                                    <li class="text-sm text-slate-400">Standard features included</li>
                                @endif
                            </ul>
                        </div>
                        
                        <!-- Button - Always at bottom -->
                        <div class="mt-4 pt-3 border-t-2 border-slate-200/60">
                            @if($isCurrentPlan)
                                <button class="w-full py-2.5 bg-slate-100 border-2 border-slate-200/60 rounded-lg text-sm font-semibold text-slate-500 cursor-default">
                                    ✅ Current Plan
                                </button>
                            @elseif($isLocked)
                                <button class="w-full py-2.5 bg-slate-50 border-2 border-slate-200/60 rounded-lg text-sm font-semibold text-slate-400 cursor-not-allowed" disabled>
                                    🔒 Locked
                                </button>
                            @else
                                <button onclick="openPaymentModal('{{ $plan->slug }}')" 
                                        class="w-full py-2.5 bg-gradient-to-r {{ $c['btn'] }} hover:shadow-lg {{ $c['shadow'] }} text-white rounded-lg text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5">
                                    {{ $isExpiredPlan ? '🔄 Renew Now' : '💳 Subscribe' }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Recent History -->
        @if($history->count() > 0)
            <div class="mt-6 bg-white/80 backdrop-blur-sm rounded-xl border-2 border-slate-200/60 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b-2 border-slate-200/60 flex justify-between items-center">
                    <h4 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                        <i class="ti ti-history text-orange-500"></i> Recent Transactions
                    </h4>
                    <a href="{{ route('institution.subscription.history') }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium">
                        View All →
                    </a>
                </div>
                <div class="divide-y divide-slate-200/60">
                    @foreach($history->take(4) as $item)
                        <div class="px-5 py-2.5 flex items-center justify-between hover:bg-orange-50/30 transition">
                            <div>
                                <p class="text-sm font-medium text-slate-700">{{ ucfirst($item->plan) }} Plan</p>
                                <p class="text-xs text-slate-400">{{ $item->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-700">TSh {{ number_format($item->amount, 2) }}</p>
                                <span class="text-[10px] font-medium px-2.5 py-0.5 rounded-full border-2
                                    @if($item->status === 'active') bg-emerald-50 text-emerald-700 border-emerald-200/60
                                    @elseif($item->status === 'cancelled') bg-red-50 text-red-700 border-red-200/60
                                    @elseif($item->status === 'pending') bg-amber-50 text-amber-700 border-amber-200/60
                                    @else bg-slate-50 text-slate-500 border-slate-200/60 @endif">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white/95 backdrop-blur-md border-2 border-white/60 rounded-2xl max-w-md w-full mx-4 overflow-hidden shadow-2xl">
        
        <!-- Modal Header -->
        <div class="px-5 py-3.5 border-b-2 border-slate-200/50 bg-gradient-to-r from-orange-50 to-amber-50">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <span class="bg-gradient-to-br from-orange-500 to-amber-500 p-1.5 rounded-lg shadow-lg shadow-orange-500/20">
                        <i class="ti ti-credit-card text-white text-sm"></i>
                    </span>
                    Subscribe to <span id="modalPlanName" class="text-orange-600"></span>
                </h3>
                <button onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="ti ti-x text-xl"></i>
                </button>
            </div>
        </div>
        
        <div class="p-5">
            <form method="POST" action="{{ route('institution.subscription.initiate-payment') }}" id="paymentForm">
                @csrf
                <input type="hidden" name="plan" id="modalPlan" value="">
                
                <div class="space-y-3.5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Billing Period</label>
                        <select name="period" id="billingPeriod" class="w-full px-4 py-2.5 bg-white/80 backdrop-blur-sm border-2 border-slate-200/60 rounded-lg text-sm text-slate-800 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200 transition" required>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly (Save 10%)</option>
                            <option value="semi_annual">Semi-Annual (Save 15%)</option>
                            <option value="annual">Annual (Save 20%)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Payment Method</label>
                        <select name="payment_method" id="paymentMethod" class="w-full px-4 py-2.5 bg-white/80 backdrop-blur-sm border-2 border-slate-200/60 rounded-lg text-sm text-slate-800 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200 transition" required onchange="togglePhoneField()">
                            <option value="mpesa">📱 M-Pesa</option>
                            <option value="tigopesa">📱 TigoPesa</option>
                            <option value="halopesa">📱 HaloPesa</option>
                            <option value="pesapal">💳 PesaPal</option>
                            <option value="bank">🏦 Bank Transfer</option>
                        </select>
                    </div>
                    
                    <div id="phoneField">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Phone Number</label>
                        <input type="text" name="phone_number" id="phoneNumber" placeholder="0712345678" 
                               class="w-full px-4 py-2.5 bg-white/80 backdrop-blur-sm border-2 border-slate-200/60 rounded-lg text-sm text-slate-800 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200 transition">
                        <p class="text-xs text-slate-400 mt-0.5">Enter your phone number registered with the selected mobile money service</p>
                    </div>
                    
                    <div class="bg-amber-50/80 backdrop-blur-sm border-2 border-amber-200/60 rounded-lg p-3">
                        <p class="text-xs text-amber-700 flex items-center gap-2">
                            <i class="ti ti-info-circle"></i>
                            <span id="paymentMethodInfo">You will receive an M-Pesa STK Push on your phone</span>
                        </p>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="terms" value="1" required class="w-4 h-4 accent-orange-600 cursor-pointer">
                        <label class="text-sm text-slate-600 cursor-pointer">I agree to the terms and conditions</label>
                    </div>
                    
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-orange-600 to-amber-600 hover:shadow-lg hover:shadow-orange-500/20 text-white rounded-lg text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5">
                        <i class="ti ti-lock"></i> Pay Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openPaymentModal(plan) {
    @if($stats['is_active'])
        alert('You already have an active subscription. You cannot change your plan until it expires.');
        return;
    @endif
    
    const modal = document.getElementById('paymentModal');
    const planNames = {
        'basic': 'Basic',
        'premium': 'Premium',
        'enterprise': 'Enterprise'
    };
    document.getElementById('modalPlan').value = plan;
    document.getElementById('modalPlanName').textContent = planNames[plan] || plan;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    togglePhoneField();
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function togglePhoneField() {
    const method = document.getElementById('paymentMethod').value;
    const phoneField = document.getElementById('phoneField');
    const phoneInput = document.getElementById('phoneNumber');
    const paymentInfo = document.getElementById('paymentMethodInfo');
    
    const methods = ['mpesa', 'tigopesa', 'halopesa'];
    if (methods.includes(method)) {
        phoneField.style.display = 'block';
        phoneInput.required = true;
        const names = {'mpesa': 'M-Pesa', 'tigopesa': 'TigoPesa', 'halopesa': 'HaloPesa'};
        paymentInfo.textContent = 'You will receive a ' + names[method] + ' STK Push on your phone. Enter PIN to complete payment.';
    } else if (method === 'pesapal') {
        phoneField.style.display = 'none';
        phoneInput.required = false;
        paymentInfo.textContent = 'You will be redirected to PesaPal to complete payment (supports cards and mobile money).';
    } else if (method === 'bank') {
        phoneField.style.display = 'none';
        phoneInput.required = false;
        paymentInfo.textContent = 'You will see bank transfer instructions after submitting.';
    } else {
        phoneField.style.display = 'none';
        phoneInput.required = false;
        paymentInfo.textContent = 'Please select a payment method.';
    }
}

document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) closePaymentModal();
});

@if(session()->has('success') && str_contains(session('success'), 'STK Push'))
    setTimeout(function() { window.location.reload(); }, 10000);
@endif
</script>

<style>
    [x-cloak] { display: none !important; }
</style>

@endsection