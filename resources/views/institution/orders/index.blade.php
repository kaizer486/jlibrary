@extends('layouts.librarian')

@section('title', 'Orders & Sales')
@section('page-title', 'Orders & Sales')

@section('content')

<div class="max-w-7xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Manage your bookstore orders and sales</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #5b21b6; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $stats['total'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Total Orders</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(217, 119, 6, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #d97706; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #d97706; margin: 0;">{{ $stats['pending'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Pending</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(6, 95, 70, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #065f46; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #065f46; margin: 0;">{{ $stats['completed'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Completed</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(220, 38, 38, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #dc2626; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #dc2626; margin: 0;">{{ $stats['cancelled'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Cancelled</p>
        </div>
    </div>

    <!-- Orders Table -->
    @if($orders->count() > 0)
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div class="overflow-x-auto">
                <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                    <thead>
                        <tr style="background: rgba(91, 33, 182, 0.04); text-align: left; border-bottom: 1px solid #e2e0db;">
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Order ID</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Customer</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Total</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Date</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="border-top: 1px solid #e2e0db;">
                        @foreach($orders as $order)
                            <tr style="transition: background 0.2s; border-bottom: 1px solid #f0ede8;">
                                <td style="padding: 0.75rem 1rem; font-weight: 500; color: #1a1a2e;">#{{ $order->id }}</td>
                                <td style="padding: 0.75rem 1rem; color: #4b5563;">{{ $order->user->full_name ?? 'Unknown' }}</td>
                                <td style="padding: 0.75rem 1rem; font-weight: 600; color: #db570a;">TSh {{ number_format($order->total, 2) }}</td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span style="display: inline-block; padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500; 
                                        @if($order->status === 'completed') color: #065f46; background: rgba(6, 95, 70, 0.08);
                                        @elseif($order->status === 'pending') color: #d97706; background: rgba(217, 119, 6, 0.08);
                                        @elseif($order->status === 'processing') color: #2563eb; background: rgba(37, 99, 235, 0.08);
                                        @else color: #dc2626; background: rgba(220, 38, 38, 0.08); @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: #6b7280; font-size: 0.85rem;">{{ $order->created_at->format('M d, Y') }}</td>
                                <td style="padding: 0.75rem 1rem; text-align: right;">
                                    <a href="{{ route('institution.orders.show', $order) }}" style="color: #5b21b6; transition: color 0.2s; text-decoration: none;" title="View">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div style="margin-top: 1.5rem;">{{ $orders->links() }}</div>
    @else
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem; padding: 3rem; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <i class="ti ti-shopping-cart" style="font-size: 3.5rem; color: #d6d2cb; display: block; margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.5rem;">No Orders Yet</h3>
            <p style="color: #6b7280;">Your bookstore hasn't received any orders yet.</p>
        </div>
    @endif

</div>

<style>
    /* ========================================== */
    /* CLEAN TABLE & STATS STYLES                */
    /* ========================================== */

    /* Stats card hover */
    div[style*="border-left: 4px solid"] {
        transition: all 0.2s ease;
    }
    
    div[style*="border-left: 4px solid"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06) !important;
    }
    
    /* Table row hover */
    tbody tr:hover {
        background: rgba(91, 33, 182, 0.03) !important;
    }
    
    /* View icon hover */
    a[title="View"]:hover {
        color: #4c1d95 !important;
    }
    
    /* Pagination styling */
    .pagination {
        display: flex;
        gap: 0.25rem;
        justify-content: center;
    }
    
    .pagination span,
    .pagination a {
        padding: 0.4rem 0.8rem;
        border-radius: 0.4rem;
        border: 1px solid #e2e0db;
        background: white;
        color: #1a1a2e;
        text-decoration: none;
        transition: all 0.2s;
        font-size: 0.875rem;
    }
    
    .pagination a:hover {
        border-color: #db570a;
        background: #faf8f5;
    }
    
    .pagination .active span {
        background: #db570a;
        border-color: #db570a;
        color: white;
    }
    
    @media (max-width: 768px) {
        .grid-cols-4 {
            grid-template-columns: 1fr 1fr !important;
        }
        
        table {
            font-size: 0.75rem !important;
        }
        
        td, th {
            padding: 0.5rem !important;
        }
    }
</style>

@endsection