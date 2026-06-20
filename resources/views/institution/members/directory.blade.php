@extends('layouts.institution')

@section('title', 'Institution Members Directory')

@section('content')

@php
    // ==========================================
    // SECURITY CHECKS
    // ==========================================
    
    // Check if user belongs to an institution
    if (!auth()->user()->institution_id) {
        abort(403, 'You do not belong to any institution.');
    }
    
    // Check if institution exists
    if (!isset($institution) || !$institution) {
        abort(404, 'Institution not found.');
    }
    
    // Check if user has access to this institution
    if (auth()->user()->institution_id != $institution->id) {
        abort(403, 'You do not have access to this institution.');
    }
    
    // ==========================================
    // ROLE CONFIGURATION
    // ==========================================
    $roleLabels = [
        'institution_admin' => '🏢 Institution Administrators',
        'admin' => '🏢 Institution Administrators',
        'librarian' => '📚 Librarians',
        'instructor' => '👨‍🏫 Instructors',
        'user' => '👤 Members'
    ];
    
    $roleColors = [
        'institution_admin' => 'from-purple-600 to-pink-600',
        'admin' => 'from-purple-600 to-pink-600',
        'librarian' => 'from-blue-600 to-cyan-600',
        'instructor' => 'from-green-600 to-emerald-600',
        'user' => 'from-gray-600 to-gray-700'
    ];
    
    $roleBadgeColors = [
        'institution_admin' => 'bg-purple-100 text-purple-700',
        'admin' => 'bg-purple-100 text-purple-700',
        'librarian' => 'bg-blue-100 text-blue-700',
        'instructor' => 'bg-green-100 text-green-700',
        'user' => 'bg-gray-100 text-gray-700'
    ];
    
    $roleBadgeLabels = [
        'institution_admin' => 'Admin',
        'admin' => 'Admin',
        'librarian' => 'Librarian',
        'instructor' => 'Instructor',
        'user' => 'Member'
    ];
    
    // Get all members grouped by role
    $allMembers = $members ?? collect();
    $groupedMembers = $allMembers->groupBy(function($user) {
        return $user->getRoleNames()->first() ?? 'user';
    });
@endphp

<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        
        <!-- ========================================== -->
        <!-- HEADER                                      -->
        <!-- ========================================== -->
        <div class="mb-8">
            <a href="{{ route('dashboard') }}" class="text-purple-300 hover:text-purple-200 transition mb-4 inline-block">
                <i class="ti ti-arrow-left"></i> Back to Dashboard
            </a>
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center">
                        <i class="ti ti-building text-white text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">{{ $institution->name }}</h1>
                        <p class="text-purple-200">Institution Members Directory</p>
                    </div>
                    <div class="ml-auto flex items-center gap-3">
                        <div class="bg-white/20 rounded-full px-4 py-2 text-white">
                            <i class="ti ti-users"></i> {{ $allMembers->count() }} Members
                        </div>
                        @can('create', App\Models\User::class)
                            <a href="{{ route('institution.members.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md">
                                <i class="ti ti-plus"></i> Add Member
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MEMBERS BY ROLE                            -->
        <!-- ========================================== -->
        <div class="space-y-6">
            
            @foreach($groupedMembers as $role => $roleMembers)
                @if($roleMembers->count() > 0)
                    @php
                        $label = $roleLabels[$role] ?? ucfirst($role) . 's';
                        $color = $roleColors[$role] ?? 'from-gray-600 to-gray-700';
                        $badgeColor = $roleBadgeColors[$role] ?? 'bg-gray-100 text-gray-700';
                        $badgeLabel = $roleBadgeLabels[$role] ?? ucfirst($role);
                    @endphp
                    
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r {{ $color }} px-6 py-3">
                            <div class="flex justify-between items-center">
                                <h2 class="text-white font-semibold flex items-center gap-2">
                                    <i class="ti ti-users"></i> {{ $label }}
                                </h2>
                                <span class="text-white/80 text-sm bg-white/20 px-3 py-1 rounded-full">
                                    {{ $roleMembers->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="p-4 divide-y divide-gray-100">
                            @foreach($roleMembers as $member)
                            <div class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-xl transition">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                                    <span class="text-white font-bold">{{ substr($member->full_name, 0, 1) }}</span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800">{{ $member->full_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $member->email }}</p>
                                    <p class="text-xs text-gray-400 mt-1">Joined: {{ $member->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs {{ $badgeColor }} px-2 py-1 rounded-full font-medium">
                                        {{ $badgeLabel }}
                                    </span>
                                </div>
                                @can('update', $member)
                                    <div class="flex items-center gap-2">
                                        <button onclick="openEditModal({{ $member->id }})" 
                                                class="text-blue-600 hover:text-blue-800 transition" 
                                                title="Edit Member">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @can('delete', $member)
                                            <button onclick="openDeleteModal({{ $member->id }}, '{{ addslashes($member->full_name) }}')" 
                                                    class="text-red-600 hover:text-red-800 transition" 
                                                    title="Remove Member">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        @endcan
                                    </div>
                                @endcan
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- ========================================== -->
            <!-- EMPTY STATE                                -->
            <!-- ========================================== -->
            @if($groupedMembers->isEmpty())
            <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
                <i class="ti ti-users text-6xl text-gray-400 mb-4 block"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Members Found</h3>
                <p class="text-gray-500">No members have joined this institution yet.</p>
                @can('create', App\Models\User::class)
                    <a href="{{ route('institution.members.create') }}" class="mt-4 inline-block bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                        <i class="ti ti-plus"></i> Add First Member
                    </a>
                @endcan
            </div>
            @endif
            
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- EDIT MEMBER MODAL                          -->
<!-- ========================================== -->
<div id="editMemberModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl max-w-md w-full mx-4 overflow-hidden transform transition-all">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">✏️ Edit Member</h3>
                <button onclick="closeEditModal()" class="text-white/80 hover:text-white transition">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <form id="editMemberForm" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" id="edit_full_name" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="edit_email" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                    <select name="role" id="edit_role" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="user">👤 Member</option>
                        <option value="librarian">📚 Librarian</option>
                        <option value="instructor">👨‍🏫 Instructor</option>
                        <option value="institution_admin">🏢 Institution Admin</option>
                    </select>
                </div>
            </div>
            
            <div class="flex gap-3 mt-8 pt-4 border-t border-gray-200">
                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-2.5 rounded-lg hover:shadow-lg transition font-semibold">
                    <i class="ti ti-device-floppy"></i> Save Changes
                </button>
                <button type="button" onclick="closeEditModal()" class="px-6 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- DELETE CONFIRMATION MODAL                  -->
<!-- ========================================== -->
<div id="deleteMemberModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl max-w-md w-full mx-4 overflow-hidden transform transition-all">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">⚠️ Confirm Delete</h3>
                <button onclick="closeDeleteModal()" class="text-white/80 hover:text-white transition">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="text-center mb-6">
                <i class="ti ti-alert-triangle text-red-500 text-5xl mb-3 block"></i>
                <p class="text-gray-700 mb-2">Are you sure you want to remove</p>
                <p class="font-semibold text-gray-900 text-lg" id="delete_member_name"></p>
                <p class="text-gray-500 text-sm mt-2">This action cannot be undone.</p>
            </div>
            
            <form id="deleteMemberForm" method="POST" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-lg transition font-semibold">
                    <i class="ti ti-trash"></i> Yes, Remove Member
                </button>
                <button type="button" onclick="closeDeleteModal()" class="flex-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
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
// ==========================================
// EDIT MODAL FUNCTIONS
// ==========================================
function openEditModal(memberId) {
    fetch(`/institution/members/${memberId}/edit`, {
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

@endsection