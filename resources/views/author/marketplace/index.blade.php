@extends('layouts.author')

@section('title', 'Marketplace Dashboard')
@section('page-title', 'Marketplace Dashboard')

@section('content')

<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Books</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalBooks }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="ti ti-books text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-2 flex gap-2 text-xs">
                <span class="text-green-600">Published: {{ $publishedBooks }}</span>
                <span class="text-gray-300">|</span>
                <span class="text-yellow-600">Pending: {{ $pendingBooks }}</span>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Sales</p>
                    <p class="text-2xl font-bold text-blue-600">TSh {{ number_format($totalSales, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="ti ti-shopping-cart text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Royalties (10%)</p>
                    <p class="text-2xl font-bold text-green-600">TSh {{ number_format($totalRoyalties, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="ti ti-coin text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Orders</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $recentOrders->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="ti ti-package text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('author.marketplace.listings') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="ti ti-list"></i> View All Listings
        </a>
        <a href="{{ route('author.books.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="ti ti-plus"></i> Upload New Book
        </a>
        <a href="{{ route('author.marketplace.orders') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="ti ti-package"></i> View Orders
        </a>
        <a href="{{ route('author.marketplace.earnings') }}" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="ti ti-wallet"></i> Earnings
        </a>
    </div>
    
    <!-- Recent Orders & Top Books -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">🛒 Recent Orders</h3>
                <a href="{{ route('author.marketplace.orders') }}" class="text-sm text-purple-600 hover:text-purple-700">View All</a>
            </div>
            
            @if($recentOrders->count() > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @foreach($recentOrders as $order)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div>
                                <p class="font-medium text-gray-800 text-sm">Order #{{ $order->id }}</p>
                                <p class="text-xs text-gray-500">By: {{ $order->user->full_name ?? 'Guest' }}</p>
                                <p class="text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-right">
                                <span class="font-semibold text-green-600">TSh {{ number_format($order->total, 2) }}</span>
                                <p class="text-xs text-gray-500">{{ ucfirst($order->status) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="ti ti-package-off text-3xl block mb-2"></i>
                    <p>No orders yet</p>
                </div>
            @endif
        </div>
        
        <!-- Top Books -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">📚 Top Selling Books</h3>
                <a href="{{ route('author.marketplace.listings') }}" class="text-sm text-purple-600 hover:text-purple-700">View All</a>
            </div>
            
            @if($topBooks->count() > 0)
                <div class="space-y-3">
                    @foreach($topBooks as $book)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex items-center gap-3">
                                @if($book->cover_image)
                                    <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-12 h-16 object-cover rounded">
                                @else
                                    <div class="w-12 h-16 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="ti ti-book text-gray-400"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-800 text-sm">{{ $book->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $book->sales_count ?? 0 }} sales</p>
                                </div>
                            </div>
                            <span class="font-semibold text-green-600">TSh {{ number_format($book->price, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="ti ti-book-off text-3xl block mb-2"></i>
                    <p>No books listed yet</p>
                    <a href="{{ route('author.books.create') }}" class="text-purple-600 text-sm hover:underline">Upload your first book</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection