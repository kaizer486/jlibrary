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
            <label class="text-xs text-gray-500 uppercase mb-1 block">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Order #, book, buyer, phone..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
        </div>
        <div>
            <label class="text-xs text-gray-500 uppercase mb-1 block">Order Status</label>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">All</option>
                @foreach(['pending','processing','completed','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500 uppercase mb-1 block">Commission</label>
            <select name="commission_status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">All</option>
                @foreach(['pending','negotiated','collected'] as $s)
                    <option value="{{ $s }}" {{ request('commission_status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500 uppercase mb-1 block">Payment</label>
            <select name="payment_status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">All</option>
                <option value="unpaid"             {{ request('payment_status')==='unpaid'             ? 'selected':'' }}>🔵 Unpaid</option>
                <option value="seller_marked_paid" {{ request('payment_status')==='seller_marked_paid' ? 'selected':'' }}>🟡 Awaiting Confirmation</option>
                <option value="buyer_confirmed"    {{ request('payment_status')==='buyer_confirmed'    ? 'selected':'' }}>🟢 Confirmed</option>
                <option value="disputed"           {{ request('payment_status')==='disputed'           ? 'selected':'' }}>🔴 Disputed</option>
            </select>
        </div>
        <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            <i class="ti ti-search"></i> Filter
        </button>
        @if(request()->hasAny(['search','status','commission_status','payment_status']))
            <a href="{{ route('super-admin.hardcopy-orders.index') }}"
               class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                Clear
            </a>
        @endif
    </form>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Seq</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Order #</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Book</th>
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
                        <tr class="hover:bg-gray-50 align-top">
                            {{-- Sequence number: 1-based per page, resets each page --}}
                            <td class="px-4 py-3 text-xs font-bold text-gray-400">
                                {{ $orders->firstItem() + $loop->index }}
                            </td>

                            {{-- Raw DB order ID --}}
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                #{{ $order->id }}
                                @if($order->bulk_order_id)
                                    <div class="mt-1">
                                        <span class="inline-flex items-center gap-1 text-[9px] font-semibold bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded">
                                            <i class="ti ti-link"></i> BULK
                                        </span>
                                    </div>
                                @endif
                            </td>

                            {{-- Book title + book code --}}
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
                                        <p class="text-sm text-gray-800 font-medium line-clamp-1">
                                            {{ $order->book->title ?? 'N/A' }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-mono">
                                            {{ $order->book->book_code ?? '—' }}
                                        </p>
                                        <p class="text-xs text-gray-500">× {{ $order->quantity }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <p class="text-gray-800 font-medium">{{ $order->owner_name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $order->author_id ? 'Author' : 'Institution' }}
                                </p>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <p class="text-gray-800">{{ $order->user->full_name ?? 'Guest' }}</p>
                                @if($order->customer_phone)
                                    <p class="text-xs text-gray-500">{{ $order->customer_phone }}</p>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm font-semibold text-emerald-600">
                                TSh {{ number_format($order->total, 0) }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                @if($order->hasCommission())
                                    <span class="font-semibold text-purple-700">TSh {{ number_format($order->platform_commission, 0) }}</span>
                                    <div class="text-xs mt-0.5">{!! $order->commission_badge !!}</div>
                                @else
                                    <span class="text-gray-400 text-xs">Not set</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm text-blue-700">
                                TSh {{ number_format($order->ownerPayout(), 0) }}
                            </td>

                            <td class="px-4 py-3 text-sm">{!! $order->status_badge !!}</td>

                            <td class="px-4 py-3 text-sm">
                                {!! $order->payment_badge !!}
                                @if($order->isSellerMarkedPaid() && $order->payment_received_at)
                                    <p class="text-[10px] text-gray-400 mt-1">
                                        {{ $order->payment_received_at->diffForHumans() }}
                                    </p>
                                @elseif($order->isBuyerConfirmed() && $order->payment_confirmed_by_buyer_at)
                                    <p class="text-[10px] text-emerald-600 mt-1">
                                        {{ $order->payment_confirmed_by_buyer_at->diffForHumans() }}
                                    </p>
                                @elseif($order->isPaymentDisputed() && $order->payment_dispute_reason)
                                    <p class="text-[10px] text-red-600 mt-1 max-w-[140px] truncate"
                                       title="{{ $order->payment_dispute_reason }}">
                                        <i class="ti ti-alert-triangle text-[10px]"></i>
                                        {{ Str::limit($order->payment_dispute_reason, 30) }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('super-admin.hardcopy-orders.show', $order->id) }}"
                                   class="inline-flex items-center gap-1 text-sm text-purple-600 hover:text-purple-800 font-medium">
                                    View <i class="ti ti-chevron-right text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-4 py-12 text-center text-gray-500">
                                <i class="ti ti-package-off text-4xl text-gray-300 mb-2 block"></i>
                                <p>No hardcopy orders found.</p>
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