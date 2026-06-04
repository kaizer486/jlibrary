@extends('layouts.super-admin')

@section('title', 'Withdrawal Requests')

@section('content')
<div class="mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <i class="ti ti-arrow-up-circle text-purple-600"></i>
            Withdrawal Requests
        </h1>
        <p class="text-gray-500 text-sm mt-1">Manage all withdrawal requests from users and institutions</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border-l-4 border-yellow-500 shadow-sm">
        <p class="text-gray-500 text-sm">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">TSh {{ number_format($stats['pending'], 2) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-blue-500 shadow-sm">
        <p class="text-gray-500 text-sm">Processing</p>
        <p class="text-2xl font-bold text-blue-600">TSh {{ number_format($stats['processing'], 2) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-green-500 shadow-sm">
        <p class="text-gray-500 text-sm">Completed</p>
        <p class="text-2xl font-bold text-green-600">TSh {{ number_format($stats['completed'], 2) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-purple-500 shadow-sm">
        <p class="text-gray-500 text-sm">Total Requested</p>
        <p class="text-2xl font-bold text-purple-600">TSh {{ number_format($stats['total'], 2) }}</p>
    </div>
</div>

<!-- Tabs -->
<div class="border-b border-gray-200 mb-6">
    <nav class="flex gap-8">
        <a href="{{ route('super-admin.payments.index') }}" class="pb-3 px-1 text-gray-500 hover:text-gray-700">All Payments</a>
        <a href="{{ route('super-admin.payments.transactions') }}" class="pb-3 px-1 text-gray-500 hover:text-gray-700">Transactions</a>
        <a href="{{ route('super-admin.payments.withdrawals') }}" class="pb-3 px-1 text-purple-600 border-b-2 border-purple-600 font-medium">Withdrawals</a>
    </nav>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('super-admin.payments.withdrawals') }}" class="flex flex-col md:flex-row gap-4">
        <div>
            <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg bg-white">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>🔄 Processing</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>✅ Completed</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
            </select>
        </div>
        <div>
            <select name="type" class="px-4 py-2 border border-gray-200 rounded-lg bg-white">
                <option value="">All Types</option>
                <option value="user" {{ request('type') == 'user' ? 'selected' : '' }}>👤 User Withdrawals</option>
                <option value="institution" {{ request('type') == 'institution' ? 'selected' : '' }}>🏢 Institution Withdrawals</option>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">🔍 Filter</button>
        </div>
        <div>
            <a href="{{ route('super-admin.payments.withdrawals') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition inline-block">Clear</a>
        </div>
    </form>
</div>

<!-- Withdrawals Table -->
@if($withdrawals->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requester</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($withdrawals as $wd)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $wd->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-6 py-4">
                        @if($wd->user_id)
                            <p class="font-medium text-gray-800">{{ $wd->user->full_name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">User</p>
                        @else
                            <p class="font-medium text-gray-800">{{ $wd->institution->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">Institution</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($wd->user_id)
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">👤 User</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">🏢 Institution</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-semibold text-red-600">TSh {{ number_format($wd->amount, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ $wd->payment_method }}</td>
                    <td class="px-6 py-4">
                        @if($wd->status === 'pending')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⏳ Pending</span>
                        @elseif($wd->status === 'processing')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">🔄 Processing</span>
                        @elseif($wd->status === 'completed')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✅ Completed</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">❌ Rejected</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('super-admin.withdrawals.show', $wd) }}" class="text-blue-600 hover:text-blue-800" title="View Details">
                                <i class="ti ti-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $withdrawals->appends(request()->query())->links() }}
</div>

@else
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="ti ti-arrow-up-circle text-6xl text-gray-400 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Withdrawal Requests</h3>
    <p class="text-gray-500">Withdrawal requests will appear here when users or institutions request payouts.</p>
</div>
@endif
@endsection