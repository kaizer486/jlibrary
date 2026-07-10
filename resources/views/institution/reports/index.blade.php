@extends('layouts.librarian')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')

@section('content')

<div class="max-w-7xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">View library analytics and reports</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('institution.reports.export') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: rgba(255,255,255,0.7); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; text-decoration: none;">
                <i class="ti ti-download"></i> Export Report
            </a>
            <button onclick="window.location.reload()" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: rgba(255,255,255,0.7); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                <i class="ti ti-refresh"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 0.75rem; padding: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $stats['total_books'] ?? 0 }}</p>
                    <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Total Books</p>
                </div>
                <div style="width: 2.5rem; height: 2.5rem; background: rgba(91, 33, 182, 0.08); border-radius: 9999px; display: flex; align-items: center; justify-content: center;">
                    <i class="ti ti-books" style="color: #5b21b6;"></i>
                </div>
            </div>
        </div>
        
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(37, 99, 235, 0.12); border-radius: 0.75rem; padding: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $stats['total_members'] ?? 0 }}</p>
                    <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Total Members</p>
                </div>
                <div style="width: 2.5rem; height: 2.5rem; background: rgba(37, 99, 235, 0.08); border-radius: 9999px; display: flex; align-items: center; justify-content: center;">
                    <i class="ti ti-users" style="color: #2563eb;"></i>
                </div>
            </div>
        </div>
        
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(217, 119, 6, 0.12); border-radius: 0.75rem; padding: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ number_format($stats['total_views'] ?? 0) }}</p>
                    <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Total Views</p>
                </div>
                <div style="width: 2.5rem; height: 2.5rem; background: rgba(217, 119, 6, 0.08); border-radius: 9999px; display: flex; align-items: center; justify-content: center;">
                    <i class="ti ti-eye" style="color: #d97706;"></i>
                </div>
            </div>
        </div>
        
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(6, 95, 70, 0.12); border-radius: 0.75rem; padding: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ number_format($stats['total_downloads'] ?? 0) }}</p>
                    <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Total Downloads</p>
                </div>
                <div style="width: 2.5rem; height: 2.5rem; background: rgba(6, 95, 70, 0.08); border-radius: 9999px; display: flex; align-items: center; justify-content: center;">
                    <i class="ti ti-download" style="color: #065f46;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 0.75rem; padding: 1rem; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #5b21b6; margin: 0;">{{ $stats['total_borrowings'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Total Borrowings</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(37, 99, 235, 0.12); border-radius: 0.75rem; padding: 1rem; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #2563eb; margin: 0;">{{ $stats['active_borrowings'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Active Borrowings</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Popular Books -->
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <h3 style="font-size: 1rem; font-weight: 600; color: #1a1a2e; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ti ti-trending-up" style="color: #db570a;"></i> Most Popular Books
            </h3>
            @if($popularBooks->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($popularBooks as $book)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.75rem; background: rgba(0,0,0,0.02); border-radius: 0.5rem; border: 1px solid rgba(0,0,0,0.04);">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <span style="font-size: 0.875rem; font-weight: 700; color: #5b21b6; width: 1.5rem;">{{ $loop->iteration }}</span>
                                <div>
                                    <p style="font-weight: 500; color: #1a1a2e; font-size: 0.875rem; margin: 0;">{{ Str::limit($book->title, 30) }}</p>
                                    <p style="font-size: 0.7rem; color: #6b7280; margin: 0;">{{ $book->author ?? 'Unknown' }}</p>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <p style="font-size: 0.875rem; font-weight: 700; color: #5b21b6; margin: 0;">{{ number_format($book->views_count ?? 0) }}</p>
                                <p style="font-size: 0.6rem; color: #6b7280; margin: 0;">views</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color: #9ca3af; text-align: center; padding: 1rem 0;">No data available</p>
            @endif
        </div>

        <!-- Top Categories -->
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <h3 style="font-size: 1rem; font-weight: 600; color: #1a1a2e; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ti ti-tags" style="color: #5b21b6;"></i> Top Categories
            </h3>
            @if($topCategories->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($topCategories as $category)
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.15rem;">
                                <span style="color: #1a1a2e;">{{ $category->category ?? 'Uncategorized' }}</span>
                                <span style="font-weight: 600; color: #5b21b6;">{{ $category->total }}</span>
                            </div>
                            <div style="width: 100%; background: rgba(0,0,0,0.06); border-radius: 9999px; height: 0.35rem; overflow: hidden;">
                                @php
                                    $max = $topCategories->first()->total ?? 1;
                                    $percentage = ($category->total / $max) * 100;
                                @endphp
                                <div style="height: 0.35rem; border-radius: 9999px; background: linear-gradient(90deg, #5b21b6, #7c3aed); width: {{ $percentage }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color: #9ca3af; text-align: center; padding: 1rem 0;">No data available</p>
            @endif
        </div>
    </div>

</div>

<style>
    /* ========================================== */
    /* CLEAN REPORTS STYLES                      */
    /* ========================================== */

    a[style*="Export Report"]:hover {
        border-color: #db570a !important;
        background: rgba(255,255,255,0.9) !important;
        color: #1a1a2e !important;
    }
    
    button[style*="Refresh"]:hover {
        border-color: #db570a !important;
        background: rgba(255,255,255,0.9) !important;
        color: #1a1a2e !important;
    }
    
    /* Stats card hover */
    div[style*="background: rgba(255,255,255,0.85)"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: rgba(255,255,255,0.85)"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06) !important;
    }
    
    /* Popular book item hover */
    div[style*="padding: 0.6rem 0.75rem;"] {
        transition: all 0.2s ease;
    }
    
    div[style*="padding: 0.6rem 0.75rem;"]:hover {
        background: rgba(91, 33, 182, 0.04) !important;
    }
    
    @media (max-width: 768px) {
        .grid-cols-4 {
            grid-template-columns: 1fr 1fr !important;
        }
        
        .grid-cols-2 {
            grid-template-columns: 1fr !important;
        }
        
        div[style*="display: flex; align-items: center; justify-content: space-between;"] {
            flex-wrap: wrap !important;
            gap: 0.5rem !important;
        }
    }
</style>

@endsection