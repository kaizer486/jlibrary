@extends('layouts.app')

@section('content')

<div style="position: fixed; inset: 0; background: linear-gradient(135deg, #1e293b, #0f172a, #312e81); z-index: -10;"></div>

<div style="position: relative; z-index: 10; min-height: 100vh;">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <i class="ti ti-building-community" style="color: #a78bfa; font-size: 1.75rem;"></i>
                        <h1 style="font-size: 1.75rem; font-weight: 700; color: #ffffff;">All Institutions</h1>
                    </div>
                    <div style="width: 5rem; height: 0.25rem; background: #fbbf24; border-radius: 9999px; margin-bottom: 0.75rem;"></div>
                    <p style="color: #d1d5db;">Browse and connect with learning communities worldwide</p>
                </div>
                <a href="{{ route('dashboard') }}" style="color: #d1d5db; background: rgba(255,255,255,0.05); padding: 0.5rem 1rem; border-radius: 9999px; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; border: 1px solid rgba(255,255,255,0.05);">
                    <i class="ti ti-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
        
        <!-- Institutions Grid -->
        @if($institutions->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($institutions as $institution)
            <div style="background: rgba(255,255,255,0.95); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border-radius: 1rem; box-shadow: 0 10px 40px rgba(0,0,0,0.08); overflow: hidden; transition: all 0.3s ease;">
                <!-- Colored top accent -->
                <div style="height: 0.25rem; background: linear-gradient(90deg, #6366f1, #8b5cf6, #ec4899);"></div>
                
                <div style="padding: 1.25rem;">
                    <!-- Icon & Name -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div style="width: 3rem; height: 3rem; background: linear-gradient(135deg, #e0e7ff, #ede9fe); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-building" style="color: #4f46e5; font-size: 1.25rem;"></i>
                            </div>
                            <div>
                                <h3 style="font-weight: 700; color: #1f2937; font-size: 1.125rem;">{{ Str::limit($institution->name, 25) }}</h3>
                                <span style="font-size: 0.7rem; color: #6b7280;">{{ $institution->type_label ?? 'Institution' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div class="flex items-center gap-4" style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.75rem;">
                        <div class="flex items-center gap-1">
                            <i class="ti ti-users"></i>
                            <span>{{ $institution->users_count ?? 0 }} members</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <i class="ti ti-books"></i>
                            <span>{{ $institution->books_count ?? 0 }} books</span>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <p style="color: #4b5563; font-size: 0.875rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 1rem;">
                        {{ $institution->description ?? ($institution->city ? 'Located in ' . $institution->city : 'Join this institution to access exclusive learning resources.') }}
                    </p>
                    
                    <!-- View Details Button -->
                    <a href="{{ route('institutions.show', $institution->id) }}" 
                       style="width: 100%; display: block; text-align: center; background: #f3f4f6; color: #4f46e5; font-weight: 500; padding: 0.5rem 1rem; border-radius: 0.75rem; transition: all 0.2s; font-size: 0.875rem; border: 1px solid #e5e7eb; text-decoration: none;">
                        <i class="ti ti-eye"></i> View Details
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div style="margin-top: 2rem;">
            {{ $institutions->links() }}
        </div>
        
        @else
        <div style="background: rgba(255,255,255,0.05); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border-radius: 1rem; padding: 3rem; text-align: center; border: 1px solid rgba(255,255,255,0.1);">
            <i class="ti ti-building" style="font-size: 3rem; color: #9ca3af; display: block; margin-bottom: 0.75rem;"></i>
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #ffffff; margin-bottom: 0.5rem;">No Institutions Available</h3>
            <p style="color: #d1d5db;">Check back later for institutions to join</p>
        </div>
        @endif
        
    </div>
</div>

<style>
    /* ========================================== */
    /* CLEAN INSTITUTIONS PAGE STYLES             */
    /* ========================================== */

    a[style*="Back to Dashboard"]:hover {
        background: rgba(255,255,255,0.1) !important;
        color: #ffffff !important;
    }
    
    /* Card hover */
    div[style*="background: rgba(255,255,255,0.95)"] {
        transition: all 0.3s ease;
    }
    
    div[style*="background: rgba(255,255,255,0.95)"]:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12) !important;
    }
    
    /* View button hover */
    a[style*="background: #f3f4f6"]:hover {
        background: #e0e7ff !important;
        border-color: #818cf8 !important;
        transform: translateY(-1px);
    }
    
    /* Pagination styling */
    .pagination {
        display: flex;
        gap: 0.25rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .pagination span,
    .pagination a {
        padding: 0.4rem 0.8rem;
        border-radius: 0.4rem;
        border: 1px solid rgba(255,255,255,0.1);
        background: rgba(255,255,255,0.05);
        color: #d1d5db;
        text-decoration: none;
        transition: all 0.2s;
        font-size: 0.875rem;
    }
    
    .pagination a:hover {
        background: rgba(255,255,255,0.1);
        border-color: rgba(255,255,255,0.2);
        color: #ffffff;
    }
    
    .pagination .active span {
        background: #8b5cf6;
        border-color: #8b5cf6;
        color: #ffffff;
    }
    
    .pagination .disabled span {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    @media (max-width: 768px) {
        .grid-cols-4 {
            grid-template-columns: 1fr 1fr !important;
        }
        
        .grid-cols-3 {
            grid-template-columns: 1fr !important;
        }
        
        div[style*="display: flex; justify-content: space-between;"] {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 0.5rem !important;
        }
    }
</style>

@endsection