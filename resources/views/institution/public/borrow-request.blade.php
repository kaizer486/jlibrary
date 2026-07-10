@extends('layouts.library')

@section('title', 'Request to Borrow')

@section('content')

<div class="max-w-3xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <div class="mb-6">
        <a href="{{ route('institution.public.show', [$institution->id, $book->id]) }}" 
           style="color: #5b21b6; transition: color 0.2s; display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.875rem; text-decoration: none; font-weight: 500;">
            <i class="ti ti-arrow-left"></i> Back to Book
        </a>
    </div>

    <!-- ========================================== -->
    <!-- SUCCESS / ERROR MESSAGES                   -->
    <!-- ========================================== -->
    @if(session('success'))
        <div style="background: rgba(6, 95, 70, 0.06); border: 1px solid rgba(6, 95, 70, 0.12); border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem; color: #065f46; display: flex; align-items: flex-start; gap: 0.75rem;">
            <i class="ti ti-check-circle" style="font-size: 1.25rem; margin-top: 0.1rem;"></i>
            <div>
                <p style="font-weight: 600; margin: 0;">Success!</p>
                <p style="font-size: 0.875rem; color: rgba(6, 95, 70, 0.7); margin: 0;">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div style="background: rgba(220, 38, 38, 0.06); border: 1px solid rgba(220, 38, 38, 0.12); border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem; color: #dc2626; display: flex; align-items: flex-start; gap: 0.75rem;">
            <i class="ti ti-alert-circle" style="font-size: 1.25rem; margin-top: 0.1rem;"></i>
            <div>
                <p style="font-weight: 600; margin: 0;">Error!</p>
                <p style="font-size: 0.875rem; color: rgba(220, 38, 38, 0.7); margin: 0;">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div style="background: rgba(217, 119, 6, 0.06); border: 1px solid rgba(217, 119, 6, 0.12); border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem; color: #d97706; display: flex; align-items: flex-start; gap: 0.75rem;">
            <i class="ti ti-alert-triangle" style="font-size: 1.25rem; margin-top: 0.1rem;"></i>
            <div>
                <p style="font-weight: 600; margin: 0;">Please fix the following errors:</p>
                <ul style="font-size: 0.875rem; color: rgba(217, 119, 6, 0.7); margin: 0.25rem 0 0 0; padding-left: 1.25rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- MAIN FORM CARD                            -->
    <!-- ========================================== -->
    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 1rem; box-shadow: 0 8px 32px rgba(0,0,0,0.06); overflow: hidden;">
        
        <!-- Header -->
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(91, 33, 182, 0.08); background: rgba(91, 33, 182, 0.04);">
            <h1 style="font-size: 1.125rem; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                <i class="ti ti-book-plus" style="color: #db570a;"></i> Request to Borrow
            </h1>
            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">Fill in the details to request this book</p>
        </div>

        <div style="padding: 1.5rem;">
            
            <!-- Book Preview -->
            <div style="background: rgba(91, 33, 182, 0.04); border: 1px solid rgba(91, 33, 182, 0.06); border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
                <div style="width: 4rem; height: 5rem; background: rgba(91, 33, 182, 0.06); border-radius: 0.5rem; overflow: hidden; flex-shrink: 0;">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-book" style="font-size: 1.5rem; color: rgba(91, 33, 182, 0.2);"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <h3 style="color: #1a1a2e; font-weight: 600; margin: 0;">{{ $book->title }}</h3>
                    <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">by {{ $book->author ?? 'Unknown' }}</p>
                    <p style="font-size: 0.7rem; color: #9ca3af; margin-top: 0.25rem;">
                        <i class="ti ti-map-pin"></i> {{ $book->shelf_number ?? 'Location not specified' }}
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('borrow.request.store') }}" id="borrowForm">
                @csrf
                <input type="hidden" name="book_id" value="{{ $book->id }}">
                <input type="hidden" name="institution_id" value="{{ $institution->id }}">

                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    
                    <!-- ========================================== -->
                    <!-- USER INFO                                  -->
                    <!-- ========================================== -->
                    <div style="background: white; border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 0.75rem; overflow: hidden;">
                        <div style="padding: 0.6rem 1.25rem; background: rgba(91, 33, 182, 0.04); border-bottom: 1px solid rgba(91, 33, 182, 0.08);">
                            <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                                <i class="ti ti-user" style="color: #5b21b6;"></i> Your Information
                            </span>
                        </div>
                        
                        <div style="padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Your Name</label>
                                <input type="text" value="{{ auth()->user()->full_name ?? auth()->user()->name }}" 
                                       style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" disabled>
                            </div>

                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Email Address</label>
                                <input type="text" value="{{ auth()->user()->email }}" 
                                       style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" disabled>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- BORROW DURATION                           -->
                    <!-- ========================================== -->
                    <div style="background: white; border: 1px solid rgba(219, 87, 10, 0.12); border-radius: 0.75rem; overflow: hidden;">
                        <div style="padding: 0.6rem 1.25rem; background: rgba(219, 87, 10, 0.04); border-bottom: 1px solid rgba(219, 87, 10, 0.08);">
                            <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                                <i class="ti ti-calendar" style="color: #db570a;"></i> Borrow Duration
                            </span>
                        </div>
                        
                        <div style="padding: 1.25rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Borrow Duration</label>
                            <select name="duration_days" style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;" required>
                                <option value="7" {{ old('duration_days') == 7 ? 'selected' : '' }}>7 days</option>
                                <option value="14" {{ old('duration_days') == 14 || !old('duration_days') ? 'selected' : '' }}>14 days (Standard)</option>
                                <option value="21" {{ old('duration_days') == 21 ? 'selected' : '' }}>21 days</option>
                                <option value="30" {{ old('duration_days') == 30 ? 'selected' : '' }}>30 days</option>
                            </select>
                            <p style="font-size: 0.7rem; color: #6b7280; margin-top: 0.25rem;">Choose how long you need the book</p>
                            @error('duration_days') 
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- REASON                                     -->
                    <!-- ========================================== -->
                    <div style="background: white; border: 1px solid rgba(6, 95, 70, 0.12); border-radius: 0.75rem; overflow: hidden;">
                        <div style="padding: 0.6rem 1.25rem; background: rgba(6, 95, 70, 0.04); border-bottom: 1px solid rgba(6, 95, 70, 0.08);">
                            <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                                <i class="ti ti-edit" style="color: #065f46;"></i> Reason / Notes
                            </span>
                        </div>
                        
                        <div style="padding: 1.25rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Reason / Notes</label>
                            <textarea name="reason" rows="4" style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; min-height: 100px; resize: vertical; font-family: inherit;" 
                                      placeholder="Why do you need this book? (e.g., research, assignment, personal reading...)" required>{{ old('reason') }}</textarea>
                            @error('reason') 
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- TERMS                                      -->
                    <!-- ========================================== -->
                    <div style="background: rgba(91, 33, 182, 0.03); border: 1px solid rgba(91, 33, 182, 0.06); border-radius: 0.75rem; padding: 1rem; display: flex; align-items: flex-start; gap: 0.75rem;">
                        <input type="checkbox" name="terms" id="terms" required
                               {{ old('terms') ? 'checked' : '' }}
                               style="margin-top: 0.15rem; width: 1rem; height: 1rem; accent-color: #5b21b6; cursor: pointer; flex-shrink: 0;">
                        <label for="terms" style="color: #4b5563; font-size: 0.85rem; cursor: pointer;">
                            I agree to return the book by the due date and take care of it. 
                            I understand that late returns may result in penalties.
                        </label>
                    </div>
                    @error('terms') 
                        <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                    @enderror

                    <!-- ========================================== -->
                    <!-- FORM ACTIONS                               -->
                    <!-- ========================================== -->
                    <div style="display: flex; gap: 1rem; padding-top: 0.5rem; border-top: 1px solid #e2e0db;">
                        <button type="submit" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 2rem; background: #2563eb; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.95rem; transition: all 0.2s; cursor: pointer;" id="submitBtn">
                            <i class="ti ti-send"></i> <span id="submitText">Submit Borrow Request</span>
                        </button>
                        <a href="{{ route('institution.public.show', [$institution->id, $book->id]) }}" 
                           style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 1.5rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.95rem; transition: all 0.2s; text-decoration: none;">
                            Cancel
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('borrowForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    const text = document.getElementById('submitText');
    btn.disabled = true;
    text.textContent = 'Submitting...';
    btn.style.opacity = '0.7';
});
</script>

<style>
    /* ========================================== */
    /* CLEAN FORM STYLES                         */
    /* ========================================== */

    a[style*="Back to Book"]:hover {
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
    
    /* Submit button hover */
    button[type="submit"]:hover {
        background: #1d4ed8 !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.35);
    }
    
    /* Cancel button hover */
    a[style*="Cancel"]:hover {
        border-color: #db570a !important;
        background: white !important;
        color: #1a1a2e !important;
    }
    
    /* Card hover effect */
    div[style*="background: white; border: 1px solid"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: white; border: 1px solid"]:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }
    
    /* Disabled input styling */
    input:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    input:disabled:hover {
        border-color: #e2e0db !important;
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