@extends('layouts.librarian')

@section('title', 'Borrowing Details')
@section('page-title', 'Borrowing Details')

@section('content')

<div class="max-w-3xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <div class="mb-6">
        <a href="{{ route('institution.borrowings.index') }}" style="color: #5b21b6; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.25rem; text-decoration: none; font-weight: 500;">
            <i class="ti ti-arrow-left"></i> Back to Borrowings
        </a>
    </div>

    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 1rem; box-shadow: 0 8px 32px rgba(0,0,0,0.06); overflow: hidden;">
        
        <!-- Header -->
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(91, 33, 182, 0.08); background: rgba(91, 33, 182, 0.04);">
            <h1 style="font-size: 1.125rem; font-weight: 700; color: #1a1a2e; margin: 0;">Borrowing Details</h1>
            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">#{{ $borrowing->id }} - {{ $borrowing->book->title }}</p>
        </div>

        <div style="padding: 1.5rem;">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Book Info -->
                <div style="background: white; border: 1px solid rgba(91, 33, 182, 0.08); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(91, 33, 182, 0.04); border-bottom: 1px solid rgba(91, 33, 182, 0.06);">
                        <h3 style="font-weight: 600; color: #1a1a2e; font-size: 0.85rem; margin: 0;">
                            <i class="ti ti-book" style="color: #5b21b6;"></i> Book Information
                        </h3>
                    </div>
                    <div style="padding: 1rem 1.25rem;">
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f0ede8;">
                            <span style="color: #6b7280; font-size: 0.85rem;">Title</span>
                            <span style="color: #1a1a2e; font-weight: 500; font-size: 0.85rem;">{{ $borrowing->book->title }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f0ede8;">
                            <span style="color: #6b7280; font-size: 0.85rem;">Author</span>
                            <span style="color: #1a1a2e; font-weight: 500; font-size: 0.85rem;">{{ $borrowing->book->author ?? 'Unknown' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f0ede8;">
                            <span style="color: #6b7280; font-size: 0.85rem;">Shelf</span>
                            <span style="color: #1a1a2e; font-weight: 500; font-size: 0.85rem;">{{ $borrowing->book->shelf_number ?? 'Not assigned' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                            <span style="color: #6b7280; font-size: 0.85rem;">Status</span>
                            <span>{!! $borrowing->book->status_badge !!}</span>
                        </div>
                    </div>
                </div>

                <!-- Borrowing Info -->
                <div style="background: white; border: 1px solid rgba(219, 87, 10, 0.08); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(219, 87, 10, 0.04); border-bottom: 1px solid rgba(219, 87, 10, 0.06);">
                        <h3 style="font-weight: 600; color: #1a1a2e; font-size: 0.85rem; margin: 0;">
                            <i class="ti ti-info-circle" style="color: #db570a;"></i> Borrowing Information
                        </h3>
                    </div>
                    <div style="padding: 1rem 1.25rem;">
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f0ede8;">
                            <span style="color: #6b7280; font-size: 0.85rem;">Borrower</span>
                            <span style="color: #1a1a2e; font-weight: 500; font-size: 0.85rem;">{{ $borrowing->user->full_name }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f0ede8;">
                            <span style="color: #6b7280; font-size: 0.85rem;">Email</span>
                            <span style="color: #1a1a2e; font-weight: 500; font-size: 0.85rem;">{{ $borrowing->user->email }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f0ede8;">
                            <span style="color: #6b7280; font-size: 0.85rem;">Borrowed At</span>
                            <span style="color: #1a1a2e; font-weight: 500; font-size: 0.85rem;">{{ $borrowing->borrowed_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f0ede8;">
                            <span style="color: #6b7280; font-size: 0.85rem;">Due Date</span>
                            <span style="color: {{ $borrowing->isOverdue() ? '#dc2626' : '#1a1a2e' }}; font-weight: {{ $borrowing->isOverdue() ? '700' : '500' }}; font-size: 0.85rem;">
                                {{ $borrowing->due_date->format('M d, Y') }}
                                @if($borrowing->isOverdue())
                                    <span style="color: #dc2626; font-weight: 700;">(Overdue)</span>
                                @endif
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f0ede8;">
                            <span style="color: #6b7280; font-size: 0.85rem;">Status</span>
                            <span>{!! $borrowing->status_badge !!}</span>
                        </div>
                        @if($borrowing->notes)
                            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                                <span style="color: #6b7280; font-size: 0.85rem;">Notes</span>
                                <span style="color: #1a1a2e; font-weight: 500; font-size: 0.85rem; text-align: right; max-width: 60%;">{{ $borrowing->notes }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e2e0db;">
                @if($borrowing->isActive())
                    <form method="POST" action="{{ route('institution.borrowings.return', $borrowing) }}" style="display: inline;">
                        @csrf
                        <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #065f46; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                            <i class="ti ti-check"></i> Return Book
                        </button>
                    </form>
                    <form method="POST" action="{{ route('institution.borrowings.destroy', $borrowing) }}" 
                          onsubmit="return confirm('Cancel this borrowing?')" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: rgba(220, 38, 38, 0.06); color: #dc2626; border: 1px solid rgba(220, 38, 38, 0.12); border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                            <i class="ti ti-x"></i> Cancel
                        </button>
                    </form>
                @endif
                <a href="{{ route('institution.borrowings.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                    Back
                </a>
            </div>

            <!-- Processed By -->
            @if($borrowing->borrowed_by || $borrowing->returned_to)
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e0db; font-size: 0.7rem; color: #6b7280;">
                    @if($borrowing->borrowed_by)
                        <p style="margin: 0.15rem 0;">Processed by: {{ $borrowing->borrowedBy?->full_name ?? 'Unknown' }}</p>
                    @endif
                    @if($borrowing->returned_to)
                        <p style="margin: 0.15rem 0;">Returned to: {{ $borrowing->returnedTo?->full_name ?? 'Unknown' }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

</div>

<style>
    /* ========================================== */
    /* CLEAN DETAILS STYLES                      */
    /* ========================================== */

    a[style*="Back to Borrowings"]:hover {
        color: #4c1d95 !important;
    }
    
    /* Return button hover */
    button[style*="background: #065f46"]:hover {
        background: #044d37 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(6, 95, 70, 0.3);
    }
    
    /* Cancel button hover */
    button[style*="background: rgba(220, 38, 38, 0.06)"]:hover {
        background: rgba(220, 38, 38, 0.12) !important;
        transform: translateY(-1px);
    }
    
    /* Back button hover */
    a[style*="Back"]:hover {
        border-color: #db570a !important;
        background: white !important;
        color: #1a1a2e !important;
    }
    
    /* Cards hover effect */
    div[style*="background: white; border: 1px solid"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: white; border: 1px solid"]:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }
    
    /* Status badge styling for book status */
    .badge-approved {
        display: inline-block;
        padding: 0.15rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 500;
        color: #065f46;
        background: rgba(6, 95, 70, 0.08);
    }
    
    .badge-pending {
        display: inline-block;
        padding: 0.15rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 500;
        color: #d97706;
        background: rgba(217, 119, 6, 0.08);
    }
    
    .badge-rejected {
        display: inline-block;
        padding: 0.15rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 500;
        color: #dc2626;
        background: rgba(220, 38, 38, 0.08);
    }
    
    /* Borrowing status badges */
    .badge-borrowed {
        display: inline-block;
        padding: 0.15rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 500;
        color: #2563eb;
        background: rgba(37, 99, 235, 0.08);
    }
    
    .badge-overdue {
        display: inline-block;
        padding: 0.15rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 500;
        color: #dc2626;
        background: rgba(220, 38, 38, 0.08);
    }
    
    .badge-returned {
        display: inline-block;
        padding: 0.15rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 500;
        color: #065f46;
        background: rgba(6, 95, 70, 0.08);
    }
    
    @media (max-width: 768px) {
        .grid-cols-2 {
            grid-template-columns: 1fr !important;
        }
        
        div[style*="display: flex; flex-wrap: wrap; gap: 0.75rem;"] {
            flex-direction: column !important;
        }
        
        div[style*="display: flex; flex-wrap: wrap; gap: 0.75rem;"] form,
        div[style*="display: flex; flex-wrap: wrap; gap: 0.75rem;"] a {
            width: 100% !important;
        }
        
        div[style*="display: flex; flex-wrap: wrap; gap: 0.75rem;"] button,
        div[style*="display: flex; flex-wrap: wrap; gap: 0.75rem;"] a {
            width: 100% !important;
            justify-content: center !important;
        }
    }
</style>

@endsection