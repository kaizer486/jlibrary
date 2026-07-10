@extends('layouts.app')

@section('title', 'Author Dashboard')

@section('content')

<!-- ========================================== -->
<!-- LIGHT BISQUE BACKGROUND                    -->
<!-- ========================================== -->
<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <!-- ========================================== -->
        <!-- HEADER CARD                                 -->
        <!-- ========================================== -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-orange-600 to-amber-600 rounded-2xl p-6 text-white shadow-lg border-2 border-orange-400/30">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <i class="ti ti-edit text-3xl"></i>
                            <h1 class="text-2xl font-bold">Author Dashboard</h1>
                        </div>
                        <p class="text-orange-100">Welcome back, {{ $user->full_name }}!</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-orange-100">Author since</p>
                        <p class="font-semibold">{{ $approvedApp->created_at->format('M Y') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- APPROVAL INFO CARD                         -->
        <!-- ========================================== -->
        @if($approvedApp)
        <div class="bg-emerald-50 rounded-xl p-4 mb-6 border-2 border-emerald-200/80 shadow-sm">
            <div class="flex items-center gap-3">
                <i class="ti ti-check-circle text-emerald-600 text-xl"></i>
                <div>
                    <p class="text-sm font-medium text-emerald-800">✓ Approved Author</p>
                    <p class="text-xs text-emerald-600">
                        Approved by {{ $approvedApp->reviewer->full_name ?? 'Admin' }} 
                        on {{ $approvedApp->reviewed_at ? $approvedApp->reviewed_at->format('M d, Y') : 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- ========================================== -->
        <!-- STATS CARDS                                -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-5 shadow-md border-2 border-slate-200/80 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm">Total Books</p>
                        <p class="text-3xl font-bold text-slate-800">{{ number_format($totalBooks) }}</p>
                    </div>
                    <i class="ti ti-books text-3xl text-orange-500"></i>
                </div>
                <div class="mt-2 text-xs text-slate-500">
                    {{ $publishedBooks }} published · {{ $pendingBooks }} pending
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-5 shadow-md border-2 border-slate-200/80 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm">Total Sales</p>
                        <p class="text-2xl font-bold text-emerald-600">TSh {{ number_format($totalSales, 2) }}</p>
                    </div>
                    <i class="ti ti-shopping-cart text-3xl text-emerald-500"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-5 shadow-md border-2 border-slate-200/80 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm">Royalties Earned (10%)</p>
                        <p class="text-2xl font-bold text-orange-600">TSh {{ number_format($totalRoyalties, 2) }}</p>
                    </div>
                    <i class="ti ti-wallet text-3xl text-orange-500"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-5 shadow-md border-2 border-slate-200/80 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm">Available Balance</p>
                        <p class="text-2xl font-bold text-amber-600">TSh {{ number_format($availableBalance, 2) }}</p>
                    </div>
                    <i class="ti ti-cash text-3xl text-amber-500"></i>
                </div>
                <div class="mt-2 text-xs text-slate-500">
                    Withdrawn: TSh {{ number_format($totalWithdrawn, 2) }}
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- QUICK ACTIONS                              -->
        <!-- ========================================== -->
        <div class="grid md:grid-cols-2 gap-4 mb-8">
            <a href="{{ route('author.books.create') }}" class="bg-gradient-to-r from-orange-600 to-amber-600 text-white rounded-xl p-4 text-center hover:shadow-lg hover:shadow-orange-600/25 transition border-2 border-orange-400/30">
                <i class="ti ti-plus text-2xl mb-1 block"></i>
                <span class="font-semibold">Upload New Book</span>
            </a>
            <a href="{{ route('author.withdrawals.create') }}" class="bg-gradient-to-r from-amber-600 to-yellow-600 text-white rounded-xl p-4 text-center hover:shadow-lg hover:shadow-amber-600/25 transition border-2 border-amber-400/30">
                <i class="ti ti-arrow-up-circle text-2xl mb-1 block"></i>
                <span class="font-semibold">Request Withdrawal</span>
            </a>
        </div>

        <!-- ========================================== -->
        <!-- CHARTS SECTION                             -->
        <!-- ========================================== -->
        <div class="grid lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 p-6">
                <h2 class="text-lg font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-chart-line text-orange-500"></i>
                    Monthly Earnings ({{ date('Y') }})
                </h2>
                <canvas id="earningsChart" height="250"></canvas>
            </div>
            
            <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 p-6">
                <h2 class="text-lg font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-books text-orange-500"></i>
                    Top Performing Books
                </h2>
                <div class="space-y-3">
                    @forelse($topBooks as $book)
                        <div class="flex items-center justify-between p-3 bg-orange-50/60 rounded-lg border-2 border-orange-100/60">
                            <div>
                                <p class="font-medium text-slate-800">{{ Str::limit($book->title, 40) }}</p>
                                <p class="text-xs text-slate-500">{{ number_format($book->downloads ?? 0) }} downloads</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-emerald-600">TSh {{ number_format($book->price * 0.10, 2) }}</p>
                                <p class="text-xs text-slate-400">royalty per sale</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500 text-center py-4">No books uploaded yet</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- RECENT SALES                               -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 overflow-hidden">
            <div class="px-6 py-4 bg-orange-50/60 border-b-2 border-slate-200">
                <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                    <i class="ti ti-receipt text-orange-500"></i>
                    Recent Sales
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-orange-50/40">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase border-b-2 border-slate-200">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase border-b-2 border-slate-200">Book</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase border-b-2 border-slate-200">Buyer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase border-b-2 border-slate-200">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase border-b-2 border-slate-200">Your Royalty (10%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($recentSales as $sale)
                            @php
                                $book = \App\Models\Book::find($sale->payable_id);
                            @endphp
                            <tr class="hover:bg-orange-50/50 transition">
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $sale->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-800">{{ $book->title ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $sale->user->full_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-blue-600">TSh {{ number_format($sale->amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-emerald-600">TSh {{ number_format($sale->amount * 0.10, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">No sales yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- ROYALTIES HISTORY                          -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 overflow-hidden mt-8">
            <div class="px-6 py-4 bg-orange-50/60 border-b-2 border-slate-200">
                <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                    <i class="ti ti-coin text-orange-500"></i>
                    Royalties History
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase border-b-2 border-slate-200">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($royalties as $royalty)
                            @php
                                $book = \App\Models\Book::find($royalty->payable_id);
                            @endphp
                            <tr class="hover:bg-orange-50/50 transition">
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $royalty->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-800">{{ $book->title ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $royalty->user->full_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-blue-600">TSh {{ number_format($royalty->amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-emerald-600">TSh {{ number_format($royalty->amount * 0.10, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        ✅ Completed
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500">No royalties yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const earningsCtx = document.getElementById('earningsChart').getContext('2d');
    new Chart(earningsCtx, {
        type: 'line',
        data: {
            labels: {{ json_encode($months) }},
            datasets: [{
                label: 'Royalties (TSh)',
                data: {{ json_encode($monthlyEarnings) }},
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    labels: {
                        color: '#1e293b'
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        color: '#64748b'
                    }
                },
                x: {
                    ticks: {
                        color: '#64748b'
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection