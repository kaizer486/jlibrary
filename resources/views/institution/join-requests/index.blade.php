@extends('layouts.librarian')

@section('title', 'Join Requests')
@section('page-title', 'Join Requests')

@section('content')

<div class="max-w-7xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Manage requests to join {{ $institution->name }}</p>
        </div>
        <a href="{{ route('institution.dashboard') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: rgba(255,255,255,0.7); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; text-decoration: none;">
            <i class="ti ti-dashboard"></i> Dashboard
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(217, 119, 6, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #d97706; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #d97706; margin: 0;">{{ $stats['pending'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Pending</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(6, 95, 70, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #065f46; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #065f46; margin: 0;">{{ $stats['approved'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Approved</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(220, 38, 38, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #dc2626; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #dc2626; margin: 0;">{{ $stats['rejected'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Rejected</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #6b7280; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $stats['total'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Total</p>
        </div>
    </div>

    <!-- Search -->
    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <form method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Search by name or email..." 
                       value="{{ request('search') }}"
                       style="width: 100%; padding: 0.6rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;">
            </div>
            <select name="status" style="padding: 0.6rem 2.5rem 0.6rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer; min-width: 150px;">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                <i class="ti ti-search"></i> Filter
            </button>
            <a href="{{ route('institution.join-requests.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                <i class="ti ti-x"></i> Clear
            </a>
        </form>
    </div>

    <!-- Requests Table -->
    @if($requests->count() > 0)
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div class="overflow-x-auto">
                <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                    <thead>
                        <tr style="background: rgba(91, 33, 182, 0.04); text-align: left; border-bottom: 1px solid #e2e0db;">
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">User</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Message</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Requested</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="border-top: 1px solid #e2e0db;">
                    @foreach($requests as $request)
                        <tr style="transition: background 0.2s; border-bottom: 1px solid #f0ede8;">
                            <td style="padding: 0.75rem 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 2rem; height: 2rem; border-radius: 9999px; background: linear-gradient(135deg, #5b21b6, #7c3aed); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <span style="color: white; font-size: 0.65rem; font-weight: 700;">
                                            @if($request->user)
                                                {{ substr($request->user->full_name, 0, 1) }}
                                            @else
                                                <i class="ti ti-user" style="color: white; font-size: 0.65rem;"></i>
                                            @endif
                                        </span>
                                    </div>
                                    <div>
                                        <p style="font-weight: 500; color: #1a1a2e; margin: 0;">
                                            @if($request->user)
                                                {{ $request->user->full_name }}
                                            @else
                                                <span style="color: #dc2626;">User Deleted</span>
                                            @endif
                                        </p>
                                        <p style="font-size: 0.65rem; color: #6b7280; margin: 0;">
                                            @if($request->user)
                                                {{ $request->user->email }}
                                            @else
                                                <span style="color: rgba(220, 38, 38, 0.6);">User ID: {{ $request->user_id }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 0.75rem 1rem; color: #6b7280; font-size: 0.875rem;">
                                {{ $request->message ?? 'No message' }}
                            </td>
                            <td style="padding: 0.75rem 1rem; color: #6b7280; font-size: 0.85rem;">
                                {{ $request->created_at->diffForHumans() }}
                            </td>
                            <td style="padding: 0.75rem 1rem;">
                                @php
                                    $statusColors = [
                                        'pending' => 'color: #d97706; background: rgba(217, 119, 6, 0.08);',
                                        'approved' => 'color: #065f46; background: rgba(6, 95, 70, 0.08);',
                                        'rejected' => 'color: #dc2626; background: rgba(220, 38, 38, 0.08);'
                                    ];
                                    $color = $statusColors[$request->status] ?? 'color: #6b7280; background: rgba(0,0,0,0.04);';
                                @endphp
                                <span style="display: inline-block; padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500; {{ $color }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 1rem; text-align: right;">
                                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                                    <a href="{{ route('institution.join-requests.show', $request->id) }}" 
                                       style="color: #5b21b6; transition: color 0.2s; text-decoration: none;" title="View">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    @if($request->status === 'pending')
                                        <form method="POST" action="{{ route('institution.join-requests.approve', $request->id) }}" 
                                              onsubmit="return confirm('Approve {{ $request->user->full_name }} to join?')" style="display: inline;">
                                            @csrf
                                            <button type="submit" style="color: #065f46; background: none; border: none; cursor: pointer; transition: color 0.2s; padding: 0;" title="Approve">
                                                <i class="ti ti-check"></i>
                                            </button>
                                        </form>
                                        <button onclick="openRejectModal({{ $request->id }})" 
                                                style="color: #dc2626; background: none; border: none; cursor: pointer; transition: color 0.2s; padding: 0;" title="Reject">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div style="margin-top: 1.5rem;">{{ $requests->links() }}</div>
    @else
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem; padding: 3rem; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <i class="ti ti-users" style="font-size: 3.5rem; color: #d6d2cb; display: block; margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem;">No Join Requests</h3>
            <p style="color: #9ca3af;">There are no join requests for your institution at the moment.</p>
        </div>
    @endif

</div>

<!-- ========================================== -->
<!-- REJECT MODAL                               -->
<!-- ========================================== -->
<div id="rejectModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50">
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
                <button type="button" onclick="closeRejectModal()" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
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
    /* CLEAN TABLE & MODAL STYLES                */
    /* ========================================== */

    a[style*="Dashboard"]:hover {
        border-color: #db570a !important;
        background: rgba(255,255,255,0.9) !important;
        color: #1a1a2e !important;
    }
    
    input:focus, 
    select:focus,
    textarea:focus {
        border-color: #db570a !important;
        box-shadow: 0 0 0 3px rgba(219, 87, 10, 0.1) !important;
        background: white !important;
    }
    
    input:hover, 
    select:hover,
    textarea:hover {
        border-color: #c5c0b8 !important;
        background: white !important;
    }
    
    button[type="submit"]:hover {
        background: #c44a08 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(219, 87, 10, 0.3);
    }
    
    a[style*="Clear"]:hover {
        border-color: #db570a !important;
        background: white !important;
        color: #1a1a2e !important;
    }
    
    /* Stats card hover */
    div[style*="border-left: 4px solid"] {
        transition: all 0.2s ease;
    }
    
    div[style*="border-left: 4px solid"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06) !important;
    }
    
    /* Table row hover */
    tbody tr:hover {
        background: rgba(91, 33, 182, 0.03) !important;
    }
    
    /* Action icons hover */
    a[title="View"]:hover {
        color: #4c1d95 !important;
    }
    
    button[title="Approve"]:hover {
        color: #059669 !important;
    }
    
    button[title="Reject"]:hover {
        color: #b91c1c !important;
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
    
    @media (max-width: 768px) {
        .grid-cols-4 {
            grid-template-columns: 1fr 1fr !important;
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
        
        table {
            font-size: 0.75rem !important;
        }
        
        td, th {
            padding: 0.5rem !important;
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