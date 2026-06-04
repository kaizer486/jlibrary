@extends('layouts.super-admin')

@section('title', 'Payment Management')

@section('content')
<div class="mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <i class="ti ti-wallet text-purple-600"></i>
            Payment Management
        </h1>
        <p class="text-gray-500 text-sm mt-1">Track all financial transactions across the platform</p>
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
        <p class="text-gray-500 text-sm">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">TSh {{ number_format($pendingPayments, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-red-500 shadow-sm">
        <p class="text-gray-500 text-sm">Withdrawals</p>
        <p class="text-2xl font-bold text-red-600">TSh {{ number_format($totalWithdrawals, 2) }}</p>
    </div>
</div>

<!-- Tabs -->
<div class="border-b border-gray-200 mb-6">
    <nav class="flex gap-8">
        <a href="{{ route('super-admin.payments.index') }}" class="pb-3 px-1 text-purple-600 border-b-2 border-purple-600 font-medium">All Payments</a>
        <a href="{{ route('super-admin.payments.transactions') }}" class="pb-3 px-1 text-gray-500 hover:text-gray-700">Transactions</a>
        <a href="{{ route('super-admin.payments.withdrawals') }}" class="pb-3 px-1 text-gray-500 hover:text-gray-700">Withdrawals</a>
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
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">🔍 Filter</button>
        </div>
        <div>
            <a href="{{ route('super-admin.payments.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition inline-block">Clear</a>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
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
                            <span class="text-purple-600">📚 Book Purchase</span>
                        @else
                            <span class="text-green-600">💰 Deposit</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-semibold text-gray-800">TSh {{ number_format($payment->amount, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ $payment->method ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        @if($payment->status === 'completed')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✅ Completed</span>
                        @elseif($payment->status === 'pending')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⏳ Pending</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">❌ Failed</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-500 font-mono">{{ $payment->reference ?? 'N/A' }}</td>
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
@endsection