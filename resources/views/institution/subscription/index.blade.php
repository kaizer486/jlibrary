@extends('layouts.librarian')

@section('title', 'Subscription Management')
@section('page-title', '📋 Subscription Management')

@section('content')

<div class="max-w-7xl mx-auto">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p class="text-slate-400 text-sm">Manage your institution subscription</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('institution.subscription.history') }}" class="btn-library-outline">
                <i class="ti ti-history"></i> History
            </a>
        </div>
    </div>

    <!-- Current Subscription Status -->
    <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-slate-400 text-sm">Current Subscription</p>
                <h2 class="text-2xl font-bold text-white">{{ $stats['plan_label'] }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-sm {{ $stats['is_active'] ? 'text-emerald-400' : 'text-red-400' }}">
                        {{ $stats['status_label'] }}
                    </span>
                    @if($stats['is_active'])
                        <span class="text-sm text-slate-400">
                            ({{ $stats['days_left'] }} days remaining)
                        </span>
                    @endif
                </div>
                @if($stats['expires_at'])
                    <p class="text-xs text-slate-500 mt-1">
                        Expires: {{ \Carbon\Carbon::parse($stats['expires_at'])->format('F d, Y') }}
                    </p>
                @endif
              
            </div>
           
        </div>
        
        <!-- Progress Bar -->
        @if($stats['is_active'])
            <div class="mt-4">
                <div class="flex justify-between text-sm text-slate-400 mb-1">
                    <span>Subscription Progress</span>
                    <span>{{ $stats['progress'] }}%</span>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-2.5">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-2.5 rounded-full transition-all duration-500" 
                         style="width: {{ $stats['progress'] }}%"></div>
                </div>
            </div>
        @endif
    </div>

    <!-- Plans -->
    <h3 class="text-lg font-semibold text-white mb-4">
        @if($stats['is_active'])
            <span class="text-amber-400">⛔ Plan locked until subscription expires</span>
        @else
            Choose a Plan
        @endif
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @foreach($plans as $plan)
            @php
                $isCurrentPlan = $stats['plan'] === $plan->id && $stats['is_active'];
                $isLocked = $stats['is_active'] && !$isCurrentPlan;
            @endphp
            <div class="bg-slate-900 border border-slate-700 rounded-2xl overflow-hidden 
                @if($isCurrentPlan) border-emerald-500/50 ring-1 ring-emerald-500/30 
                @elseif($isLocked) opacity-60 @endif
                hover:border-purple-500/30 transition">
                
                @if($isCurrentPlan)
                    <div class="bg-emerald-500/20 text-emerald-400 text-center text-xs font-semibold py-1 border-b border-emerald-500/20">
                        <i class="ti ti-check"></i> CURRENT PLAN
                    </div>
                @endif
                
                <div class="p-6">
                    <h4 class="text-xl font-bold text-white">{{ $plan->name }}</h4>
                    <p class="text-slate-400 text-sm mt-1">{{ $plan->description }}</p>
                    
                    <div class="mt-4">
                        <span class="text-2xl font-bold text-white">TSh {{ number_format($plan->monthly_price) }}</span>
                        <span class="text-slate-400 text-sm">/ month</span>
                    </div>
                    
                    <ul class="mt-4 space-y-2">
                        @foreach($plan->features as $feature)
                            <li class="text-slate-300 text-sm flex items-center gap-2">
                                <i class="ti ti-check text-emerald-400"></i>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                    
                    @if($isCurrentPlan)
                        <button class="w-full mt-6 bg-emerald-500/20 text-emerald-400 py-2.5 rounded-lg border border-emerald-500/20 cursor-default">
                            <i class="ti ti-check"></i> Current Plan
                        </button>
                    @elseif($isLocked)
                        <button class="w-full mt-6 bg-slate-800 text-slate-500 py-2.5 rounded-lg border border-slate-700 cursor-not-allowed" disabled>
                            <i class="ti ti-lock"></i> Locked until expiry
                        </button>
                    @else
                        <button onclick="openPaymentModal('{{ $plan->id }}')" 
                                class="w-full mt-6 btn-library justify-center">
                            <i class="ti ti-credit-card"></i> Subscribe
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Recent History -->
    @if($history->count() > 0)
        <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-700 flex justify-between items-center">
                <h4 class="font-semibold text-white">Recent Transactions</h4>
                <a href="{{ route('institution.subscription.history') }}" class="text-sm text-purple-400 hover:text-purple-300">
                    View All →
                </a>
            </div>
            <div class="divide-y divide-slate-800">
                @foreach($history as $item)
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-slate-800/50 transition">
                        <div>
                            <p class="text-white font-medium">{{ ucfirst($item->plan) }} Plan</p>
                            <p class="text-xs text-slate-400">{{ $item->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-white font-medium">TSh {{ number_format($item->amount, 2) }}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full 
                                @if($item->status === 'active') bg-emerald-500/20 text-emerald-400
                                @elseif($item->status === 'cancelled') bg-red-500/20 text-red-400
                                @else bg-yellow-500/20 text-yellow-400 @endif">
                                {{ ucfirst($item->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-white">Subscribe to <span id="modalPlanName"></span></h3>
            <button onclick="closePaymentModal()" class="text-slate-400 hover:text-white">
                <i class="ti ti-x text-2xl"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('institution.subscription.extend') }}">
            @csrf
            <input type="hidden" name="plan" id="modalPlan" value="">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Billing Period</label>
                    <select name="period" class="search-bar" required>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly (Save 10%)</option>
                        <option value="semi_annual">Semi-Annual (Save 15%)</option>
                        <option value="annual">Annual (Save 20%)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Payment Method</label>
                    <select name="payment_method" class="search-bar" required>
                        <option value="mpesa">M-Pesa</option>
                        <option value="tigopesa">TigoPesa</option>
                        <option value="halopesa">HaloPesa</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="pesapal">PesaPal</option>
                    </select>
                </div>
                
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="auto_renew" value="1" checked class="w-4 h-4 accent-purple-500">
                    <label class="text-sm text-slate-300">Auto-renew subscription</label>
                </div>
                
                <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-3">
                    <p class="text-xs text-amber-400 flex items-center gap-1">
                        <i class="ti ti-info-circle"></i>
                        You cannot change your plan until this subscription expires.
                    </p>
                </div>
                
                <button type="submit" class="btn-library w-full justify-center">
                    <i class="ti ti-lock"></i> Confirm Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openPaymentModal(plan) {
    // Check if user already has active subscription
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
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});
</script>

@endsection