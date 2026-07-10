@extends('layouts.librarian')

@section('title', 'Join Request Details')

@section('content')

@php
    // Security check
    if (!auth()->user()->institution_id) {
        abort(403, 'You do not belong to any institution.');
    }
    
    if (!isset($institution) || !$institution) {
        abort(404, 'Institution not found.');
    }
    
    if (auth()->user()->institution_id != $institution->id) {
        abort(403, 'You do not have access to this institution.');
    }
    
    if (!isset($joinRequest) || !$joinRequest) {
        abort(404, 'Join request not found.');
    }
    
    if ($joinRequest->institution_id != $institution->id) {
        abort(403, 'This request does not belong to your institution.');
    }
    
    $statusColors = [
        'pending' => 'color: #d97706; background: rgba(217, 119, 6, 0.08);',
        'approved' => 'color: #065f46; background: rgba(6, 95, 70, 0.08);',
        'rejected' => 'color: #dc2626; background: rgba(220, 38, 38, 0.08);'
    ];
    $statusIcons = [
        'pending' => '⏳',
        'approved' => '✅',
        'rejected' => '❌'
    ];
    $color = $statusColors[$joinRequest->status] ?? 'color: #6b7280; background: rgba(0,0,0,0.04);';
    $icon = $statusIcons[$joinRequest->status] ?? '';
@endphp

<div class="max-w-3xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <div class="mb-6">
        <a href="{{ route('institution.join-requests.index') }}" style="color: #5b21b6; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.25rem; text-decoration: none; font-weight: 500;">
            <i class="ti ti-arrow-left"></i> Back to Join Requests
        </a>
    </div>

    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 1rem; box-shadow: 0 8px 32px rgba(0,0,0,0.06); overflow: hidden;">
        
        <!-- Header -->
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(91, 33, 182, 0.08); background: rgba(91, 33, 182, 0.04);">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h1 style="font-size: 1.125rem; font-weight: 700; color: #1a1a2e; margin: 0;">Join Request Details</h1>
                    <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">Request from {{ $joinRequest->user->full_name }}</p>
                </div>
                <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; {{ $color }}">
                    {{ $icon }} {{ ucfirst($joinRequest->status) }}
                </span>
            </div>
        </div>

        <div style="padding: 1.5rem;">
            
            <!-- User Info -->
            <div style="background: rgba(91, 33, 182, 0.04); border: 1px solid rgba(91, 33, 182, 0.06); border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 4rem; height: 4rem; border-radius: 9999px; background: linear-gradient(135deg, #5b21b6, #7c3aed); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <span style="color: white; font-size: 1.25rem; font-weight: 700;">{{ substr($joinRequest->user->full_name, 0, 1) }}</span>
                    </div>
                    <div>
                        <p style="font-size: 1.125rem; font-weight: 600; color: #1a1a2e; margin: 0;">{{ $joinRequest->user->full_name }}</p>
                        <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">{{ $joinRequest->user->email }}</p>
                        <p style="font-size: 0.7rem; color: #9ca3af; margin: 0;">Requested: {{ $joinRequest->created_at->format('F d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Request Details -->
            <div class="grid md:grid-cols-2 gap-4">
                <div style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.04); border-radius: 0.75rem; padding: 1rem;">
                    <p style="font-size: 0.65rem; color: #6b7280; text-transform: uppercase; font-weight: 600; margin: 0;">Status</p>
                    <p style="margin-top: 0.25rem;">
                        <span style="display: inline-block; padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500; {{ $color }}">
                            {{ $icon }} {{ ucfirst($joinRequest->status) }}
                        </span>
                    </p>
                </div>
                <div style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.04); border-radius: 0.75rem; padding: 1rem;">
                    <p style="font-size: 0.65rem; color: #6b7280; text-transform: uppercase; font-weight: 600; margin: 0;">Requested On</p>
                    <p style="color: #1a1a2e; font-weight: 500; margin: 0; font-size: 0.875rem;">{{ $joinRequest->created_at->format('F d, Y h:i A') }}</p>
                    <p style="font-size: 0.7rem; color: #6b7280; margin: 0;">{{ $joinRequest->created_at->diffForHumans() }}</p>
                </div>
            </div>

            <!-- Message -->
            @if($joinRequest->message)
                <div style="margin-top: 1rem; background: rgba(59, 130, 246, 0.04); border: 1px solid rgba(59, 130, 246, 0.08); border-radius: 0.75rem; padding: 1rem;">
                    <p style="font-size: 0.65rem; color: #2563eb; text-transform: uppercase; font-weight: 600; margin: 0;">User's Message</p>
                    <p style="color: #1a1a2e; margin-top: 0.25rem; font-size: 0.9rem;">{{ $joinRequest->message }}</p>
                </div>
            @endif

            <!-- Rejection Reason -->
            @if($joinRequest->status === 'rejected' && $joinRequest->rejection_reason)
                <div style="margin-top: 1rem; background: rgba(220, 38, 38, 0.04); border-left: 4px solid #dc2626; border-radius: 0.75rem; padding: 1rem;">
                    <p style="font-size: 0.65rem; color: #dc2626; text-transform: uppercase; font-weight: 600; margin: 0;">Rejection Reason</p>
                    <p style="color: #dc2626; margin-top: 0.25rem;">{{ $joinRequest->rejection_reason }}</p>
                </div>
            @endif

            <!-- Approval Info -->
            @if($joinRequest->status === 'approved' && $joinRequest->approved_at)
                <div style="margin-top: 1rem; background: rgba(6, 95, 70, 0.04); border-left: 4px solid #065f46; border-radius: 0.75rem; padding: 1rem;">
                    <p style="font-size: 0.65rem; color: #065f46; text-transform: uppercase; font-weight: 600; margin: 0;">Approved</p>
                    <p style="color: #065f46; margin-top: 0.25rem;">Approved on {{ $joinRequest->approved_at->format('F d, Y h:i A') }}</p>
                    <p style="font-size: 0.875rem; color: #065f46;">{{ $joinRequest->user->full_name }} is now a member of {{ $institution->name }}</p>
                </div>
            @endif

            <!-- Actions (only for pending requests) -->
            @if($joinRequest->status === 'pending')
                <div style="margin-top: 1.5rem; background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.04); border-radius: 0.75rem; padding: 1rem;">
                    <h3 style="font-weight: 600; color: #1a1a2e; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="ti ti-settings" style="color: #5b21b6;"></i> Review Decision
                    </h3>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                        <form method="POST" action="{{ route('institution.join-requests.approve', $joinRequest->id) }}" 
                              onsubmit="return confirm('Approve {{ $joinRequest->user->full_name }} to join {{ $institution->name }}?')" style="display: inline;">
                            @csrf
                            <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.5rem; background: #065f46; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                                <i class="ti ti-check"></i> Approve
                            </button>
                        </form>

                        <button onclick="openRejectModal({{ $joinRequest->id }})" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.5rem; background: #dc2626; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                            <i class="ti ti-x"></i> Reject
                        </button>
                    </div>
                </div>
            @endif

            <!-- Back Button -->
            <div style="margin-top: 1.5rem; text-align: center;">
                <a href="{{ route('institution.join-requests.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.5rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; text-decoration: none;">
                    <i class="ti ti-arrow-left"></i> Back to Join Requests
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
    <div style="background: rgba(255,255,255,0.9); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.3); border-radius: 1rem; max-width: 28rem; width: 100%; margin: 0 1rem; overflow: hidden; box-shadow: 0 24px 80px rgba(0,0,0,0.3);">
        
        <!-- Modal Header -->
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(220, 38, 38, 0.1); background: rgba(220, 38, 38, 0.04);">
            <div class="flex justify-between items-center">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                    <i class="ti ti-x" style="color: #dc2626;"></i> Reject Join Request
                </h3>
                <button onclick="closeRejectModal()" style="color: #6b7280; background: none; border: none; cursor: pointer; transition: color 0.2s; font-size: 1.5rem; padding: 0;">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        </div>
        
        <form id="rejectForm" method="POST" style="padding: 1.5rem;">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">
                        Reason for Rejection (Optional)
                    </label>
                    <textarea name="rejection_reason" rows="3" 
                              style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; min-height: 80px; resize: vertical; font-family: inherit;"
                              placeholder="Why are you rejecting this request?"></textarea>
                </div>
                <p style="font-size: 0.7rem; color: #6b7280;">This reason will be visible to the user.</p>
            </div>
            <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e2e0db;">
                <button type="submit" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #dc2626; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                    <i class="ti ti-x"></i> Reject Request
                </button>
                <button type="button" onclick="closeRejectModal()" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(requestId) {
    const form = document.getElementById('rejectForm');
    form.action = `/institution/join-requests/${requestId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
}

document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>

<style>
    /* ========================================== */
    /* CLEAN DETAILS & MODAL STYLES              */
    /* ========================================== */

    a[style*="Back to Join Requests"]:hover {
        color: #4c1d95 !important;
    }
    
    /* Approve button hover */
    button[style*="background: #065f46"]:hover {
        background: #044d37 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(6, 95, 70, 0.3);
    }
    
    /* Reject button hover */
    button[style*="background: #dc2626"]:hover {
        background: #b91c1c !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }
    
    /* Modal reject button hover */
    button[style*="background: #dc2626"]:hover {
        background: #b91c1c !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }
    
    button[style*="background: #faf8f5"]:hover {
        border-color: #db570a !important;
        background: white !important;
        color: #1a1a2e !important;
    }
    
    /* Cards hover effect */
    div[style*="background: rgba(91, 33, 182, 0.04)"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: rgba(91, 33, 182, 0.04)"]:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }
    
    div[style*="background: rgba(0,0,0,0.02)"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: rgba(0,0,0,0.02)"]:hover {
        background: rgba(0,0,0,0.04) !important;
    }
    
    textarea:focus {
        border-color: #db570a !important;
        box-shadow: 0 0 0 3px rgba(219, 87, 10, 0.1) !important;
        background: white !important;
    }
    
    textarea:hover {
        border-color: #c5c0b8 !important;
        background: white !important;
    }
    
    @media (max-width: 768px) {
        .grid-cols-2 {
            grid-template-columns: 1fr !important;
        }
        
        div[style*="display: flex; flex-wrap: wrap; gap: 0.75rem;"] {
            flex-direction: column !important;
        }
        
        div[style*="display: flex; flex-wrap: wrap; gap: 0.75rem;"] form,
        div[style*="display: flex; flex-wrap: wrap; gap: 0.75rem;"] button {
            width: 100% !important;
        }
        
        div[style*="display: flex; gap: 0.75rem; margin-top: 1.5rem;"] {
            flex-direction: column !important;
        }
        
        div[style*="display: flex; gap: 0.75rem; margin-top: 1.5rem;"] button {
            width: 100% !important;
            justify-content: center !important;
        }
    }
</style>

@endsection