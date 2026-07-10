@extends('layouts.librarian')

@section('title', 'Members Directory')
@section('page-title', 'Members Directory')

@section('content')

<div class="max-w-7xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">View all members of {{ $institution->name }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('institution.members.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                <i class="ti ti-plus"></i> Add Member
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #5b21b6; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $allMembers->count() }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Total Members</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(6, 95, 70, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #065f46; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #065f46; margin: 0;">
                {{ $allMembers->whereNotNull('email_verified_at')->count() }}
            </p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Active</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(217, 119, 6, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #d97706; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #d97706; margin: 0;">
                {{ $allMembers->whereNull('email_verified_at')->count() }}
            </p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Pending</p>
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
            <select name="role" style="padding: 0.6rem 2.5rem 0.6rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer; min-width: 150px;">
                <option value="">All Roles</option>
                @foreach($roleLabels as $key => $label)
                    <option value="{{ $key }}" {{ request('role') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                <i class="ti ti-search"></i> Filter
            </button>
            <a href="{{ route('institution.members.directory') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                <i class="ti ti-x"></i> Clear
            </a>
        </form>
    </div>

    <!-- Members by Role -->
    @if($groupedMembers->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            @foreach($groupedMembers as $role => $roleMembers)
                @if($roleMembers->count() > 0)
                    @php
                        $label = $roleLabels[$role] ?? ucfirst($role) . 's';
                        $color = $roleColors[$role] ?? 'from-slate-600 to-slate-700';
                        $badgeColor = $roleBadgeColors[$role] ?? 'color: #6b7280; background: rgba(0,0,0,0.04);';
                        $badgeLabel = $roleBadgeLabels[$role] ?? ucfirst($role);
                    @endphp
                    
                    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                        <div style="padding: 0.6rem 1.25rem; border-bottom: 1px solid rgba(0,0,0,0.06); background: rgba(91, 33, 182, 0.04);">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <h2 style="color: #1a1a2e; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; margin: 0;">
                                    <i class="ti ti-users" style="color: #5b21b6;"></i> {{ $label }}
                                </h2>
                                <span style="color: #6b7280; font-size: 0.7rem; background: rgba(0,0,0,0.04); padding: 0.1rem 0.6rem; border-radius: 9999px;">
                                    {{ $roleMembers->count() }}
                                </span>
                            </div>
                        </div>
                        <div style="border-top: 1px solid #e2e0db;">
                            @foreach($roleMembers as $member)
                                <div style="display: flex; align-items: center; gap: 1rem; padding: 0.6rem 1.25rem; border-bottom: 1px solid #f0ede8; transition: background 0.2s;">
                                    <div style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; background: linear-gradient(135deg, #5b21b6, #7c3aed); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <span style="color: white; font-weight: 700; font-size: 0.75rem;">{{ substr($member->full_name, 0, 1) }}</span>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <p style="font-weight: 500; color: #1a1a2e; margin: 0; font-size: 0.875rem;">{{ $member->full_name }}</p>
                                        <p style="font-size: 0.7rem; color: #6b7280; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $member->email }}</p>
                                        <p style="font-size: 0.65rem; color: #9ca3af; margin: 0;">{{ $member->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <div style="text-align: right; flex-shrink: 0;">
                                        @if($member->email_verified_at)
                                            <span style="display: inline-block; padding: 0.1rem 0.5rem; border-radius: 9999px; font-size: 0.6rem; font-weight: 500; color: #065f46; background: rgba(6, 95, 70, 0.08);">Active</span>
                                        @else
                                            <span style="display: inline-block; padding: 0.1rem 0.5rem; border-radius: 9999px; font-size: 0.6rem; font-weight: 500; color: #d97706; background: rgba(217, 119, 6, 0.08);">Pending</span>
                                        @endif
                                        <span style="display: block; font-size: 0.6rem; font-weight: 500; margin-top: 0.25rem; {{ $badgeColor }} padding: 0.05rem 0.4rem; border-radius: 9999px;">
                                            {{ $badgeLabel }}
                                        </span>
                                    </div>
                                    @if(auth()->user()->canManageUser($member))
                                        <div style="display: flex; align-items: center; gap: 0.25rem; flex-shrink: 0;">
                                            <a href="{{ route('institution.members.edit', $member) }}" 
                                               style="color: #2563eb; transition: color 0.2s; text-decoration: none; padding: 0.25rem;" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            @if(auth()->user()->canDeleteUser($member) && auth()->user()->id !== $member->id)
                                                <form method="POST" action="{{ route('institution.members.destroy', $member) }}" 
                                                      onsubmit="return confirm('Remove {{ $member->full_name }} from the institution?')" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="color: #dc2626; background: none; border: none; cursor: pointer; transition: color 0.2s; padding: 0.25rem;" title="Remove">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem; padding: 3rem; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <i class="ti ti-users" style="font-size: 3.5rem; color: #d6d2cb; display: block; margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem;">No Members Found</h3>
            <p style="color: #9ca3af;">No members have joined this institution yet.</p>
            <a href="{{ route('institution.members.create') }}" style="display: inline-block; margin-top: 1rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                <i class="ti ti-plus"></i> Add First Member
            </a>
        </div>
    @endif

</div>

<style>
    /* ========================================== */
    /* CLEAN DIRECTORY STYLES                    */
    /* ========================================== */

    a[style*="Add Member"]:hover {
        background: #c44a08 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(219, 87, 10, 0.3);
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
    
    /* Member row hover */
    div[style*="border-bottom: 1px solid #f0ede8"]:hover {
        background: rgba(91, 33, 182, 0.03) !important;
    }
    
    /* Edit icon hover */
    a[title="Edit"]:hover {
        color: #1d4ed8 !important;
    }
    
    /* Remove button hover */
    button[title="Remove"]:hover {
        color: #b91c1c !important;
    }
    
    /* Role group header hover */
    div[style*="background: rgba(91, 33, 182, 0.04)"] {
        transition: all 0.2s ease;
    }
    
    @media (max-width: 768px) {
        .grid-cols-3 {
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
        
        div[style*="display: flex; align-items: center; gap: 1rem; padding: 0.6rem 1.25rem;"] {
            flex-wrap: wrap !important;
        }
        
        div[style*="text-align: right; flex-shrink: 0;"] {
            text-align: left !important;
            width: 100% !important;
        }
    }
</style>

@endsection