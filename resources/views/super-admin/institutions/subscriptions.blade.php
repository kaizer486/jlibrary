@extends('layouts.super-admin')

@section('title', 'Institution Subscriptions')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="ti ti-building text-purple-600"></i>
                 Subscriptions
            </h1>
            <p class="text-gray-500 text-sm mt-1">Manage all institution subscriptions across the platform</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('super-admin.institutions.subscriptions.export', request()->query()) }}" 
               class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl transition flex items-center gap-2 text-sm">
                <i class="ti ti-download"></i> Export CSV
            </a>
            <button onclick="openBulkAssignModal()" 
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl transition flex items-center gap-2 text-sm">
                <i class="ti ti-layers"></i> Bulk Assign
            </button>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- STATS CARDS                               -->
<!-- ========================================== -->
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-purple-500">
        <p class="text-gray-500 text-xs">Total Institutions</p>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total'] ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-emerald-500">
        <p class="text-gray-500 text-xs">With Active Subscription</p>
        <p class="text-2xl font-bold text-emerald-600">{{ number_format($stats['active'] ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-red-500">
        <p class="text-gray-500 text-xs">Without Subscription</p>
        <p class="text-2xl font-bold text-red-600">{{ number_format($stats['inactive'] ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-orange-500">
        <p class="text-gray-500 text-xs">Expiring Soon</p>
        <p class="text-2xl font-bold text-orange-600">{{ number_format($stats['expiring_soon'] ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-yellow-500">
        <p class="text-gray-500 text-xs">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['pending'] ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-purple-500">
        <p class="text-gray-500 text-xs">Subscription Revenue</p>
        <p class="text-2xl font-bold text-purple-600">TSh {{ number_format(array_sum($stats['revenue_by_plan'] ?? []), 0) }}</p>
    </div>
</div>

<!-- ========================================== -->
<!-- FILTERS                                    -->
<!-- ========================================== -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('super-admin.institutions.subscriptions.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <input type="text" name="search" placeholder="🔍 Search institution..." value="{{ request('search') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
        <div>
            <select name="subscription_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white">
                <option value="all">All Subscription Status</option>
                <option value="active" {{ request('subscription_status') == 'active' ? 'selected' : '' }}>✅ Active</option>
                <option value="expired" {{ request('subscription_status') == 'expired' ? 'selected' : '' }}>❌ No Subscription</option>
                <option value="pending" {{ request('subscription_status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="cancelled" {{ request('subscription_status') == 'cancelled' ? 'selected' : '' }}>🚫 Cancelled</option>
            </select>
        </div>
        <div>
            <select name="plan" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white">
                <option value="all">All Plans</option>
                <option value="basic" {{ request('plan') == 'basic' ? 'selected' : '' }}>📘 Basic</option>
                <option value="premium" {{ request('plan') == 'premium' ? 'selected' : '' }}>📚 Premium</option>
                <option value="enterprise" {{ request('plan') == 'enterprise' ? 'selected' : '' }}>🏢 Enterprise</option>
            </select>
        </div>
        <div>
            <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white">
                <option value="all">All Types</option>
                <option value="school" {{ request('type') == 'school' ? 'selected' : '' }}>🏫 School</option>
                <option value="college" {{ request('type') == 'college' ? 'selected' : '' }}>🎓 College</option>
                <option value="university" {{ request('type') == 'university' ? 'selected' : '' }}>🏛️ University</option>
                <option value="library" {{ request('type') == 'library' ? 'selected' : '' }}>📚 Library</option>
                <option value="bookstore" {{ request('type') == 'bookstore' ? 'selected' : '' }}>📖 Bookstore</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition flex-1">
                <i class="ti ti-filter"></i> Filter
            </button>
            <a href="{{ route('super-admin.institutions.subscriptions.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
                <i class="ti ti-x"></i>
            </a>
        </div>
    </form>
</div>

<!-- ========================================== -->
<!-- INSTITUTIONS TABLE                        -->
<!-- ========================================== -->
@if($institutions->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-purple-600">
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Institution</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expires</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Users</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($institutions as $inst)
                @php
                    $sub = $inst->activeSubscription;
                    $hasActive = $sub && $sub->isActive();
                    $daysLeft = $sub && $sub->ends_at ? $sub->ends_at->diffInDays(now()) : 0;
                    $isExpiringSoon = $hasActive && $daysLeft <= 7;
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        <input type="checkbox" name="institution_ids[]" value="{{ $inst->id }}" class="institution-checkbox rounded border-gray-300 text-purple-600">
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($inst->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 text-sm">{{ Str::limit($inst->name, 25) }}</p>
                                <p class="text-xs text-gray-500">{{ $inst->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        {{ $inst->type_label ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($hasActive)
                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                @if($sub->plan === 'enterprise') bg-purple-100 text-purple-700
                                @elseif($sub->plan === 'premium') bg-blue-100 text-blue-700
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ ucfirst($sub->plan) }}
                            </span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                None
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($hasActive)
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($isExpiringSoon) bg-orange-100 text-orange-700
                                @else bg-emerald-100 text-emerald-700 @endif">
                                @if($isExpiringSoon)
                                    ⚠️ {{ $daysLeft }} days left
                                @else
                                    ✅ Active
                                @endif
                            </span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                ❌ No Subscription
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-800">
                        @if($hasActive)
                            TSh {{ number_format($sub->amount, 0) }}
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        @if($hasActive && $sub->ends_at)
                            {{ $sub->ends_at->format('M d, Y') }}
                            @if($isExpiringSoon)
                                <span class="block text-[10px] text-orange-600">Expiring soon!</span>
                            @endif
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800">
                        {{ $inst->users()->count() }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1">
                            <button onclick="openAssignModal({{ $inst->id }}, '{{ $inst->name }}')" 
                                    class="text-purple-600 hover:text-purple-800 p-1.5 rounded-lg hover:bg-purple-50 transition" title="Assign Subscription">
                                <i class="ti ti-settings"></i>
                            </button>
                            @if($hasActive)
                                <form method="POST" action="{{ route('super-admin.institutions.subscriptions.cancel', $sub->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-orange-600 hover:text-orange-800 p-1.5 rounded-lg hover:bg-orange-50 transition" title="Cancel" onclick="return confirm('Cancel this subscription?')">
                                        <i class="ti ti-ban"></i>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('super-admin.institutions.show', $inst) }}" 
                               class="text-gray-500 hover:text-gray-700 p-1.5 rounded-lg hover:bg-gray-50 transition" title="View Institution">
                                <i class="ti ti-building"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $institutions->appends(request()->query())->links() }}
</div>

@else
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="ti ti-building text-6xl text-gray-300 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-800 mb-2">No Institutions Found</h3>
    <p class="text-gray-500">Try adjusting your filters or search terms.</p>
</div>
@endif

<!-- ========================================== -->
<!-- ASSIGN SUBSCRIPTION MODAL                  -->
<!-- ========================================== -->
<div id="assignModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="ti ti-settings text-purple-600"></i>
                Assign Subscription
            </h3>
            <button onclick="closeAssignModal()" class="text-gray-400 hover:text-gray-600">
                <i class="ti ti-x text-2xl"></i>
            </button>
        </div>
        
        <p class="text-gray-500 text-sm mb-4" id="assignInstitutionName">Assigning to: <span class="text-gray-800 font-semibold"></span></p>
        
        <form id="assignForm" method="POST">
            @csrf
            <input type="hidden" name="institution_id" id="assignInstitutionId">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Plan <span class="text-red-500">*</span></label>
                    <select name="plan" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                        <option value="basic">📘 Basic</option>
                        <option value="premium">📚 Premium</option>
                        <option value="enterprise">🏢 Enterprise</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Period <span class="text-red-500">*</span></label>
                    <select name="period" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="semi_annual">Semi-Annual</option>
                        <option value="annual">Annual</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (TSh) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" step="0.01" required placeholder="Enter amount"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                        <option value="active">✅ Active</option>
                        <option value="pending">⏳ Pending</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
                    <select name="payment_method" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                        <option value="">Select Method</option>
                        <option value="mpesa">📱 M-Pesa</option>
                        <option value="tigopesa">📱 TigoPesa</option>
                        <option value="halopesa">📱 HaloPesa</option>
                        <option value="pesapal">💳 PesaPal</option>
                        <option value="bank">🏦 Bank Transfer</option>
                        <option value="manual">📝 Manual</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500" placeholder="Optional notes..."></textarea>
                </div>
                
                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-xl hover:shadow-lg transition font-semibold">
                    <i class="ti ti-check"></i> Assign Subscription
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- BULK ASSIGN MODAL                         -->
<!-- ========================================== -->
<div id="bulkAssignModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="ti ti-layers text-purple-600"></i>
                Bulk Assign Subscription
            </h3>
            <button onclick="closeBulkAssignModal()" class="text-gray-400 hover:text-gray-600">
                <i class="ti ti-x text-2xl"></i>
            </button>
        </div>
        
        <p class="text-gray-500 text-sm mb-4">Assign subscription to multiple institutions at once.</p>
        
        <form id="bulkAssignForm" method="POST" action="{{ route('super-admin.institutions.subscriptions.bulk') }}">
            @csrf
            <input type="hidden" name="institution_ids" id="bulkInstitutionIds">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Plan <span class="text-red-500">*</span></label>
                    <select name="plan" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                        <option value="basic">📘 Basic</option>
                        <option value="premium">📚 Premium</option>
                        <option value="enterprise">🏢 Enterprise</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Period <span class="text-red-500">*</span></label>
                    <select name="period" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="semi_annual">Semi-Annual</option>
                        <option value="annual">Annual</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (TSh) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" step="0.01" required placeholder="Enter amount"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                        <option value="active">✅ Active</option>
                        <option value="pending">⏳ Pending</option>
                    </select>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <p class="text-xs text-yellow-700 flex items-center gap-1">
                        <i class="ti ti-info-circle"></i>
                        This will apply the same subscription to all selected institutions.
                    </p>
                </div>
                
                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-xl hover:shadow-lg transition font-semibold">
                    <i class="ti ti-check"></i> Assign to Selected
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT                                -->
<!-- ========================================== -->
<script>
// Select All
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.institution-checkbox').forEach(cb => cb.checked = this.checked);
});

// Assign Modal
function openAssignModal(id, name) {
    document.getElementById('assignInstitutionId').value = id;
    document.getElementById('assignInstitutionName').querySelector('span').textContent = name;
   document.getElementById('assignForm').action = "/super-admin/institution-subscriptions/" + id + "/assign";
    document.getElementById('assignModal').classList.remove('hidden');
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
}

// Bulk Assign Modal
function openBulkAssignModal() {
    const selected = document.querySelectorAll('.institution-checkbox:checked');
    if (selected.length === 0) {
        alert('Please select at least one institution.');
        return;
    }
    
    const ids = Array.from(selected).map(cb => cb.value);
    document.getElementById('bulkInstitutionIds').value = JSON.stringify(ids);
    document.getElementById('bulkAssignModal').classList.remove('hidden');
}

function closeBulkAssignModal() {
    document.getElementById('bulkAssignModal').classList.add('hidden');
}

// Close modals on outside click
document.getElementById('assignModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeAssignModal();
});
document.getElementById('bulkAssignModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeBulkAssignModal();
});
</script>
@endsection