@extends('layouts.library')

@section('title', $shelf->name . ' - ' . $institution->name)

@section('content')

<!-- Back Button -->
<div class="mb-4">
    <a href="{{ route('institution.public.index', $institution->id) }}" 
       style="color: #5b21b6; transition: color 0.2s; display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.875rem; text-decoration: none; font-weight: 500;">
        <i class="ti ti-arrow-left"></i> Back to Library
    </a>
</div>

<!-- ========================================== -->
<!-- SHELF HEADER                               -->
<!-- ========================================== -->
<div style="background: linear-gradient(135deg, rgba(91,33,182,0.08), rgba(219,87,10,0.05)); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 2px solid rgba(91,33,182,0.12); border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <h1 style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e;">{{ $shelf->name }}</h1>
                <span style="display: inline-block; padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 500; 
                    @if($shelf->status === 'full') color: #dc2626; background: rgba(220, 38, 38, 0.08);
                    @elseif($shelf->status === 'active') color: #065f46; background: rgba(6, 95, 70, 0.08);
                    @else color: #6b7280; background: rgba(0,0,0,0.04); @endif">
                    {{ ucfirst($shelf->status) }}
                </span>
            </div>
            <p style="color: #6b7280; font-size: 0.875rem; margin-top: 0.25rem;">
                <i class="ti ti-hash"></i> Code: {{ $shelf->code }}
                @if($shelf->floor)
                    | <i class="ti ti-map-pin"></i> Floor: {{ $shelf->floor }}
                @endif
                @if($shelf->section)
                    | Section: {{ $shelf->section }}
                @endif
            </p>
            @if($shelf->description)
                <p style="color: #9ca3af; font-size: 0.875rem; margin-top: 0.5rem;">{{ $shelf->description }}</p>
            @endif
        </div>
        <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.875rem;">
            <div style="text-align: center; background: rgba(91,33,182,0.06); padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid rgba(91,33,182,0.08);">
                <p style="font-size: 1.5rem; font-weight: 700; color: #5b21b6;">{{ $books->total() }}</p>
                <p style="font-size: 0.65rem; color: #6b7280;">Books</p>
            </div>
            <div style="text-align: center; background: rgba(219,87,10,0.06); padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid rgba(219,87,10,0.08);">
                <p style="font-size: 1.5rem; font-weight: 700; color: #db570a;">{{ $shelf->current_count }}/{{ $shelf->capacity }}</p>
                <p style="font-size: 0.65rem; color: #6b7280;">Capacity</p>
            </div>
        </div>
    </div>
    
    <!-- Capacity Progress -->
    <div style="margin-top: 1rem;">
        @php
            $percentage = $shelf->capacity > 0 ? round(($shelf->current_count / $shelf->capacity) * 100) : 0;
        @endphp
        <div style="display: flex; justify-content: space-between; font-size: 0.65rem; color: #6b7280; margin-bottom: 0.25rem;">
            <span>Capacity Usage</span>
            <span>{{ $percentage }}%</span>
        </div>
        <div style="width: 100%; background: rgba(0,0,0,0.06); border-radius: 9999px; height: 0.5rem; overflow: hidden;">
            <div style="height: 0.5rem; border-radius: 9999px; transition: width 0.5s; 
                @if($percentage >= 90) background: linear-gradient(90deg, #dc2626, #ef4444);
                @elseif($percentage >= 70) background: linear-gradient(90deg, #5b21b6, #7c3aed);
                @else background: linear-gradient(90deg, #065f46, #10b981); @endif" 
                style="width: {{ $percentage }}%;"></div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- VISUAL BOOKSHELF WITH COLORED SPINES       -->
<!-- ========================================== -->
<div style="background: linear-gradient(180deg, rgba(91,33,182,0.04), rgba(219,87,10,0.03)); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 2px solid rgba(91,33,182,0.08); border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3 style="font-size: 1rem; font-weight: 600; color: #1a1a2e; display: flex; align-items: center; gap: 0.5rem;">
            <i class="ti ti-books" style="color: #5b21b6;"></i>
            Shelf View ({{ $books->count() }} / {{ $shelf->capacity }} books)
        </h3>
    </div>

    @if($books->count() > 0)
        <div style="background: linear-gradient(180deg, rgba(91,33,182,0.03) 0%, rgba(0,0,0,0.02) 100%); border-radius: 0.75rem; padding: 1rem; border: 2px solid rgba(91,33,182,0.06);">
            
            <!-- Shelf Board (Top) - Colored Wood -->
            <div style="height: 0.6rem; background: linear-gradient(180deg, #8B7355, #6B5340, #5C4533); border-radius: 4px 4px 0 0; margin-bottom: 0.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.15); border: 1px solid rgba(139,115,85,0.3);"></div>
            
            <!-- Books on Shelf -->
            <div style="display: flex; align-items: flex-end; gap: 3px; min-height: 100px; padding: 0.5rem 0; flex-wrap: wrap;">
                @foreach($books as $book)
                    @php
                        $colors = [
                            'linear-gradient(180deg, #7c3aed, #4f46e5)',
                            'linear-gradient(180deg, #dc2626, #b91c1c)',
                            'linear-gradient(180deg, #059669, #047857)',
                            'linear-gradient(180deg, #d97706, #b45309)',
                            'linear-gradient(180deg, #2563eb, #1d4ed8)',
                            'linear-gradient(180deg, #7c3aed, #4f46e5)',
                            'linear-gradient(180deg, #db570a, #c44a08)',
                            'linear-gradient(180deg, #0d9488, #0f766e)',
                            'linear-gradient(180deg, #8b5cf6, #6d28d9)',
                            'linear-gradient(180deg, #ec4899, #be185d)'
                        ];
                        $randomColor = $colors[array_rand($colors)];
                    @endphp
                    <a href="{{ route('institution.public.show', [$institution->id, $book->id]) }}" 
                       style="display: inline-block; text-decoration: none; transition: all 0.3s ease; cursor: pointer;"
                       title="{{ $book->title }} - {{ $book->author }}">
                        <div style="height: {{ rand(65, 95) }}px; width: {{ rand(20, 28) }}px; border-radius: 3px 3px 2px 2px; box-shadow: 2px 0 8px rgba(0,0,0,0.2), inset -1px 0 0 rgba(255,255,255,0.05); position: relative; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,0.08); background: {{ $book->cover_image ? 'url('.url('media/'.$book->cover_image).') center/cover' : $randomColor }};"></div>
                             onmouseover="this.style.transform='translateY(-6px) scale(1.05)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.3)'"
                             onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='2px 0 8px rgba(0,0,0,0.2)'">
                            @if(!$book->cover_image)
                                <span style="color: rgba(255,255,255,0.9); font-size: 0.45rem; font-weight: 700; writing-mode: vertical-rl; text-orientation: mixed; letter-spacing: 1px; text-shadow: 0 0 10px rgba(0,0,0,0.8); padding: 2px; text-align: center; line-height: 1.2;">{{ strtoupper(substr($book->title, 0, 4)) }}</span>
                            @endif
                        </div>
                    </a>
                @endforeach
                
                <!-- Empty spaces on shelf -->
                @php
                    $emptySpaces = $shelf->capacity - $books->count();
                @endphp
                @for($i = 0; $i < $emptySpaces; $i++)
                    <div style="width: 16px; height: 8px; background: rgba(0,0,0,0.02); border-radius: 2px; border: 1px dashed rgba(0,0,0,0.06); flex-shrink: 0; margin-top: auto; margin-bottom: 0;"></div>
                @endfor
            </div>
            
            <!-- Shelf Board (Bottom) - Colored Wood -->
            <div style="height: 0.6rem; background: linear-gradient(180deg, #6B5340, #5C4533, #4A3728); border-radius: 0 0 4px 4px; margin-top: 0.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.15); border: 1px solid rgba(139,115,85,0.3);"></div>
        </div>
        
        <!-- Capacity Stats -->
        <div style="margin-top: 1rem; display: flex; flex-wrap: wrap; justify-content: space-between; font-size: 0.75rem; color: #6b7280;">
            <span style="background: rgba(91,33,182,0.06); padding: 0.2rem 0.8rem; border-radius: 9999px;">📚 {{ $books->count() }} books on shelf</span>
            <span style="background: rgba(219,87,10,0.06); padding: 0.2rem 0.8rem; border-radius: 9999px;">📦 {{ $emptySpaces }} spaces available</span>
            <span style="background: rgba(6,95,70,0.06); padding: 0.2rem 0.8rem; border-radius: 9999px;">📊 {{ round(($books->count() / $shelf->capacity) * 100) }}% full</span>
        </div>
        
        <!-- Progress Bar -->
        <div style="margin-top: 0.5rem; width: 100%; background: rgba(0,0,0,0.06); border-radius: 9999px; height: 0.25rem; overflow: hidden;">
            <div style="height: 0.25rem; border-radius: 9999px; transition: width 0.5s; 
                @if($percentage >= 90) background: linear-gradient(90deg, #dc2626, #ef4444);
                @elseif($percentage >= 70) background: linear-gradient(90deg, #5b21b6, #7c3aed);
                @else background: linear-gradient(90deg, #065f46, #10b981); @endif" 
                style="width: {{ $percentage }}%;"></div>
        </div>
    @else
        <div style="text-align: center; padding: 3rem 0; color: #9ca3af;">
            <i class="ti ti-books" style="font-size: 3rem; display: block; margin-bottom: 0.5rem; color: rgba(91,33,182,0.1);"></i>
            <p>No books on this shelf yet</p>
        </div>
    @endif
</div>

<!-- ========================================== -->
<!-- ALL BOOKS GRID - SMALLER CARDS             -->
<!-- ========================================== -->
@if($books->count() > 0)
    <h2 style="font-size: 1.125rem; font-weight: 700; color: #1a1a2e; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
        <i class="ti ti-books" style="color: #5b21b6;"></i> 
        All Books on this Shelf ({{ $books->total() }})
    </h2>
    
    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-3">
        @foreach($books as $book)
            <a href="{{ route('institution.public.show', [$institution->id, $book->id]) }}" 
               style="background: rgba(255,255,255,0.6); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.5rem; padding: 0.5rem; transition: all 0.3s ease; text-decoration: none; display: block;">
                <div style="aspect-ratio: 2/3; background: rgba(91,33,182,0.04); border-radius: 0.4rem; overflow: hidden; position: relative;">
                    @if($book->cover_image)
                        <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}"
                             style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;">
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-book" style="font-size: 1.5rem; color: rgba(91,33,182,0.1);"></i>
                        </div>
                    @endif
                    
                    {{-- Price Badge --}}
                    @php
                        $hasPrice = false;
                        if ($institution->type === 'bookstore') {
                            if (isset($book->price) && $book->price > 0) {
                                $hasPrice = true;
                            }
                        } elseif (isset($book->is_paid) && $book->is_paid) {
                            $hasPrice = true;
                        }
                    @endphp
                    
                    @if($hasPrice)
                        <span style="position: absolute; top: 0.2rem; right: 0.2rem; background: rgba(219, 87, 10, 0.85); color: white; font-size: 0.45rem; font-weight: 600; padding: 0.05rem 0.3rem; border-radius: 9999px; z-index: 5;">Paid</span>
                    @else
                        <span style="position: absolute; top: 0.2rem; right: 0.2rem; background: rgba(6, 95, 70, 0.85); color: white; font-size: 0.45rem; font-weight: 600; padding: 0.05rem 0.3rem; border-radius: 9999px; z-index: 5;">Free</span>
                    @endif
                </div>
                <div style="margin-top: 0.3rem;">
                    <p style="font-size: 0.65rem; font-weight: 500; color: #1a1a2e; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Str::limit($book->title, 20) }}</p>
                    <p style="font-size: 0.55rem; color: #6b7280; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Str::limit($book->author ?? 'Unknown', 15) }}</p>
                    @if($institution->type === 'bookstore' && isset($book->price) && $book->price > 0)
                        <p style="font-size: 0.5rem; color: #db570a; margin-top: 0.1rem; font-weight: 500;">
                            TSh {{ number_format($book->price, 2) }}
                        </p>
                    @endif
                    <p style="font-size: 0.5rem; color: #9ca3af; margin-top: 0.1rem;">
                        <i class="ti ti-map-pin" style="font-size: 0.35rem;"></i> {{ $book->shelf_number ?? 'No shelf' }}
                    </p>
                </div>
            </a>
        @endforeach
    </div>
    
    <!-- Pagination -->
    <div style="margin-top: 1.5rem;">
        {{ $books->appends(request()->query())->links() }}
    </div>
@endif

<style>
    /* ========================================== */
    /* SHELF PAGE STYLES                         */
    /* ========================================== */

    a[style*="Back to Library"]:hover {
        color: #4c1d95 !important;
    }
    
    /* Book spine hover */
    a[style*="transition: all 0.3s ease;"]:hover {
        z-index: 10;
    }
    
    /* Book card hover */
    a[style*="background: rgba(255,255,255,0.6);"]:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
        border-color: rgba(91, 33, 182, 0.12) !important;
    }
    
    a[style*="background: rgba(255,255,255,0.6);"]:hover img {
        transform: scale(1.05);
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
    
    /* Book spine gradient colors animation */
    @keyframes spineGlow {
        0% { box-shadow: 2px 0 8px rgba(0,0,0,0.2); }
        50% { box-shadow: 2px 0 15px rgba(0,0,0,0.3); }
        100% { box-shadow: 2px 0 8px rgba(0,0,0,0.2); }
    }
    
    .shelf-card {
        animation: spineGlow 3s ease-in-out infinite;
    }
    
    @media (max-width: 768px) {
        .grid-cols-3 {
            grid-template-columns: 1fr 1fr 1fr !important;
        }
        
        .grid-cols-4 {
            grid-template-columns: 1fr 1fr !important;
        }
        
        .grid-cols-5 {
            grid-template-columns: 1fr 1fr !important;
        }
        
        .grid-cols-6 {
            grid-template-columns: 1fr 1fr !important;
        }
        
        .grid-cols-7 {
            grid-template-columns: 1fr 1fr !important;
        }
        
        div[style*="display: flex; align-items: flex-end; gap: 3px;"] {
            gap: 2px !important;
        }
        
        div[style*="display: flex; flex-wrap: wrap; justify-content: space-between;"] {
            gap: 0.3rem !important;
        }
    }
</style>

@endsection