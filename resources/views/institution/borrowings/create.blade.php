@extends('layouts.librarian')

@section('title', 'New Borrowing')
@section('page-title', 'New Borrowing')

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
            <h1 style="font-size: 1.125rem; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                <i class="ti ti-book-plus" style="color: #db570a;"></i> Borrow Book
            </h1>
            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 1.75rem;">Borrow a book for a library member</p>
        </div>

        <form method="POST" action="{{ route('institution.borrowings.store') }}" style="padding: 1.5rem;">
            @csrf

            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                
                <!-- ========================================== -->
                <!-- BOOK SELECTION                             -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(91, 33, 182, 0.04); border-bottom: 1px solid rgba(91, 33, 182, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-book" style="color: #5b21b6;"></i> Book Selection
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Book</label>
                        @if($book)
                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                            <div style="background: rgba(91, 33, 182, 0.04); border: 1px solid rgba(91, 33, 182, 0.08); border-radius: 0.5rem; padding: 0.75rem 1rem;">
                                <p style="font-weight: 500; color: #1a1a2e; margin: 0;">{{ $book->title }}</p>
                                <p style="color: #6b7280; font-size: 0.75rem; margin: 0;">by {{ $book->author ?? 'Unknown' }}</p>
                            </div>
                        @else
                            <select name="book_id" style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;" required>
                                <option value="">Select a book...</option>
                                @foreach($books as $bookItem)
                                    <option value="{{ $bookItem->id }}" {{ old('book_id') == $bookItem->id ? 'selected' : '' }}>
                                        {{ $bookItem->title }} by {{ $bookItem->author ?? 'Unknown' }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('book_id') 
                            <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                        @enderror
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- MEMBER SELECTION                           -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(219, 87, 10, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(219, 87, 10, 0.04); border-bottom: 1px solid rgba(219, 87, 10, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-user" style="color: #db570a;"></i> Member Selection
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Member</label>
                        <select name="user_id" style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;" required>
                            <option value="">Select a member...</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}" {{ old('user_id') == $member->id ? 'selected' : '' }}>
                                    {{ $member->full_name }} ({{ $member->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id') 
                            <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                        @enderror
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- DUE DATE                                   -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(6, 95, 70, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(6, 95, 70, 0.04); border-bottom: 1px solid rgba(6, 95, 70, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-calendar" style="color: #065f46;"></i> Due Date
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Due Date</label>
                        <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(14)->format('Y-m-d')) }}" 
                               style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" 
                               required min="{{ now()->addDay()->format('Y-m-d') }}">
                        <p style="font-size: 0.7rem; color: #6b7280; margin-top: 0.25rem;">Default: 14 days from today</p>
                        @error('due_date') 
                            <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                        @enderror
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- NOTES                                      -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(124, 58, 237, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(124, 58, 237, 0.04); border-bottom: 1px solid rgba(124, 58, 237, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-notes" style="color: #7c3aed;"></i> Notes
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Notes</label>
                        <textarea name="notes" rows="3" style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; min-height: 80px; resize: vertical; font-family: inherit;" placeholder="Additional notes...">{{ old('notes') }}</textarea>
                        @error('notes') 
                            <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                        @enderror
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- FORM ACTIONS                               -->
                <!-- ========================================== -->
                <div style="display: flex; gap: 1rem; padding-top: 0.5rem; border-top: 1px solid #e2e0db; margin-top: 0.5rem;">
                    <button type="submit" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 2rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.95rem; transition: all 0.2s; cursor: pointer;">
                        <i class="ti ti-device-floppy"></i> Borrow Book
                    </button>
                    <a href="{{ route('institution.borrowings.index') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 1.5rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.95rem; transition: all 0.2s; cursor: pointer; text-decoration: none;">
                        Cancel
                    </a>
                </div>

            </div>
        </form>
    </div>

</div>

<style>
    /* ========================================== */
    /* CLEAN FORM STYLES                         */
    /* ========================================== */

    a[style*="Back to Borrowings"]:hover {
        color: #4c1d95 !important;
    }
    
    input:focus, 
    textarea:focus, 
    select:focus {
        border-color: #db570a !important;
        box-shadow: 0 0 0 3px rgba(219, 87, 10, 0.1) !important;
        background: white !important;
    }
    
    input:hover, 
    textarea:hover, 
    select:hover {
        border-color: #c5c0b8 !important;
        background: white !important;
    }
    
    button[type="submit"]:hover {
        background: #c44a08 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(219, 87, 10, 0.3);
    }
    
    a[style*="Cancel"]:hover {
        border-color: #db570a !important;
        background: white !important;
        color: #1a1a2e !important;
    }
    
    div[style*="background: white; border: 1px solid"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: white; border: 1px solid"]:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
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
    
    input[type="date"] {
        cursor: pointer;
    }
    
    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: 0.6;
    }
    
    input[type="date"]::-webkit-calendar-picker-indicator:hover {
        opacity: 1;
    }
    
    @media (max-width: 768px) {
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