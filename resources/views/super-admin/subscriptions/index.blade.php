@extends('layouts.super-admin')

@section('title', 'Subscription Management')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="ti ti-subscription text-purple-400"></i>
                Subscription Management
            </h1>
            <p class="text-slate-400 text-sm mt-1">Manage all institution subscriptions across the platform</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('super-admin.subscriptions.export', request()->query()) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl transition flex items-center gap-2 text-sm">
                <i class="ti ti-download"></i> Export CSV
            </a>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- SUBSCRIPTION STATS CARDS                   -->
<!-- ========================================== -->
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 hover:border-purple-500/30 transition">
        <p class="text-slate-400 text-xs">Total</p>
        <p class="text-2xl font-bold text-white">{{ number_format($statusCounts['all'] ?? 0) }}</p>
    </div>
    <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 hover:border-emerald-500/30 transition">
        <p class="text-slate-400 text-xs">Active</p>
        <p class="text-2xl font-bold text-emerald-400">{{ number_format($statusCounts['active'] ?? 0) }}</p>
    </div>
    <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 hover:border-yellow-500/30 transition">
        <p class="text-slate-400 text-xs">Pending</p>
        <p class="text-2xl font-bold text-yellow-400">{{ number_format($statusCounts['pending'] ?? 0) }}</p>
    </div>
    <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 hover:border-red-500/30 transition">
        <p class="text-slate-400 text-xs">Expired</p>
        <p class="text-2xl font-bold text-red-400">{{ number_format($statusCounts['expired'] ?? 0) }}</p>
    </div>
    <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 hover:border-gray-500/30 transition">
        <p class="text-slate-400 text-xs">Cancelled</p>
        <p class="text-2xl font-bold text-slate-400">{{ number_format($statusCounts['cancelled'] ?? 0) }}</p>
    </div>
    <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 hover:border-purple-500/30 transition">
        <p class="text-slate-400 text-xs">Revenue</p>
        <p class="text-2xl font-bold text-purple-400">TSh {{ number_format($subscriptionStats['revenue'] ?? 0) }}</p>
    </div>
</div>

<!-- ========================================== -->
<!-- FILTERS                                    -->
<!-- ========================================== -->
<div class="bg-slate-900 border border-slate-700 rounded-xl p-4 mb-6">
    <form method="GET" action="{{ route('super-admin.subscriptions.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <input type="text" name="search" placeholder="🔍 Search institution..." value="{{ request('search') }}"
                   class="search-bar w-full">
        </div>
        <div>
            <select name="status" class="search-bar w-full">
                <option value="all">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>✅ Active</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>❌ Expired</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>🚫 Cancelled</option>
            </select>
        </div>
        <div>
            <select name="plan" class="search-bar w-full">
                <option value="all">All Plans</option>
                <option value="basic" {{ request('plan') == 'basic' ? 'selected' : '' }}>📘 Basic</option>
                <option value="premium" {{ request('plan') == 'premium' ? 'selected' : '' }}>📚 Premium</option>
                <option value="enterprise" {{ request('plan') == 'enterprise' ? 'selected' : '' }}>🏢 Enterprise</option>
            </select>
        </div>
        <div>
            <select name="payment_method" class="search-bar w-full">
                <option value="all">All Methods</option>
                <option value="mpesa" {{ request('payment_method') == 'mpesa' ? 'selected' : '' }}>📱 M-Pesa</option>
                <option value="tigopesa" {{ request('payment_method') == 'tigopesa' ? 'selected' : '' }}>📱 TigoPesa</option>
                <option value="halopesa" {{ request('payment_method') == 'halopesa' ? 'selected' : '' }}>📱 HaloPesa</option>
                <option value="pesapal" {{ request('payment_method') == 'pesapal' ? 'selected' : '' }}>💳 PesaPal</option>
                <option value="bank" {{ request('payment_method') == 'bank' ? 'selected' : '' }}>🏦 Bank</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl transition flex-1">
                <i class="ti ti-filter"></i> Filter
            </button>
            <a href="{{ route('super-admin.subscriptions.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-xl transition">
                <i class="ti ti-x"></i>
            </a>
        </div>
    </form>
</div>

<!-- ========================================== -->
<!-- SUBSCRIPTIONS TABLE                        -->
<!-- ========================================== -->
@if($subscriptions->count() > 0)
<div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-700">
            <thead class="bg-slate-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">
                        <input type="checkbox" id="selectAll" class="rounded border-slate-600 bg-slate-700 text-purple-600">
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Institution</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Plan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Amount</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Payment</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Expires</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @foreach($subscriptions as $sub)
                <tr class="hover:bg-slate-800/50 transition">
                    <td class="px-4 py-3">
                        <input type="checkbox" name="subscription_ids[]" value="{{ $sub->id }}" class="subscription-checkbox rounded border-slate-600 bg-slate-700 text-purple-600">
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($sub->institution->name ?? 'N/A', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-white font-medium text-sm">{{ Str::limit($sub->institution->name ?? 'N/A', 25) }}</p>
                                <p class="text-xs text-slate-400">{{ $sub->institution->type_label ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold 
                            @if($sub->plan === 'enterprise') bg-purple-500/20 text-purple-400
                            @elseif($sub->plan === 'premium') bg-blue-500/20 text-blue-400
                            @else bg-slate-500/20 text-slate-400 @endif">
                            {{ ucfirst($sub->plan) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-white font-medium">TSh {{ number_format($sub->amount, 0) }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                            @if($sub->status === 'active') bg-emerald-500/20 text-emerald-400
                            @elseif($sub->status === 'pending') bg-yellow-500/20 text-yellow-400
                            @elseif($sub->status === 'expired') bg-red-500/20 text-red-400
                            @else bg-slate-500/20 text-slate-400 @endif">
                            {{ ucfirst($sub->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-400">
                        @if($sub->payment_method)
                            <span class="capitalize">{{ $sub->payment_method }}</span>
                        @else
                            <span class="text-slate-500">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-400">
                        @if($sub->ends_at)
                            {{ $sub->ends_at->format('M d, Y') }}
                            @if($sub->status === 'active')
                                <span class="block text-[10px] {{ $sub->ends_at->diffInDays(now()) < 7 ? 'text-red-400' : 'text-emerald-400' }}">
                                    {{ $sub->ends_at->diffInDays(now()) }} days left
                                </span>
                            @endif
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('super-admin.subscriptions.show', $sub) }}" 
                               class="text-blue-400 hover:text-blue-300 p-1.5 rounded-lg hover:bg-blue-500/10 transition" title="View">
                                <i class="ti ti-eye"></i>
                            </a>
                            @if($sub->status === 'pending')
                                <form method="POST" action="{{ route('super-admin.subscriptions.activate', $sub) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-emerald-400 hover:text-emerald-300 p-1.5 rounded-lg hover:bg-emerald-500/10 transition" title="Activate">
                                        <i class="ti ti-check"></i>
                                    </button>
                                </form>
                            @endif
                            @if($sub->status === 'active')
                                <form method="POST" action="{{ route('super-admin.subscriptions.cancel', $sub) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-orange-400 hover:text-orange-300 p-1.5 rounded-lg hover:bg-orange-500/10 transition" title="Cancel" onclick="return confirm('Cancel this subscription?')">
                                        <i class="ti ti-ban"></i>
                                    </button>
                                </form>
                                <a href="{{ route('super-admin.subscriptions.override', $sub) }}" 
                                   class="text-purple-400 hover:text-purple-300 p-1.5 rounded-lg hover:bg-purple-500/10 transition" title="Override">
                                    <i class="ti ti-settings"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================== -->
<!-- BULK ACTIONS & PAGINATION                  -->
<!-- ========================================== -->
<div class="mt-4 flex flex-col md:flex-row justify-between items-center gap-4">
    <div class="flex items-center gap-3">
        <select id="bulkAction" class="search-bar w-40">
            <option value="">Bulk Action</option>
            <option value="activate">✅ Activate</option>
            <option value="cancel">🚫 Cancel</option>
            <option value="expire">❌ Expire</option>
            <option value="delete">🗑️ Delete</option>
        </select>
        <button id="applyBulkAction" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl transition text-sm">
            Apply
        </button>
    </div>
    <div>
        {{ $subscriptions->appends(request()->query())->links() }}
    </div>
</div>

@else
<div class="bg-slate-900 border border-slate-700 rounded-xl p-12 text-center">
    <i class="ti ti-subscription text-6xl text-slate-600 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-white mb-2">No Subscriptions Found</h3>
    <p class="text-slate-400">Try adjusting your filters or search terms.</p>
</div>
@endif

<!-- ========================================== -->
<!-- JAVASCRIPT FOR BULK ACTIONS                -->
<!-- ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.subscription-checkbox');
    
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
    
    // Bulk Action
    document.getElementById('applyBulkAction').addEventListener('click', function() {
        const action = document.getElementById('bulkAction').value;
        if (!action) {
            alert('Please select an action.');
            return;
        }
        
        const selected = document.querySelectorAll('.subscription-checkbox:checked');
        if (selected.length === 0) {
            alert('Please select at least one subscription.');
            return;
        }
        
        const ids = Array.from(selected).map(cb => cb.value);
        
        if (!confirm(`Are you sure you want to ${action} ${ids.length} subscription(s)?`)) {
            return;
        }
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("super-admin.subscriptions.bulk") }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        form.appendChild(actionInput);
        
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'subscription_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    });
});
</script>

@endsection