@extends('layouts.app')

@section('content')

<div style="position: fixed; inset: 0; background: linear-gradient(135deg, #1e293b, #0f172a, #312e81); z-index: -10;"></div>

<div style="position: relative; z-index: 10; min-height: 100vh;">
    <div class="w-full px-4 py-6">
        
        <!-- Institution Header -->
        <div style="background: linear-gradient(135deg, #4f46e5, #7c3aed, #db2777); border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; color: white; box-shadow: 0 20px 50px rgba(0,0,0,0.15);">
            <div class="flex items-center gap-4">
                <div style="width: 4rem; height: 4rem; background: rgba(255,255,255,0.15); border-radius: 1rem; display: flex; align-items: center; justify-content: center;">
                    @if($institution->logo)
                        <img src="{{ url('media/' . $institution->logo) }}" alt="{{ $institution->name }}" style="width: 3.5rem; height: 3.5rem; border-radius: 0.75rem; object-fit: cover;">
                    @else
                        <i class="ti ti-building" style="font-size: 1.75rem;"></i>
                    @endif
                </div>
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700;">{{ $institution->name }}</h1>
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem; flex-wrap: wrap;">
                        <span style="font-size: 0.875rem; background: rgba(255,255,255,0.15); padding: 0.1rem 0.75rem; border-radius: 9999px;">{{ $institution->type_label ?? 'University' }}</span>
                        @if($institution->city)
                        <span style="font-size: 0.875rem; display: flex; align-items: center; gap: 0.25rem;">
                            <i class="ti ti-map-pin"></i> {{ $institution->city }}, {{ $institution->region ?? '' }}
                        </span>
                        @endif
                        @if(isset($institution->is_verified) && $institution->is_verified)
                            <span style="font-size: 0.875rem; display: flex; align-items: center; gap: 0.25rem; background: rgba(59, 130, 246, 0.3); padding: 0.1rem 0.75rem; border-radius: 9999px;">
                                <i class="ti ti-shield-check"></i> Verified
                            </span>
                        @endif
                        @if(isset($institution->is_featured) && $institution->is_featured)
                            <span style="font-size: 0.875rem; display: flex; align-items: center; gap: 0.25rem; background: rgba(251, 191, 36, 0.3); padding: 0.1rem 0.75rem; border-radius: 9999px;">
                                <i class="ti ti-star"></i> Featured
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div style="background: rgba(255,255,255,0.95); border-radius: 0.75rem; padding: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Members</p>
                        <p style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin: 0;">{{ $institution->users_count ?? 0 }}</p>
                    </div>
                    <i class="ti ti-users" style="color: #4f46e5; font-size: 1.75rem;"></i>
                </div>
            </div>
            <div style="background: rgba(255,255,255,0.95); border-radius: 0.75rem; padding: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Books & Resources</p>
                        <p style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin: 0;">{{ $institution->books_count ?? 0 }}</p>
                    </div>
                    <i class="ti ti-books" style="color: #4f46e5; font-size: 1.75rem;"></i>
                </div>
            </div>
            <div style="background: rgba(255,255,255,0.95); border-radius: 0.75rem; padding: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Established</p>
                        <p style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin: 0;">{{ $institution->created_at?->format('Y') ?? '2020' }}</p>
                    </div>
                    <i class="ti ti-calendar" style="color: #4f46e5; font-size: 1.75rem;"></i>
                </div>
            </div>
            <div style="background: rgba(255,255,255,0.95); border-radius: 0.75rem; padding: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Rating</p>
                        <p style="font-size: 1.125rem; font-weight: 600; color: #d97706; margin: 0;">★★★★☆ 4.8</p>
                    </div>
                    <i class="ti ti-star" style="color: #d97706; font-size: 1.75rem;"></i>
                </div>
            </div>
        </div>
        
        <!-- Contact & About -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            @if($institution->email || $institution->phone)
            <div style="background: rgba(255,255,255,0.95); border-radius: 0.75rem; padding: 1.25rem; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
                <h3 style="font-weight: 600; color: #1f2937; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ti ti-mail"></i> Contact Information
                </h3>
                <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.875rem;">
                    @if($institution->email)
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="ti ti-mail" style="color: #9ca3af;"></i>
                        <span style="color: #4b5563;">{{ $institution->email }}</span>
                    </div>
                    @endif
                    @if($institution->phone)
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="ti ti-phone" style="color: #9ca3af;"></i>
                        <span style="color: #4b5563;">{{ $institution->phone }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            <div style="background: rgba(255,255,255,0.95); border-radius: 0.75rem; padding: 1.25rem; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
                <h3 style="font-weight: 600; color: #1f2937; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ti ti-info-circle"></i> About This Institution
                </h3>
                <p style="color: #4b5563; font-size: 0.875rem; line-height: 1.7;">
                    {{ $institution->description ?? 'This institution is dedicated to providing quality education and resources to its members. Join to access exclusive learning materials, connect with peers, and participate in community events.' }}
                </p>
            </div>
        </div>
        
        <!-- Yellow Separator -->
        <div style="width: 6rem; height: 0.25rem; background: #fbbf24; border-radius: 9999px; margin: 1.5rem 0;"></div>
        
        <!-- Books Section -->
        <div style="margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #ffffff; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ti ti-books"></i> Books & Resources
            </h2>
            @if($institutionBooks->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($institutionBooks as $book)
                <div style="background: rgba(255,255,255,0.95); border-radius: 0.75rem; padding: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.04); transition: all 0.2s;">
                    <div style="width: 3rem; height: 3rem; background: #e0e7ff; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                        <i class="ti ti-book" style="color: #4f46e5; font-size: 1.25rem;"></i>
                    </div>
                    <h3 style="font-weight: 600; color: #1f2937;">{{ Str::limit($book->title, 40) }}</h3>
                    <p style="font-size: 0.7rem; color: #6b7280; margin-top: 0.25rem;">{{ $book->author ?? 'Unknown' }}</p>
                </div>
                @endforeach
            </div>
            <div style="margin-top: 1rem;">
                {{ $institutionBooks->links() }}
            </div>
            @else
            <div style="background: rgba(255,255,255,0.05); border-radius: 0.75rem; padding: 2rem; text-align: center;">
                <i class="ti ti-books" style="font-size: 2.5rem; color: #9ca3af; display: block; margin-bottom: 0.5rem;"></i>
                <p style="color: #d1d5db;">No books available yet</p>
            </div>
            @endif
        </div>
        
        <!-- Members Section -->
        <div style="margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #ffffff; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ti ti-users"></i> Members ({{ $institution->users_count ?? 0 }})
            </h2>
            @if($members->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($members as $member)
                <div style="background: rgba(255,255,255,0.95); border-radius: 0.75rem; padding: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.04); display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 2.5rem; height: 2.5rem; background: linear-gradient(135deg, #e0e7ff, #ede9fe); border-radius: 9999px; display: flex; align-items: center; justify-content: center;">
                        <i class="ti ti-user" style="color: #4f46e5;"></i>
                    </div>
                    <div>
                        <p style="font-weight: 500; color: #1f2937;">{{ $member->full_name ?? $member->name }}</p>
                        <p style="font-size: 0.65rem; color: #6b7280;">Member</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div style="margin-top: 1rem;">
                {{ $members->links() }}
            </div>
            @else
            <div style="background: rgba(255,255,255,0.05); border-radius: 0.75rem; padding: 2rem; text-align: center;">
                <i class="ti ti-users" style="font-size: 2.5rem; color: #9ca3af; display: block; margin-bottom: 0.5rem;"></i>
                <p style="color: #d1d5db;">No members yet</p>
            </div>
            @endif
        </div>
        
        <!-- REQUEST TO JOIN SECTION -->
        @if(!$isMember)
        <div style="background: linear-gradient(135deg, rgba(79, 70, 229, 0.08), rgba(124, 58, 237, 0.08)); border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 1.5rem; border: 1px solid rgba(124, 58, 237, 0.08);">
            <div style="text-align: center;">
                <i class="ti ti-building-community" style="color: #7c3aed; font-size: 2.5rem; display: block; margin-bottom: 0.75rem;"></i>
                
                @if(isset($existingRequest) && $existingRequest && $existingRequest->status === 'pending')
                    <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(251, 191, 36, 0.08); color: #d97706; padding: 0.25rem 1rem; border-radius: 9999px; margin-bottom: 0.75rem;">
                        <i class="ti ti-clock"></i>
                        <span style="font-size: 0.875rem;">Request Pending Approval</span>
                    </div>
                    <p style="color: #4b5563; margin-bottom: 0.75rem;">Your request to join has been submitted.</p>
                    <form method="POST" action="{{ route('join-requests.cancel', $existingRequest->id) }}" style="display: inline;" onsubmit="return confirm('Cancel your request?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="color: #dc2626; font-size: 0.875rem; background: none; border: none; cursor: pointer; text-decoration: underline;">Cancel Request</button>
                    </form>
                @elseif(isset($existingRequest) && $existingRequest && $existingRequest->status === 'rejected')
                    <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(220, 38, 38, 0.08); color: #dc2626; padding: 0.25rem 1rem; border-radius: 9999px; margin-bottom: 0.75rem;">
                        <i class="ti ti-x-circle"></i>
                        <span style="font-size: 0.875rem;">Request Declined</span>
                    </div>
                    <button onclick="openJoinModal()" style="background: linear-gradient(135deg, #7c3aed, #4f46e5); color: white; padding: 0.5rem 1.5rem; border-radius: 0.5rem; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s;">
                        Submit New Request
                    </button>
                @else
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem;">Want to join this institution?</h3>
                    <p style="color: #4b5563; margin-bottom: 1rem;">Submit a request to become a member</p>
                    <button onclick="openJoinModal()" style="background: linear-gradient(135deg, #7c3aed, #4f46e5); color: white; padding: 0.75rem 2rem; border-radius: 0.75rem; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s;">
                        <i class="ti ti-send"></i> Request to Join
                    </button>
                @endif
            </div>
        </div>
        @endif
        
        <!-- BACK TO BUTTON AT BOTTOM -->
        <div style="text-align: center; padding: 1.5rem 0; display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
            <a href="{{ route('discover.institutions') }}" 
               style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.05); color: white; padding: 0.75rem 1.5rem; border-radius: 9999px; transition: all 0.2s; text-decoration: none; border: 1px solid rgba(255,255,255,0.05);">
                <i class="ti ti-arrow-left"></i> Back to Discover
            </a>
            <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.05); color: white; padding: 0.75rem 1.5rem; border-radius: 9999px; transition: all 0.2s; cursor: pointer; border: 1px solid rgba(255,255,255,0.05);">
                <i class="ti ti-arrow-up"></i> Back to Top
            </button>
        </div>
        
    </div>
</div>

<!-- Join Request Modal -->
<div id="joinModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center; z-index: 50;">
    <div style="background: white; border-radius: 1rem; max-width: 28rem; width: 100%; margin: 0 1rem; overflow: hidden; box-shadow: 0 24px 80px rgba(0,0,0,0.3);">
        <div style="background: linear-gradient(135deg, #7c3aed, #db2777); padding: 1rem 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.25rem; font-weight: 700; color: white;">Request to Join</h3>
                <button onclick="closeJoinModal()" style="color: rgba(255,255,255,0.7); background: none; border: none; cursor: pointer; font-size: 1.5rem; transition: color 0.2s;">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('join-requests.store') }}" style="padding: 1.5rem;">
            @csrf
            <input type="hidden" name="institution_id" value="{{ $institution->id }}">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Why do you want to join? (Optional)</label>
                <textarea name="message" rows="3" style="width: 100%; padding: 0.5rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; outline: none; transition: all 0.2s; resize: vertical;" placeholder="Tell the admin why you'd like to join..."></textarea>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button type="submit" style="flex: 1; background: linear-gradient(135deg, #7c3aed, #db2777); color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s;">Send Request</button>
                <button type="button" onclick="closeJoinModal()" style="padding: 0.5rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background: none; cursor: pointer; font-weight: 500;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openJoinModal() {
    document.getElementById('joinModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeJoinModal() {
    document.getElementById('joinModal').style.display = 'none';
    document.body.style.overflow = '';
}
</script>

<style>
    /* ========================================== */
    /* CLEAN INSTITUTION SHOW STYLES              */
    /* ========================================== */

    a[style*="Back to Discover"]:hover {
        background: rgba(255,255,255,0.15) !important;
        transform: translateY(-2px);
    }
    
    button[onclick*="scrollTo"]:hover {
        background: rgba(255,255,255,0.15) !important;
        transform: translateY(-2px);
    }
    
    /* Request to Join button hover */
    button[onclick="openJoinModal()"]:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 24px rgba(124, 58, 237, 0.3);
    }
    
    /* Book card hover */
    div[style*="background: rgba(255,255,255,0.95)"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: rgba(255,255,255,0.95)"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06) !important;
    }
    
    /* Submit button hover */
    button[type="submit"]:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 24px rgba(124, 58, 237, 0.3);
    }
    
    /* Modal cancel button hover */
    button[onclick="closeJoinModal()"]:hover {
        background: #f3f4f6;
    }
    
    /* Textarea focus */
    textarea:focus {
        border-color: #7c3aed !important;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    }
    
    @media (max-width: 768px) {
        .grid-cols-4 {
            grid-template-columns: 1fr 1fr !important;
        }
        
        .grid-cols-3 {
            grid-template-columns: 1fr !important;
        }
        
        div[style*="display: flex; align-items: center; gap: 4;"] {
            flex-wrap: wrap !important;
        }
    }
</style>

@endsection