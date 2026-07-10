@extends('layouts.librarian')

@section('title', 'Order #' . $order->id)
@section('page-title', 'Order #' . $order->id)

@section('content')

<div class="max-w-4xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <div class="mb-6">
        <a href="{{ route('institution.orders.index') }}" style="color: #5b21b6; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.25rem; text-decoration: none; font-weight: 500;">
            <i class="ti ti-arrow-left"></i> Back to Orders
        </a>
    </div>

    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 1rem; box-shadow: 0 8px 32px rgba(0,0,0,0.06); overflow: hidden;">
        
        <!-- Header -->
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(91, 33, 182, 0.08); background: rgba(91, 33, 182, 0.04); display: flex; justify-content: space-between; align-items: center;">
            <h1 style="font-size: 1.125rem; font-weight: 700; color: #1a1a2e; margin: 0;">Order #{{ $order->id }}</h1>
            <span style="display: inline-block; padding: 0.2rem 0.75rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500; 
                @if($order->status === 'completed') color: #065f46; background: rgba(6, 95, 70, 0.08);
                @elseif($order->status === 'pending') color: #d97706; background: rgba(217, 119, 6, 0.08);
                @elseif($order->status === 'processing') color: #2563eb; background: rgba(37, 99, 235, 0.08);
                @else color: #dc2626; background: rgba(220, 38, 38, 0.08); @endif">
                {{ ucfirst($order->status) }}
            </span>
        </div>

        <div style="padding: 1.5rem;">
            
            <!-- Order Info Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <!-- Customer -->
                <div style="background: rgba(91, 33, 182, 0.04); border: 1px solid rgba(91, 33, 182, 0.06); border-radius: 0.75rem; padding: 1rem;">
                    <p style="font-size: 0.65rem; color: #6b7280; text-transform: uppercase; font-weight: 600; margin: 0;">Customer</p>
                    <p style="color: #1a1a2e; font-weight: 500; margin: 0.25rem 0 0 0;">{{ $order->user->full_name ?? 'Unknown' }}</p>
                    <p style="font-size: 0.85rem; color: #6b7280; margin: 0;">{{ $order->user->email ?? 'No email' }}</p>
                </div>
                
                <!-- Order Date -->
                <div style="background: rgba(219, 87, 10, 0.04); border: 1px solid rgba(219, 87, 10, 0.06); border-radius: 0.75rem; padding: 1rem;">
                    <p style="font-size: 0.65rem; color: #6b7280; text-transform: uppercase; font-weight: 600; margin: 0;">Order Date</p>
                    <p style="color: #1a1a2e; font-weight: 500; margin: 0.25rem 0 0 0;">{{ $order->created_at->format('F d, Y H:i') }}</p>
                </div>
                
                <!-- Total -->
                <div style="background: rgba(6, 95, 70, 0.04); border: 1px solid rgba(6, 95, 70, 0.06); border-radius: 0.75rem; padding: 1rem;">
                    <p style="font-size: 0.65rem; color: #6b7280; text-transform: uppercase; font-weight: 600; margin: 0;">Total</p>
                    <p style="font-size: 1.5rem; font-weight: 700; color: #db570a; margin: 0.25rem 0 0 0;">TSh {{ number_format($order->total, 2) }}</p>
                </div>
                
                <!-- Payment Method -->
                <div style="background: rgba(124, 58, 237, 0.04); border: 1px solid rgba(124, 58, 237, 0.06); border-radius: 0.75rem; padding: 1rem;">
                    <p style="font-size: 0.65rem; color: #6b7280; text-transform: uppercase; font-weight: 600; margin: 0;">Payment Method</p>
                    <p style="color: #1a1a2e; font-weight: 500; margin: 0.25rem 0 0 0;">{{ $order->payment_method ?? 'Not specified' }}</p>
                </div>
            </div>

            <!-- Notes -->
            @if($order->notes)
                <div style="margin-top: 1.5rem; padding: 1rem; background: rgba(59, 130, 246, 0.04); border: 1px solid rgba(59, 130, 246, 0.06); border-radius: 0.75rem;">
                    <p style="font-size: 0.65rem; color: #6b7280; text-transform: uppercase; font-weight: 600; margin: 0;">Notes</p>
                    <p style="color: #1a1a2e; margin: 0.25rem 0 0 0;">{{ $order->notes }}</p>
                </div>
            @endif

            <!-- Back Button -->
            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e2e0db;">
                <a href="{{ route('institution.orders.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; text-decoration: none;">
                    <i class="ti ti-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

</div>

<style>
    /* ========================================== */
    /* CLEAN DETAILS STYLES                      */
    /* ========================================== */

    a[style*="Back to Orders"]:hover {
        color: #4c1d95 !important;
    }
    
    a[style*="Back"]:hover {
        border-color: #db570a !important;
        background: white !important;
        color: #1a1a2e !important;
    }
    
    /* Info cards hover effect */
    div[style*="background: rgba(91, 33, 182, 0.04)"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: rgba(91, 33, 182, 0.04)"]:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }
    
    div[style*="background: rgba(219, 87, 10, 0.04)"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: rgba(219, 87, 10, 0.04)"]:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }
    
    div[style*="background: rgba(6, 95, 70, 0.04)"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: rgba(6, 95, 70, 0.04)"]:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }
    
    div[style*="background: rgba(124, 58, 237, 0.04)"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: rgba(124, 58, 237, 0.04)"]:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }
    
    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
        
        div[style*="display: flex; justify-content: space-between; align-items: center;"] {
            flex-direction: column !important;
            gap: 0.5rem !important;
            align-items: flex-start !important;
        }
    }
</style>

@endsection