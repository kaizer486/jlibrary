@extends('layouts.librarian')

@section('title', 'Institution Members')
@section('page-title', 'Institution Members')

@section('content')

<div class="max-w-7xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 mt-2">
        <div>
            <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Manage members of {{ $institution->name }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('institution.members.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                <i class="ti ti-plus"></i> Add Member
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #5b21b6; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{$stats['total'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Total Members</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(37, 99, 235, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #2563eb; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #2563eb; margin: 0;">{{ $stats['admins'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Admins</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(6, 182, 212, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #06b6d4; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #06b6d4; margin: 0;">{{ $stats['librarians'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Librarians</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(6, 95, 70, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #065f46; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #065f46; margin: 0;">{{ $stats['instructors'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Instructors</p>
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
                <option value="librarian" {{ request('role') == 'librarian' ? 'selected' : '' }}>Librarian</option>
                <option value="instructor" {{ request('role') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                <option value="institution_admin" {{ request('role') == 'institution_admin' ? 'selected' : '' }}>Admin</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Member</option>
            </select>
            <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                <i class="ti ti-search"></i> Filter
            </button>
            <a href="{{ route('institution.members.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                <i class="ti ti-x"></i> Clear
            </a>
        </form>
    </div>

    <!-- Members Table -->
    @if($members->count() > 0)
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div class="overflow-x-auto">
                <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                    <thead>
                        <tr style="background: rgba(91, 33, 182, 0.04); text-align: left; border-bottom: 1px solid #e2e0db;">
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Member</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Email</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Role</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Joined</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="border-top: 1px solid #e2e0db;">
                        @foreach($members as $member)
                            <tr style="transition: background 0.2s; border-bottom: 1px solid #f0ede8;">
                                <td style="padding: 0.75rem 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 2rem; height: 2rem; border-radius: 9999px; background: linear-gradient(135deg, #5b21b6, #7c3aed); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <span style="color: white; font-size: 0.65rem; font-weight: 700;">{{ substr($member->full_name, 0, 1) }}</span>
                                        </div>
                                        <span style="font-weight: 500; color: #1a1a2e;">{{ $member->full_name }}</span>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: #4b5563;">{{ $member->email }}</td>
                                <!-- Role Badge -->
                                <td style="padding: 0.75rem 1rem;">
                                    @php
                                        $userRole = $member->getRoleNames()->first() ?? 'user';
                                        $roleLabels = [
                                            'librarian' => 'Librarian',
                                            'instructor' => 'Instructor',
                                            'institution_admin' => 'Admin',
                                            'user' => 'Member'
                                        ];
                                        $roleColors = [
                                            'librarian' => 'color: #2563eb; background: rgba(37, 99, 235, 0.08);',
                                            'instructor' => 'color: #065f46; background: rgba(6, 95, 70, 0.08);',
                                            'institution_admin' => 'color: #5b21b6; background: rgba(91, 33, 182, 0.08);',
                                            'user' => 'color: #6b7280; background: rgba(0,0,0,0.04);'
                                        ];
                                    @endphp
                                    <span style="display: inline-block; padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500; {{ $roleColors[$userRole] ?? 'color: #6b7280; background: rgba(0,0,0,0.04);' }}">
                                        {{ $roleLabels[$userRole] ?? ucfirst($userRole) }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: #6b7280; font-size: 0.85rem;">{{ $member->created_at->format('M d, Y') }}</td>
                                <td style="padding: 0.75rem 1rem; text-align: right;">
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                                        <button onclick="openEditModal({{ $member->id }})" 
                                                style="color: #2563eb; background: none; border: none; cursor: pointer; transition: color 0.2s; padding: 0;" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @if(auth()->user()->id !== $member->id)
                                            <button onclick="openDeleteModal({{ $member->id }}, '{{ addslashes($member->full_name) }}')" 
                                                    style="color: #dc2626; background: none; border: none; cursor: pointer; transition: color 0.2s; padding: 0;" title="Remove">
                                                <i class="ti ti-trash"></i>
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
        <div style="margin-top: 1.5rem;">{{ $members->links() }}</div>
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

<!-- ========================================== -->
<!-- EDIT MEMBER MODAL                          -->
<!-- ========================================== -->
<div id="editMemberModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50">
    <div style="background: rgba(255,255,255,0.9); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.3); border-radius: 1rem; max-width: 28rem; width: 100%; margin: 0 1rem; overflow: hidden; box-shadow: 0 24px 80px rgba(0,0,0,0.3);">
        
        <!-- Modal Header -->
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(91, 33, 182, 0.08); background: rgba(91, 33, 182, 0.04);">
            <div class="flex justify-between items-center">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                    <i class="ti ti-edit" style="color: #5b21b6;"></i> Edit Member
                </h3>
                <button onclick="closeEditModal()" style="color: #6b7280; background: none; border: none; cursor: pointer; transition: color 0.2s; font-size: 1.5rem; padding: 0;">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        </div>
        
        <form id="editMemberForm" method="POST" style="padding: 1.5rem;">
            @csrf
            @method('PUT')
            
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Full Name</label>
                    <input type="text" name="full_name" id="edit_full_name" required 
                           style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;">
                </div>
                
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Email Address</label>
                    <input type="email" name="email" id="edit_email" required 
                           style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;">
                </div>
                
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Role</label>
                    <select name="role" id="edit_role" style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;">
                        <option value="user">Member</option>
                        <option value="librarian">Librarian</option>
                        <option value="instructor">Instructor</option>
                        <option value="institution_admin">Admin</option>
                    </select>
                </div>
            </div>
            
            <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e2e0db;">
                <button type="submit" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                    <i class="ti ti-device-floppy"></i> Save Changes
                </button>
                <button type="button" onclick="closeEditModal()" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- DELETE CONFIRMATION MODAL                  -->
<!-- ========================================== -->
<div id="deleteMemberModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50">
    <div style="background: rgba(255,255,255,0.9); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.3); border-radius: 1rem; max-width: 28rem; width: 100%; margin: 0 1rem; overflow: hidden; box-shadow: 0 24px 80px rgba(0,0,0,0.3);">
        
        <!-- Modal Header -->
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(220, 38, 38, 0.1); background: rgba(220, 38, 38, 0.04);">
            <div class="flex justify-between items-center">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                    <i class="ti ti-alert-triangle" style="color: #dc2626;"></i> Confirm Delete
                </h3>
                <button onclick="closeDeleteModal()" style="color: #6b7280; background: none; border: none; cursor: pointer; transition: color 0.2s; font-size: 1.5rem; padding: 0;">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        </div>
        
        <div style="padding: 1.5rem;">
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <i class="ti ti-alert-triangle" style="color: #dc2626; font-size: 3rem; display: block; margin-bottom: 0.75rem;"></i>
                <p style="color: #4b5563; margin-bottom: 0.5rem;">Are you sure you want to remove</p>
                <p style="font-weight: 600; color: #1a1a2e; font-size: 1.125rem;" id="delete_member_name"></p>
                <p style="color: #6b7280; font-size: 0.875rem; margin-top: 0.5rem;">This action cannot be undone.</p>
            </div>
            
            <form id="deleteMemberForm" method="POST" style="display: flex; gap: 0.75rem;">
                @csrf
                @method('DELETE')
                <button type="submit" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #dc2626; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                    <i class="ti ti-trash"></i> Yes, Remove Member
                </button>
                <button type="button" onclick="closeDeleteModal()" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                    Cancel
                </button>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- SCRIPTS                                    -->
<!-- ========================================== -->
<script>
function openEditModal(memberId) {
    fetch(`/institution/members/${memberId}/json`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('edit_full_name').value = data.member.full_name;
            document.getElementById('edit_email').value = data.member.email;
            document.getElementById('edit_role').value = data.member.role;
            
            const form = document.getElementById('editMemberForm');
            form.action = `/institution/members/${memberId}`;
            
            document.getElementById('editMemberModal').classList.remove('hidden');
            document.getElementById('editMemberModal').classList.add('flex');
        } else {
            alert('Error loading member data');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading member data');
    });
}

function closeEditModal() {
    document.getElementById('editMemberModal').classList.add('hidden');
    document.getElementById('editMemberModal').classList.remove('flex');
}

// ==========================================
// DELETE MODAL FUNCTIONS
// ==========================================
let currentDeleteMemberId = null;

function openDeleteModal(memberId, memberName) {
    currentDeleteMemberId = memberId;
    document.getElementById('delete_member_name').textContent = memberName;
    
    const form = document.getElementById('deleteMemberForm');
    form.action = `/institution/members/${memberId}`;
    
    document.getElementById('deleteMemberModal').classList.remove('hidden');
    document.getElementById('deleteMemberModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteMemberModal').classList.add('hidden');
    document.getElementById('deleteMemberModal').classList.remove('flex');
    currentDeleteMemberId = null;
}

// ==========================================
// CLOSE MODALS ON OUTSIDE CLICK
// ==========================================
document.getElementById('editMemberModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

document.getElementById('deleteMemberModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>

<style>
    /* ========================================== */
    /* CLEAN TABLE & MODAL STYLES                */
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
    
    /* Table row hover */
    tbody tr:hover {
        background: rgba(91, 33, 182, 0.03) !important;
    }
    
    /* Edit icon hover */
    button[title="Edit"]:hover {
        color: #1d4ed8 !important;
    }
    
    /* Delete icon hover */
    button[title="Remove"]:hover {
        color: #b91c1c !important;
    }
    
    /* Modal delete button hover */
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