@extends('layouts.app')

@section('title', 'Author Dashboard')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl p-6 text-white shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <i class="ti ti-edit text-3xl"></i>
                            <h1 class="text-2xl font-bold">Author Dashboard</h1>
                        </div>
                        <p class="text-purple-200">Welcome back, {{ $user->full_name }}!</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-purple-200">Author since</p>
                        <p class="font-semibold">{{ $approvedApp->created_at->format('M Y') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Info Card -->
        @if($approvedApp)
        <div class="bg-green-50 rounded-xl p-4 mb-6 border border-green-200">
            <div class="flex items-center gap-3">
                <i class="ti ti-check-circle text-green-600 text-xl"></i>
                <div>
                    <p class="text-sm font-medium text-green-800">✓ Approved Author</p>
                    <p class="text-xs text-green-600">
                        Approved by {{ $approvedApp->reviewer->full_name ?? 'Admin' }} 
                        on {{ $approvedApp->reviewed_at ? $approvedApp->reviewed_at->format('M d, Y') : 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Books</p>
                        <p class="text-3xl font-bold text-gray-800">{{ number_format($totalBooks) }}</p>
                    </div>
                    <i class="ti ti-books text-3xl text-blue-500"></i>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    {{ $publishedBooks }} published · {{ $pendingBooks }} pending
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Sales</p>
                        <p class="text-2xl font-bold text-green-600">TSh {{ number_format($totalSales, 2) }}</p>
                    </div>
                    <i class="ti ti-shopping-cart text-3xl text-green-500"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Royalties Earned (10%)</p>
                        <p class="text-2xl font-bold text-purple-600">TSh {{ number_format($totalRoyalties, 2) }}</p>
                    </div>
                    <i class="ti ti-wallet text-3xl text-purple-500"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Available Balance</p>
                        <p class="text-2xl font-bold text-yellow-600">TSh {{ number_format($availableBalance, 2) }}</p>
                    </div>
                    <i class="ti ti-cash text-3xl text-yellow-500"></i>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    Withdrawn: TSh {{ number_format($totalWithdrawn, 2) }}
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-2 gap-4 mb-8">
            <a href="{{ route('author.books.create') }}" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl p-4 text-center hover:shadow-lg transition">
                <i class="ti ti-plus text-2xl mb-1 block"></i>
                <span class="font-semibold">Upload New Book</span>
            </a>
            <a href="{{ route('author.withdrawals.create') }}" class="bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl p-4 text-center hover:shadow-lg transition">
                <i class="ti ti-arrow-up-circle text-2xl mb-1 block"></i>
                <span class="font-semibold">Request Withdrawal</span>
            </a>
        </div>

        <!-- Charts -->
        <div class="grid lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-chart-line text-purple-600"></i>
                    Monthly Earnings ({{ date('Y') }})
                </h2>
                <canvas id="earningsChart" height="250"></canvas>
            </div>
            
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-books text-purple-600"></i>
                    Top Performing Books
                </h2>
                <div class="space-y-3">
                    @forelse($topBooks as $book)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-800">{{ Str::limit($book->title, 40) }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($book->downloads ?? 0) }} downloads</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-green-600">TSh {{ number_format($book->price * 0.10, 2) }}</p>
                                <p class="text-xs text-gray-400">royalty per sale</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No books uploaded yet</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Sales -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="ti ti-receipt text-purple-600"></i>
                    Recent Sales
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Book</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buyer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Your Royalty (10%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentSales as $sale)
                            @php
                                $book = \App\Models\Book::find($sale->payable_id);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $sale->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-800">{{ $book->title ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $sale->user->full_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-blue-600">TSh {{ number_format($sale->amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-green-600">TSh {{ number_format($sale->amount * 0.10, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No sales yet</td>
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
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { responsive: true, maintainAspectRatio: true }
    });
</script>
@endpush
@endsection