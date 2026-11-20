@extends('layouts.super-admin')

@section('title', 'Commissions')

@section('content')
<div class="space-y-6">

    <!-- ========================================== -->
    <!-- HEADER                                     -->
    <!-- ========================================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="ti ti-percentage text-purple-600"></i>
                Commission Management
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Track platform fees and author earnings from sales
            </p>
        </div>
        <a href="{{ route('super-admin.payments.index') }}"
           class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition text-sm">
            <i class="ti ti-arrow-left"></i> Back to Payments
        </a>
    </div>

    <!-- ========================================== -->
    <!-- STATS CARDS                                -->
    <!-- ========================================== -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-emerald-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Author Earnings</p>
                    <p class="text-2xl font-bold text-emerald-700 mt-1">
                        TSh {{ number_format($totalAuthorEarnings ?? 0, 0) }}
                    </p>
                    <p class="text-[10px] text-gray-400 mt-1">Total paid out to authors</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-users text-emerald-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Platform Fees</p>
                    <p class="text-2xl font-bold text-purple-700 mt-1">
                        TSh {{ number_format($totalPlatformFees ?? 0, 0) }}
                    </p>
                    <p class="text-[10px] text-gray-400 mt-1">Commission earned by platform</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-building-bank text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Transactions</p>
                    <p class="text-2xl font-bold text-blue-700 mt-1">
                        {{ number_format($totalTransactions ?? 0) }}
                    </p>
                    <p class="text-[10px] text-gray-400 mt-1">Commissioned sales recorded</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-receipt text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TOP AUTHORS                                -->
    <!-- ========================================== -->
    @if(isset($topAuthors) && $topAuthors->count() > 0)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="ti ti-trophy text-yellow-500"></i>
                    Top Earning Authors
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Authors ranked by total earnings</p>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($topAuthors as $i => $author)
                    <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0
                                    {{ $i === 0 ? 'bg-yellow-100 text-yellow-700' : 
                                       ($i === 1 ? 'bg-slate-200 text-slate-700' : 
                                       ($i === 2 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-500')) }}">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm text-gray-800 truncate">
                                {{ $author->author->full_name ?? $author->author->name ?? 'Unknown Author' }}
                            </p>
                            @if($author->author && $author->author->email)
                                <p class="text-xs text-gray-500 truncate">{{ $author->author->email }}</p>
                            @endif
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-bold text-emerald-700 text-sm">
                                TSh {{ number_format($author->total_earnings ?? 0, 0) }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- COMMISSION LOGS TABLE                      -->
    <!-- ========================================== -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="ti ti-list text-purple-600"></i>
                Commission Logs
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">All platform commission transactions</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Seq</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Author</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Sale Amount</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Author Earnings</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Platform Fee</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Payout</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($commissions as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-xs font-bold text-gray-400">
                                {{ $commissions->firstItem() + $loop->index }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $log->created_at->format('M d, Y') }}
                                <p class="text-[10px] text-gray-400">{{ $log->created_at->format('H:i') }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <p class="font-medium text-gray-800">
                                    {{ $log->author->full_name ?? $log->author->name ?? 'Unknown' }}
                                </p>
                                @if($log->author && $log->author->email)
                                    <p class="text-xs text-gray-500 truncate">{{ $log->author->email }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $log->currency ?? 'TSh' }} {{ number_format($log->total_amount ?? 0, 0) }}
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-emerald-700">
                                {{ $log->currency ?? 'TSh' }} {{ number_format($log->author_earnings ?? 0, 0) }}
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-purple-700">
                                {{ $log->currency ?? 'TSh' }} {{ number_format($log->platform_fee ?? 0, 0) }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($log->status === 'completed' && $log->payout_date)
                                    <span class="text-[10px] text-emerald-600 font-medium">
                                        <i class="ti ti-check"></i> Paid {{ \Carbon\Carbon::parse($log->payout_date)->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-[10px] text-gray-400">Not yet paid</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'completed' => 'bg-emerald-100 text-emerald-700',
                                        'pending'   => 'bg-yellow-100 text-yellow-700',
                                        'failed'    => 'bg-red-100 text-red-700',
                                        'refunded'  => 'bg-gray-100 text-gray-700',
                                    ];
                                @endphp
                                <span class="text-xs px-2.5 py-1 rounded-full font-medium
                                             {{ $statusColors[$log->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($log->status ?? 'unknown') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                                <i class="ti ti-receipt-off text-4xl text-gray-300 mb-3 block"></i>
                                <p class="font-medium">No commission logs yet</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Commission logs are created automatically when book sales complete
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($commissions->hasPages())
            <div class="px-4 py-3 border-t">
                {{ $commissions->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection