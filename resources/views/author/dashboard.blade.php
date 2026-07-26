@extends('layouts.author')

@section('title', 'Author & Seller Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- ========================================== -->
    <!-- STATUS BANNER                              -->
    <!-- ========================================== -->
    @if($approvedApp)
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="ti ti-check text-white text-xl"></i>
                </div>
                <div>
                    <p class="font-semibold text-green-800">✅ Approved Author & Seller</p>
                    <p class="text-sm text-green-600">Publish books and sell products in the marketplace!</p>
                </div>
            </div>
            <span class="text-xs bg-green-200 text-green-800 px-3 py-1 rounded-full">
                Since {{ $approvedApp->created_at->format('M d, Y') }}
            </span>
        </div>
    @else
        <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border border-yellow-200 rounded-xl p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center">
                    <i class="ti ti-clock text-white text-xl"></i>
                </div>
                <div>
                    <p class="font-semibold text-yellow-800">⏳ Pending Approval</p>
                    <p class="text-sm text-yellow-600">Your application is being reviewed by administrators.</p>
                </div>
            </div>
            <a href="{{ route('applications.create', 'author') }}" class="text-sm text-yellow-700 hover:text-yellow-900 underline">
                Check Status
            </a>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- COMBINED STATS CARDS                       -->
    <!-- ========================================== -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Books -->
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">My Books</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalBooks }}</p>
                </div>
                <div class="stat-icon bg-purple-100 text-purple-600">
                    <i class="ti ti-books"></i>
                </div>
            </div>
            <div class="mt-2 flex gap-2 text-xs">
                <span class="text-green-600">Pub: {{ $publishedBooks }}</span>
                <span class="text-gray-300">|</span>
                <span class="text-yellow-600">Pend: {{ $pendingBooks }}</span>
            </div>
        </div>

        <!-- Total Listings -->
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Marketplace Listings</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalListings }}</p>
                </div>
                <div class="stat-icon bg-orange-100 text-orange-600">
                    <i class="ti ti-shopping-bag"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-orange-600">
                {{ $totalProductSales }} sales made
            </div>
        </div>

        <!-- Total Earnings (Combined) -->
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Earnings</p>
                    <p class="text-2xl font-bold text-emerald-600">TSh {{ number_format($totalEarnings, 2) }}</p>
                </div>
                <div class="stat-icon bg-emerald-100 text-emerald-600">
                    <i class="ti ti-coin"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                Books: TSh {{ number_format($bookRoyalties, 2) }} | Products: TSh {{ number_format($productEarnings, 2) }}
            </div>
        </div>

        <!-- Available Balance -->
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Available Balance</p>
                    <p class="text-2xl font-bold text-purple-600">TSh {{ number_format($availableBalance, 2) }}</p>
                </div>
                <div class="stat-icon bg-purple-100 text-purple-600">
                    <i class="ti ti-wallet"></i>
                </div>
            </div>
            @if($pendingWithdrawal > 0)
                <p class="text-xs text-yellow-600 mt-1">Pending: TSh {{ number_format($pendingWithdrawal, 2) }}</p>
            @endif
        </div>
    </div>

    <!-- ========================================== -->
    <!-- QUICK ACTIONS                              -->
    <!-- ========================================== -->
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('author.books.create') }}" class="btn-author flex items-center gap-2">
            <i class="ti ti-plus"></i>
            Upload Book
        </a>
        <a href="{{ route('marketplace.create') }}" class="btn-seller flex items-center gap-2">
            <i class="ti ti-shopping-bag"></i>
            Add Listing
        </a>
        <a href="{{ route('author.withdrawals.create') }}" class="btn-withdraw flex items-center gap-2">
            <i class="ti ti-wallet"></i>
            Withdraw
        </a>
    </div>

    <!-- ========================================== -->
    <!-- CHARTS & ANALYTICS                         -->
    <!-- ========================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Combined Monthly Earnings -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">📊 Monthly Earnings (Books + Products)</h3>
            <div class="h-64 flex items-end gap-2">
                @foreach($months as $index => $month)
                    <div class="flex-1 flex flex-col items-center gap-2">
                        @php
                            $height = $monthlyEarnings[$index] > 0 ? ($monthlyEarnings[$index] / max($monthlyEarnings) * 100) : 5;
                            $bookHeight = $bookMonthlyEarnings[$index] > 0 ? ($bookMonthlyEarnings[$index] / max($monthlyEarnings) * 100) : 0;
                            $productHeight = $productMonthlyEarnings[$index] > 0 ? ($productMonthlyEarnings[$index] / max($monthlyEarnings) * 100) : 0;
                        @endphp
                        <div class="w-full relative" style="height: {{ $height }}%; min-height: 5px;">
                            <!-- Book portion -->
                            <div class="absolute bottom-0 w-full bg-purple-200 rounded-t transition-all hover:bg-purple-300 relative group" style="height: {{ $bookHeight > 0 ? ($bookHeight / $height * 100) : 0 }}%;">
                            </div>
                            <!-- Product portion -->
                            <div class="absolute top-0 w-full bg-orange-200 rounded-t transition-all hover:bg-orange-300 relative group" style="height: {{ $productHeight > 0 ? ($productHeight / $height * 100) : 0 }}%;">
                            </div>
                            <!-- Tooltip -->
                            <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap z-10">
                                Books: TSh {{ number_format($bookMonthlyEarnings[$index], 0) }}<br>
                                Products: TSh {{ number_format($productMonthlyEarnings[$index], 0) }}
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">{{ $month }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center gap-4 mt-4 justify-center">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-purple-200 rounded"></div>
                    <span class="text-xs text-gray-500">Book Royalties</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-orange-200 rounded"></div>
                    <span class="text-xs text-gray-500">Product Sales</span>
                </div>
            </div>
        </div>

        <!-- Top Books -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">📚 Top Books</h3>
            @if($topBooks->count() > 0)
                <div class="space-y-3">
                    @foreach($topBooks as $book)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-800 text-sm truncate">{{ $book->title }}</p>
                                <p class="text-xs text-gray-500">{{ $book->downloads ?? 0 }} downloads</p>
                            </div>
                            <span class="text-xs text-green-600 font-semibold">TSh {{ number_format($book->price, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 text-gray-500">
                    <i class="ti ti-book-off text-2xl block mb-2"></i>
                    <p class="text-sm">No books uploaded yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- ========================================== -->
    <!-- RECENT ACTIVITY (Two Columns)              -->
    <!-- ========================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Recent Book Sales -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">📖 Recent Book Sales</h3>
                <span class="text-xs text-gray-500">Library purchases</span>
            </div>
            @if($recentBookSales->count() > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @foreach($recentBookSales as $sale)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-800 text-sm">{{ $sale->payable->title ?? 'Unknown Book' }}</p>
                                <p class="text-xs text-gray-500">Buyer: {{ $sale->user->full_name ?? 'Guest' }}</p>
                                <p class="text-xs text-gray-400">{{ $sale->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="font-semibold text-green-600">TSh {{ number_format($sale->amount, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="ti ti-shopping-cart-off text-3xl block mb-2"></i>
                    <p>No book sales yet</p>
                </div>
            @endif
        </div>

        <!-- Recent Product Sales -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">🛒 Recent Product Sales</h3>
                <span class="text-xs text-gray-500">Marketplace orders</span>
            </div>
            @if($recentProductSales->count() > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @foreach($recentProductSales as $sale)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-800 text-sm">{{ $sale->listing->title ?? 'Unknown Product' }}</p>
                                <p class="text-xs text-gray-500">Buyer: {{ $sale->buyer->full_name ?? 'Guest' }}</p>
                                <p class="text-xs text-gray-400">{{ $sale->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="font-semibold text-orange-600">TSh {{ number_format($sale->seller_earnings ?? $sale->amount, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="ti ti-shopping-bag-off text-3xl block mb-2"></i>
                    <p>No product sales yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- ========================================== -->
    <!-- YOUR MARKETPLACE LISTINGS                  -->
    <!-- ========================================== -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="ti ti-shopping-bag text-orange-500"></i>
                Your Marketplace Listings
            </h3>
            <a href="{{ route('seller.listings') }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium">Manage All →</a>
        </div>
        <div class="grid md:grid-cols-3 gap-4">
            @forelse($listings as $listing)
            <div class="border border-gray-200 rounded-xl p-4 hover:shadow-lg hover:border-orange-300 transition bg-white">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="ti ti-book text-orange-600 text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-800 truncate">{{ $listing->title }}</h4>
                        <p class="text-xs text-gray-500">TSh {{ number_format($listing->price, 2) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs px-2.5 py-1 rounded-full 
                        {{ $listing->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ ucfirst($listing->status) }}
                    </span>
                    <span class="text-xs text-gray-500">{{ $listing->orders_count ?? 0 }} sales</span>
                    <a href="{{ route('marketplace.edit', $listing) }}" class="ml-auto text-orange-600 hover:text-orange-700 text-sm font-medium">Edit</a>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-8 text-gray-500">
                <i class="ti ti-package-off text-4xl mb-2 block text-orange-300"></i>
                <p>No listings yet. <a href="{{ route('marketplace.create') }}" class="text-orange-600 hover:underline">Create your first one!</a></p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- ========================================== -->
    <!-- WITHDRAWAL ALERTS                          -->
    <!-- ========================================== -->
    @if($pendingWithdrawal > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center">
                    <i class="ti ti-clock text-white text-xl"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-yellow-800">Pending Withdrawal</p>
                    <p class="text-sm text-yellow-700">
                        You have a pending request of <strong>TSh {{ number_format($pendingWithdrawal, 2) }}</strong>.
                    </p>
                </div>
                <a href="{{ route('author.withdrawals.index') }}" class="text-sm text-yellow-700 hover:text-yellow-900 underline">
                    View Status →
                </a>
            </div>
        </div>
    @endif

    @if($totalWithdrawn > 0)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                    <i class="ti ti-wallet text-white text-xl"></i>
                </div>
                <div>
                    <p class="font-semibold text-blue-800">Total Withdrawn</p>
                    <p class="text-sm text-blue-700">
                        You have withdrawn a total of <strong>TSh {{ number_format($totalWithdrawn, 2) }}</strong>.
                    </p>
                </div>
            </div>
        </div>
    @endif

</div>

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.1);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .btn-author {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        color: white;
        padding: 10px 24px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-author:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(124, 58, 237, 0.35);
        color: white;
    }
    .btn-seller {
        background: linear-gradient(135deg, #ea580c, #c2410c);
        color: white;
        padding: 10px 24px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-seller:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(234, 88, 12, 0.35);
        color: white;
    }
    .btn-withdraw {
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
        padding: 10px 24px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-withdraw:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(5, 150, 105, 0.35);
        color: white;
    }
</style>
@endpush
@endsection