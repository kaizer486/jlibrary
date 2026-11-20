@extends('layouts.super-admin')

@section('title', 'Hardcopy Orders')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="ti ti-truck-delivery text-orange-600"></i>
                Hardcopy Orders
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Monitor all hardcopy orders · negotiate commission with owners
            </p>
        </div>
        <a href="{{ route('super-admin.hardcopy-orders.export-excel', ['status' => request('status')]) }}"
           class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap">
            <i class="ti ti-file-spreadsheet"></i> Export All
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-500">Total Orders</p>
            <p class="text-xl font-bold text-gray-800">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-xs text-yellow-600">Pending</p>
            <p class="text-xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-xs text-emerald-600">Total Revenue</p>
            <p class="text-sm font-bold text-emerald-700">TSh {{ number_format($stats['revenue'], 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-xs text-purple-600">Commission</p>
            <p class="text-sm font-bold text-purple-700">TSh {{ number_format($stats['commission'], 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-xs text-blue-600">Owner Payouts</p>
            <p class="text-sm font-bold text-blue-700">TSh {{ number_format($stats['payouts'], 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-xs text-orange-600">Unset Commission</p>
            <p class="text-xl font-bold text-orange-600">{{ $stats['unset'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="bg-white rounded-xl shadow-sm p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="text-xs text-gray-500 uppercase mb-1 block font-semibold">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Order #, book, buyer, phone..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
        </div>
        <div>
            <label class="text-xs text-gray-500 uppercase mb-1 block font-semibold">Order Status</label>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                <option value="">All</option>
                @foreach(['pending','processing','completed','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500 uppercase mb-1 block font-semibold">Commission</label>
            <select name="commission_status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                <option value="">All</option>
                @foreach(['pending','negotiated','collected'] as $s)
                    <option value="{{ $s }}" {{ request('commission_status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500 uppercase mb-1 block font-semibold">Payment</label>
            <select name="payment_status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                <option value="">All</option>
                <option value="unpaid"             {{ request('payment_status')==='unpaid'             ? 'selected':'' }}>Unpaid</option>
                <option value="seller_marked_paid" {{ request('payment_status')==='seller_marked_paid' ? 'selected':'' }}>Awaiting Confirm</option>
                <option value="buyer_confirmed"    {{ request('payment_status')==='buyer_confirmed'    ? 'selected':'' }}>Confirmed</option>
                <option value="disputed"           {{ request('payment_status')==='disputed'           ? 'selected':'' }}>Disputed</option>
            </select>
        </div>
        <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <i class="ti ti-search"></i> Filter
        </button>
        @if(request()->hasAny(['search','status','commission_status','payment_status']))
            <a href="{{ route('super-admin.hardcopy-orders.index') }}"
               class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium">
                Clear
            </a>
        @endif
    </form>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Book Details</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Owner</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Buyer</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Total</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Commission</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Payout</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Order Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Payment</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 align-middle transition">
                            
                            {{-- Sequence Column (1, 2, 3...) --}}
                            <td class="px-4 py-3 text-sm font-bold text-gray-500">
                                {{ $orders->firstItem() + $loop->index }}
                            </td>

                            {{-- Book Details Column --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-10 bg-orange-100 rounded flex items-center justify-center flex-shrink-0 overflow-hidden border border-gray-200">
                                        @if($order->book && $order->book->cover_image)
                                            <img src="{{ url('media/' . $order->book->cover_image) }}"
                                                 class="w-full h-full object-cover"
                                                 alt="{{ $order->book->title }}">
                                        @else
                                            <i class="ti ti-book text-orange-500"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm text-gray-800 font-semibold truncate max-w-[150px]" title="{{ $order->book->title ?? 'N/A' }}">
                                            {{ $order->book->title ?? 'N/A' }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-mono">{{ $order->book->book_code ?? '—' }}</p>
                                        <p class="text-[11px] text-orange-600 font-medium mt-0.5">Qty: {{ $order->quantity }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <p class="text-gray-800 font-medium">{{ $order->owner_name }}</p>
                                <p class="text-[11px] text-gray-500">{{ $order->author_id ? 'Author' : 'Institution' }}</p>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <p class="text-gray-800 font-medium">{{ $order->user->full_name ?? 'Guest' }}</p>
                                @if($order->customer_phone)
                                    <p class="text-[11px] text-gray-500 mt-0.5"><i class="ti ti-phone"></i> {{ $order->customer_phone }}</p>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm font-bold text-emerald-600">
                                TSh {{ number_format($order->total, 0) }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                @if($order->hasCommission())
                                    <span class="font-semibold text-purple-700">TSh {{ number_format($order->platform_commission, 0) }}</span>
                                    <div class="text-[10px] mt-0.5">{!! $order->commission_badge !!}</div>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                                        <i class="ti ti-clock"></i> Not set
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm font-bold text-blue-700">
                                TSh {{ number_format($order->ownerPayout(), 0) }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="text-[11px] px-2.5 py-1 rounded-full font-medium
                                    {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 
                                       ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                       ($order->status === 'processing' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                                    {{ ucfirst($order->status) }}
                                </span>
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

                            <td class="px-4 py-3 text-xs text-gray-500 font-medium">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2 justify-end">
                                    {{-- Decorated Invoice Button --}}
                                    <a href="{{ route('orders.invoice', $order->id) }}"
                                       class="inline-flex items-center justify-center bg-purple-50 hover:bg-purple-100 text-purple-600 border border-purple-200 w-8 h-8 rounded-lg transition shadow-sm"
                                       title="Download PDF Invoice">
                                        <i class="ti ti-file-invoice text-lg"></i>
                                    </a>

                                    <a href="{{ route('super-admin.hardcopy-orders.show', $order->id) }}"
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
                                    <i class="ti ti-package-off text-2xl text-gray-400"></i>
                                </div>
                                <p class="font-medium">No hardcopy orders found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="px-4 py-3 border-t">{{ $orders->withQueryString()->links() }}</div>
        @endif
    </div>

</div>

<style>
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection