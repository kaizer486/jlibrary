@extends('layouts.librarian')

@section('title', $shelf->name)
@section('page-title', $shelf->name)

@section('content')

<div class="max-w-5xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <div class="mb-6">
        <a href="{{ route('institution.shelves.index') }}" style="color: #5b21b6; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.25rem; text-decoration: none; font-weight: 500;">
            <i class="ti ti-arrow-left"></i> Back to Shelves
        </a>
    </div>

    <!-- ========================================== -->
    <!-- SHELF HEADER                               -->
    <!-- ========================================== -->
    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 1rem; overflow: hidden; margin-bottom: 1.5rem; box-shadow: 0 8px 32px rgba(0,0,0,0.06);">
        
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(30, 58, 95, 0.08); background: rgba(30, 58, 95, 0.03);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $shelf->name }}</h1>
                    <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Code: <span style="font-family: monospace; font-weight: 600; color: #1e3a5f;">{{ $shelf->code }}</span></p>
                </div>
                <div>
                    {!! $shelf->status_badge !!}
                </div>
            </div>
        </div>
        
        <div style="padding: 1.5rem;">
            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div style="text-align: center; padding: 0.75rem; background: rgba(30, 58, 95, 0.03); border-radius: 0.75rem; border: 1px solid rgba(30, 58, 95, 0.08);">
                    <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $books->count() }}</p>
                    <p style="font-size: 0.65rem; color: #6b7280; margin: 0;">Books</p>
                </div>
                <div style="text-align: center; padding: 0.75rem; background: rgba(30, 58, 95, 0.03); border-radius: 0.75rem; border: 1px solid rgba(30, 58, 95, 0.08);">
                    <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $shelf->current_count }}/{{ $shelf->capacity }}</p>
                    <p style="font-size: 0.65rem; color: #6b7280; margin: 0;">Capacity</p>
                </div>
                <div style="text-align: center; padding: 0.75rem; background: rgba(30, 58, 95, 0.03); border-radius: 0.75rem; border: 1px solid rgba(30, 58, 95, 0.08);">
                    <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $shelf->getAvailableSlots() }}</p>
                    <p style="font-size: 0.65rem; color: #6b7280; margin: 0;">Available Slots</p>
                </div>
                <div style="text-align: center; padding: 0.75rem; background: rgba(30, 58, 95, 0.03); border-radius: 0.75rem; border: 1px solid rgba(30, 58, 95, 0.08);">
                    <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $shelf->category ?? 'N/A' }}</p>
                    <p style="font-size: 0.65rem; color: #6b7280; margin: 0;">Category</p>
                </div>
            </div>

            <!-- Capacity Progress -->
            <div style="margin-top: 1rem;">
                <div style="display: flex; justify-content: space-between; font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;">
                    <span>Capacity Usage</span>
                    <span>{{ $shelf->current_count }} / {{ $shelf->capacity }} ({{ $percentage }}%)</span>
                </div>
                <div style="width: 100%; background: rgba(30, 58, 95, 0.06); border-radius: 9999px; height: 0.5rem; overflow: hidden;">
                    <div style="height: 0.5rem; border-radius: 9999px; transition: width 0.5s; 
                        @if($percentage >= 90) background: #dc2626;
                        @elseif($percentage >= 70) background: #1e3a5f;
                        @else background: #065f46; @endif" 
                        style="width: {{ $percentage }}%;"></div>
                </div>
            </div>
            
            @if($shelf->description)
                <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: rgba(30, 58, 95, 0.03); border-radius: 0.75rem; border: 1px solid rgba(30, 58, 95, 0.06);">
                    <h3 style="font-weight: 600; color: #1a1a2e; margin: 0;">Description</h3>
                    <p style="color: #4b5563; margin-top: 0.25rem;">{{ $shelf->description }}</p>
                </div>
            @endif
            
            <!-- Location -->
            @if($shelf->getFullLocationAttribute() && $shelf->getFullLocationAttribute() !== ' |  |  | ')
                <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: rgba(30, 58, 95, 0.03); border-radius: 0.75rem; border: 1px solid rgba(30, 58, 95, 0.06);">
                    <h3 style="font-weight: 600; color: #1a1a2e; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                        <i class="ti ti-map-pin" style="color: #1e3a5f;"></i> Location
                    </h3>
                    <p style="color: #4b5563; margin-top: 0.25rem;">{{ $shelf->getFullLocationAttribute() }}</p>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;">
                        @if($shelf->floor)
                            <span style="font-size: 0.65rem; background: rgba(30, 58, 95, 0.06); color: #1e3a5f; padding: 0.1rem 0.5rem; border-radius: 9999px;">Floor: {{ $shelf->floor }}</span>
                        @endif
                        @if($shelf->section)
                            <span style="font-size: 0.65rem; background: rgba(30, 58, 95, 0.06); color: #1e3a5f; padding: 0.1rem 0.5rem; border-radius: 9999px;">Section: {{ $shelf->section }}</span>
                        @endif
                    </div>
                </div>
            @endif
            
            <!-- Actions -->
            <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                <a href="{{ route('institution.shelves.edit', $shelf) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                    <i class="ti ti-edit"></i> Edit Shelf
                </a>
                <form method="POST" action="{{ route('institution.shelves.destroy', $shelf) }}" 
                      onsubmit="return confirm('Delete this shelf?')" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #dc2626; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                        <i class="ti ti-trash"></i> Delete Shelf
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- VISUAL BOOKSHELF WITH BOOK SPINES          -->
    <!-- ========================================== -->
    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 1rem; overflow: hidden; margin-bottom: 1.5rem; box-shadow: 0 8px 32px rgba(0,0,0,0.06);">
        
        <div style="padding: 0.75rem 1.5rem; border-bottom: 1px solid rgba(30, 58, 95, 0.08); background: rgba(30, 58, 95, 0.03);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-weight: 600; color: #1a1a2e; display: flex; align-items: center; gap: 0.5rem; margin: 0; font-size: 1rem;">
                    <i class="ti ti-books" style="color: #1e3a5f;"></i>
                    Shelf View ({{ $books->count() }} / {{ $shelf->capacity }} books)
                </h3>
                <a href="{{ route('institution.books.create') }}?shelf={{ $shelf->code }}" style="color: #1e3a5f; font-size: 0.875rem; transition: color 0.2s; text-decoration: none;">
                    <i class="ti ti-plus"></i> Add Book
                </a>
            </div>
        </div>
        
        <div style="padding: 1.5rem;">
            @if($books->count() > 0)
                <div style="background: linear-gradient(180deg, rgba(30,58,95,0.03) 0%, rgba(0,0,0,0.02) 100%); border-radius: 0.75rem; padding: 1rem; border: 1px solid rgba(30,58,95,0.06);">
                    
                    <!-- Shelf Board (Top) -->
                    <div style="height: 0.5rem; background: linear-gradient(180deg, #8B7355, #6B5340); border-radius: 0.25rem; margin-bottom: 0.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1);"></div>
                    
                    <!-- Books on Shelf -->
                    <div style="display: flex; align-items: flex-end; gap: 0.25rem; min-height: 120px; padding: 0.5rem 0; flex-wrap: wrap;">
                        @foreach($books as $book)
                            <a href="{{ route('institution.books.show', $book->id) }}" 
                               style="display: inline-block; text-decoration: none; transition: transform 0.2s ease; cursor: pointer;"
                               title="{{ $book->title }} - {{ $book->author }}">
                                <div style="height: {{ rand(60, 100) }}px; width: {{ rand(18, 30) }}px; border-radius: 3px 3px 0 0; box-shadow: 2px 0 5px rgba(0,0,0,0.15); position: relative; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,0.05); background: {{ $book->cover_image ? 'url('.url('media/'.$book->cover_image).') center/cover' : 'linear-gradient(135deg, #7c3aed, #4f46e5)' }};">
                                    @if(!$book->cover_image)
                                        <span style="color: white; font-size: 0.5rem; font-weight: 700; writing-mode: vertical-rl; text-orientation: mixed; letter-spacing: 1px; text-shadow: 0 0 5px rgba(0,0,0,0.5); padding: 2px;">{{ strtoupper(substr($book->title, 0, 3)) }}</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                        
                        <!-- Empty spaces on shelf -->
                        @php
                            $emptySpaces = $shelf->capacity - $books->count();
                        @endphp
                        @for($i = 0; $i < $emptySpaces; $i++)
                            <div style="width: 14px; height: 10px; background: rgba(0,0,0,0.02); border-radius: 2px; border: 1px dashed rgba(0,0,0,0.06); flex-shrink: 0;"></div>
                        @endfor
                    </div>
                    
                    <!-- Shelf Board (Bottom) -->
                    <div style="height: 0.5rem; background: linear-gradient(180deg, #6B5340, #5C4533); border-radius: 0.25rem; margin-top: 0.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1);"></div>
                </div>
                
                <!-- Capacity Stats -->
                <div style="margin-top: 1rem; display: flex; flex-wrap: wrap; justify-content: space-between; font-size: 0.75rem; color: #6b7280;">
                    <span>{{ $books->count() }} books on shelf</span>
                    <span>{{ $emptySpaces }} spaces available</span>
                    <span>{{ round(($books->count() / $shelf->capacity) * 100) }}% full</span>
                </div>
                
                <!-- Progress Bar -->
                <div style="margin-top: 0.5rem; width: 100%; background: rgba(30, 58, 95, 0.06); border-radius: 9999px; height: 0.25rem; overflow: hidden;">
                    <div style="height: 0.25rem; border-radius: 9999px; transition: width 0.5s; 
                        @if($percentage >= 90) background: #dc2626;
                        @elseif($percentage >= 70) background: #1e3a5f;
                        @else background: #065f46; @endif" 
                        style="width: {{ $percentage }}%;"></div>
                </div>
            @else
                <div style="text-align: center; padding: 3rem 0; color: #9ca3af;">
                    <i class="ti ti-books" style="font-size: 3rem; display: block; margin-bottom: 0.5rem; color: rgba(30,58,95,0.1);"></i>
                    <p>No books on this shelf yet</p>
                    <a href="{{ route('institution.books.create') }}" style="color: #1e3a5f; text-decoration: none; display: block; margin-top: 0.5rem;">
                        <i class="ti ti-plus"></i> Add first book
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>

<style>
    /* ========================================== */
    /* 1px DIM DARK BLUE BORDER STYLES            */
    /* ========================================== */

    a[style*="Back to Shelves"]:hover {
        color: #4c1d95 !important;
    }
    
    /* Edit button hover */
    a[style*="background: #db570a"]:hover {
        background: #c44a08 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(219, 87, 10, 0.3);
    }
    
    /* Delete button hover */
    button[style*="background: #dc2626"]:hover {
        background: #b91c1c !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }
    
    /* Add Book link hover */
    a[style*="color: #1e3a5f"]:hover {
        color: #4c1d95 !important;
    }
    
    /* Book spine hover */
    a[style*="transition: transform 0.2s ease;"]:hover {
        transform: translateY(-5px) scale(1.05);
        z-index: 10;
    }
    
    /* Stats card hover */
    div[style*="background: rgba(30, 58, 95, 0.03)"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: rgba(30, 58, 95, 0.03)"]:hover {
        background: rgba(30, 58, 95, 0.06) !important;
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
        
        div[style*="display: flex; align-items: flex-end; gap: 0.25rem;"] {
            gap: 0.15rem !important;
        }
        
        div[style*="display: flex; gap: 0.75rem; margin-top: 1.5rem;"] {
            flex-direction: column !important;
        }
        
        a[style*="background: #db570a"],
        button[style*="background: #dc2626"] {
            width: 100% !important;
            justify-content: center !important;
        }
    }
</style>

@endsection