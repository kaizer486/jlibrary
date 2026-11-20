@extends('layouts.app')

@section('title', 'My Orders')
@section('content')

<div class="container mx-auto px-4 py-8 max-w-7xl" x-data="{ tab: '{{ request('tab', 'hardcopy') }}' }">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="ti ti-truck-delivery text-orange-600"></i>
                My Orders
            </h1>
            <p class="text-gray-500 text-sm mt-1">Track your hardcopy deliveries and access your digital library</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('library.orders.export-excel') }}"
               class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 px-4 py-2 rounded-xl transition text-sm font-medium">
                <i class="ti ti-file-spreadsheet"></i> Export Orders
            </a>
            <a href="{{ route('library.index') }}"
               class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-xl transition text-sm font-medium shadow-sm">
                <i class="ti ti-books"></i> Browse Library
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
            <div class="w-11 h-11 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="ti ti-package text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Hardcopy Orders</p>
                <p class="text-xl font-bold text-gray-800">{{ $hardcopyCount }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
            <div class="w-11 h-11 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="ti ti-clock text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Pending</p>
                <p class="text-xl font-bold text-gray-800">{{ $pendingCount }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
            <div class="w-11 h-11 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="ti ti-device-mobile text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Softcopy Purchases</p>
                <p class="text-xl font-bold text-gray-800">{{ $softcopyCount }}</p>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-gray-100/80 inline-flex p-1.5 rounded-xl mb-6">
        <button @click="tab = 'hardcopy'"
                :class="tab === 'hardcopy' ? 'bg-white text-gray-900 shadow-sm border border-gray-200' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2 text-sm font-medium rounded-lg transition flex items-center gap-1.5">
            <i class="ti ti-package text-base"></i> Hardcopy Orders
            @if($hardcopyCount > 0)
                <span class="bg-orange-100 text-orange-700 text-xs px-1.5 py-0.5 rounded-full">{{ $hardcopyCount }}</span>
            @endif
        </button>
        <button @click="tab = 'softcopy'"
                :class="tab === 'softcopy' ? 'bg-white text-gray-900 shadow-sm border border-gray-200' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2 text-sm font-medium rounded-lg transition flex items-center gap-1.5">
            <i class="ti ti-device-mobile text-base"></i> Softcopy Purchases
            @if($softcopyCount > 0)
                <span class="bg-purple-100 text-purple-700 text-xs px-1.5 py-0.5 rounded-full">{{ $softcopyCount }}</span>
            @endif
        </button>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: Hardcopy Orders                     -->
    <!-- ========================================== -->
    <div x-show="tab === 'hardcopy'" x-cloak class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">#</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Book</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Qty × Price</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Total</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Payment</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($hardcopyOrders as $order)
                        <tr class="hover:bg-gray-50 align-middle">
                            
                            <td class="px-4 py-3 text-sm font-bold text-gray-500">
                                {{ $hardcopyOrders->firstItem() + $loop->index }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-10 bg-orange-100 rounded flex items-center justify-center flex-shrink-0 overflow-hidden border border-gray-200">
                                        @if($order->book && $order->book->cover_image)
                                            <img src="{{ url('media/' . $order->book->cover_image) }}" class="w-full h-full object-cover" alt="{{ $order->book->title }}">
                                        @else
                                            <i class="ti ti-book text-orange-500"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm text-gray-800 font-semibold truncate max-w-[200px]" title="{{ $order->book->title ?? 'N/A' }}">
                                            {{ $order->book->title ?? 'Book unavailable' }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-mono">{{ $order->book->book_code ?? '—' }}</p>
                                        <p class="text-[10px] text-orange-600 font-medium"><i class="ti ti-package"></i> Hardcopy</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $order->quantity }} × TSh {{ number_format($order->price_per_unit, 0) }}
                            </td>

                            <td class="px-4 py-3 font-semibold text-emerald-600">
                                TSh {{ number_format($order->total, 2) }}
                            </td>

                            <td class="px-4 py-3">
                                {!! $order->status_badge !!}
                                @if($order->canBuyerRespond())
                                    <p class="text-[10px] text-yellow-600 font-bold mt-1"><i class="ti ti-bell animate-pulse"></i> Action needed</p>
                                @elseif($order->isPaymentDisputed())
                                    <p class="text-[10px] text-red-600 font-bold mt-1"><i class="ti ti-alert-triangle"></i> Disputed</p>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                @if($order->isContactPayment())
                                    {!! $order->payment_badge !!}
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('library.order.show', $order->id) }}"
                                   class="inline-flex items-center gap-1 {{ $order->canBuyerRespond() ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-orange-500 hover:bg-orange-600' }} text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition whitespace-nowrap">
                                    @if($order->canBuyerRespond())
                                        <i class="ti ti-bell"></i> Confirm
                                    @else
                                        <i class="ti ti-eye"></i> Track
                                    @endif
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="ti ti-package-off text-2xl text-orange-400"></i>
                                </div>
                                <h3 class="text-gray-800 font-semibold mb-1">No hardcopy orders yet</h3>
                                <p class="text-sm text-gray-500 mb-4">Browse the library and order a physical copy.</p>
                                <a href="{{ route('library.index') }}" class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-medium transition text-sm">
                                    <i class="ti ti-books"></i> Browse Books
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($hardcopyOrders->hasPages())
            <div class="px-4 py-3 border-t">
                {{ $hardcopyOrders->appends(['tab' => 'hardcopy'])->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 2: Softcopy Purchases                  -->
    <!-- ========================================== -->
    <div x-show="tab === 'softcopy'" x-cloak class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">#</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Book</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Amount Paid</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($softcopyPurchases as $payment)
                        <tr class="hover:bg-gray-50 align-middle">
                            
                            <td class="px-4 py-3 text-sm font-bold text-gray-500">
                                {{ $softcopyPurchases->firstItem() + $loop->index }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-10 bg-purple-100 rounded flex items-center justify-center flex-shrink-0 overflow-hidden border border-gray-200">
                                        @if($payment->payable && $payment->payable->cover_image)
                                            <img src="{{ url('media/' . $payment->payable->cover_image) }}" class="w-full h-full object-cover" alt="{{ $payment->payable->title }}">
                                        @else
                                            <i class="ti ti-book text-purple-500"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm text-gray-800 font-semibold truncate max-w-[200px]" title="{{ $payment->payable->title ?? 'N/A' }}">
                                            {{ $payment->payable->title ?? 'Book unavailable' }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-mono">{{ $payment->payable->book_code ?? '—' }}</p>
                                        <p class="text-[10px] text-purple-600 font-medium"><i class="ti ti-device-mobile"></i> Softcopy</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 font-semibold text-emerald-600">
                                TSh {{ number_format($payment->amount, 2) }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="text-xs text-emerald-600 font-semibold flex items-center gap-1 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100 w-max">
                                    <i class="ti ti-circle-check"></i> Completed
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $payment->created_at->format('M d, Y') }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center justify-end gap-2">
                                    <a href="{{ route('library.read', $payment->payable->id) }}"
                                       class="inline-flex items-center gap-1 bg-purple-500 hover:bg-purple-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition whitespace-nowrap">
                                        <i class="ti ti-book-open"></i> Read
                                    </a>
                                    <a href="{{ route('library.show', $payment->payable->id) }}"
                                       class="inline-flex items-center justify-center border border-gray-200 text-gray-500 hover:bg-gray-50 w-8 h-8 rounded-lg transition">
                                        <i class="ti ti-info-circle text-base"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="w-16 h-16 bg-purple-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="ti ti-device-mobile-off text-2xl text-purple-400"></i>
                                </div>
                                <h3 class="text-gray-800 font-semibold mb-1">No softcopy purchases yet</h3>
                                <p class="text-sm text-gray-500 mb-4">Purchase digital books to read them instantly.</p>
                                <a href="{{ route('library.index') }}" class="inline-flex items-center gap-2 bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg font-medium transition text-sm">
                                    <i class="ti ti-books"></i> Browse Books
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($softcopyPurchases->hasPages())
            <div class="px-4 py-3 border-t">
                {{ $softcopyPurchases->appends(['tab' => 'softcopy'])->links() }}
            </div>
            @endif
        </div>
    </div>

</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection