@extends('layouts.app')

@section('title', 'Order #' . $order->id)
@section('content')

<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Back -->
    <div class="mb-6">
        <a href="{{ url('/library') }}" class="inline-flex items-center text-jlibrary-600 hover:text-jlibrary-700">
            <i class="ti ti-arrow-left mr-1"></i> Back to Library
        </a>
    </div>

    <!-- Success banner if just placed -->
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
            <i class="ti ti-circle-check text-2xl text-green-500"></i>
            <p class="text-green-800 text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Header card -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-orange-500 to-amber-600 px-6 py-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold flex items-center gap-2">
                        <i class="ti ti-package"></i> Order #{{ $order->id }}
                    </h1>
                    <p class="text-orange-100 text-sm mt-1">
                        Placed {{ $order->created_at->format('F d, Y \a\t H:i') }}
                    </p>
                </div>
                {!! $order->status_badge !!}
            </div>
        </div>

        <!-- Info banner -->
        @if($order->payment_method === 'contact')
            <div class="bg-blue-50 border-b border-blue-200 px-6 py-3 flex items-start gap-2">
                <i class="ti ti-info-circle text-blue-600 mt-0.5"></i>
                <p class="text-xs text-blue-800">
                    The book owner will contact you on <strong>{{ $order->customer_phone }}</strong>
                    to confirm delivery and arrange payment.
                </p>
            </div>
        @endif

        <div class="p-6 grid md:grid-cols-2 gap-6">
            <!-- Left: Book -->
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Book</p>
                <div class="flex gap-3">
                    <div class="w-16 h-20 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        @if($order->book && $order->book->cover_image)
                            <img src="{{ url('media/' . $order->book->cover_image) }}"
                                 alt="{{ $order->book->title }}"
                                 class="w-full h-full object-cover rounded-lg">
                        @else
                            <i class="ti ti-book text-2xl text-orange-500"></i>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 line-clamp-2">
                            {{ $order->book->title ?? 'Book unavailable' }}
                        </p>
                        <p class="text-sm text-gray-500">{{ $order->book->author ?? '' }}</p>
                        <p class="text-xs text-orange-600 mt-1 font-medium">
                            <i class="ti ti-package"></i> Hardcopy
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right: Seller -->
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Seller</p>
                <p class="font-semibold text-gray-800">{{ $order->owner_name }}</p>
                @if($order->author_id)
                    <p class="text-sm text-gray-500">Author</p>
                @elseif($order->institution_id)
                    <p class="text-sm text-gray-500">Institution</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Order details -->
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Order summary -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ti ti-receipt text-orange-600"></i> Order Summary
            </h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Unit Price</span>
                    <span class="font-medium">TSh {{ number_format($order->price_per_unit, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Quantity</span>
                    <span class="font-medium">x{{ $order->quantity }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Payment Method</span>
                    <span class="font-medium">
                        @if($order->payment_method === 'contact')
                            <i class="ti ti-phone"></i> Contact on delivery
                        @else
                            {{ ucfirst($order->payment_method) }}
                        @endif
                    </span>
                </div>
                <div class="flex justify-between py-3 border-t-2 border-gray-200">
                    <span class="font-semibold text-gray-800">Total</span>
                    <span class="font-bold text-orange-600 text-lg">TSh {{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Delivery info -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ti ti-truck-delivery text-orange-600"></i> Delivery Details
            </h2>
            <div class="space-y-3 text-sm">
                <div class="py-2 border-b border-gray-100">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Full Name</p>
                    <p class="font-medium">{{ $order->user->full_name ?? $order->user->name ?? 'N/A' }}</p>
                </div>
                <div class="py-2 border-b border-gray-100">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Phone</p>
                    <p class="font-medium">
                        <i class="ti ti-phone text-gray-400 mr-1"></i>{{ $order->customer_phone }}
                    </p>
                </div>
                <div class="py-2">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Delivery Location</p>
                    <p class="font-medium flex items-start gap-1">
                        <i class="ti ti-map-pin text-gray-400 mt-0.5"></i>
                        <span>{{ $order->shipping_address }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes (extracted from the "Name: X | Notes: Y" format) -->
    @php
        $notes = $order->notes ?? '';
        $userNotes = '';
        if (preg_match('/\|\s*Notes:\s*(.+)$/', $notes, $m)) {
            $userNotes = trim($m[1]);
        }
    @endphp
    @if($userNotes)
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                <i class="ti ti-notes text-orange-600"></i> Your Notes
            </h2>
            <p class="text-gray-700 text-sm leading-relaxed">{{ $userNotes }}</p>
        </div>
    @endif

    <!-- Timeline -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="ti ti-checklist text-orange-600"></i> Status
        </h2>
        @php
            $steps = [
                ['key' => 'pending',    'label' => 'Order Placed',       'icon' => 'ti-shopping-cart'],
                ['key' => 'processing', 'label' => 'Being Prepared',     'icon' => 'ti-loader'],
                ['key' => 'completed',  'label' => 'Delivered',          'icon' => 'ti-circle-check'],
            ];
            $currentIndex = array_search($order->status, array_column($steps, 'key'));
            if ($order->status === 'cancelled') $currentIndex = -1;
        @endphp

        @if($order->status === 'cancelled')
            <div class="bg-red-50 rounded-xl p-4 flex items-center gap-2">
                <i class="ti ti-circle-x text-red-500 text-xl"></i>
                <p class="text-red-700 font-medium text-sm">This order has been cancelled.</p>
            </div>
        @else
            <div class="flex items-start justify-between relative">
                @foreach($steps as $i => $step)
                    @php $done = $currentIndex !== false && $i <= $currentIndex; @endphp
                    <div class="flex-1 flex flex-col items-center relative z-10">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center mb-2
                            {{ $done ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                            <i class="ti {{ $step['icon'] }}"></i>
                        </div>
                        <p class="text-xs text-center {{ $done ? 'text-gray-800 font-medium' : 'text-gray-400' }}">
                            {{ $step['label'] }}
                        </p>
                    </div>
                    @if(!$loop->last)
                        <div class="absolute top-5 left-0 right-0 h-0.5 -z-0"
                             style="width: calc(100% - 5rem); margin-left: 2.5rem;
                                    background: {{ ($currentIndex !== false && $i < $currentIndex) ? '#f97316' : '#e5e7eb' }}">
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <!-- Actions -->
    <div class="flex gap-3">
        <a href="{{ url('/my-orders') }}"
           class="flex-1 bg-white border-2 border-gray-200 text-gray-700 py-3 rounded-xl font-medium text-center hover:bg-gray-50 transition">
            <i class="ti ti-list"></i> All My Orders
        </a>
        <a href="{{ url('/library') }}"
           class="flex-1 bg-gradient-to-r from-orange-500 to-amber-600 text-white py-3 rounded-xl font-semibold text-center hover:shadow-lg transition">
            <i class="ti ti-books"></i> Continue Browsing
        </a>
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