@extends('layouts.institution')

@section('title', 'Institution Members')

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
@endphp

<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">👥 Institution Members</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $institution->name }}</p>
            <p class="text-xs text-green-600 mt-1">
                <i class="ti ti-lock"></i> Private - Only visible to your institution
            </p>
        </div>
        @can('create', App\Models\User::class)
            <a href="{{ route('institution.members.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2 shadow-md">
                <i class="ti ti-plus"></i> Add Member
            </a>
        @endcan
    </div>
</div>

<!-- ========================================== -->
<!-- MEMBER STATISTICS                          -->
<!-- ========================================== -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
        <p class="text-sm text-gray-500">Total Members</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Institution Admins</p>
        <p class="text-2xl font-bold text-purple-600">{{ $stats['admins'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-cyan-500">
        <p class="text-sm text-gray-500">Librarians</p>
        <p class="text-2xl font-bold text-blue-600">{{ $stats['librarians'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
        <p class="text-sm text-gray-500">Instructors</p>
        <p class="text-2xl font-bold text-green-600">{{ $stats['instructors'] ?? 0 }}</p>
    </div>
</div>

<!-- ========================================== -->
<!-- SEARCH AND FILTER                          -->
<!-- ========================================== -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('institution.members.index') }}" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
            <i class="ti ti-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" placeholder="Search by name or email..." value="{{ request('search') }}" 
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
        </div>
        <div>
            <select name="role" class="px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-purple-500">
                <option value="">All Roles</option>
                <option value="librarian" {{ request('role') == 'librarian' ? 'selected' : '' }}>📚 Librarian</option>
                <option value="instructor" {{ request('role') == 'instructor' ? 'selected' : '' }}>👨‍🏫 Instructor</option>
                <option value="institution_admin" {{ request('role') == 'institution_admin' ? 'selected' : '' }}>🏢 Institution Admin</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>👤 Member</option>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition flex items-center gap-2">
                <i class="ti ti-search"></i> Search
            </button>
        </div>
        <div>
            <a href="{{ route('institution.members.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition inline-block">
                <i class="ti ti-x"></i> Clear
            </a>
        </div>
    </form>
</div>

<!-- ========================================== -->
<!-- MEMBERS TABLE                              -->
<!-- ========================================== -->
@if($members->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                    @can('update', App\Models\User::class)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    @endcan
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($members as $member)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                                <span class="text-white text-xs font-bold">{{ substr($member->full_name, 0, 1) }}</span>
                            </div>
                            <span class="font-medium text-gray-900">{{ $member->full_name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $member->email }}</td>
                    <td class="px-6 py-4">
                        @php
                            $roleLabels = [
                                'librarian' => '📚 Librarian',
                                'instructor' => '👨‍🏫 Instructor',
                                'institution_admin' => '🏢 Institution Admin',
                                'user' => '👤 Member'
                            ];
                            $roleColors = [
                                'librarian' => 'bg-blue-100 text-blue-700',
                                'instructor' => 'bg-green-100 text-green-700',
                                'institution_admin' => 'bg-purple-100 text-purple-700',
                                'user' => 'bg-gray-100 text-gray-700'
                            ];
                            $userRole = $member->getRoleNames()->first() ?? 'user';
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $roleColors[$userRole] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $roleLabels[$userRole] ?? ucfirst($userRole) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $member->created_at->format('M d, Y') }}</td>
                    @can('update', App\Models\User::class)
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-2">
                                @can('update', $member)
                                    <button onclick="openEditModal({{ $member->id }})" 
                                            class="text-blue-600 hover:text-blue-800 transition" 
                                            title="Edit Member">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                @endcan
                                @can('delete', $member)
                                    <button onclick="openDeleteModal({{ $member->id }}, '{{ addslashes($member->full_name) }}')" 
                                            class="text-red-600 hover:text-red-800 transition" 
                                            title="Remove Member">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    @endcan
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">{{ $members->links() }}</div>
@else
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="ti ti-users text-6xl text-gray-400 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Members Found</h3>
    <p class="text-gray-500">Click "Add Member" to add users to your institution.</p>
    @can('create', App\Models\User::class)
        <a href="{{ route('institution.members.create') }}" class="mt-4 inline-block bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
            <i class="ti ti-plus"></i> Add First Member
        </a>
    @endcan
</div>
@endif

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

// ==========================================
// CSRF TOKEN FOR AJAX REQUESTS
// ==========================================
document.querySelector('meta[name="csrf-token"]')?.setAttribute('content', '{{ csrf_token() }}');
</script>

@endsection