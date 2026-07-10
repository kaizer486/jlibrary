@extends('layouts.app')

@section('title', 'Seller Dashboard')

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
                    <i class="ti ti-shopping-cart text-3xl"></i>
                    <h1 class="text-2xl font-bold">Seller Dashboard</h1>
                </div>
                <p class="text-orange-100">Manage your books and track sales</p>
                <div class="mt-2 flex flex-wrap items-center gap-3">
                    <span class="bg-white/20 px-3 py-1 rounded-full text-sm border border-white/20">
                        {{ auth()->user()->getSellerType() ?? 'Seller' }}
                    </span>
                    <span class="bg-white/20 px-3 py-1 rounded-full text-sm border border-white/20">
                        <i class="ti ti-currency-dollar"></i> TSh {{ number_format($totalEarnings, 2) }} earned
                    </span>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex gap-3">
                <a href="{{ route('marketplace.create') }}" class="bg-white text-orange-600 px-6 py-2.5 rounded-xl hover:shadow-lg transition font-semibold border-2 border-white/30">
                    <i class="ti ti-plus"></i> Add New Book
                </a>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- STATS CARDS                                -->
    <!-- ========================================== -->
    <div class="grid md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-5 shadow-md border-2 border-slate-200/80 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm">Total Listings</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total_listings'] }}</p>
                </div>
                <i class="ti ti-package text-3xl text-orange-500"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-5 shadow-md border-2 border-slate-200/80 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm">Total Sales</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total_sales'] }}</p>
                </div>
                <i class="ti ti-shopping-cart text-3xl text-emerald-500"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-5 shadow-md border-2 border-slate-200/80 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm">Total Earnings</p>
                    <p class="text-2xl font-bold text-amber-600">TSh {{ number_format($stats['total_earnings'], 2) }}</p>
                </div>
                <i class="ti ti-wallet text-3xl text-amber-500"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-5 shadow-md border-2 border-slate-200/80 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm">Pending Orders</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['pending_orders'] }}</p>
                </div>
                <i class="ti ti-clock text-3xl text-blue-500"></i>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- RECENT SALES CARD                          -->
    <!-- ========================================== -->
    <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                <i class="ti ti-receipt text-orange-500"></i>
                Recent Sales
            </h2>
            <a href="{{ route('seller.orders') }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium">View All →</a>
        </div>
        @if($recentSales->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-slate-200">
                            <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase">Book</th>
                            <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase">Buyer</th>
                            <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase">Amount</th>
                            <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSales as $sale)
                        <tr class="border-b border-slate-200 last:border-0 hover:bg-orange-50/50 transition">
                            <td class="py-2 font-medium text-slate-800">{{ $sale->listing->title ?? 'N/A' }}</td>
                            <td class="py-2 text-slate-600">{{ $sale->buyer->full_name ?? 'N/A' }}</td>
                            <td class="py-2 font-semibold text-emerald-600">TSh {{ number_format($sale->amount, 2) }}</td>
                            <td class="py-2 text-slate-500 text-sm">{{ $sale->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-slate-500">
                <i class="ti ti-shopping-cart-off text-4xl mb-2 block text-orange-400/30"></i>
                <p>No sales yet. Start promoting your books!</p>
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- YOUR LISTINGS CARD                         -->
    <!-- ========================================== -->
    <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                <i class="ti ti-package text-orange-500"></i>
                Your Listings
            </h2>
            <a href="{{ route('seller.listings') }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium">Manage All →</a>
        </div>
        <div class="grid md:grid-cols-3 gap-4">
            @forelse($listings->take(6) as $listing)
            <div class="border-2 border-slate-200/80 rounded-xl p-4 hover:shadow-lg hover:border-orange-300/60 transition bg-white">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center border-2 border-orange-200/60">
                        <i class="ti ti-book text-orange-600 text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-slate-800 truncate">{{ $listing->title }}</h4>
                        <p class="text-xs text-slate-500">TSh {{ number_format($listing->price, 2) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs px-2.5 py-1 rounded-full border 
                        {{ $listing->status === 'approved' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-yellow-100 text-yellow-700 border-yellow-200' }}">
                        {{ ucfirst($listing->status) }}
                    </span>
                    <span class="text-xs text-slate-500">{{ $listing->sales_count }} sales</span>
                    <a href="{{ route('marketplace.edit', $listing) }}" class="ml-auto text-orange-600 hover:text-orange-700 text-sm font-medium hover:underline">Edit</a>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-8 text-slate-500">
                <i class="ti ti-package-off text-4xl mb-2 block text-orange-400/30"></i>
                <p>No listings yet. Create your first one!</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection