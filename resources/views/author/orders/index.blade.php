@extends('layouts.author')

@section('title', 'Book Orders')
@section('page-title', 'Book Orders')

@section('content')

<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div class="relative z-10 min-h-screen py-8" x-data="{ tab: '{{ request('tab', 'hardcopy') }}' }">
    <div class="container mx-auto px-4 max-w-6xl">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="bg-gradient-to-br from-orange-500 to-amber-500 p-2 rounded-xl shadow-lg shadow-orange-500/20">
                        <i class="ti ti-truck-delivery text-white text-xl"></i>
                    </span>
                    Book Orders
                </h1>
                <p class="text-slate-600 text-sm mt-1">Manage hardcopy orders and track softcopy sales</p>
            </div>
            <a href="{{ route('author.books.index') }}"
               class="inline-flex items-center gap-2 bg-white border-2 border-slate-200/80 text-slate-700 px-4 py-2 rounded-xl hover:bg-slate-50 transition text-sm font-medium">
                <i class="ti ti-books"></i> My Books
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-md border-2 border-slate-200/80 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="ti ti-package text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-slate-500 truncate">Total Orders</p>
                        <p class="text-xl font-bold text-slate-800">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md border-2 border-slate-200/80 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="ti ti-clock text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-slate-500 truncate">Pending</p>
                        <p class="text-xl font-bold text-slate-800">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md border-2 border-slate-200/80 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="ti ti-coin text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-slate-500 truncate">Hardcopy Revenue</p>
                        <p class="text-xl font-bold text-slate-800">TSh {{ number_format($hardcopyRevenue, 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md border-2 border-slate-200/80 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="ti ti-device-mobile text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-slate-500 truncate">Softcopy Revenue</p>
                        <p class="text-xl font-bold text-slate-800">TSh {{ number_format($softcopyRevenue, 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="inline-flex bg-gray-100 p-1 rounded-xl mb-6">
            <button @click="tab = 'hardcopy'"
                    :class="tab === 'hardcopy' ? 'bg-white text-gray-900 shadow' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition flex items-center gap-1.5">
                <i class="ti ti-package text-base"></i>
                Hardcopy Orders
                @if($stats['pending'] > 0)
                    <span class="ml-1 bg-orange-500 text-white text-xs px-1.5 py-0.5 rounded-full">{{ $stats['pending'] }}</span>
                @endif
            </button>
            <button @click="tab = 'softcopy'"
                    :class="tab === 'softcopy' ? 'bg-white text-gray-900 shadow' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition flex items-center gap-1.5">
                <i class="ti ti-device-mobile text-base"></i>
                Softcopy Sales
                @if($softcopySales->total() > 0)
                    <span class="ml-1 bg-purple-500 text-white text-xs px-1.5 py-0.5 rounded-full">{{ $softcopySales->total() }}</span>
                @endif
            </button>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: Hardcopy Orders                     -->
        <!-- ========================================== -->
        <div x-show="tab === 'hardcopy'" x-cloak>
            @if($orders->count() > 0)
                <div class="space-y-3">
                    @foreach($orders as $order)
                        <div class="bg-white rounded-xl shadow-md border-2 border-slate-200/80 p-4 hover:shadow-lg hover:border-orange-300/60 transition">
                            <div class="flex flex-col md:flex-row md:items-center gap-4">
                                <!-- Order info -->
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div class="w-12 h-14 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                                        @if($order->book && $order->book->cover_image)
                                            <img src="{{ url('media/' . $order->book->cover_image) }}"
                                                 class="w-full h-full object-cover"
                                                 alt="{{ $order->book->title }}">
                                        @else
                                            <i class="ti ti-book text-orange-500 text-xl"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                                            {{-- Seq badge --}}
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">
                                                {{ $orders->firstItem() + $loop->index }}
                                            </span>
                                            <span class="text-xs text-slate-400 font-medium">#{{ $order->id }}</span>
                                            {!! $order->status_badge !!}
                                        </div>
                                        <p class="font-semibold text-slate-800 line-clamp-1">
                                            {{ $order->book->title ?? 'Book unavailable' }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-mono">
                                            {{ $order->book->book_code ?? '—' }}
                                        </p>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            {{ $order->user->full_name ?? 'Guest' }}
                                            <span class="mx-1">·</span>
                                            <i class="ti ti-phone text-xs"></i> {{ $order->customer_phone }}
                                        </p>
                                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">
                                            <i class="ti ti-map-pin text-xs"></i> {{ $order->shipping_address }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Amount -->
                                <div class="text-left md:text-right md:flex-shrink-0 md:w-32">
                                    <p class="text-xs text-slate-400">Qty × Price</p>
                                    <p class="text-sm text-slate-700 font-medium">{{ $order->quantity }} × TSh {{ number_format($order->price_per_unit, 0) }}</p>
                                    <p class="font-bold text-orange-600 text-lg">TSh {{ number_format($order->total, 0) }}</p>
                                </div>

                                                               <!-- Actions -->
                                <div class="flex items-center gap-2 md:flex-shrink-0">
                                    
                                    {{-- ADDED: Download Invoice Button --}}
                                    <a href="{{ route('orders.invoice', $order->id) }}"
                                       class="inline-flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 border border-slate-200 w-9 h-9 rounded-lg transition"
                                       title="Download Invoice">
                                        <i class="ti ti-file-invoice text-lg"></i>
                                    </a>

                                    <a href="{{ route('author.orders.show', $order->id) }}"
                                       class="inline-flex items-center gap-1 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                                        <i class="ti ti-eye"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($orders->hasPages())
                    <div class="mt-4">{{ $orders->appends(['tab' => 'hardcopy'])->links() }}</div>
                @endif
            @else
                <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 p-12 text-center">
                    <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ti ti-package-off text-3xl text-orange-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800 mb-1">No hardcopy orders yet</h3>
                    <p class="text-slate-500 text-sm mb-4">When buyers order physical copies of your books, they'll appear here</p>
                    <a href="{{ route('author.books.index') }}"
                       class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg font-medium transition">
                        <i class="ti ti-books"></i> View My Books
                    </a>
                </div>
            @endif
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: Softcopy Sales                      -->
        <!-- ========================================== -->
        <div x-show="tab === 'softcopy'" x-cloak>
            @if($softcopySales->count() > 0)
                <div class="bg-white rounded-xl shadow-md border-2 border-slate-200/80 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Seq</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Book</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Buyer</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Amount</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($softcopySales as $payment)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 text-xs font-bold text-slate-400">
                                            {{ $softcopySales->firstItem() + $loop->index }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <p class="text-sm text-slate-800 font-medium line-clamp-1">
                                                {{ $payment->payable->title ?? 'Book unavailable' }}
                                            </p>
                                            <p class="text-[10px] text-slate-400 font-mono">
                                                {{ $payment->payable->book_code ?? '—' }}
                                            </p>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-700">
                                            {{ $payment->user->full_name ?? 'Guest' }}
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-purple-600">
                                            TSh {{ number_format($payment->amount, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-500">
                                            {{ $payment->created_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($softcopySales->hasPages())
                    <div class="mt-4">{{ $softcopySales->appends(['tab' => 'softcopy'])->links() }}</div>
                @endif
            @else
                <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 p-12 text-center">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ti ti-device-mobile-off text-3xl text-purple-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800 mb-1">No softcopy sales yet</h3>
                    <p class="text-slate-500 text-sm">When buyers purchase softcopies of your books, sales will appear here</p>
                </div>
            @endif
        </div>

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