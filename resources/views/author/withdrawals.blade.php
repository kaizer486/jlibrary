@extends('layouts.author')

@section('title', 'My Withdrawals')
@section('page-title', 'My Withdrawals')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Withdrawals</h2>
        <a href="{{ route('author.withdrawals.create') }}" class="btn-author flex items-center gap-2">
            <i class="ti ti-plus"></i>
            Request Withdrawal
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Method</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($withdrawals as $withdrawal)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold text-gray-800">TSh {{ number_format($withdrawal->amount, 2) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ ucfirst($withdrawal->payment_method) }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2.5 py-1 rounded-full 
                                {{ $withdrawal->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 
                                   ($withdrawal->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($withdrawal->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $withdrawal->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                            <i class="ti ti-wallet-off text-4xl mb-2 block text-gray-300"></i>
                            <p>No withdrawals yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($withdrawals->hasPages())
        <div class="px-4 py-3 border-t">
            {{ $withdrawals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection