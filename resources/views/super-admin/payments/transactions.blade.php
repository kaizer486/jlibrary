@extends('layouts.super-admin')

@section('title', 'Transaction History')

@section('content')
<div class="mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <i class="ti ti-history text-purple-600"></i>
            Transaction History
        </h1>
        <p class="text-gray-500 text-sm mt-1">View all wallet transactions across the platform</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border-l-4 border-green-500 shadow-sm">
        <p class="text-gray-500 text-sm">Total Credits (Incoming)</p>
        <p class="text-2xl font-bold text-green-600">TSh {{ number_format($totalCredits, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-red-500 shadow-sm">
        <p class="text-gray-500 text-sm">Total Debits (Outgoing)</p>
        <p class="text-2xl font-bold text-red-600">TSh {{ number_format($totalDebits, 2) }}</p>
    </div>
</div>

<!-- Tabs -->
<div class="border-b border-gray-200 mb-6">
    <nav class="flex gap-8">
        <a href="{{ route('super-admin.payments.index') }}" class="pb-3 px-1 text-gray-500 hover:text-gray-700">All Payments</a>
        <a href="{{ route('super-admin.payments.transactions') }}" class="pb-3 px-1 text-purple-600 border-b-2 border-purple-600 font-medium">Transactions</a>
        <a href="{{ route('super-admin.payments.withdrawals') }}" class="pb-3 px-1 text-gray-500 hover:text-gray-700">Withdrawals</a>
    </nav>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('super-admin.payments.transactions') }}" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
            <i class="ti ti-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" placeholder="Search by user name or email..." value="{{ request('search') }}" 
                   class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
        <div>
            <select name="type" class="px-4 py-2 border border-gray-200 rounded-lg bg-white">
                <option value="">All Types</option>
                <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>💰 Credit (Incoming)</option>
                <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>💸 Debit (Outgoing)</option>
                <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>🏦 Withdrawal</option>
                <option value="commission" {{ request('type') == 'commission' ? 'selected' : '' }}>📊 Commission</option>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">🔍 Filter</button>
        </div>
        <div>
            <a href="{{ route('super-admin.payments.transactions') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition inline-block">Clear</a>
        </div>
    </form>
</div>

<!-- Transactions Table -->
@if($transactions->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance After</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($transactions as $transaction)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-800">{{ $transaction->user->full_name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $transaction->user->email ?? 'N/A' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($transaction->type === 'credit')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">💰 Credit</span>
                        @elseif($transaction->type === 'debit')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">💸 Debit</span>
                        @elseif($transaction->type === 'withdrawal')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">🏦 Withdrawal</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">📊 Commission</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-semibold {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type === 'credit' ? '+' : '-' }} TSh {{ number_format($transaction->amount, 2) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">TSh {{ number_format($transaction->balance_after, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">{{ Str::limit($transaction->description, 50) }}</td>
                    <td class="px-6 py-4">
                        @if($transaction->status === 'completed')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✅ Completed</span>
                        @elseif($transaction->status === 'pending')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⏳ Pending</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">❌ Failed</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $transactions->appends(request()->query())->links() }}
</div>

@else
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="ti ti-history text-6xl text-gray-400 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Transactions Found</h3>
    <p class="text-gray-500">Transactions will appear here when users make wallet transactions.</p>
</div>
@endif
@endsection