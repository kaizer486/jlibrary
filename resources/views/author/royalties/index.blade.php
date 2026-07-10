@extends('layouts.app')

@section('title', 'My Royalties')

@section('content')

<!-- ========================================== -->
<!-- LIGHT BISQUE BACKGROUND                    -->
<!-- ========================================== -->
<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-5xl">
        
        <!-- ========================================== -->
        <!-- HEADER                                     -->
        <!-- ========================================== -->
        <div class="mb-6">
            <a href="{{ route('author.dashboard') }}" class="text-slate-600 hover:text-slate-800 flex items-center gap-2 mb-4">
                <i class="ti ti-arrow-left"></i> Back to Dashboard
            </a>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <span class="bg-gradient-to-br from-orange-500 to-amber-500 p-2 rounded-xl shadow-lg shadow-orange-500/20">
                    <i class="ti ti-wallet text-white text-xl"></i>
                </span>
                My Royalties
            </h1>
            <p class="text-slate-600 text-sm mt-1">Track your earnings from book sales</p>
        </div>

        <!-- ========================================== -->
        <!-- STATS CARDS                                -->
        <!-- ========================================== -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-5 shadow-md border-2 border-slate-200/80 hover:shadow-lg transition">
                <p class="text-slate-500 text-sm">Total Royalties</p>
                <p class="text-2xl font-bold text-orange-600">TSh {{ number_format($totalRoyalties ?? 0, 2) }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-md border-2 border-slate-200/80 hover:shadow-lg transition">
                <p class="text-slate-500 text-sm">Paid Out</p>
                <p class="text-2xl font-bold text-emerald-600">TSh {{ number_format($totalWithdrawn ?? 0, 2) }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-md border-2 border-slate-200/80 hover:shadow-lg transition">
                <p class="text-slate-500 text-sm">Available Balance</p>
                <p class="text-2xl font-bold text-amber-600">TSh {{ number_format($available ?? 0, 2) }}</p>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- ROYALTIES TABLE                            -->
        <!-- ========================================== -->
        @if($royalties->count() > 0)
        <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 overflow-hidden">
            <div class="px-6 py-4 bg-orange-50/60 border-b-2 border-slate-200">
                <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                    <i class="ti ti-coin text-orange-500"></i>
                    Royalty History
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-orange-50/40">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase border-b-2 border-slate-200">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase border-b-2 border-slate-200">Book</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase border-b-2 border-slate-200">Buyer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase border-b-2 border-slate-200">Sale Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase border-b-2 border-slate-200">Your Royalty (10%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($royalties as $royalty)
                            @php
                                $book = \App\Models\Book::find($royalty->payable_id);
                            @endphp
                            <tr class="hover:bg-orange-50/50 transition">
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $royalty->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-800">{{ $book->title ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $royalty->user->full_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-blue-600">TSh {{ number_format($royalty->amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-emerald-600">TSh {{ number_format($royalty->amount * 0.10, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-6">
            {{ $royalties->links() }}
        </div>
        
        @else
        <!-- ========================================== -->
        <!-- EMPTY STATE CARD                           -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 p-16 text-center">
            <div class="w-24 h-24 bg-gradient-to-br from-orange-500 to-amber-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl shadow-orange-500/20">
                <i class="ti ti-wallet text-4xl text-white"></i>
            </div>
            <h3 class="text-xl font-semibold text-slate-800 mb-2">No Royalties Yet</h3>
            <p class="text-slate-500">Royalties will appear here when your books are purchased.</p>
        </div>
        @endif

    </div>
</div>
@endsection