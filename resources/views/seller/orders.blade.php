@extends('layouts.author')

@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
<div class="space-y-6" x-data="{ tab: '{{ request('tab', 'books') }}' }">

    <!-- Header + tabs -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Orders</h2>
            <p class="text-sm text-gray-500 mt-1">Manage book &amp; product orders in one place</p>
        </div>

        <div class="inline-flex bg-gray-100 p-1 rounded-xl">
            <button @click="tab = 'books'"
                    :class="tab === 'books' ? 'bg-white text-gray-900 shadow' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition flex items-center gap-1.5">
                <i class="ti ti-books text-base"></i>
                Book Orders
                @if($pendingBooks > 0)
                    <span class="ml-1 bg-purple-500 text-white text-xs px-1.5 py-0.5 rounded-full">{{ $pendingBooks }}</span>
                @endif
            </button>
            <button @click="tab = 'products'"
                    :class="tab === 'products' ? 'bg-white text-gray-900 shadow' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition flex items-center gap-1.5">
                <i class="ti ti-shopping-cart text-base"></i>
                Product Orders
                @if($pendingProducts > 0)
                    <span class="ml-1 bg-orange-500 text-white text-xs px-1.5 py-0.5 rounded-full">{{ $pendingProducts }}</span>
                @endif
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1 — Book (Hardcopy) Orders             -->
    <!-- ========================================== -->
    <div x-show="tab === 'books'" x-cloak>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
                <p class="text-sm font-medium text-gray-700">
                    <i class="ti ti-truck-delivery text-orange-500"></i>
                    Hardcopy Orders
                </p>
                <a href="{{ route('author.orders.export-excel', ['status' => request('status')]) }}"
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
                    <i class="ti ti-file-spreadsheet"></i> Export to Excel
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">#</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Book</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Buyer</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Delivery</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Total</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Commission</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Payout</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Payment</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($bookOrders as $order)
                        <tr class="hover:bg-gray-50 align-middle transition">
                            
                            <td class="px-4 py-3 text-sm font-bold text-gray-500">
                                {{ $bookOrders->firstItem() + $loop->index }}
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
                                        <p class="text-sm text-gray-800 font-semibold truncate max-w-[150px]" title="{{ $order->book->title ?? 'N/A' }}">
                                            {{ $order->book->title ?? 'N/A' }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-mono">{{ $order->book->book_code ?? '' }}</p>
                                        <p class="text-[10px] text-orange-600 font-medium"><i class="ti ti-package"></i> Hardcopy</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-800 font-medium">{{ $order->user->full_name ?? 'Guest' }}</p>
                                @if($order->customer_phone)
                                    <p class="text-[11px] text-gray-500 mt-0.5"><i class="ti ti-phone"></i> {{ $order->customer_phone }}</p>
                                @endif
                            </td>
                            
                            <td class="px-4 py-3">
                                @if($order->shipping_address)
                                    <p class="text-xs text-gray-700 max-w-[140px] truncate" title="{{ $order->shipping_address }}">
                                        <i class="ti ti-map-pin text-orange-500"></i>
                                        {{ $order->shipping_address }}
                                    </p>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            
                            <td class="px-4 py-3 font-semibold text-emerald-600">
                                TSh {{ number_format($order->total, 2) }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                @if($order->hasCommission())
                                    <p class="font-semibold text-slate-700">TSh {{ number_format($order->platform_commission, 0) }}</p>
                                    <p class="mt-0.5">{!! $order->commission_badge !!}</p>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                                        <i class="ti ti-clock"></i> Not set
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="font-bold text-slate-800">TSh {{ number_format($order->ownerPayout(), 2) }}</span>
                            </td>

                            <td class="px-4 py-3">
                                <span class="text-[11px] px-2.5 py-1 rounded-full font-medium
                                    {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 
                                       ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                       ($order->status === 'processing' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                                @if($order->payment_method === 'contact')
                                    <p class="text-[10px] text-gray-500 mt-1"><i class="ti ti-phone"></i> Contact on delivery</p>
                                @endif
                            </td>

                            {{-- Cleaned up Payment Column --}}
                            <td class="px-4 py-3">
                                <div class="flex flex-col items-start gap-1">
                                    @if($order->isBuyerConfirmed() || $order->isSellerMarkedPaid())
                                        <span class="inline-flex items-center gap-1 text-[11px] px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200">
                                            <i class="ti ti-circle-check text-sm"></i> PAID
                                        </span>
                                    @elseif($order->isPaymentDisputed())
                                        <span class="inline-flex items-center gap-1 text-[11px] px-2.5 py-1 rounded-full bg-red-50 text-red-700 font-semibold border border-red-200">
                                            <i class="ti ti-alert-triangle text-sm"></i> DISPUTED
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 font-semibold border border-gray-200">
                                            <i class="ti ti-clock text-sm"></i> UNPAID
                                        </span>
                                    @endif
                                    
                                    @if($order->isSellerMarkedPaid() && $order->payment_received_at)
                                        <span class="text-[10px] text-gray-400 ml-1">{{ $order->payment_received_at->diffForHumans() }}</span>
                                    @elseif($order->isBuyerConfirmed() && $order->payment_confirmed_by_buyer_at)
                                        <span class="text-[10px] text-emerald-600 ml-1">Confirmed {{ $order->payment_confirmed_by_buyer_at->diffForHumans() }}</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2 justify-end">
                                    {{-- ADDED & DECORATED: Download Invoice Button for Book Orders --}}
                                    <a href="{{ route('orders.invoice', $order->id) }}"
                                       class="inline-flex items-center justify-center bg-purple-50 hover:bg-purple-100 text-purple-600 border border-purple-200 w-8 h-8 rounded-lg transition shadow-sm"
                                       title="Download PDF Invoice">
                                        <i class="ti ti-file-invoice text-base"></i>
                                    </a>

                                    <a href="{{ route('author.orders.show', $order->id) }}" 
                                       class="inline-flex items-center gap-1 text-sm text-gray-600 hover:text-purple-700 font-medium transition ml-1">
                                        View <i class="ti ti-chevron-right text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="px-4 py-12 text-center text-gray-500">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="ti ti-truck-delivery text-2xl text-gray-400"></i>
                                </div>
                                <p>No book orders yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($bookOrders->hasPages())
            <div class="px-4 py-3 border-t">
                {{ $bookOrders->appends(['tab' => 'books'])->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 2 — Product (Marketplace) Orders       -->
    <!-- ========================================== -->
    <div x-show="tab === 'products'" x-cloak>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">#</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Product</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Buyer</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Amount</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-sm font-bold text-gray-500">{{ $orders->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3">{{ $order->listing->title ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $order->buyer->full_name ?? 'Guest' }}</td>
                            <td class="px-4 py-3 font-semibold text-emerald-600">TSh {{ number_format($order->amount, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="text-[11px] px-2.5 py-1 rounded-full font-medium
                                    {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 
                                       ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2 justify-end">
                                    {{-- DECORATED: Download Invoice Button --}}
                                    <a href="{{ route('orders.invoice', $order->id) }}" 
                                       class="inline-flex items-center justify-center bg-purple-50 hover:bg-purple-100 text-purple-600 border border-purple-200 w-8 h-8 rounded-lg transition shadow-sm" 
                                       title="Download PDF Invoice">
                                        <i class="ti ti-file-invoice text-base"></i>
                                    </a>

                                    <a href="{{ route('author.orders.show', $order->id) }}" class="inline-flex items-center gap-1 text-sm text-gray-600 hover:text-purple-700 font-medium transition ml-1">
                                        View <i class="ti ti-chevron-right text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="ti ti-package-off text-2xl text-gray-400"></i>
                                </div>
                                <p>No product orders yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
            <div class="px-4 py-3 border-t">
                {{ $orders->appends(['tab' => 'products'])->links() }}
            </div>
            @endif
        </div>
    </div>

</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection