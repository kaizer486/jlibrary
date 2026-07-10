@extends('layouts.librarian')

@section('title', 'Subscription History')

@section('content')

<div class="max-w-6xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <div class="mb-6">
        <a href="{{ route('institution.subscription.index') }}" style="color: #5b21b6; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.25rem; text-decoration: none; font-weight: 500;">
            <i class="ti ti-arrow-left"></i> Back to Subscription
        </a>
    </div>

    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 1rem; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.06);">
        
        <!-- Header -->
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(30, 58, 95, 0.08); background: rgba(30, 58, 95, 0.03);">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                <i class="ti ti-history" style="color: #1e3a5f;"></i> Full Subscription History
            </h2>
        </div>
        
        <div style="padding: 1.5rem;">
            @if($history->count() > 0)
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                        <thead style="background: rgba(30, 58, 95, 0.03); border-bottom: 1px solid rgba(30, 58, 95, 0.08);">
                            <tr>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">#</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Plan</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Start Date</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">End Date</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Amount</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Method</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                            </tr>
                        </thead>
                        <tbody style="border-top: 1px solid rgba(30, 58, 95, 0.06);">
                            @foreach($history as $entry)
                            <tr style="transition: background 0.2s; border-bottom: 1px solid rgba(30, 58, 95, 0.04);">
                                <td style="padding: 0.75rem 1rem; color: #6b7280; font-size: 0.875rem;">{{ $loop->iteration }}</td>
                                <td style="padding: 0.75rem 1rem; font-weight: 600; color: #1a1a2e; font-size: 0.875rem; text-transform: capitalize;">
                                    {{ $entry->plan->name ?? $entry->plan ?? 'N/A' }}
                                </td>
                                <td style="padding: 0.75rem 1rem; color: #4b5563; font-size: 0.875rem;">
                                    {{ $entry->start_date ? $entry->start_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td style="padding: 0.75rem 1rem; color: #4b5563; font-size: 0.875rem;">
                                    {{ $entry->end_date ? $entry->end_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td style="padding: 0.75rem 1rem; font-weight: 600; color: #db570a; font-size: 0.875rem;">TSh {{ number_format($entry->amount ?? 0, 2) }}</td>
                                <td style="padding: 0.75rem 1rem; color: #4b5563; font-size: 0.875rem; text-transform: uppercase;">{{ $entry->payment_method ?? 'N/A' }}</td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span style="display: inline-block; padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500; 
                                        @if($entry->status === 'active') color: #065f46; background: rgba(6, 95, 70, 0.08);
                                        @elseif($entry->status === 'cancelled') color: #6b7280; background: rgba(0, 0, 0, 0.04);
                                        @elseif($entry->status === 'expired') color: #dc2626; background: rgba(220, 38, 38, 0.08);
                                        @else color: #d97706; background: rgba(217, 119, 6, 0.08); @endif">
                                        {{ ucfirst($entry->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div style="margin-top: 1.5rem;">
                    {{ $history->links() }}
                </div>
            @else
                <p style="color: #9ca3af; text-align: center; padding: 2rem 0;">No subscription history found.</p>
            @endif
        </div>
    </div>
</div>

<style>
    /* ========================================== */
    /* 1px DIM DARK BLUE BORDER STYLES            */
    /* ========================================== */

    a[style*="Back to Subscription"]:hover {
        color: #4c1d95 !important;
    }
    
    /* Table row hover */
    tbody tr:hover {
        background: rgba(30, 58, 95, 0.03) !important;
    }
    
    /* Main card hover */
    div[style*="background: rgba(255,255,255,0.85)"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: rgba(255,255,255,0.85)"]:hover {
        box-shadow: 0 4px 16px rgba(30, 58, 95, 0.04) !important;
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
        border: 1px solid rgba(30, 58, 95, 0.12);
        background: white;
        color: #1a1a2e;
        text-decoration: none;
        transition: all 0.2s;
        font-size: 0.875rem;
    }
    
    .pagination a:hover {
        border-color: #1e3a5f;
        background: #faf8f5;
    }
    
    .pagination .active span {
        background: #1e3a5f;
        border-color: #1e3a5f;
        color: white;
    }
    
    @media (max-width: 768px) {
        table {
            font-size: 0.75rem !important;
        }
        
        td, th {
            padding: 0.5rem !important;
        }
    }
</style>

@endsection