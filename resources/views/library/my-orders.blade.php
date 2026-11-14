@extends('layouts.app')

@section('title', 'My Orders')
@section('content')

<div class="container mx-auto px-4 py-8" x-data="{ tab: '{{ request('tab', 'hardcopy') }}' }">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="ti ti-truck-delivery text-orange-600"></i>
                My Orders
            </h1>
            <p class="text-gray-600 text-sm mt-1">Track your hardcopy orders and view your softcopy purchases</p>
        </div>
        <a href="{{ route('library.index') }}"
           class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-xl hover:bg-gray-50 transition text-sm">
            <i class="ti ti-books"></i> Browse Library
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
            <div class="w-11 h-11 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center">
                <i class="ti ti-package text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Hardcopy Orders</p>
                <p class="text-xl font-bold text-gray-800">{{ $hardcopyCount }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
            <div class="w-11 h-11 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center">
                <i class="ti ti-clock text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Pending</p>
                <p class="text-xl font-bold text-gray-800">{{ $pendingCount }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
            <div class="w-11 h-11 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                <i class="ti ti-device-mobile text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Softcopy Purchases</p>
                <p class="text-xl font-bold text-gray-800">{{ $softcopyCount }}</p>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-gray-100 inline-flex p-1 rounded-xl mb-6">
        <button @click="tab = 'hardcopy'"
                :class="tab === 'hardcopy' ? 'bg-white text-gray-900 shadow' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2 text-sm font-medium rounded-lg transition">
            <i class="ti ti-package mr-1"></i> Hardcopy Orders
            @if($hardcopyCount > 0)
                <span class="ml-1 bg-orange-500 text-white text-xs px-1.5 py-0.5 rounded-full">{{ $hardcopyCount }}</span>
            @endif
        </button>
        <button @click="tab = 'softcopy'"
                :class="tab === 'softcopy' ? 'bg-white text-gray-900 shadow' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2 text-sm font-medium rounded-lg transition">
            <i class="ti ti-device-mobile mr-1"></i> Softcopy Purchases
            @if($softcopyCount > 0)
                <span class="ml-1 bg-purple-500 text-white text-xs px-1.5 py-0.5 rounded-full">{{ $softcopyCount }}</span>
            @endif
        </button>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: Hardcopy Orders                     -->
    <!-- ========================================== -->
    <div x-show="tab === 'hardcopy'" x-cloak class="space-y-4">
        @forelse($hardcopyOrders as $order)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                <div class="p-5 flex flex-col md:flex-row md:items-center gap-4">
                    <!-- Book cover + info -->
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="w-16 h-20 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                            @if($order->book && $order->book->cover_image)
                                <img src="{{ url('media/' . $order->book->cover_image) }}"
                                     alt="{{ $order->book->title }}"
                                     class="w-full h-full object-cover">
                            @else
                                <i class="ti ti-book text-2xl text-orange-500"></i>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs text-gray-400 font-medium">#{{ $order->id }}</span>
                                {!! $order->status_badge !!}
                            </div>
                            <p class="font-semibold text-gray-800 line-clamp-1">
                                {{ $order->book->title ?? 'Book unavailable' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Qty {{ $order->quantity }} × TSh {{ number_format($order->price_per_unit, 0) }}
                                <span class="mx-1">·</span>
                                <span class="font-semibold text-orange-600">TSh {{ number_format($order->total, 0) }}</span>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="ti ti-calendar"></i>
                                Placed {{ $order->created_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 md:flex-shrink-0">
                        <a href="{{ route('library.order.show', $order->id) }}"
                           class="inline-flex items-center gap-1 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                            <i class="ti ti-eye"></i> Track Order
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ti ti-package-off text-3xl text-orange-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-1">No hardcopy orders yet</h3>
                <p class="text-gray-500 text-sm mb-4">Browse the library and order a physical copy of any book</p>
                <a href="{{ route('library.index') }}"
                   class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg font-medium transition">
                    <i class="ti ti-books"></i> Browse Books
                </a>
            </div>
        @endforelse

        @if($hardcopyOrders->hasPages())
            <div class="mt-4">
                {{ $hardcopyOrders->appends(['tab' => 'hardcopy'])->links() }}
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- TAB 2: Softcopy Purchases                  -->
    <!-- ========================================== -->
    <div x-show="tab === 'softcopy'" x-cloak class="space-y-4">
        @forelse($softcopyPurchases as $payment)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                <div class="p-5 flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="w-16 h-20 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                            @if($payment->payable && $payment->payable->cover_image)
                                <img src="{{ url('media/' . $payment->payable->cover_image) }}"
                                     alt="{{ $payment->payable->title }}"
                                     class="w-full h-full object-cover">
                            @else
                                <i class="ti ti-book text-2xl text-purple-500"></i>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full font-medium">Softcopy</span>
                                <span class="text-xs text-emerald-600 font-medium">
                                    <i class="ti ti-circle-check"></i> Completed
                                </span>
                            </div>
                            <p class="font-semibold text-gray-800 line-clamp-1">
                                {{ $payment->payable->title ?? 'Book unavailable' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Paid TSh {{ number_format($payment->amount, 0) }}
                                <span class="mx-1">·</span>
                                {{ $payment->created_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 md:flex-shrink-0">
                        @if($payment->payable)
                            <a href="{{ route('library.read', $payment->payable->id) }}"
                               class="inline-flex items-center gap-1 bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                                <i class="ti ti-book-open"></i> Read Now
                            </a>
                            <a href="{{ route('library.show', $payment->payable->id) }}"
                               class="inline-flex items-center gap-1 border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-2 rounded-lg text-sm font-medium transition">
                                <i class="ti ti-info-circle"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ti ti-device-mobile-off text-3xl text-purple-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-1">No softcopy purchases yet</h3>
                <p class="text-gray-500 text-sm mb-4">Purchase digital books from the library to read them anytime</p>
                <a href="{{ route('library.index') }}"
                   class="inline-flex items-center gap-2 bg-purple-500 hover:bg-purple-600 text-white px-5 py-2.5 rounded-lg font-medium transition">
                    <i class="ti ti-books"></i> Browse Books
                </a>
            </div>
        @endforelse

        @if($softcopyPurchases->hasPages())
            <div class="mt-4">
                {{ $softcopyPurchases->appends(['tab' => 'softcopy'])->links() }}
            </div>
        @endif
    </div>

</div>

<style>
    [x-cloak] { display: none !important; }
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection