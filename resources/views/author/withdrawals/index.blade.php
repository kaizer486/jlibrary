@extends('layouts.app')

@section('title', 'Withdrawal History')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl p-6 mb-8 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <i class="ti ti-wallet text-3xl"></i>
                    <h1 class="text-2xl font-bold">Withdrawal History</h1>
                </div>
                <p class="text-purple-100">Track all your withdrawal requests</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('author.withdrawals.create') }}" class="bg-white text-purple-600 px-6 py-2.5 rounded-lg hover:shadow-lg transition font-semibold">
                    <i class="ti ti-plus"></i> New Withdrawal
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-purple-500">
            <p class="text-gray-500 text-sm">Total Withdrawn</p>
            <p class="text-2xl font-bold text-gray-800">
                TSh {{ number_format($withdrawals->where('status', 'completed')->sum('amount'), 2) }}
            </p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-yellow-500">
            <p class="text-gray-500 text-sm">Pending</p>
            <p class="text-2xl font-bold text-yellow-600">
                TSh {{ number_format($withdrawals->where('status', 'pending')->sum('amount'), 2) }}
            </p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-green-500">
            <p class="text-gray-500 text-sm">Completed</p>
            <p class="text-2xl font-bold text-green-600">
                {{ $withdrawals->where('status', 'completed')->count() }} requests
            </p>
        </div>
    </div>

    <!-- Withdrawals Table -->
    @if($withdrawals->count() > 0)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($withdrawals as $withdrawal)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-semibold text-gray-800">
                        TSh {{ number_format($withdrawal->amount, 2) }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="capitalize">{{ $withdrawal->method }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($withdrawal->status === 'pending')
                            <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">
                                ⏳ Pending
                            </span>
                        @elseif($withdrawal->status === 'completed')
                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                ✅ Completed
                            </span>
                        @elseif($withdrawal->status === 'cancelled')
                            <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">
                                ❌ Cancelled
                            </span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                                {{ ucfirst($withdrawal->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $withdrawal->created_at->format('M d, Y h:i A') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $withdrawal->transaction_id ?? 'N/A' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $withdrawals->links() }}</div>
    @else
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <i class="ti ti-wallet-off text-6xl text-gray-400 mb-4 block"></i>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Withdrawals Yet</h3>
        <p class="text-gray-500">You haven't made any withdrawal requests.</p>
        <a href="{{ route('author.withdrawals.create') }}" class="mt-4 inline-block text-purple-600 hover:underline">
            Request Your First Withdrawal →
        </a>
    </div>
    @endif
</div>
@endsection