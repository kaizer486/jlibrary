@extends('layouts.admin')

@section('title', 'Withdrawals')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">💰 Withdrawals</h1>
            <p class="text-gray-500 text-sm mt-1">Manage your institution's earnings and withdrawals</p>
        </div>
        <a href="{{ route('institution.withdrawals.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
            <i class="ti ti-plus"></i> Request Withdrawal
        </a>
    </div>
</div>

<!-- Wallet Summary -->
<div class="grid md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border-l-4 border-purple-500 shadow-sm">
        <p class="text-gray-500 text-sm">Available Balance</p>
        <p class="text-2xl font-bold text-purple-600">TSh {{ number_format($wallet->balance ?? 0, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-yellow-500 shadow-sm">
        <p class="text-gray-500 text-sm">Pending Withdrawal</p>
        <p class="text-2xl font-bold text-yellow-600">TSh {{ number_format($wallet->pending_withdrawal ?? 0, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-green-500 shadow-sm">
        <p class="text-gray-500 text-sm">Total Earned</p>
        <p class="text-2xl font-bold text-green-600">TSh {{ number_format($wallet->total_earned ?? 0, 2) }}</p>
    </div>
</div>

<!-- Info Box -->
<div class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4 mb-6">
    <div class="flex items-center gap-3">
        <i class="ti ti-info-circle text-blue-500 text-xl"></i>
        <div>
            <p class="text-sm text-blue-800">Minimum withdrawal amount is <strong>TSh {{ number_format($minWithdrawal, 2) }}</strong></p>
            <p class="text-xs text-blue-600 mt-1">Withdrawals are processed within 2-3 business days</p>
        </div>
    </div>
</div>

<!-- Withdrawals Table -->
@if($withdrawals->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($withdrawals as $withdrawal)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-600">{{ $withdrawal->created_at->format('M d, Y') }}</td>
                <td class="px-6 py-4 text-sm font-semibold text-gray-800">TSh {{ number_format($withdrawal->amount, 2) }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ strtoupper($withdrawal->payment_method) }}</td>
                <td class="px-6 py-4">
                    @if($withdrawal->status === 'pending')
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⏳ Pending</span>
                    @elseif($withdrawal->status === 'processing')
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">🔄 Processing</span>
                    @elseif($withdrawal->status === 'completed')
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✅ Completed</span>
                    @elseif($withdrawal->status === 'rejected')
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">❌ Rejected</span>
                    @else
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Cancelled</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm">
                    <a href="{{ route('institution.withdrawals.show', $withdrawal) }}" class="text-purple-600 hover:text-purple-800">View</a>
                    @if($withdrawal->status === 'pending')
                        <form method="POST" action="{{ route('institution.withdrawals.cancel', $withdrawal) }}" class="inline ml-2">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Cancel this withdrawal request?')">Cancel</button>
                        </form>
                    @endif
                </td>
            </td>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $withdrawals->links() }}</div>
@else
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="ti ti-wallet text-6xl text-gray-400 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Withdrawals Yet</h3>
    <p class="text-gray-500">Click "Request Withdrawal" to withdraw your earnings.</p>
</div>
@endif
@endsection