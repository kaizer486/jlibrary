@extends('layouts.super-admin')

@section('title', 'Deleted Transactions')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Deleted Transactions</h1>
            <p class="text-gray-500 text-sm mt-1">View and restore soft-deleted transactions</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('super-admin.payments.transactions') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition text-sm">
                <i class="ti ti-arrow-left"></i> Back to Transactions
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-red-500">
            <p class="text-sm text-gray-500">Deleted Transactions</p>
            <p class="text-2xl font-bold text-red-600">{{ $transactions->total() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500">Total Amount Deleted</p>
            <p class="text-2xl font-bold text-yellow-600">{{ number_format($transactions->sum('amount'), 2) }} TSh</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
            <p class="text-sm text-gray-500">Pending Restore</p>
            <p class="text-2xl font-bold text-purple-600">{{ $transactions->where('status', 'pending')->count() }}</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form action="{{ route('super-admin.payments.deleted-transactions') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Search deleted transactions..." 
                       value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">
            </div>
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition text-sm">
                <i class="ti ti-search"></i> Filter
            </button>
            @if(request('search'))
            <a href="{{ route('super-admin.payments.deleted-transactions') }}" class="text-gray-500 hover:text-gray-700 text-sm">
                <i class="ti ti-x"></i> Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        @if($transactions->count() > 0)
        <form id="bulk-restore-form" action="{{ route('super-admin.payments.bulk-restore-transactions') }}" method="POST">
            @csrf
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left w-10">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deleted At</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($transactions as $transaction)
                        <tr class="hover:bg-gray-50 transition bg-red-50">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="transaction_ids[]" value="{{ $transaction->id }}" 
                                       class="transaction-checkbox rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 font-mono">
                                {{ $transaction->reference }}
                                @if($transaction->order_id)
                                <br><span class="text-xs text-gray-500">Order: {{ $transaction->order_id }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if($transaction->user)
                                    <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-sm font-medium">
                                        {{ substr($transaction->user->full_name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $transaction->user->full_name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $transaction->user->email ?? '' }}</p>
                                    </div>
                                    @else
                                    <span class="text-sm text-gray-500">User deleted</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-bold {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type === 'credit' ? '+' : '-' }} {{ number_format($transaction->amount, 2) }} TSh
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full font-medium
                                    {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $transaction->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $transaction->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $transaction->deleted_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Restore -->
                                    <form action="{{ route('super-admin.payments.restore-transaction', $transaction->id) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Restore this transaction?');">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 transition" title="Restore">
                                            <i class="ti ti-restore text-lg"></i>
                                        </button>
                                    </form>
                                    
                                    <!-- Permanent Delete -->
                                    <form action="{{ route('super-admin.payments.hard-delete-transaction', $transaction->id) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('⚠️ Permanently delete this transaction? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 transition" title="Permanently Delete">
                                            <i class="ti ti-trash-off text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Bulk Actions -->
            <div class="px-4 py-3 bg-gray-50 border-t flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span id="selected-count" class="text-sm text-gray-500">0 selected</span>
                    
                    <!-- Bulk Restore -->
                    <button type="submit" id="bulk-restore-btn" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                        <i class="ti ti-restore"></i> Restore Selected
                    </button>
                </div>
                
                <!-- Pagination -->
                <div>
                    {{ $transactions->links() }}
                </div>
            </div>
        </form>
        @else
        <div class="text-center py-12">
            <div class="text-6xl mb-4">🗑️</div>
            <h3 class="text-lg font-medium text-gray-900">No deleted transactions</h3>
            <p class="text-gray-500">All transactions are active</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.transaction-checkbox');
        const selectedCount = document.getElementById('selected-count');
        const bulkRestoreBtn = document.getElementById('bulk-restore-btn');
        
        function updateSelectedCount() {
            const checked = document.querySelectorAll('.transaction-checkbox:checked').length;
            selectedCount.textContent = checked + ' selected';
            bulkRestoreBtn.disabled = checked === 0;
        }
        
        selectAll?.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = document.querySelectorAll('.transaction-checkbox:checked').length === checkboxes.length;
                selectAll.checked = allChecked;
                updateSelectedCount();
            });
        });
        
        updateSelectedCount();
    });
</script>
@endpush
@endsection