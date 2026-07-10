@extends('layouts.librarian')

@section('title', 'Borrow Requests')
@section('page-title', '📋 Borrow Requests')

@section('content')

<div class="max-w-7xl mx-auto">
    
    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-purple-500">
            <p class="text-2xl font-bold text-white">{{ $stats['total'] }}</p>
            <p class="text-xs text-slate-400">📚 Total Requests</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-amber-500">
            <p class="text-2xl font-bold text-amber-400">{{ $stats['pending'] }}</p>
            <p class="text-xs text-slate-400">⏳ Pending</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-emerald-500">
            <p class="text-2xl font-bold text-emerald-400">{{ $stats['approved'] }}</p>
            <p class="text-xs text-slate-400">✅ Approved</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-red-500">
            <p class="text-2xl font-bold text-red-400">{{ $stats['rejected'] }}</p>
            <p class="text-xs text-slate-400">❌ Rejected</p>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Search by book or user..." 
                       value="{{ request('search') }}"
                       class="search-bar">
            </div>
            <select name="status" class="search-bar w-auto">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
            </select>
            <button type="submit" class="btn-library">
                <i class="ti ti-search"></i> Filter
            </button>
            <a href="{{ route('librarian.borrow-requests.index') }}" class="bg-slate-800 text-slate-400 px-4 py-2 rounded-lg hover:bg-slate-700 transition border border-slate-700">
                <i class="ti ti-x"></i> Clear
            </a>
        </form>
    </div>

    <!-- Requests Table -->
    @if($requests->count() > 0)
        <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-800/50 text-left border-b border-slate-700">
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Book</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Duration</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Requested</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($requests as $request)
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="px-4 py-3">
                                    <div>
                                        <span class="font-medium text-white">{{ Str::limit($request->book->title, 25) }}</span>
                                        <p class="text-xs text-slate-500">by {{ $request->book->author ?? 'Unknown' }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div>
                                        <span class="text-slate-300">{{ $request->user->full_name }}</span>
                                        <p class="text-xs text-slate-500">{{ $request->user->email }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-400">{{ $request->duration_days }} days</td>
                                <td class="px-4 py-3 text-slate-400">{{ $request->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    {!! $request->status_badge !!}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($request->isPending())
                                            <button onclick="openApproveModal({{ $request->id }})" 
                                                    class="text-emerald-400 hover:text-emerald-300 transition" title="Approve">
                                                <i class="ti ti-check"></i>
                                            </button>
                                            <button onclick="openRejectModal({{ $request->id }})" 
                                                    class="text-red-400 hover:text-red-300 transition" title="Reject">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        @endif
                                        <button onclick="openViewModal({{ $request->id }})" 
                                                class="text-purple-400 hover:text-purple-300 transition" title="View Details">
                                            <i class="ti ti-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-6">
            {{ $requests->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-12 text-center">
            <i class="ti ti-inbox text-6xl text-slate-600 mb-4 block"></i>
            <h3 class="text-xl font-semibold text-white/60 mb-2">No Borrow Requests</h3>
            <p class="text-slate-500">No borrow requests found.</p>
        </div>
    @endif

</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-white mb-4">✅ Approve Request</h3>
        <p class="text-slate-400 mb-4">Are you sure you want to approve this borrow request?</p>
        <form id="approveForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm text-slate-400 mb-2">Admin Notes (Optional)</label>
                <textarea name="admin_notes" rows="3" class="search-bar" placeholder="Add any notes..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-library flex-1 justify-center bg-emerald-600 hover:bg-emerald-700">
                    <i class="ti ti-check"></i> Approve
                </button>
                <button type="button" onclick="closeModals()" class="bg-slate-800 text-slate-400 px-4 py-2 rounded-lg hover:bg-slate-700 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-white mb-4">❌ Reject Request</h3>
        <p class="text-slate-400 mb-4">Are you sure you want to reject this borrow request?</p>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm text-slate-400 mb-2">Reason for Rejection <span class="text-red-400">*</span></label>
                <textarea name="admin_notes" rows="3" class="search-bar" placeholder="Why is this request being rejected?" required></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-library flex-1 justify-center bg-red-600 hover:bg-red-700">
                    <i class="ti ti-x"></i> Reject
                </button>
                <button type="button" onclick="closeModals()" class="bg-slate-800 text-slate-400 px-4 py-2 rounded-lg hover:bg-slate-700 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openApproveModal(id) {
    document.getElementById('approveForm').action = '{{ route("librarian.borrow-requests.approve", "") }}/' + id;
    document.getElementById('approveModal').classList.remove('hidden');
    document.getElementById('approveModal').style.display = 'flex';
}

function openRejectModal(id) {
    document.getElementById('rejectForm').action = '{{ route("librarian.borrow-requests.reject", "") }}/' + id;
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeModals() {
    document.querySelectorAll('.fixed.inset-0').forEach(modal => {
        modal.style.display = 'none';
        modal.classList.add('hidden');
    });
}

// Close modals on background click
document.querySelectorAll('.fixed.inset-0').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModals();
        }
    });
});
</script>

@endsection