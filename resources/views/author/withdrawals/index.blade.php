@extends('layouts.app')

@section('title', 'Withdrawal History')

@section('content')

<!-- ========================================== -->
<!-- LIGHT BISQUE BACKGROUND                    -->
<!-- ========================================== -->
<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div class="relative z-10 max-w-7xl mx-auto py-8">
    
    <!-- ========================================== -->
    <!-- HEADER CARD                                 -->
    <!-- ========================================== -->
    <div class="bg-gradient-to-r from-orange-600 to-amber-600 rounded-2xl p-6 mb-8 text-white shadow-lg border-2 border-orange-400/30">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <i class="ti ti-wallet text-3xl"></i>
                    <h1 class="text-2xl font-bold">Withdrawal History</h1>
                </div>
                <p class="text-orange-100">Track all your withdrawal requests</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('author.withdrawals.create') }}" class="bg-white text-orange-600 px-6 py-2.5 rounded-xl hover:shadow-lg transition font-semibold border-2 border-white/30">
                    <i class="ti ti-plus"></i> New Withdrawal
                </a>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- STATS CARDS                                -->
    <!-- ========================================== -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-5 shadow-md border-2 border-slate-200/80 hover:shadow-lg transition">
            <p class="text-slate-500 text-sm">Total Withdrawn</p>
            <p class="text-2xl font-bold text-orange-600">
                TSh {{ number_format($withdrawals->where('status', 'completed')->sum('amount'), 2) }}
            </p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-md border-2 border-slate-200/80 hover:shadow-lg transition">
            <p class="text-slate-500 text-sm">Pending</p>
            <p class="text-2xl font-bold text-amber-600">
                TSh {{ number_format($withdrawals->where('status', 'pending')->sum('amount'), 2) }}
            </p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-md border-2 border-slate-200/80 hover:shadow-lg transition">
            <p class="text-slate-500 text-sm">Completed</p>
            <p class="text-2xl font-bold text-emerald-600">
                {{ $withdrawals->where('status', 'completed')->count() }} requests
            </p>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- WITHDRAWALS TABLE                          -->
    <!-- ========================================== -->
    @if($withdrawals->count() > 0)
    <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 overflow-hidden">
        <div class="px-6 py-4 bg-orange-50/60 border-b-2 border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                <i class="ti ti-receipt text-orange-500"></i>
                Withdrawal History
            </h2>
        </div>
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-orange-50/40">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase border-b-2 border-slate-200">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase border-b-2 border-slate-200">Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase border-b-2 border-slate-200">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase border-b-2 border-slate-200">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase border-b-2 border-slate-200">Reference</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($withdrawals as $withdrawal)
                <tr class="hover:bg-orange-50/50 transition">
                    <td class="px-6 py-4 font-semibold text-slate-800">
                        TSh {{ number_format($withdrawal->amount, 2) }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="capitalize text-slate-600">{{ $withdrawal->method }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($withdrawal->status === 'pending')
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 border border-yellow-200">
                                ⏳ Pending
                            </span>
                        @elseif($withdrawal->status === 'completed')
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-200">
                                ✅ Completed
                            </span>
                        @elseif($withdrawal->status === 'cancelled')
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 border border-red-200">
                                ❌ Cancelled
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                {{ ucfirst($withdrawal->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        {{ $withdrawal->created_at->format('M d, Y h:i A') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500">
                        {{ $withdrawal->transaction_id ?? 'N/A' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $withdrawals->links() }}</div>
    
    @else
    <!-- ========================================== -->
    <!-- EMPTY STATE CARD                           -->
    <!-- ========================================== -->
    <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 p-16 text-center">
        <div class="w-24 h-24 bg-gradient-to-br from-orange-500 to-amber-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl shadow-orange-500/20">
            <i class="ti ti-wallet-off text-4xl text-white"></i>
        </div>
        <h3 class="text-xl font-semibold text-slate-800 mb-2">No Withdrawals Yet</h3>
        <p class="text-slate-500">You haven't made any withdrawal requests.</p>
        <a href="{{ route('author.withdrawals.create') }}" class="mt-4 inline-block text-orange-600 hover:text-orange-700 font-medium hover:underline">
            Request Your First Withdrawal →
        </a>
    </div>
    @endif
</div>
@endsection