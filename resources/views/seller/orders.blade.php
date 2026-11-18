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
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
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
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Order ID</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Book</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Buyer</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Delivery</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Qty × Price</th>
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
                        <tr class="hover:bg-gray-50 align-top">
                                                        <td class="px-4 py-3 font-medium text-gray-800">
                                #{{ $order->id }}
                                @if($order->bulk_order_id)
                                    <div class="mt-1">
                                        <span class="inline-flex items-center gap-1 text-[9px] font-semibold bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded">
                                            <i class="ti ti-link"></i> BULK
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-10 bg-orange-100 rounded flex items-center justify-center flex-shrink-0 overflow-hidden">
                                        @if($order->book && $order->book->cover_image)
                                            <img src="{{ url('media/' . $order->book->cover_image) }}"
                                                 class="w-full h-full object-cover"
                                                 alt="{{ $order->book->title }}">
                                        @else
                                            <i class="ti ti-book text-orange-500"></i>
                                        @endif
                                    </div>
                                                                     <div class="min-w-0">
                                        <p class="text-sm text-gray-800 line-clamp-1 font-medium">{{ $order->book->title ?? 'N/A' }}</p>
                                        <p class="text-[10px] text-slate-400 font-mono">{{ $order->book->book_code ?? '' }}</p>
                                        <p class="text-xs text-orange-600">
                                            <i class="ti ti-package text-xs"></i> Hardcopy
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-800 font-medium">{{ $order->user->full_name ?? 'Guest' }}</p>
                                @if($order->customer_phone)
                                    <p class="text-xs text-gray-600 mt-0.5">
                                        <i class="ti ti-phone text-xs"></i> {{ $order->customer_phone }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($order->shipping_address)
                                    <p class="text-xs text-gray-700 max-w-[180px] line-clamp-2">
                                        <i class="ti ti-map-pin text-xs text-orange-500"></i>
                                        {{ $order->shipping_address }}
                                    </p>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $order->quantity }} × TSh {{ number_format($order->price_per_unit, 0) }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-emerald-600">
                                TSh {{ number_format($order->total, 2) }}
                            </td>

                            <!-- COMMISSION COLUMN -->
                            <td class="px-4 py-3 text-sm">
                                @if($order->hasCommission())
                                    <p class="font-semibold text-slate-700">TSh {{ number_format($order->platform_commission, 0) }}</p>
                                    <p class="mt-0.5">{!! $order->commission_badge !!}</p>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                                        <i class="ti ti-clock text-xs"></i> Awaiting
                                    </span>
                                @endif
                            </td>

                            <!-- PAYOUT COLUMN -->
                            <td class="px-4 py-3 text-sm">
                                <span class="font-bold text-slate-800">
                                    TSh {{ number_format($order->ownerPayout(), 2) }}
                                </span>
                            </td>

                            <!-- STATUS COLUMN -->
                            <td class="px-4 py-3">
                                <span class="text-xs px-2.5 py-1 rounded-full 
                                    {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 
                                       ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                       ($order->status === 'processing' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                                @if($order->payment_method === 'contact')
                                    <p class="text-[10px] text-gray-500 mt-1">
                                        <i class="ti ti-phone text-[10px]"></i> Contact on delivery
                                    </p>
                                @endif
                            </td>

                            <!-- PAYMENT STATUS COLUMN -->
                            <td class="px-4 py-3">
                                {!! $order->payment_badge !!}
                                @if($order->isSellerMarkedPaid() && $order->payment_received_at)
                                    <p class="text-[10px] text-gray-400 mt-1">
                                        {{ $order->payment_received_at->diffForHumans() }}
                                    </p>
                                @endif
                                @if($order->isBuyerConfirmed() && $order->payment_confirmed_by_buyer_at)
                                    <p class="text-[10px] text-emerald-600 mt-1">
                                        Confirmed {{ $order->payment_confirmed_by_buyer_at->diffForHumans() }}
                                    </p>
                                @endif
                                @if($order->isPaymentDisputed() && $order->payment_dispute_reason)
                                    <p class="text-[10px] text-red-600 mt-1 max-w-[180px] truncate"
                                       title="{{ $order->payment_dispute_reason }}">
                                        <i class="ti ti-alert-triangle text-[10px]"></i>
                                        {{ Str::limit($order->payment_dispute_reason, 40) }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>

                            <!-- ACTIONS COLUMN -->
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2 flex-wrap justify-end">
                                    @if($order->canSellerMarkPaid())
                                        <form method="POST"
                                              action="{{ route('author.orders.mark-paid', $order->id) }}"
                                              onsubmit="return confirm('Mark this order as PAID? The buyer will be notified to confirm.')"
                                              class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-2.5 py-1.5 rounded-lg transition whitespace-nowrap">
                                                <i class="ti ti-check"></i> Mark Paid
                                            </button>
                                        </form>
                                    @elseif($order->canSellerUnmarkPaid())
                                        <form method="POST"
                                              action="{{ route('author.orders.unmark-paid', $order->id) }}"
                                              onsubmit="return confirm('Un-mark this payment? The buyer will be notified.')"
                                              class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold px-2.5 py-1.5 rounded-lg transition whitespace-nowrap">
                                                <i class="ti ti-arrow-back-up"></i> Unmark
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('author.orders.show', $order->id) }}"
                                       class="inline-flex items-center gap-1 text-sm text-purple-600 hover:text-purple-800 font-medium">
                                        View <i class="ti ti-chevron-right text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="px-4 py-8 text-center text-gray-500">
                                <i class="ti ti-truck-delivery text-4xl mb-2 block text-gray-300"></i>
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
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Order ID</th>
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
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">#{{ $order->id }}</td>
                            <td class="px-4 py-3">{{ $order->listing->title ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $order->buyer->full_name ?? 'Guest' }}</td>
                            <td class="px-4 py-3 font-semibold text-emerald-600">TSh {{ number_format($order->amount, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2.5 py-1 rounded-full 
                                    {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 
                                       ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('seller.orders.show', $order->id) }}"
                                   class="text-sm text-orange-600 hover:text-orange-800 font-medium">
                                    View →
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                <i class="ti ti-package-off text-4xl mb-2 block text-gray-300"></i>
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
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection