@extends('layouts.librarian')

@section('title', 'Subscription Management')
@section('page-title', 'Subscription Management')

@section('content')

<div class="max-w-7xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Manage your institution subscription</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('institution.subscription.history') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: rgba(255,255,255,0.7); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); color: #475569; border: 1px solid rgba(30, 58, 95, 0.12); border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; text-decoration: none;">
                <i class="ti ti-history"></i> History
            </a>
        </div>
    </div>

    <!-- Current Subscription Status -->
    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Current Subscription</p>
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $stats['plan_label'] }}</h2>
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.25rem;">
                    <span style="font-size: 0.875rem; {{ $stats['status_color'] }}">
                        {{ $stats['status_label'] }}
                    </span>
                    @if($stats['is_active'])
                        <span style="font-size: 0.875rem; color: #6b7280;">
                            ({{ $stats['days_left'] }} days remaining)
                        </span>
                    @endif
                </div>
                @if($stats['expires_at'])
                    <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">
                        Expires: {{ \Carbon\Carbon::parse($stats['expires_at'])->format('F d, Y') }}
                    </p>
                @endif
            </div>
            @if($stats['is_active'])
                <div style="background: rgba(6, 95, 70, 0.06); border: 1px solid rgba(6, 95, 70, 0.1); border-radius: 0.5rem; padding: 0.5rem 1rem;">
                    <p style="font-size: 0.875rem; color: #065f46; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                        <i class="ti ti-lock"></i>
                        Plan locked until expiry
                    </p>
                </div>
            @else
                <div style="background: rgba(217, 119, 6, 0.06); border: 1px solid rgba(217, 119, 6, 0.1); border-radius: 0.5rem; padding: 0.5rem 1rem;">
                    <p style="font-size: 0.875rem; color: #d97706; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                        <i class="ti ti-alert-triangle"></i>
                        No active subscription - Choose a plan below
                    </p>
                </div>
            @endif
        </div>
        
        <!-- Progress Bar -->
        @if($stats['is_active'])
            <div style="margin-top: 1rem;">
                <div style="display: flex; justify-content: space-between; font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;">
                    <span>Subscription Progress</span>
                    <span>{{ $stats['progress'] }}%</span>
                </div>
                <div style="width: 100%; background: rgba(30, 58, 95, 0.06); border-radius: 9999px; height: 0.4rem; overflow: hidden;">
                    <div style="height: 0.4rem; border-radius: 9999px; background: linear-gradient(90deg, #5b21b6, #7c3aed); transition: width 0.5s; width: {{ $stats['progress'] }}%;"></div>
                </div>
            </div>
        @endif
    </div>

    <!-- Plans -->
    <h3 style="font-size: 1.125rem; font-weight: 600; color: #1a1a2e; margin-bottom: 1rem;">
        @if($stats['is_active'])
            <span style="color: #d97706;">Plan locked until subscription expires</span>
        @else
            Choose Your Plan
        @endif
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @foreach($plans as $plan)
            @php
                $isCurrentPlan = $stats['plan'] === $plan->id && $stats['is_active'];
                $isLocked = $stats['is_active'] && !$isCurrentPlan;
            @endphp
            <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 1rem; overflow: hidden; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.04); 
                @if($isCurrentPlan) border-color: rgba(6, 95, 70, 0.3); 
                @elseif($isLocked) opacity: 0.6; @endif">
                
                @if($isCurrentPlan)
                    <div style="background: rgba(6, 95, 70, 0.06); color: #065f46; text-align: center; font-size: 0.65rem; font-weight: 600; padding: 0.25rem; border-bottom: 1px solid rgba(6, 95, 70, 0.08);">
                        <i class="ti ti-check"></i> CURRENT PLAN
                    </div>
                @elseif($stats['is_expired'] && $plan->id === $stats['plan'])
                    <div style="background: rgba(220, 38, 38, 0.06); color: #dc2626; text-align: center; font-size: 0.65rem; font-weight: 600; padding: 0.25rem; border-bottom: 1px solid rgba(220, 38, 38, 0.08);">
                        <i class="ti ti-alert-triangle"></i> EXPIRED - Renew Now!
                    </div>
                @endif
                
                <div style="padding: 1.5rem;">
                    <h4 style="font-size: 1.25rem; font-weight: 700; color: #1a1a2e;">{{ $plan->name }}</h4>
                    <p style="color: #6b7280; font-size: 0.875rem; margin-top: 0.25rem;">{{ $plan->description }}</p>
                    
                    <div style="margin-top: 1rem;">
                        <span style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e;">TSh {{ number_format($plan->monthly_price) }}</span>
                        <span style="color: #6b7280; font-size: 0.875rem;">/ month</span>
                    </div>
                    
                    <ul style="margin-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem;">
                        @foreach($plan->features as $feature)
                            <li style="color: #4b5563; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="ti ti-check" style="color: #065f46;"></i>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                    
                    @if($isCurrentPlan)
                        <button style="width: 100%; margin-top: 1.5rem; background: rgba(6, 95, 70, 0.06); color: #065f46; padding: 0.6rem; border-radius: 0.5rem; border: 1px solid rgba(6, 95, 70, 0.1); cursor: default;">
                            <i class="ti ti-check"></i> Current Plan
                        </button>
                    @elseif($isLocked)
                        <button style="width: 100%; margin-top: 1.5rem; background: rgba(0,0,0,0.02); color: #6b7280; padding: 0.6rem; border-radius: 0.5rem; border: 1px solid rgba(0,0,0,0.06); cursor: not-allowed;" disabled>
                            <i class="ti ti-lock"></i> Locked until expiry
                            <span style="display: block; font-size: 0.65rem; margin-top: 0.15rem; color: #9ca3af;">Contact Super Admin to upgrade</span>
                        </button>
                    @else
                        <button onclick="openPaymentModal('{{ $plan->id }}')" 
                                style="width: 100%; margin-top: 1.5rem; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                            <i class="ti ti-credit-card"></i> 
                            {{ $stats['is_expired'] && $plan->id === $stats['plan'] ? 'Renew Now' : 'Subscribe' }}
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Recent History -->
    @if($history->count() > 0)
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 0.75rem; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div style="padding: 0.75rem 1.5rem; border-bottom: 1px solid rgba(30, 58, 95, 0.08); display: flex; justify-content: space-between; align-items: center;">
                <h4 style="font-weight: 600; color: #1a1a2e; margin: 0;">Recent Transactions</h4>
                <a href="{{ route('institution.subscription.history') }}" style="color: #1e3a5f; font-size: 0.875rem; transition: color 0.2s; text-decoration: none;">
                    View All →
                </a>
            </div>
            <div style="border-top: 1px solid rgba(30, 58, 95, 0.04);">
                @foreach($history as $item)
                    <div style="padding: 0.6rem 1.5rem; display: flex; align-items: center; justify-content: space-between; transition: background 0.2s; border-bottom: 1px solid rgba(30, 58, 95, 0.04);">
                        <div>
                            <p style="font-weight: 500; color: #1a1a2e; margin: 0;">{{ ucfirst($item->plan) }} Plan</p>
                            <p style="font-size: 0.7rem; color: #6b7280; margin: 0;">{{ $item->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-weight: 500; color: #1a1a2e; margin: 0;">TSh {{ number_format($item->amount, 2) }}</p>
                            <span style="display: inline-block; padding: 0.1rem 0.5rem; border-radius: 9999px; font-size: 0.6rem; font-weight: 500; 
                                @if($item->status === 'active') color: #065f46; background: rgba(6, 95, 70, 0.08);
                                @elseif($item->status === 'cancelled') color: #dc2626; background: rgba(220, 38, 38, 0.08);
                                @elseif($item->status === 'pending') color: #d97706; background: rgba(217, 119, 6, 0.08);
                                @else color: #6b7280; background: rgba(0, 0, 0, 0.04); @endif">
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
<div id="paymentModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div style="background: rgba(255,255,255,0.95); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.3); border-radius: 1rem; max-width: 28rem; width: 100%; padding: 1.5rem; box-shadow: 0 24px 80px rgba(0,0,0,0.15);">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1.125rem; font-weight: 700; color: #1a1a2e;">Subscribe to <span id="modalPlanName"></span></h3>
            <button onclick="closePaymentModal()" style="color: #6b7280; background: none; border: none; cursor: pointer; font-size: 1.5rem; padding: 0; transition: color 0.2s;">
                <i class="ti ti-x"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('institution.subscription.initiate-payment') }}" id="paymentForm">
            @csrf
            <input type="hidden" name="plan" id="modalPlan" value="">
            
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Billing Period</label>
                    <select name="period" id="billingPeriod" style="width: 100%; padding: 0.6rem 2.5rem 0.6rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;" required>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly (Save 10%)</option>
                        <option value="semi_annual">Semi-Annual (Save 15%)</option>
                        <option value="annual">Annual (Save 20%)</option>
                    </select>
                </div>
                
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Payment Method</label>
                    <select name="payment_method" id="paymentMethod" style="width: 100%; padding: 0.6rem 2.5rem 0.6rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;" required onchange="togglePhoneField()">
                        <option value="mpesa">M-Pesa</option>
                        <option value="tigopesa">TigoPesa</option>
                        <option value="halopesa">HaloPesa</option>
                        <option value="pesapal">PesaPal</option>
                        <option value="bank">Bank Transfer</option>
                    </select>
                </div>
                
                <div id="phoneField">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Phone Number</label>
                    <input type="text" name="phone_number" id="phoneNumber" placeholder="0712345678" 
                           style="width: 100%; padding: 0.6rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;">
                    <p style="font-size: 0.65rem; color: #6b7280; margin-top: 0.25rem;">Enter your M-Pesa registered phone number</p>
                </div>
                
                <div style="background: rgba(217, 119, 6, 0.04); border: 1px solid rgba(217, 119, 6, 0.08); border-radius: 0.5rem; padding: 0.75rem;">
                    <p style="font-size: 0.7rem; color: #d97706; display: flex; align-items: center; gap: 0.25rem; margin: 0;">
                        <i class="ti ti-info-circle"></i>
                        You will receive an M-Pesa STK Push on your phone. Enter PIN to complete payment.
                    </p>
                </div>
                
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="terms" value="1" required style="width: 1rem; height: 1rem; accent-color: #5b21b6; cursor: pointer;">
                    <label style="font-size: 0.875rem; color: #4b5563; cursor: pointer;">I agree to the terms and conditions</label>
                </div>
                
                <button type="submit" style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.7rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.95rem; transition: all 0.2s; cursor: pointer;">
                    <i class="ti ti-lock"></i> Pay Now
                </button>
            </div>
        </form>
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
    
    // Reset phone field visibility
    togglePhoneField();
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

function togglePhoneField() {
    const method = document.getElementById('paymentMethod').value;
    const phoneField = document.getElementById('phoneField');
    const phoneInput = document.getElementById('phoneNumber');
    
    if (method === 'mpesa' || method === 'tigopesa' || method === 'halopesa') {
        phoneField.style.display = 'block';
        phoneInput.required = true;
    } else {
        phoneField.style.display = 'none';
        phoneInput.required = false;
    }
}

// Close modal on outside click
document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});

// Auto-refresh payment status every 10 seconds if pending
@if(session()->has('success') && str_contains(session('success'), 'STK Push'))
    setTimeout(function() {
        window.location.reload();
    }, 10000);
@endif
</script>

<style>
    /* ========================================== */
    /* 1px DIM DARK BLUE BORDER STYLES            */
    /* ========================================== */

    a[style*="History"]:hover {
        border-color: #1e3a5f !important;
        background: rgba(255,255,255,0.9) !important;
        color: #1a1a2e !important;
    }
    
    input:focus, 
    select:focus {
        border-color: #db570a !important;
        box-shadow: 0 0 0 3px rgba(219, 87, 10, 0.08) !important;
        background: white !important;
    }
    
    input:hover, 
    select:hover {
        border-color: #c5c0b8 !important;
        background: white !important;
    }
    
    /* Subscribe button hover */
    button[onclick*="openPaymentModal"]:hover {
        background: #c44a08 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(219, 87, 10, 0.3);
    }
    
    /* Pay Now button hover */
    button[type="submit"]:hover {
        background: #c44a08 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(219, 87, 10, 0.3);
    }
    
    /* Plan card hover */
    div[style*="background: rgba(255,255,255,0.85)"] {
        transition: all 0.3s ease;
    }
    
    div[style*="background: rgba(255,255,255,0.85)"]:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06) !important;
    }
    
    /* History row hover */
    div[style*="border-bottom: 1px solid rgba(30, 58, 95, 0.04)"]:hover {
        background: rgba(30, 58, 95, 0.03) !important;
    }
    
    /* View All link hover */
    a[style*="color: #1e3a5f"]:hover {
        color: #4c1d95 !important;
    }
    
    /* Modal close button hover */
    button[onclick="closePaymentModal()"]:hover {
        color: #1a1a2e !important;
    }
    
    @media (max-width: 768px) {
        .grid-cols-3 {
            grid-template-columns: 1fr !important;
        }
        
        div[style*="display: flex; gap: 3;"] {
            flex-direction: column !important;
        }
        
        div[style*="display: flex; align-items: center; justify-content: space-between;"] {
            flex-wrap: wrap !important;
            gap: 0.5rem !important;
        }
    }
</style>

@endsection