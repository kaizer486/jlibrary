@extends('layouts.librarian')

@section('title', 'Borrowing Management')
@section('page-title', 'Borrowing Management')

@section('content')

<div class="max-w-7xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Manage book borrowings</p>
        </div>
        <a href="{{ route('institution.borrowings.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
            <i class="ti ti-plus"></i> New Borrowing
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #5b21b6; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $stats['total'] }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Total Borrowings</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(37, 99, 235, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #2563eb; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #2563eb; margin: 0;">{{ $stats['active'] }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Active</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(220, 38, 38, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #dc2626; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #dc2626; margin: 0;">{{ $stats['overdue'] }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Overdue</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(6, 95, 70, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #065f46; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #065f46; margin: 0;">{{ $stats['returned'] }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Returned</p>
        </div>
    </div>

    <!-- Search & Filters -->
    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <form method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Search by book title..." 
                       value="{{ request('search') }}"
                       style="width: 100%; padding: 0.6rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;">
            </div>
            <select name="status" style="padding: 0.6rem 2.5rem 0.6rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer; min-width: 150px;">
                <option value="">All Status</option>
                <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
            </select>
            <select name="user_id" style="padding: 0.6rem 2.5rem 0.6rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer; min-width: 150px;">
                <option value="">All Users</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" {{ request('user_id') == $member->id ? 'selected' : '' }}>
                        {{ $member->full_name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                <i class="ti ti-search"></i> Filter
            </button>
            <a href="{{ route('institution.borrowings.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                <i class="ti ti-x"></i> Clear
            </a>
        </form>
    </div>

    <!-- Borrowings Table -->
    @if($borrowings->count() > 0)
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div class="overflow-x-auto">
                <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                    <thead>
                        <tr style="background: rgba(91, 33, 182, 0.04); text-align: left; border-bottom: 1px solid #e2e0db;">
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Book</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Borrower</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Borrowed</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Due Date</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="border-top: 1px solid #e2e0db;">
                        @foreach($borrowings as $borrowing)
                            <tr style="transition: background 0.2s; border-bottom: 1px solid #f0ede8;">
                                <td style="padding: 0.75rem 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span style="font-weight: 500; color: #1a1a2e;">{{ Str::limit($borrowing->book->title, 30) }}</span>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: #4b5563;">{{ $borrowing->user->full_name }}</td>
                                <td style="padding: 0.75rem 1rem; color: #6b7280;">{{ $borrowing->borrowed_at->format('M d, Y') }}</td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span style="{{ $borrowing->isOverdue() ? 'color: #dc2626; font-weight: 700;' : 'color: #6b7280;' }}">
                                        {{ $borrowing->due_date->format('M d, Y') }}
                                        @if($borrowing->isOverdue())
                                            <span style="color: #dc2626;">({{ $borrowing->getDaysLeft() }} days overdue)</span>
                                        @endif
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    {!! $borrowing->status_badge !!}
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: right;">
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                                        @if($borrowing->isActive())
                                            <form method="POST" action="{{ route('institution.borrowings.return', $borrowing) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" style="color: #065f46; background: none; border: none; cursor: pointer; transition: color 0.2s; padding: 0;" title="Return Book">
                                                    <i class="ti ti-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('institution.borrowings.show', $borrowing) }}" style="color: #5b21b6; transition: color 0.2s; text-decoration: none;" title="View">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        @if($borrowing->isActive())
                                            <form method="POST" action="{{ route('institution.borrowings.destroy', $borrowing) }}" 
                                                  onsubmit="return confirm('Cancel this borrowing?')" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="color: #dc2626; background: none; border: none; cursor: pointer; transition: color 0.2s; padding: 0;" title="Cancel">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div style="margin-top: 1.5rem;">
            {{ $borrowings->withQueryString()->links() }}
        </div>
    @else
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem; padding: 3rem; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <i class="ti ti-book" style="font-size: 3.5rem; color: #d6d2cb; display: block; margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem;">No Borrowings</h3>
            <p style="color: #9ca3af;">No borrowing records found.</p>
        </div>
    @endif

</div>

<style>
    /* ========================================== */
    /* CLEAN TABLE & STATS STYLES                */
    /* ========================================== */

    a[style*="New Borrowing"]:hover {
        background: #c44a08 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(219, 87, 10, 0.3);
    }
    
    input:focus, 
    select:focus {
        border-color: #db570a !important;
        box-shadow: 0 0 0 3px rgba(219, 87, 10, 0.1) !important;
        background: white !important;
    }
    
    input:hover, 
    select:hover {
        border-color: #c5c0b8 !important;
        background: white !important;
    }
    
    button[type="submit"]:hover {
        background: #c44a08 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(219, 87, 10, 0.3);
    }
    
    a[style*="Clear"]:hover {
        border-color: #db570a !important;
        background: white !important;
        color: #1a1a2e !important;
    }
    
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
    
    /* Action icons hover */
    button[title="Return Book"]:hover {
        color: #059669 !important;
    }
    
    a[title="View"]:hover {
        color: #4c1d95 !important;
    }
    
    button[title="Cancel"]:hover {
        color: #b91c1c !important;
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
        
        form[method="GET"] {
            flex-direction: column !important;
        }
        
        form[method="GET"] > div,
        form[method="GET"] select,
        form[method="GET"] button,
        form[method="GET"] a {
            width: 100% !important;
            min-width: unset !important;
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