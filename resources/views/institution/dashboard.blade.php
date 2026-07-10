@extends('layouts.librarian')

@section('title', 'Institution Dashboard')
@section('page-title', 'Institution Dashboard')

@section('content')

<div class="max-w-7xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <!-- Welcome Section -->
    <div class="mb-8">
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">
                        Welcome back, {{ auth()->user()->full_name }}
                    </h2>
                    <p style="color: #6b7280; margin-top: 0.25rem;">Manage your institution from here.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('institution.books.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                        <i class="ti ti-plus"></i> Add Book
                    </a>
                    <a href="{{ route('institution.shelves.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: rgba(255,255,255,0.7); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); color: #475569; border: 1px solid rgba(30, 58, 95, 0.12); border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; text-decoration: none;">
                        <i class="ti ti-plus"></i> Add Shelf
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(30, 58, 95, 0.08); border-radius: 0.75rem; padding: 1.25rem; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid #5b21b6; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div>
                <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $stats['total_books'] ?? 0 }}</p>
                <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">Total Books</p>
            </div>
            <i class="ti ti-books" style="color: rgba(91, 33, 182, 0.3); font-size: 1.75rem;"></i>
        </div>
        
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(30, 58, 95, 0.08); border-radius: 0.75rem; padding: 1.25rem; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid #065f46; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div>
                <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $stats['total_members'] ?? 0 }}</p>
                <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">Total Members</p>
            </div>
            <i class="ti ti-users" style="color: rgba(91, 33, 182, 0.3); font-size: 1.75rem;"></i>
        </div>
        
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(30, 58, 95, 0.08); border-radius: 0.75rem; padding: 1.25rem; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid #d97706; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div>
                <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $stats['pending_requests'] ?? 0 }}</p>
                <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">Join Requests</p>
            </div>
            <i class="ti ti-user-plus" style="color: rgba(217, 119, 6, 0.3); font-size: 1.75rem;"></i>
        </div>
        
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(30, 58, 95, 0.08); border-radius: 0.75rem; padding: 1.25rem; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid #dc2626; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div>
                <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $stats['pending_withdrawal_requests'] ?? 0 }}</p>
                <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">Pending Withdrawals</p>
            </div>
            <i class="ti ti-wallet" style="color: rgba(220, 38, 38, 0.3); font-size: 1.75rem;"></i>
        </div>
    </div>

    <!-- Subscription Status -->
    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(30, 58, 95, 0.08); border-radius: 0.75rem; padding: 1rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Subscription Status</p>
                <p style="font-size: 1.25rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $subscription['plan_label'] }}</p>
                <p style="font-size: 0.875rem; color: {{ $subscription['is_active'] ? '#065f46' : '#dc2626' }}; margin: 0;">
                    {{ $subscription['status_label'] }}
                </p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Days Left</p>
                    <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $subscription['days_left'] }}</p>
                </div>
                <a href="{{ route('institution.subscription.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: rgba(255,255,255,0.7); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); color: #475569; border: 1px solid rgba(30, 58, 95, 0.12); border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; text-decoration: none;">
                        <i class="ti ti-settings"></i> Manage
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Recent Books -->
        <div class="lg:col-span-2">
            <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(30, 58, 95, 0.08); border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <div class="flex items-center justify-between mb-4">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #1a1a2e; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                        <i class="ti ti-books" style="color: #5b21b6;"></i> Recent Books
                    </h3>
                    <a href="{{ route('institution.books.index') }}" style="font-size: 0.875rem; color: #5b21b6; font-weight: 500; text-decoration: none; transition: color 0.2s;">
                        View All →
                    </a>
                </div>
                
                @if(isset($recentBooks) && $recentBooks->count() > 0)
                    <div class="overflow-x-auto">
                        <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                            <thead>
                                <tr style="text-align: left; color: #6b7280; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid rgba(30, 58, 95, 0.06);">
                                    <th style="padding-bottom: 0.5rem; font-weight: 500;">Book</th>
                                    <th style="padding-bottom: 0.5rem; font-weight: 500;">Author</th>
                                    <th style="padding-bottom: 0.5rem; font-weight: 500;">Status</th>
                                </tr>
                            </thead>
                            <tbody style="border-top: 1px solid rgba(30, 58, 95, 0.04);">
                                @foreach($recentBooks as $book)
                                    <tr style="transition: background 0.2s; border-bottom: 1px solid rgba(30, 58, 95, 0.04);">
                                        <td style="padding: 0.75rem 1rem 0.75rem 0;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <span style="font-weight: 500; color: #1a1a2e;">{{ Str::limit($book->title, 30) }}</span>
                                            </div>
                                        </td>
                                        <td style="padding: 0.75rem 1rem 0.75rem 0; color: #6b7280;">{{ $book->author ?? 'Unknown' }}</td>
                                        <td style="padding: 0.75rem 1rem 0.75rem 0;">
                                            @if($book->status === 'approved')
                                                <span style="display: inline-block; padding: 0.1rem 0.5rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 500; color: #065f46; background: rgba(6, 95, 70, 0.08);">Approved</span>
                                            @elseif($book->status === 'pending')
                                                <span style="display: inline-block; padding: 0.1rem 0.5rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 500; color: #d97706; background: rgba(217, 119, 6, 0.08);">Pending</span>
                                            @else
                                                <span style="display: inline-block; padding: 0.1rem 0.5rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 500; color: #dc2626; background: rgba(220, 38, 38, 0.08);">Rejected</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; padding: 2rem 0; color: #9ca3af;">
                        <i class="ti ti-books" style="font-size: 2.5rem; display: block; margin-bottom: 0.5rem; color: rgba(91, 33, 182, 0.15);"></i>
                        <p style="margin: 0;">No books added yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            
            <!-- Pending Join Requests -->
            <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(30, 58, 95, 0.08); border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <div class="flex items-center justify-between mb-4">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #1a1a2e; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                        <i class="ti ti-user-plus" style="color: #d97706;"></i> Join Requests
                    </h3>
                    <a href="{{ route('institution.join-requests.index') }}" style="font-size: 0.875rem; color: #5b21b6; font-weight: 500; text-decoration: none; transition: color 0.2s;">
                        View All →
                    </a>
                </div>
                
                @if(isset($recentRequests) && $recentRequests->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        @foreach($recentRequests as $request)
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem; background: rgba(30, 58, 95, 0.03); border-radius: 0.5rem;">
                                <div>
                                    <p style="font-size: 0.875rem; font-weight: 500; color: #1a1a2e; margin: 0;">{{ $request->user->full_name }}</p>
                                    <p style="font-size: 0.7rem; color: #6b7280; margin: 0;">{{ $request->user->email }}</p>
                                </div>
                                <span style="display: inline-block; padding: 0.1rem 0.5rem; border-radius: 9999px; font-size: 0.6rem; font-weight: 500; color: #d97706; background: rgba(217, 119, 6, 0.08);">Pending</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color: #9ca3af; font-size: 0.875rem; text-align: center; padding: 1rem 0; margin: 0;">No pending requests</p>
                @endif
            </div>

            <!-- Quick Actions -->
            <div style="background: linear-gradient(135deg, rgba(91,33,182,0.04), rgba(219,87,10,0.03)); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(91,33,182,0.08); border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #1a1a2e; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                    <i class="ti ti-zap" style="color: #5b21b6;"></i> Quick Actions
                </h3>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('institution.books.create') }}" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); color: #4b5563; padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 500; text-align: center; border: 1px solid rgba(30, 58, 95, 0.06); transition: all 0.2s; text-decoration: none; display: block;">
                        <i class="ti ti-plus" style="color: #5b21b6; font-size: 1.25rem; display: block;"></i>
                        Add Book
                    </a>
                    <a href="{{ route('institution.shelves.create') }}" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); color: #4b5563; padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 500; text-align: center; border: 1px solid rgba(30, 58, 95, 0.06); transition: all 0.2s; text-decoration: none; display: block;">
                        <i class="ti ti-layout-grid" style="color: #5b21b6; font-size: 1.25rem; display: block;"></i>
                        Add Shelf
                    </a>
                    <a href="{{ route('institution.members.index') }}" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); color: #4b5563; padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 500; text-align: center; border: 1px solid rgba(30, 58, 95, 0.06); transition: all 0.2s; text-decoration: none; display: block;">
                        <i class="ti ti-users" style="color: #5b21b6; font-size: 1.25rem; display: block;"></i>
                        Members
                    </a>
                    <a href="{{ route('institution.borrowings.index') }}" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); color: #4b5563; padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 500; text-align: center; border: 1px solid rgba(30, 58, 95, 0.06); transition: all 0.2s; text-decoration: none; display: block;">
                        <i class="ti ti-bookmark" style="color: #5b21b6; font-size: 1.25rem; display: block;"></i>
                        Borrowings
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>

<style>
    /* ========================================== */
    /* 1px DIM DARK BLUE BORDER STYLES            */
    /* ========================================== */

    a[style*="Add Book"]:hover {
        background: #c44a08 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(219, 87, 10, 0.3);
    }
    
    a[style*="Add Shelf"]:hover {
        border-color: #1e3a5f !important;
        background: rgba(255,255,255,0.9) !important;
        color: #1a1a2e !important;
    }
    
    a[style*="Manage"]:hover {
        border-color: #1e3a5f !important;
        background: rgba(255,255,255,0.9) !important;
        color: #1a1a2e !important;
    }
    
    a[style*="View All"]:hover {
        color: #4c1d95 !important;
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
        background: rgba(30, 58, 95, 0.02) !important;
    }
    
    /* Quick action hover */
    a[style*="background: rgba(255,255,255,0.6)"]:hover {
        background: rgba(30, 58, 95, 0.06) !important;
        border-color: rgba(30, 58, 95, 0.15) !important;
        color: #1a1a2e !important;
        transform: translateY(-1px);
    }
    
    /* Main card hover */
    div[style*="background: rgba(255,255,255,0.85)"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: rgba(255,255,255,0.85)"]:hover {
        box-shadow: 0 4px 16px rgba(30, 58, 95, 0.04) !important;
    }
    
    @media (max-width: 768px) {
        .grid-cols-4 {
            grid-template-columns: 1fr 1fr !important;
        }
        
        div[style*="display: flex; align-items: center; justify-content: space-between;"] {
            flex-wrap: wrap !important;
            gap: 0.5rem !important;
        }
    }
</style>

@endsection