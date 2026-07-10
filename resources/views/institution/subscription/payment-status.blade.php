@extends('layouts.librarian')

@section('title', 'Payment Status')

@section('content')

<div class="max-w-2xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 1rem; padding: 2rem; text-align: center; box-shadow: 0 8px 32px rgba(0,0,0,0.06);">
        
        @if($subscription->payment_status === 'paid')
            <!-- Success State -->
            <div style="width: 5rem; height: 5rem; border-radius: 9999px; background: rgba(6, 95, 70, 0.08); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <i class="ti ti-check" style="font-size: 2.5rem; color: #065f46;"></i>
            </div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin-bottom: 0.5rem;">Payment Successful</h2>
            <p style="color: #6b7280; margin-bottom: 1.5rem;">Your {{ ucfirst($subscription->plan) }} plan is now active.</p>
            <a href="{{ route('institution.subscription.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                <i class="ti ti-arrow-left"></i> Back to Dashboard
            </a>
            
        @elseif($subscription->payment_status === 'pending')
            <!-- Pending State -->
            <div style="width: 5rem; height: 5rem; border-radius: 9999px; background: rgba(217, 119, 6, 0.08); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <i class="ti ti-loader" style="font-size: 2.5rem; color: #d97706; animation: spin 1.5s linear infinite;"></i>
            </div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin-bottom: 0.5rem;">Payment Processing</h2>
            <p style="color: #6b7280; margin-bottom: 1rem;">Please wait while we confirm your payment.</p>
            
            <div style="background: rgba(30, 58, 95, 0.03); border: 1px solid rgba(30, 58, 95, 0.06); border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem;">
                <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Payment Method</p>
                <p style="color: #1a1a2e; font-weight: 600; margin: 0; text-transform: capitalize;">{{ $subscription->payment_method }}</p>
                @if($subscription->payment_method === 'mpesa')
                    <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">Check your phone for M-Pesa STK Push</p>
                @endif
            </div>
            
            <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('institution.subscription.payment-status', $subscription->id) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: rgba(255,255,255,0.7); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); color: #475569; border: 1px solid rgba(30, 58, 95, 0.12); border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; text-decoration: none;">
                    <i class="ti ti-refresh"></i> Check Status
                </a>
                <a href="{{ route('institution.subscription.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                    <i class="ti ti-home"></i> Go Home
                </a>
            </div>
            <p style="font-size: 0.7rem; color: #9ca3af; margin-top: 1rem;">Auto-refreshing in 10 seconds...</p>
            
        @else
            <!-- Failed State -->
            <div style="width: 5rem; height: 5rem; border-radius: 9999px; background: rgba(220, 38, 38, 0.08); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <i class="ti ti-x" style="font-size: 2.5rem; color: #dc2626;"></i>
            </div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin-bottom: 0.5rem;">Payment Failed</h2>
            <p style="color: #6b7280; margin-bottom: 0.5rem;">{{ $subscription->mpesa_response_description ?? 'Payment could not be completed.' }}</p>
            <a href="{{ route('institution.subscription.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                <i class="ti ti-refresh"></i> Try Again
            </a>
        @endif
    </div>
</div>

@if($subscription->payment_status === 'pending')
<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<script>
    // Auto-refresh every 10 seconds
    let attempts = 0;
    const maxAttempts = 30; // 5 minutes max
    
    function checkStatus() {
        attempts++;
        if (attempts > maxAttempts) return;
        
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                if (html.includes('Payment Successful') || html.includes('Payment Successful')) {
                    window.location.reload();
                }
            })
            .catch(() => {});
    }
    
    setTimeout(checkStatus, 10000);
</script>
@endif

<style>
    /* ========================================== */
    /* 1px DIM DARK BLUE BORDER STYLES            */
    /* ========================================== */

    /* Main card hover */
    div[style*="background: rgba(255,255,255,0.85)"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: rgba(255,255,255,0.85)"]:hover {
        box-shadow: 0 4px 16px rgba(30, 58, 95, 0.04) !important;
    }
    
    /* Check Status button hover */
    a[style*="Check Status"]:hover {
        border-color: #1e3a5f !important;
        background: rgba(255,255,255,0.9) !important;
        color: #1a1a2e !important;
    }
    
    /* Try Again / Back to Dashboard button hover */
    a[style*="background: #db570a"]:hover {
        background: #c44a08 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(219, 87, 10, 0.3);
    }
    
    @media (max-width: 640px) {
        div[style*="display: flex; gap: 0.75rem; justify-content: center;"] {
            flex-direction: column !important;
            align-items: center !important;
        }
        
        a[style*="display: inline-flex; gap: 0.5rem; padding: 0.6rem 1.25rem;"] {
            width: 100% !important;
            justify-content: center !important;
        }
    }
</style>

@endsection