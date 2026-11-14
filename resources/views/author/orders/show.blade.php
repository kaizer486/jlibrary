@extends('layouts.author')

@section('title', 'Order #' . $order->id)
@section('page-title', 'Order #' . $order->id)

@section('content')

<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-5xl">

        <!-- Back -->
        <div class="mb-6">
            <a href="{{ route('author.orders.index') }}"
               class="text-slate-600 hover:text-slate-800 flex items-center gap-2">
                <i class="ti ti-arrow-left"></i> Back to Orders
            </a>
        </div>

        <!-- Flash -->
        @if(session('success'))
            <div class="mb-4 bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3">
                <i class="ti ti-circle-check text-2xl text-emerald-500"></i>
                <p class="text-emerald-800 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Header card -->
        <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-orange-600 to-amber-600 px-6 py-4 border-b-2 border-orange-400/30">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="ti ti-package"></i> Order #{{ $order->id }}
                        </h1>
                        <p class="text-orange-100 text-sm mt-1">
                            Placed {{ $order->created_at->format('F d, Y \a\t H:i') }}
                        </p>
                    </div>
                    <div>{!! $order->status_badge !!}</div>
                </div>
            </div>

            @if($order->payment_method === 'contact')
                <div class="bg-blue-50 border-b border-blue-200 px-6 py-3 flex items-start gap-2">
                    <i class="ti ti-info-circle text-blue-600 mt-0.5"></i>
                    <p class="text-xs text-blue-800">
                        <strong>Contact the buyer</strong> on
                        <a href="tel:{{ $order->customer_phone }}" class="font-semibold underline">
                            {{ $order->customer_phone }}
                        </a>
                        to confirm delivery and arrange payment.
                    </p>
                </div>
            @endif

            <div class="p-6 grid md:grid-cols-2 gap-6">
                <!-- Book -->
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-2">Book</p>
                    <div class="flex gap-3">
                        <div class="w-16 h-20 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                            @if($order->book && $order->book->cover_image)
                                <img src="{{ url('media/' . $order->book->cover_image) }}"
                                     class="w-full h-full object-cover"
                                     alt="{{ $order->book->title }}">
                            @else
                                <i class="ti ti-book text-2xl text-orange-500"></i>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-800 line-clamp-2">{{ $order->book->title ?? 'Book unavailable' }}</p>
                            <p class="text-sm text-slate-500">{{ $order->book->author ?? '' }}</p>
                            <p class="text-xs text-orange-600 mt-1 font-medium">
                                <i class="ti ti-package"></i> Hardcopy
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Buyer -->
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-2">Buyer</p>
                    <p class="font-semibold text-slate-800">{{ $order->user->full_name ?? 'Guest' }}</p>
                    @if($order->user && $order->user->email)
                        <p class="text-sm text-slate-500">{{ $order->user->email }}</p>
                    @endif
                    @if($order->customer_phone)
                        <p class="text-sm text-slate-600 mt-1">
                            <i class="ti ti-phone text-xs"></i> {{ $order->customer_phone }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order summary + Delivery -->
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 p-6">
                <h2 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-receipt text-orange-600"></i> Order Summary
                </h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Unit Price</span>
                        <span class="font-medium">TSh {{ number_format($order->price_per_unit, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Quantity</span>
                        <span class="font-medium">× {{ $order->quantity }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Payment Method</span>
                        <span class="font-medium">
                            @if($order->payment_method === 'contact')
                                <i class="ti ti-phone"></i> Contact
                            @else
                                {{ ucfirst($order->payment_method) }}
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between pt-3 border-t-2 border-slate-200">
                        <span class="font-semibold text-slate-800">Total</span>
                        <span class="font-bold text-orange-600 text-lg">TSh {{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 p-6">
                <h2 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-truck-delivery text-orange-600"></i> Delivery Details
                </h2>
                <div class="space-y-3 text-sm">
                    <div class="py-2 border-b border-slate-100">
                        <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Full Name</p>
                        <p class="font-medium">{{ $order->user->full_name ?? 'N/A' }}</p>
                    </div>
                    <div class="py-2 border-b border-slate-100">
                        <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Phone</p>
                        <p class="font-medium flex items-center gap-1">
                            <i class="ti ti-phone text-slate-400"></i> {{ $order->customer_phone }}
                        </p>
                    </div>
                    <div class="py-2">
                        <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Delivery Location</p>
                        <p class="font-medium flex items-start gap-1">
                            <i class="ti ti-map-pin text-slate-400 mt-0.5"></i>
                            <span>{{ $order->shipping_address }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buyer's notes (parsed from `Name: X | Notes: Y`) -->
        @php
            $userNotes = '';
            if ($order->notes && preg_match('/\|\s*Notes:\s*(.+)$/', $order->notes, $m)) {
                $userNotes = trim($m[1]);
            }
        @endphp
        @if($userNotes)
            <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 p-6 mb-6">
                <h2 class="font-semibold text-slate-800 mb-2 flex items-center gap-2">
                    <i class="ti ti-notes text-orange-600"></i> Buyer's Notes
                </h2>
                <p class="text-slate-700 text-sm leading-relaxed">{{ $userNotes }}</p>
            </div>
        @endif

        <!-- Update status -->
        <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 p-6 mb-6">
            <h2 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <i class="ti ti-adjustments text-orange-600"></i> Update Order Status
            </h2>
            <form method="POST" action="{{ route('author.orders.update-status', $order->id) }}">
                @csrf
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                        <select name="status" required
                                class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-slate-800">
                            <option value="pending"    {{ $order->status === 'pending'    ? 'selected' : '' }}>⏳ Pending</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>🔄 Processing (Preparing)</option>
                            <option value="completed"  {{ $order->status === 'completed'  ? 'selected' : '' }}>✅ Completed (Delivered)</option>
                            <option value="cancelled"  {{ $order->status === 'cancelled'  ? 'selected' : '' }}>❌ Cancelled</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                                class="w-full bg-gradient-to-r from-orange-600 to-amber-600 text-white px-6 py-2.5 rounded-xl hover:shadow-lg hover:shadow-orange-600/25 transition font-semibold flex items-center justify-center gap-2">
                            <i class="ti ti-device-floppy"></i> Save Status
                        </button>
                    </div>
                </div>
                <p class="text-xs text-slate-400 mt-2">
                    <i class="ti ti-info-circle"></i>
                    Marking as "Completed" or "Cancelled" will notify the buyer.
                    Cancelling restores stock to the book.
                </p>
            </form>
        </div>

        <!-- Actions -->
        <div class="flex gap-3">
            <a href="{{ route('author.orders.index') }}"
               class="flex-1 bg-white border-2 border-slate-200/80 text-slate-700 py-3 rounded-xl font-medium text-center hover:bg-slate-50 transition">
                <i class="ti ti-arrow-left"></i> Back to Orders
            </a>
            <a href="{{ route('seller.orders') }}"
               class="flex-1 bg-gradient-to-r from-orange-600 to-amber-600 text-white py-3 rounded-xl font-semibold text-center hover:shadow-lg transition">
                <i class="ti ti-list"></i> All Orders
            </a>
        </div>

    </div>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

@endsection