@extends('layouts.librarian')

@section('title', 'Institution Members')
@section('page-title', '👥 Institution Members')

@section('content')

<div class="max-w-7xl mx-auto">
    
  <!-- Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 mt-2">
    <div>
        <p class="text-slate-400 text-sm">Manage members of {{ $institution->name }}</p>
    </div>

    
    <div class="flex gap-3">
    <!--   
    <a href="{{ route('institution.members.trashed') }}" class="bg-slate-800 text-slate-400 px-4 py-2 rounded-lg hover:bg-slate-700 transition border border-slate-700">
            <i class="ti ti-trash"></i> Trash
            @php
                $trashedCount = \App\Models\User::where('institution_id', $institution->id)->onlyTrashed()->count();
            @endphp
            @if($trashedCount > 0)
                <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full ml-1">{{ $trashedCount }}</span>
            @endif
        </a>
-->
        <a href="{{ route('institution.members.create') }}" class="btn-library">
            <i class="ti ti-plus"></i> Add Member
        </a>
    </div>
</div>


    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-purple-500">
            <p class="text-2xl font-bold text-white">{{$stats['total'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">👥 Total Members</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-blue-500">
            <p class="text-2xl font-bold text-blue-400">{{ $stats['admins'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">🏢 Admins</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-cyan-500">
            <p class="text-2xl font-bold text-cyan-400">{{ $stats['librarians'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">📚 Librarians</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-emerald-500">
            <p class="text-2xl font-bold text-emerald-400">{{ $stats['instructors'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">👨‍🏫 Instructors</p>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Search by name or email..." 
                       value="{{ request('search') }}"
                       class="search-bar">
            </div> 
            <select name="role" class="search-bar w-auto">
                <option value="">All Roles</option>
                <option value="librarian" {{ request('role') == 'librarian' ? 'selected' : '' }}>📚 Librarian</option>
                <option value="instructor" {{ request('role') == 'instructor' ? 'selected' : '' }}>👨‍🏫 Instructor</option>
                <option value="institution_admin" {{ request('role') == 'institution_admin' ? 'selected' : '' }}>🏢 Admin</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>👤 Member</option>
            </select>
            <button type="submit" class="btn-library">
                <i class="ti ti-search"></i> Filter
            </button>
            <a href="{{ route('institution.members.index') }}" class="bg-slate-800 text-slate-400 px-4 py-2 rounded-lg hover:bg-slate-700 transition border border-slate-700">
                <i class="ti ti-x"></i> Clear
            </a>
        </form>
    </div>

    <!-- Members Table -->
    @if($members->count() > 0)
        <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-800/50 text-left border-b border-slate-700">
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Member</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Joined</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($members as $member)
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center flex-shrink-0">
                                            <span class="text-white text-xs font-bold">{{ substr($member->full_name, 0, 1) }}</span>
                                        </div>
                                        <span class="font-medium text-white">{{ $member->full_name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-300">{{ $member->email }}</td>
                               <!-- Role Badge with Purple Gradient -->
<td class="px-4 py-3">
    @php
        $userRole = $member->getRoleNames()->first() ?? 'user';
        $roleLabels = [
            'librarian' => '📚 Librarian',
            'instructor' => '👨‍🏫 Instructor',
            'institution_admin' => '🏢 Admin',
            'user' => '👤 Member'
        ];
        $roleColors = [
            'librarian' => 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white',
            'instructor' => 'bg-gradient-to-r from-emerald-500 to-green-400 text-white',
            'institution_admin' => 'bg-gradient-to-r from-purple-500 to-pink-500 text-white',
            'user' => 'bg-gradient-to-r from-gray-500 to-gray-400 text-white'
        ];
    @endphp
    <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $roleColors[$userRole] ?? 'bg-gray-500/20 text-gray-300' }}">
        {{ $roleLabels[$userRole] ?? ucfirst($userRole) }}
    </span>
</td>
                                <td class="px-4 py-3 text-slate-400 text-sm">{{ $member->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button onclick="openEditModal({{ $member->id }})" 
                                                class="text-blue-400 hover:text-blue-300 transition" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @if(auth()->user()->id !== $member->id)
                                            <button onclick="openDeleteModal({{ $member->id }}, '{{ addslashes($member->full_name) }}')" 
                                                    class="text-red-400 hover:text-red-300 transition" title="Remove">
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
        <div class="mt-6">{{ $members->links() }}</div>
    @else
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-12 text-center">
            <i class="ti ti-users text-6xl text-slate-600 mb-4 block"></i>
            <h3 class="text-xl font-semibold text-white/60 mb-2">No Members Found</h3>
            <p class="text-slate-400">No members have joined this institution yet.</p>
            <a href="{{ route('institution.members.create') }}" class="inline-block mt-4 btn-library">
                <i class="ti ti-plus"></i> Add First Member
            </a>
        </div>
    @endif

</div>

<!-- ========================================== -->
<!-- EDIT MEMBER MODAL                          -->
<!-- ========================================== -->
<div id="editMemberModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl max-w-md w-full mx-4 overflow-hidden shadow-2xl">
        <div class="bg-gradient-to-r from-purple-900/30 to-pink-900/30 px-6 py-4 border-b border-slate-700">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">✏️ Edit Member</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-white transition">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <form id="editMemberForm" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Full Name <span class="text-red-400">*</span></label>
                    <input type="text" name="full_name" id="edit_full_name" required 
                           class="search-bar">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Email Address <span class="text-red-400">*</span></label>
                    <input type="email" name="email" id="edit_email" required 
                           class="search-bar">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Role <span class="text-red-400">*</span></label>
                    <select name="role" id="edit_role" class="search-bar">
                        <option value="user">👤 Member</option>
                        <option value="librarian">📚 Librarian</option>
                        <option value="instructor">👨‍🏫 Instructor</option>
                        <option value="institution_admin">🏢 Admin</option>
                    </select>
                </div>
            </div>
            
            <div class="flex gap-3 mt-8 pt-4 border-t border-slate-700">
                <button type="submit" class="btn-library flex-1 justify-center">
                    <i class="ti ti-device-floppy"></i> Save Changes
                </button>
                <button type="button" onclick="closeEditModal()" class="bg-slate-800 text-slate-400 px-6 py-2.5 rounded-lg hover:bg-slate-700 transition border border-slate-700">
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
    <div class="bg-slate-900 border border-slate-700 rounded-2xl max-w-md w-full mx-4 overflow-hidden shadow-2xl">
        <div class="bg-gradient-to-r from-red-900/30 to-red-800/30 px-6 py-4 border-b border-slate-700">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">⚠️ Confirm Delete</h3>
                <button onclick="closeDeleteModal()" class="text-slate-400 hover:text-white transition">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="text-center mb-6">
                <i class="ti ti-alert-triangle text-red-400 text-5xl mb-3 block"></i>
                <p class="text-slate-300 mb-2">Are you sure you want to remove</p>
                <p class="font-semibold text-white text-lg" id="delete_member_name"></p>
                <p class="text-slate-400 text-sm mt-2">This action cannot be undone.</p>
            </div>
            
            <form id="deleteMemberForm" method="POST" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-lg transition font-semibold">
                    <i class="ti ti-trash"></i> Yes, Remove Member
                </button>
                <button type="button" onclick="closeDeleteModal()" class="flex-1 bg-slate-800 text-slate-400 px-6 py-2.5 rounded-lg hover:bg-slate-700 transition border border-slate-700">
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
    fetch(`/institution/members/${memberId}/json`, {  // ← Changed from /edit to /json
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