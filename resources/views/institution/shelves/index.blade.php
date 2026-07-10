@extends('layouts.librarian')

@section('title', 'Shelf Management')
@section('page-title', 'Shelf Management')

@section('content')

<div class="max-w-7xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Organize your library shelves</p>
        </div>
        <a href="{{ route('institution.shelves.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
            <i class="ti ti-plus"></i> Add Shelf
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 0.75rem; padding: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $stats['total'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Total Shelves</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 0.75rem; padding: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $stats['active'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Active</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 0.75rem; padding: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $stats['full'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Full</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 0.75rem; padding: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $stats['inactive'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Inactive</p>
        </div>
    </div>

    <!-- Search -->
    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <form method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Search by name, code, or category..." 
                       value="{{ request('search') }}"
                       style="width: 100%; padding: 0.6rem 1rem; background: #faf8f5; border: 1px solid rgba(30, 58, 95, 0.12); border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;">
            </div>
            <select name="status" style="padding: 0.6rem 2.5rem 0.6rem 1rem; background: #faf8f5; border: 1px solid rgba(30, 58, 95, 0.12); border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer; min-width: 150px;">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="full" {{ request('status') == 'full' ? 'selected' : '' }}>Full</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                <i class="ti ti-search"></i> Filter
            </button>
            <a href="{{ route('institution.shelves.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #faf8f5; color: #475569; border: 1px solid rgba(30, 58, 95, 0.12); border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                <i class="ti ti-x"></i> Clear
            </a>
        </form>
    </div>

    <!-- Shelves Grid -->
    @if($shelves->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($shelves as $shelf)
                @php
                    $bookCount = $shelf->books()->count();
                    $percentage = $shelf->capacity > 0 ? round(($bookCount / $shelf->capacity) * 100) : 0;
                    $displayBooks = min($bookCount, 20);
                @endphp
                
                <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 0.75rem; overflow: hidden; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    
                    <div style="padding: 1rem;">
                        <!-- Header -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <h3 style="font-weight: 700; color: #1a1a2e; font-size: 1.125rem; margin: 0;">{{ $shelf->name }}</h3>
                                <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Code: <span style="font-family: monospace; font-weight: 600; color: #1e3a5f;">{{ $shelf->code }}</span></p>
                            </div>
                            {!! $shelf->status_badge !!}
                        </div>
                        
                        @if($shelf->category)
                            <p style="font-size: 0.875rem; color: #1e3a5f; margin-top: 0.25rem;">
                                <i class="ti ti-tag" style="color: #1e3a5f;"></i> {{ $shelf->category }}
                            </p>
                        @endif
                        
                        <!-- Location -->
                        @if($shelf->getFullLocationAttribute() && $shelf->getFullLocationAttribute() !== ' |  |  | ')
                            <div style="display: flex; flex-wrap: wrap; gap: 0.25rem; margin-top: 0.5rem;">
                                @if($shelf->floor)
                                    <span style="font-size: 0.65rem; background: rgba(30, 58, 95, 0.06); color: #1e3a5f; padding: 0.1rem 0.5rem; border-radius: 9999px;">{{ $shelf->floor }}</span>
                                @endif
                                @if($shelf->section)
                                    <span style="font-size: 0.65rem; background: rgba(30, 58, 95, 0.06); color: #1e3a5f; padding: 0.1rem 0.5rem; border-radius: 9999px;">{{ $shelf->section }}</span>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Stats -->
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.75rem; font-size: 0.875rem;">
                            <span style="color: #4b5563;">
                                <i class="ti ti-books" style="color: #1e3a5f;"></i> {{ $bookCount }} books
                            </span>
                            <span style="color: #4b5563;">
                                <i class="ti ti-users" style="color: #1e3a5f;"></i> {{ $shelf->current_count }}/{{ $shelf->capacity }}
                            </span>
                        </div>
                        
                        <!-- Visual Book Count (Mini Books) -->
                        <div style="display: flex; align-items: center; gap: 0.1rem; flex-wrap: wrap; margin-top: 0.5rem;">
                            @for($i = 0; $i < $displayBooks; $i++)
                                <span style="font-size: 0.6rem; color: #1e3a5f;">▬</span>
                            @endfor
                            @if($bookCount > 20)
                                <span style="font-size: 0.6rem; color: #6b7280; margin-left: 0.25rem;">+{{ $bookCount - 20 }}</span>
                            @endif
                            @if($bookCount == 0)
                                <span style="font-size: 0.6rem; color: #9ca3af;">Empty shelf</span>
                            @endif
                        </div>
                        
                        <!-- Progress Bar -->
                        <div style="margin-top: 0.5rem; width: 100%; background: rgba(30, 58, 95, 0.06); border-radius: 9999px; height: 0.4rem; overflow: hidden;">
                            <div style="height: 0.4rem; border-radius: 9999px; transition: width 0.5s; 
                                @if($percentage >= 90) background: #dc2626;
                                @elseif($percentage >= 70) background: #d97706;
                                @else background: #1e3a5f; @endif" 
                                style="width: {{ $percentage }}%;"></div>
                        </div>
                        <p style="font-size: 0.6rem; color: #6b7280; margin-top: 0.15rem; text-align: right;">{{ $percentage }}% capacity</p>
                        
                        @if($shelf->description)
                            <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $shelf->description }}</p>
                        @endif
                        
                        <!-- Actions -->
                        <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid rgba(30, 58, 95, 0.08);">
                            <a href="{{ route('institution.shelves.show', $shelf) }}" style="color: #1e3a5f; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; text-decoration: none;">
                                <i class="ti ti-eye"></i> View
                            </a>
                            <a href="{{ route('institution.shelves.edit', $shelf) }}" style="color: #1e3a5f; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; text-decoration: none;">
                                <i class="ti ti-edit"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('institution.shelves.destroy', $shelf) }}" 
                                  onsubmit="return confirm('Delete this shelf?')" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="color: #dc2626; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; background: none; border: none; cursor: pointer; padding: 0;">
                                    <i class="ti ti-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div style="margin-top: 1.5rem;">{{ $shelves->links() }}</div>
    @else
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 0.75rem; padding: 3rem; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <i class="ti ti-books" style="font-size: 3.5rem; color: #d6d2cb; display: block; margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem;">No Shelves Found</h3>
            <p style="color: #9ca3af;">Start organizing your library by creating shelves.</p>
            <a href="{{ route('institution.shelves.create') }}" style="display: inline-block; margin-top: 1rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                <i class="ti ti-plus"></i> Create First Shelf
            </a>
        </div>
    @endif
</div>

<style>
    /* ========================================== */
    /* 1px DIM DARK BLUE BORDER STYLES            */
    /* ========================================== */

    a[style*="Add Shelf"]:hover {
        background: #c44a08 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(219, 87, 10, 0.3);
    }
    
    input:focus, 
    select:focus {
        border-color: #db570a !important;
        box-shadow: 0 0 0 3px rgba(219, 87, 10, 0.08) !important;
        background: white !important;
    }
    
    input:hover, 
    select:hover {
        border-color: #1e3a5f !important;
        background: white !important;
    }
    
    button[type="submit"]:hover {
        background: #c44a08 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(219, 87, 10, 0.3);
    }
    
    a[style*="Clear"]:hover {
        border-color: #1e3a5f !important;
        background: white !important;
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
    
    /* Shelf card hover */
    div[style*="background: rgba(255,255,255,0.85)"]:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
        border-color: rgba(30, 58, 95, 0.2) !important;
    }
    
    /* Action links hover */
    a[href*="show"]:hover {
        color: #4c1d95 !important;
    }
    
    a[href*="edit"]:hover {
        color: #1d4ed8 !important;
    }
    
    button[type="submit"]:hover {
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
    
    /* Mini books color variation */
    span[style*="font-size: 0.6rem; color: #1e3a5f;"]:nth-child(6n+1) { color: #1e3a5f; }
    span[style*="font-size: 0.6rem; color: #1e3a5f;"]:nth-child(6n+2) { color: #db570a; }
    span[style*="font-size: 0.6rem; color: #1e3a5f;"]:nth-child(6n+3) { color: #065f46; }
    span[style*="font-size: 0.6rem; color: #1e3a5f;"]:nth-child(6n+4) { color: #2563eb; }
    span[style*="font-size: 0.6rem; color: #1e3a5f;"]:nth-child(6n+5) { color: #d97706; }
    span[style*="font-size: 0.6rem; color: #1e3a5f;"]:nth-child(6n+6) { color: #7c3aed; }
    
    @media (max-width: 768px) {
        .grid-cols-4 {
            grid-template-columns: 1fr 1fr !important;
        }
        
        .grid-cols-3 {
            grid-template-columns: 1fr !important;
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
    }
</style>

@endsection