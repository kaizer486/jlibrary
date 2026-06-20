@extends('layouts.master')

@section('title', 'Admin - Payments Management')

@section('page-content')

<div class="container mx-auto px-4 py-8 max-w-7xl">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Payments Management</h1>
        <p class="text-gray-600">View deposits, withdrawals, and all financial transactions</p>
        <div class="mt-2 text-sm text-amber-600 bg-amber-50 inline-block px-3 py-1 rounded-lg">
            <i class="ti ti-info-circle"></i> Note: Financial approvals are handled by SuperAdmin only
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Total Deposits</p>
                    <p class="text-2xl font-bold text-green-600">TSh {{ number_format($totalDeposits, 2) }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="ti ti-arrow-down-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Pending Deposits</p>
                    <p class="text-2xl font-bold text-yellow-600">TSh {{ number_format($pendingDeposits, 2) }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="ti ti-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Total Withdrawals</p>
                    <p class="text-2xl font-bold text-red-600">TSh {{ number_format($totalWithdrawals, 2) }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="ti ti-arrow-up-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Book Sales</p>
                    <p class="text-2xl font-bold text-purple-600">TSh {{ number_format($totalBookSales, 2) }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="ti ti-book text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pending Withdrawals Section (VIEW ONLY - No actions) -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                <i class="ti ti-alert-circle"></i> Pending Withdrawals (Awaiting SuperAdmin Approval)
                <span class="bg-white/20 px-2 py-0.5 rounded-full text-sm">{{ $pendingWithdrawals->count() }}</span>
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Method</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Requested Date</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($pendingWithdrawals as $wd)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">{{ $wd->user->full_name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $wd->user->email ?? 'N/A' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-red-600">TSh {{ number_format($wd->amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="capitalize px-2 py-1 bg-gray-100 rounded-full text-xs">{{ $wd->method }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $wd->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <button onclick="showDetails({{ json_encode($wd) }})" class="text-purple-600 hover:text-purple-800 text-sm">
                                <i class="ti ti-eye"></i> View Details
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <i class="ti ti-check-circle text-4xl mb-2 block"></i>
                            No pending withdrawals
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pending INSTITUTION Withdrawals Section (VIEW ONLY - No actions) -->
    @if(isset($pendingInstitutionWithdrawals) && $pendingInstitutionWithdrawals->count() > 0)
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                <i class="ti ti-building"></i> Institution Withdrawal Requests (Awaiting SuperAdmin Approval)
                <span class="bg-white/20 px-2 py-0.5 rounded-full text-sm">{{ $pendingInstitutionWithdrawals->count() }}</span>
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Institution</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Requested By</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Method</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Requested</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($pendingInstitutionWithdrawals as $wd)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">{{ $wd->institution->name }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-800">{{ $wd->requester->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $wd->requester->email }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-red-600">TSh {{ number_format($wd->amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="capitalize px-2 py-1 bg-gray-100 rounded-full text-xs">{{ $wd->payment_method }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $wd->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <button onclick="showInstitutionDetails({{ json_encode($wd) }})" class="text-purple-600 hover:text-purple-800 text-sm">
                                <i class="ti ti-eye"></i> View Details
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    
    <!-- All Transactions Section -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                <i class="ti ti-history"></i> All Transactions
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Method</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Reference</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">{{ $payment->user->full_name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->user->email ?? 'N/A' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($payment->payable_type === 'App\\Models\\Book')
                                <span class="text-purple-600">📚 Book Purchase</span>
                            @else
                                <span class="text-green-600">💰 Deposit</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-gray-800">TSh {{ number_format($payment->amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="capitalize">{{ $payment->method ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs 
                                {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $payment->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500 font-mono">{{ $payment->reference ?? 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="ti ti-receipt text-4xl mb-2 block"></i>
                            No transactions found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">
            {{ $payments->links() }}
        </div>
    </div>
</div>

<!-- Details Modal for User Withdrawals -->
<div id="detailsModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl max-w-md w-full mx-4 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-4 text-white">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold">Withdrawal Details</h3>
                <button onclick="closeModal()" class="text-white/80 hover:text-white">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6" id="modalContent">
            <!-- Dynamic content -->
        </div>
    </div>
</div>

<!-- Details Modal for Institution Withdrawals -->
<div id="institutionDetailsModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl max-w-md w-full mx-4 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4 text-white">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold">Institution Withdrawal Details</h3>
                <button onclick="closeInstitutionModal()" class="text-white/80 hover:text-white">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6" id="institutionModalContent">
            <!-- Dynamic content -->
        </div>
    </div>
</div>

<script>
function showDetails(data) {
    const modal = document.getElementById('detailsModal');
    const content = document.getElementById('modalContent');
    
    let detailsHtml = `
        <div class="space-y-3">
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">User:</span>
                <span class="font-semibold">${data.user?.full_name || 'N/A'}</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Amount:</span>
                <span class="font-semibold text-red-600">TSh ${new Intl.NumberFormat().format(data.amount)}</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Method:</span>
                <span class="font-semibold capitalize">${data.method}</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Reference:</span>
                <span class="font-mono text-sm">${data.reference}</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Requested:</span>
                <span>${new Date(data.created_at).toLocaleString()}</span>
            </div>
            <div class="mt-4 pt-3 border-t">
                <p class="text-xs text-gray-400 text-center">Approval is handled by SuperAdmin</p>
            </div>
        </div>
    `;
    
    content.innerHTML = detailsHtml;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function showInstitutionDetails(data) {
    const modal = document.getElementById('institutionDetailsModal');
    const content = document.getElementById('institutionModalContent');
    
    let detailsHtml = `
        <div class="space-y-3">
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Institution:</span>
                <span class="font-semibold">${data.institution?.name || 'N/A'}</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Requested By:</span>
                <span class="font-semibold">${data.requester?.full_name || 'N/A'}</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Amount:</span>
                <span class="font-semibold text-red-600">TSh ${new Intl.NumberFormat().format(data.amount)}</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Method:</span>
                <span class="font-semibold capitalize">${data.payment_method}</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Account Details:</span>
                <span class="font-mono text-sm">${data.account_details || 'N/A'}</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Requested:</span>
                <span>${new Date(data.created_at).toLocaleString()}</span>
            </div>
            ${data.notes ? `
            <div class="border-b pb-2">
                <span class="text-gray-500">Notes:</span>
                <p class="text-sm mt-1">${data.notes}</p>
            </div>
            ` : ''}
            <div class="mt-4 pt-3 border-t">
                <p class="text-xs text-gray-400 text-center">Approval is handled by SuperAdmin</p>
            </div>
        </div>
    `;
    
    content.innerHTML = detailsHtml;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {
    const modal = document.getElementById('detailsModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function closeInstitutionModal() {
    const modal = document.getElementById('institutionDetailsModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close modals on click outside
document.getElementById('detailsModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

document.getElementById('institutionDetailsModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeInstitutionModal();
    }
});
</script>
@endsection