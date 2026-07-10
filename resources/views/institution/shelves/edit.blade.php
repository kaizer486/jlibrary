@extends('layouts.librarian')

@section('title', 'Edit Shelf')
@section('page-title', 'Edit Shelf')

@section('content')

<div class="max-w-4xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <div class="mb-6">
        <a href="{{ route('librarian.shelves.index') }}" style="color: #5b21b6; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.25rem; text-decoration: none; font-weight: 500;">
            <i class="ti ti-arrow-left"></i> Back to Shelves
        </a>
    </div>

    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(30, 58, 95, 0.12); border-radius: 1rem; box-shadow: 0 8px 32px rgba(0,0,0,0.06); overflow: hidden;">
        
        <!-- Header -->
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(30, 58, 95, 0.08); background: rgba(30, 58, 95, 0.03);">
            <h1 style="font-size: 1.125rem; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                <i class="ti ti-edit" style="color: #1e3a5f;"></i> Edit Shelf
            </h1>
            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">Update shelf details - {{ $shelf->code }}</p>
        </div>

        <form method="POST" action="{{ route('librarian.shelves.update', $shelf) }}" style="padding: 1.5rem;">
            @csrf
            @method('PUT')

            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                
                <!-- ========================================== -->
                <!-- BASIC INFO                                 -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(30, 58, 95, 0.03); border-bottom: 1px solid rgba(30, 58, 95, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-info-circle" style="color: #1e3a5f;"></i> Basic Information
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Shelf Name</label>
                            <input type="text" name="name" value="{{ old('name', $shelf->name) }}" required
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e8e4de; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="e.g., Fiction Section A">
                            @error('name') 
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Shelf Code</label>
                            <input type="text" name="code" value="{{ old('code', $shelf->code) }}" required
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e8e4de; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; font-family: monospace;" placeholder="e.g., A-01, B-02, C-03">
                            <p style="font-size: 0.65rem; color: #6b7280; margin-top: 0.25rem;">Unique identifier for the shelf</p>
                            @error('code') 
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- CATEGORY & STATUS                          -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(30, 58, 95, 0.03); border-bottom: 1px solid rgba(30, 58, 95, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-tag" style="color: #1e3a5f;"></i> Category & Status
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Category</label>
                            <input type="text" name="category" value="{{ old('category', $shelf->category) }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e8e4de; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="e.g., Fiction, Science, History">
                            @error('category') 
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Status</label>
                            <select name="status" style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 1rem; background: #faf8f5; border: 1px solid #e8e4de; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;">
                                <option value="active" {{ old('status', $shelf->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $shelf->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="full" {{ old('status', $shelf->status) == 'full' ? 'selected' : '' }}>Full</option>
                            </select>
                            @error('status') 
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- DESCRIPTION                                -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(30, 58, 95, 0.03); border-bottom: 1px solid rgba(30, 58, 95, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-file-description" style="color: #1e3a5f;"></i> Description
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem;">
                        <textarea name="description" rows="3" 
                                  style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e8e4de; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; min-height: 80px; resize: vertical; font-family: inherit;" 
                                  placeholder="Describe the shelf location, contents, or special notes">{{ old('description', $shelf->description) }}</textarea>
                        @error('description') 
                            <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                        @enderror
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- PHYSICAL LOCATION                          -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(30, 58, 95, 0.03); border-bottom: 1px solid rgba(30, 58, 95, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-map-pin" style="color: #1e3a5f;"></i> Physical Location
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Floor</label>
                            <input type="text" name="floor" value="{{ old('floor', $shelf->floor) }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e8e4de; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="e.g., Ground, 1st, 2nd, Basement">
                            @error('floor') 
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Section</label>
                            <input type="text" name="section" value="{{ old('section', $shelf->section) }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e8e4de; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="e.g., East Wing, Main Hall, Reading Room">
                            @error('section') 
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Column</label>
                            <input type="text" name="column" value="{{ old('column', $shelf->column) }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e8e4de; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="e.g., Column 3, Left Wing">
                            @error('column') 
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Row</label>
                            <input type="text" name="row" value="{{ old('row', $shelf->row) }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e8e4de; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="e.g., Row 2, Top Shelf">
                            @error('row') 
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- CAPACITY                                   -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(30, 58, 95, 0.03); border-bottom: 1px solid rgba(30, 58, 95, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-cpu" style="color: #1e3a5f;"></i> Capacity
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Capacity</label>
                            <input type="number" name="capacity" value="{{ old('capacity', $shelf->capacity) }}" required min="1"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e8e4de; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="50">
                            <p style="font-size: 0.65rem; color: #6b7280; margin-top: 0.25rem;">Maximum number of books this shelf can hold</p>
                            @error('capacity') 
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Current Count</label>
                            <input type="number" name="current_count" value="{{ old('current_count', $shelf->current_count) }}" min="0"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e8e4de; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="0">
                            <p style="font-size: 0.65rem; color: #6b7280; margin-top: 0.25rem;">Current number of books on this shelf</p>
                            @error('current_count') 
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- BOOKS ON THIS SHELF                        -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(30, 58, 95, 0.03); border-bottom: 1px solid rgba(30, 58, 95, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-books" style="color: #1e3a5f;"></i> Books on this Shelf ({{ $shelf->books()->count() }})
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem;">
                        @if($shelf->books()->count() > 0)
                            <div style="background: rgba(30, 58, 95, 0.03); border-radius: 0.75rem; padding: 1rem; border: 1px solid rgba(30, 58, 95, 0.06);">
                                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                    @foreach($shelf->books()->limit(10)->get() as $book)
                                        <span style="font-size: 0.7rem; background: rgba(30, 58, 95, 0.06); border: 1px solid rgba(30, 58, 95, 0.08); border-radius: 9999px; padding: 0.15rem 0.75rem; color: #1a1a2e;">
                                            {{ Str::limit($book->title, 20) }}
                                        </span>
                                    @endforeach
                                    @if($shelf->books()->count() > 10)
                                        <span style="font-size: 0.7rem; color: #6b7280;">+{{ $shelf->books()->count() - 10 }} more</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">No books on this shelf yet.</p>
                        @endif
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- FORM ACTIONS                               -->
                <!-- ========================================== -->
                <div style="display: flex; gap: 1rem; padding-top: 0.5rem; border-top: 1px solid rgba(30, 58, 95, 0.08);">
                    <button type="submit" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 2rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.95rem; transition: all 0.3s ease; cursor: pointer; box-shadow: 0 4px 16px rgba(219,87,10,0.25);">
                        <i class="ti ti-device-floppy"></i> Update Shelf
                    </button>
                    <a href="{{ route('librarian.shelves.index') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 1.5rem; background: #faf8f5; color: #475569; border: 1px solid #e8e4de; border-radius: 0.5rem; font-weight: 500; font-size: 0.95rem; transition: all 0.2s; text-decoration: none;">
                        Cancel
                    </a>
                </div>

            </div>
        </form>
    </div>

</div>

<style>
    /* ========================================== */
    /* SUBTLE LIGHT BORDER STYLES                 */
    /* ========================================== */

    a[style*="Back to Shelves"]:hover {
        color: #4c1d95 !important;
    }
    
    input:focus, 
    textarea:focus, 
    select:focus {
        border-color: #db570a !important;
        box-shadow: 0 0 0 3px rgba(219, 87, 10, 0.08) !important;
        background: white !important;
    }
    
    input:hover, 
    textarea:hover, 
    select:hover {
        border-color: #c5c0b8 !important;
        background: white !important;
    }
    
    /* Submit button hover */
    button[type="submit"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(219, 87, 10, 0.35) !important;
        filter: brightness(0.95);
    }
    
    button[type="submit"]:active {
        transform: translateY(0);
    }
    
    /* Cancel button hover */
    a[style*="Cancel"]:hover {
        border-color: #c5c0b8 !important;
        background: white !important;
        color: #1a1a2e !important;
    }
    
    /* Card hover effect - very subtle */
    div[style*="background: white; border: 1px solid rgba(30, 58, 95, 0.1)"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: white; border: 1px solid rgba(30, 58, 95, 0.1)"]:hover {
        box-shadow: 0 4px 12px rgba(30, 58, 95, 0.04);
    }
    
    select optgroup {
        font-weight: 700;
        color: #1a1a2e;
        background: white;
    }
    
    select option {
        padding: 0.3rem 0.5rem;
        color: #334155;
    }
    
    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
        
        div[style*="padding: 1.25rem"] {
            padding: 1rem !important;
        }
        
        div[style*="display: flex; gap: 1rem; padding-top: 0.5rem;"] {
            flex-direction: column !important;
        }
        
        button[type="submit"],
        a[style*="Cancel"] {
            width: 100% !important;
            justify-content: center !important;
        }
    }
</style>

@endsection