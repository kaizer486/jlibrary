@extends('layouts.app')

@section('title', 'Seller Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl p-6 mb-8 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <i class="ti ti-shopping-cart text-3xl"></i>
                    <h1 class="text-2xl font-bold">Seller Dashboard</h1>
                </div>
                <p class="text-purple-100">Manage your books and track sales</p>
                <div class="mt-2 flex items-center gap-3">
                    <span class="bg-white/20 px-3 py-1 rounded-full text-sm">
                        {{ auth()->user()->getSellerType() ?? 'Seller' }}
                    </span>
                    <span class="bg-white/20 px-3 py-1 rounded-full text-sm">
                        <i class="ti ti-currency-dollar"></i> TSh {{ number_format($totalEarnings, 2) }} earned
                    </span>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex gap-3">
                <a href="{{ route('marketplace.create') }}" class="bg-white text-purple-600 px-6 py-2.5 rounded-lg hover:shadow-lg transition font-semibold">
                    <i class="ti ti-plus"></i> Add New Book
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Listings</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_listings'] }}</p>
                </div>
                <i class="ti ti-package text-3xl text-purple-500"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Sales</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_sales'] }}</p>
                </div>
                <i class="ti ti-shopping-cart text-3xl text-green-500"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Earnings</p>
                    <p class="text-2xl font-bold text-yellow-600">TSh {{ number_format($stats['total_earnings'], 2) }}</p>
                </div>
                <i class="ti ti-wallet text-3xl text-yellow-500"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pending Orders</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['pending_orders'] }}</p>
                </div>
                <i class="ti ti-clock text-3xl text-blue-500"></i>
            </div>
        </div>
    </div>

    <!-- Recent Sales -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Recent Sales</h2>
            <a href="{{ route('seller.orders') }}" class="text-sm text-purple-600 hover:underline">View All →</a>
        </div>
        @if($recentSales->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 text-xs font-semibold text-gray-500">Book</th>
                            <th class="text-left py-2 text-xs font-semibold text-gray-500">Buyer</th>
                            <th class="text-left py-2 text-xs font-semibold text-gray-500">Amount</th>
                            <th class="text-left py-2 text-xs font-semibold text-gray-500">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSales as $sale)
                        <tr class="border-b last:border-0">
                            <td class="py-2 font-medium text-gray-800">{{ $sale->listing->title ?? 'N/A' }}</td>
                            <td class="py-2 text-gray-600">{{ $sale->buyer->full_name ?? 'N/A' }}</td>
                            <td class="py-2 font-semibold text-green-600">TSh {{ number_format($sale->amount, 2) }}</td>
                            <td class="py-2 text-gray-500 text-sm">{{ $sale->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="ti ti-shopping-cart-off text-4xl mb-2 block"></i>
                <p>No sales yet. Start promoting your books!</p>
            </div>
        @endif
    </div>

    <!-- Your Listings -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Your Listings</h2>
            <a href="{{ route('seller.listings') }}" class="text-sm text-purple-600 hover:underline">Manage All →</a>
        </div>
        <div class="grid md:grid-cols-3 gap-4">
            @forelse($listings->take(6) as $listing)
            <div class="border rounded-lg p-4 hover:shadow-lg transition">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="ti ti-book text-purple-600 text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-800 truncate">{{ $listing->title }}</h4>
                        <p class="text-xs text-gray-500">TSh {{ number_format($listing->price, 2) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs px-2 py-1 rounded-full 
                        {{ $listing->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ ucfirst($listing->status) }}
                    </span>
                    <span class="text-xs text-gray-500">{{ $listing->sales_count }} sales</span>
                    <a href="{{ route('marketplace.edit', $listing) }}" class="ml-auto text-purple-600 text-sm hover:underline">Edit</a>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-8 text-gray-500">
                <i class="ti ti-package-off text-4xl mb-2 block"></i>
                <p>No listings yet. Create your first one!</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection