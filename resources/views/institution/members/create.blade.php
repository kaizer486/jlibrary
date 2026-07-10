@extends('layouts.librarian')

@section('title', 'Add New Member')
@section('page-title', 'Add New Member')

@section('content')

<div class="max-w-2xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <div class="mb-6">
        <a href="{{ route('institution.members.index') }}" style="color: #5b21b6; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.25rem; text-decoration: none; font-weight: 500;">
            <i class="ti ti-arrow-left"></i> Back to Members
        </a>
    </div>

    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 1rem; box-shadow: 0 8px 32px rgba(0,0,0,0.06); overflow: hidden;">
        
        <!-- Header -->
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(91, 33, 182, 0.08); background: rgba(91, 33, 182, 0.04);">
            <h3 style="font-size: 1.125rem; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                <i class="ti ti-user-plus" style="color: #5b21b6;"></i>
                Add New Member to {{ $institution->name }}
            </h3>
        </div>
        
        <form method="POST" action="{{ route('institution.members.store') }}" style="padding: 1.5rem;">
            @csrf
            
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                
                <!-- ========================================== -->
                <!-- FULL NAME                                   -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(91, 33, 182, 0.04); border-bottom: 1px solid rgba(91, 33, 182, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-user" style="color: #5b21b6;"></i> Personal Information
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Full Name</label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" required
                               style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="Enter full name">
                        @error('full_name')
                            <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- EMAIL                                      -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(219, 87, 10, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(219, 87, 10, 0.04); border-bottom: 1px solid rgba(219, 87, 10, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-mail" style="color: #db570a;"></i> Contact Details
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="Enter email address">
                        @error('email')
                            <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- ROLE                                       -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(6, 95, 70, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(6, 95, 70, 0.04); border-bottom: 1px solid rgba(6, 95, 70, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-shield" style="color: #065f46;"></i> Access Level
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Role</label>
                        <select name="role" style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;">
                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Member</option>
                            <option value="librarian" {{ old('role') == 'librarian' ? 'selected' : '' }}>Librarian</option>
                            <option value="instructor" {{ old('role') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                            <option value="institution_admin" {{ old('role') == 'institution_admin' ? 'selected' : '' }}>Institution Admin</option>
                        </select>
                        @error('role')
                            <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- INFO CARD                                  -->
                <!-- ========================================== -->
                <div style="background: rgba(91, 33, 182, 0.04); border: 1px solid rgba(91, 33, 182, 0.08); border-radius: 0.75rem; padding: 1rem;">
                    <p style="color: #6b7280; font-size: 0.875rem; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="ti ti-info-circle" style="color: #5b21b6;"></i>
                        A temporary password will be generated and sent to the member's email address.
                    </p>
                </div>

                <!-- ========================================== -->
                <!-- FORM ACTIONS                               -->
                <!-- ========================================== -->
                <div style="display: flex; gap: 1rem; padding-top: 0.5rem; border-top: 1px solid #e2e0db;">
                    <button type="submit" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 2rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.95rem; transition: all 0.2s; cursor: pointer;">
                        <i class="ti ti-device-floppy"></i> Add Member
                    </button>
                    <a href="{{ route('institution.members.index') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 1.5rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.95rem; transition: all 0.2s; text-decoration: none;">
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

    a[style*="Back to Members"]:hover {
        color: #4c1d95 !important;
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