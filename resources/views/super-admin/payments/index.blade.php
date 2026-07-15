@extends('layouts.super-admin')

@section('title', 'Payment Management')

@section('content')
<div class="mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <i class="ti ti-wallet text-purple-600"></i>
            Payment Management
        </h1>
        <p class="text-gray-500 text-sm mt-1">SuperAdmin: Full financial control and transaction oversight</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border-l-4 border-purple-500 shadow-sm">
        <p class="text-gray-500 text-sm">Total Revenue</p>
        <p class="text-2xl font-bold text-purple-600">TSh {{ number_format($totalRevenue, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-blue-500 shadow-sm">
        <p class="text-gray-500 text-sm">Book Sales</p>
        <p class="text-2xl font-bold text-blue-600">TSh {{ number_format($totalBookSales, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-green-500 shadow-sm">
        <p class="text-gray-500 text-sm">Deposits</p>
        <p class="text-2xl font-bold text-green-600">TSh {{ number_format($totalDeposits, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-yellow-500 shadow-sm">
        <p class="text-gray-500 text-sm">Pending Payments</p>
        <p class="text-2xl font-bold text-yellow-600">TSh {{ number_format($pendingPayments, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-red-500 shadow-sm">
        <p class="text-gray-500 text-sm">Withdrawals</p>
        <p class="text-2xl font-bold text-red-600">TSh {{ number_format($totalWithdrawals, 2) }}</p>
    </div>
</div>

<!-- Additional Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border-l-4 border-indigo-500 shadow-sm">
        <p class="text-gray-500 text-sm">Platform Fees (20%)</p>
        <p class="text-2xl font-bold text-indigo-600">TSh {{ number_format($totalPlatformFees ?? 0, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-cyan-500 shadow-sm">
        <p class="text-gray-500 text-sm">Author Earnings (80%)</p>
        <p class="text-2xl font-bold text-cyan-600">TSh {{ number_format($totalAuthorEarnings ?? 0, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-orange-500 shadow-sm">
        <p class="text-gray-500 text-sm">Pending Author Payouts</p>
        <p class="text-2xl font-bold text-orange-600">TSh {{ number_format($pendingAuthorPayouts ?? 0, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-teal-500 shadow-sm">
        <p class="text-gray-500 text-sm">Total Commission Logs</p>
        <p class="text-2xl font-bold text-teal-600">{{ number_format($totalTransactions ?? 0) }}</p>
    </div>
</div>

<!-- Tabs -->
<div class="border-b border-gray-200 mb-6">
    <nav class="flex flex-wrap gap-4 md:gap-8">
        <a href="{{ route('super-admin.payments.index') }}" class="pb-3 px-1 text-purple-600 border-b-2 border-purple-600 font-medium">📊 All Payments</a>
        <a href="{{ route('super-admin.payments.transactions') }}" class="pb-3 px-1 text-gray-500 hover:text-gray-700">📋 Transactions</a>
        <a href="{{ route('super-admin.payments.withdrawals') }}" class="pb-3 px-1 text-gray-500 hover:text-gray-700">💸 Withdrawals</a>
        <a href="{{ route('super-admin.payments.commissions') }}" class="pb-3 px-1 text-gray-500 hover:text-gray-700">📈 Commissions</a>
        <a href="{{ route('super-admin.payments.author-payouts') }}" class="pb-3 px-1 text-gray-500 hover:text-gray-700">👨‍💻 Author Payouts</a>
        <a href="{{ route('super-admin.payments.audit-logs') }}" class="pb-3 px-1 text-gray-500 hover:text-gray-700">📜 Audit Logs</a>
        <a href="{{ route('super-admin.payments.export') }}" class="pb-3 px-1 text-gray-500 hover:text-gray-700">📥 Export Report</a>
    </nav>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('super-admin.payments.index') }}" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
            <i class="ti ti-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" placeholder="Search by user name or email..." value="{{ request('search') }}" 
                   class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
        <div>
            <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg bg-white">
                <option value="">All Status</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>✅ Completed</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>❌ Failed</option>
                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>🔄 Refunded</option>
            </select>
        </div>
        <div>
            <select name="type" class="px-4 py-2 border border-gray-200 rounded-lg bg-white">
                <option value="">All Types</option>
                <option value="book" {{ request('type') == 'book' ? 'selected' : '' }}>📚 Book Purchase</option>
                <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>💰 Deposit</option>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                <i class="ti ti-filter"></i> Filter
            </button>
        </div>
        <div>
            <a href="{{ route('super-admin.payments.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition inline-block">
                <i class="ti ti-clear"></i> Clear
            </a>
        </div>
    </form>
</div>

<!-- Payments Table -->
@if($payments->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($payments as $payment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-800">{{ $payment->user->full_name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->user->email ?? 'N/A' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($payment->payable_type === 'App\\Models\\Book')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">📚 Book Purchase</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">💰 Deposit</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="font-semibold text-gray-800">TSh {{ number_format($payment->amount, 2) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="capitalize px-2 py-1 bg-gray-100 rounded-full text-xs">{{ $payment->method ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($payment->status === 'completed')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✅ Completed</span>
                        @elseif($payment->status === 'pending')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⏳ Pending</span>
                        @elseif($payment->status === 'refunded')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">🔄 Refunded</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">❌ Failed</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-500 font-mono">{{ $payment->reference ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('super-admin.payments.show', $payment) }}" class="text-blue-600 hover:text-blue-800" title="View Details">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="{{ route('invoices.payment', $payment->id) }}" class="text-green-600 hover:text-green-800" title="Download Invoice" target="_blank">
                                <i class="ti ti-file-invoice"></i>
                            </a>
                            <!-- Delete Payment -->
                            <button type="button" 
                                    class="text-red-600 hover:text-red-800 transition delete-payment-btn"
                                    data-id="{{ $payment->id }}"
                                    data-reference="{{ $payment->reference }}"
                                    data-amount="{{ number_format($payment->amount, 2) }}"
                                    title="Permanently Delete Payment">
                                <i class="ti ti-trash"></i>
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
    {{ $payments->appends(request()->query())->links() }}
</div>

@else
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="ti ti-receipt text-6xl text-gray-400 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Payments Found</h3>
    <p class="text-gray-500">Payments will appear here when users make transactions.</p>
</div>
@endif

<!-- Monthly Revenue Chart Section -->
@if(isset($monthlyRevenue))
<div class="mt-8 bg-white rounded-xl shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="ti ti-chart-line text-purple-600"></i>
        Monthly Revenue Trend
    </h3>
    <div class="h-64">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyRevenue['months'] ?? []) !!},
                datasets: [{
                    label: 'Revenue (TSh)',
                    data: {!! json_encode($monthlyRevenue['revenue'] ?? []) !!},
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'TSh ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endif

<!-- ========================================== -->
<!-- DELETE PAYMENT CONFIRMATION MODAL -->
<!-- ========================================== -->
<div id="deletePaymentModal" class="fixed inset-0 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closePaymentModal()"></div>
        <div class="relative bg-white rounded-xl max-w-sm w-full p-6 shadow-2xl transform transition-all">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Payment?</h3>
                
                <div class="bg-gray-50 rounded-lg p-3 mb-4 text-left text-sm">
                    <p><span class="font-medium">Reference:</span> <span id="payment-modal-ref" class="font-mono"></span></p>
                    <p><span class="font-medium">Amount:</span> <span id="payment-modal-amount" class="font-bold text-red-600"></span></p>
                </div>
                
                <p class="text-red-600 font-medium text-sm mb-6"> This action cannot be undone!</p>
                
                <div class="flex gap-3 justify-center">
                    <button onclick="closePaymentModal()" 
                            class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition text-sm font-medium">
                        Cancel
                    </button>
                    <form id="deletePaymentForm" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition text-sm font-medium">
                            Yes, Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete Payment button - Open Modal
        document.querySelectorAll('.delete-payment-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const reference = this.dataset.reference;
                const amount = this.dataset.amount;
                
                document.getElementById('payment-modal-ref').textContent = reference;
                document.getElementById('payment-modal-amount').textContent = 'TSh ' + amount;
               document.getElementById('deletePaymentForm').action = '{{ route("super-admin.payments.delete-payment", ":id") }}'.replace(':id', id);
                document.getElementById('deletePaymentModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
        });
        
        // Keep scroll position
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                localStorage.setItem('scrollPosition', window.scrollY);
                localStorage.setItem('currentPage', window.location.href);
            });
        });
    });
    
    function closePaymentModal() {
        document.getElementById('deletePaymentModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePaymentModal();
    });
</script>
@endpush
@endsection