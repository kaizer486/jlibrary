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
    <!-- ========================================== -->
    <!-- PAYMENT CONFIRMATION                       -->
    <!-- ========================================== -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="ti ti-coin text-orange-600"></i> Payment Status
        </h2>

        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 rounded-xl p-3 text-sm text-green-800 flex items-center gap-2">
                <i class="ti ti-circle-check"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-3 text-sm text-red-800 flex items-center gap-2">
                <i class="ti ti-alert-circle"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Current state badge -->
        <div class="flex items-center gap-3 mb-4">
            {!! $order->payment_badge !!}
            @if($order->isBuyerConfirmed() && $order->payment_confirmed_by_buyer_at)
                <span class="text-xs text-emerald-600">
                    Confirmed {{ $order->payment_confirmed_by_buyer_at->diffForHumans() }}
                </span>
            @elseif($order->isSellerMarkedPaid() && $order->payment_received_at)
                <span class="text-xs text-yellow-600">
                    Seller marked {{ $order->payment_received_at->diffForHumans() }}
                </span>
            @endif
        </div>

        {{-- ============================================ --}}
        {{-- SCENARIO 1: Seller marked paid — ask buyer --}}
        {{-- ============================================ --}}
        @if($order->canBuyerRespond())
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="ti ti-help-circle text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-yellow-900 mb-1">
                            Did you pay TSh {{ number_format($order->total, 2) }} to the seller?
                        </p>
                        <p class="text-xs text-yellow-800 mb-3">
                            The seller has marked this payment as <strong>received</strong>.
                            Please confirm on your side so the order can be fully settled.
                            If you did not pay, please report it so we can investigate.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-2">
                            <!-- YES -->
                            <form method="POST"
                                  action="{{ route('library.order.confirm-payment', $order->id) }}"
                                  onsubmit="return confirm('Confirm that you paid TSh {{ number_format($order->total, 2) }} to the seller?')"
                                  class="flex-1">
                                @csrf
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2.5 rounded-xl transition">
                                    <i class="ti ti-circle-check"></i> Yes, I Paid
                                </button>
                            </form>

                            <!-- NO -->
                            <button type="button"
                                    onclick="document.getElementById('disputeForm').classList.toggle('hidden')"
                                    class="flex-1 inline-flex items-center justify-center gap-2 bg-white border-2 border-red-300 hover:bg-red-50 text-red-700 font-semibold px-4 py-2.5 rounded-xl transition">
                                <i class="ti ti-alert-triangle"></i> No, I Didn't Pay
                            </button>
                        </div>

                        <!-- Dispute form (hidden by default) -->
                        <div id="disputeForm" class="hidden mt-3 pt-3 border-t border-yellow-300">
                            <form method="POST"
                                  action="{{ route('library.order.dispute-payment', $order->id) }}">
                                @csrf
                                <label class="block text-xs font-semibold text-red-800 mb-2">
                                    Please tell us what happened <span class="text-red-500">*</span>
                                </label>
                                <textarea name="reason" rows="3" required minlength="5" maxlength="1000"
                                          class="w-full px-3 py-2 border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm"
                                          placeholder="e.g. I already paid but the amount wasn't what we agreed, or I didn't receive the correct book..."></textarea>
                                @error('reason') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                <div class="flex gap-2 mt-2">
                                    <button type="submit"
                                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
                                        <i class="ti ti-send"></i> Report Dispute
                                    </button>
                                    <button type="button"
                                            onclick="document.getElementById('disputeForm').classList.add('hidden')"
                                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                                        Cancel
                                    </button>
                                </div>
                                <p class="text-[10px] text-red-600 mt-2">
                                    <i class="ti ti-info-circle"></i>
                                    Your dispute will be reviewed by the platform team. The seller will be notified.
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        {{-- ============================================ --}}
        {{-- SCENARIO 2: Payment fully confirmed        --}}
        {{-- ============================================ --}}
        @elseif($order->isBuyerConfirmed())
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <i class="ti ti-circle-check text-emerald-500 text-2xl flex-shrink-0"></i>
                    <div>
                        <p class="font-semibold text-emerald-900 mb-1">Payment Confirmed ✅</p>
                        <p class="text-xs text-emerald-800">
                            Thank you for confirming your payment. This order is now fully settled.
                        </p>
                    </div>
                </div>
            </div>

        {{-- ============================================ --}}
        {{-- SCENARIO 3: Disputed — awaiting review     --}}
        {{-- ============================================ --}}
        @elseif($order->isPaymentDisputed())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <i class="ti ti-alert-triangle text-red-500 text-2xl flex-shrink-0"></i>
                    <div>
                        <p class="font-semibold text-red-900 mb-1">Payment Disputed</p>
                        <p class="text-xs text-red-800 mb-2">
                            You reported an issue with this payment. The platform team has been notified and will contact you to resolve it.
                        </p>
                        @if($order->payment_dispute_reason)
                            <div class="bg-white/60 rounded p-2 mt-2">
                                <p class="text-[10px] uppercase tracking-wide text-red-600 font-semibold mb-0.5">Your reason</p>
                                <p class="text-xs text-red-900">{{ $order->payment_dispute_reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        {{-- ============================================ --}}
        {{-- SCENARIO 4: Still unpaid                  --}}
        {{-- ============================================ --}}
        @elseif($order->isPaymentUnpaid())
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <i class="ti ti-clock text-gray-500 text-2xl flex-shrink-0"></i>
                    <div>
                        <p class="font-semibold text-gray-900 mb-1">Awaiting Payment</p>
                        <p class="text-xs text-gray-700">
                            Please pay <strong>TSh {{ number_format($order->total, 2) }}</strong> to the seller directly
                            (as agreed by phone). Once the seller receives the payment, they will mark it as paid here,
                            and you'll be asked to confirm.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Payment timeline -->
        <div class="mt-5 pt-5 border-t border-gray-100">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold mb-3">Payment Timeline</p>
            <div class="space-y-3">
                @foreach($order->payment_timeline as $step)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                    {{ $step['done'] ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                            <i class="ti {{ $step['icon'] }} text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm {{ $step['done'] ? 'text-gray-800' : 'text-gray-400' }}">
                                {{ $step['label'] }}
                            </p>
                            @if($step['timestamp'])
                                <p class="text-xs text-gray-500">
                                    {{ $step['timestamp']->format('M d, Y \a\t H:i') }}
                                    <span class="text-gray-400">· {{ $step['timestamp']->diffForHumans() }}</span>
                                </p>
                            @else
                                <p class="text-xs text-gray-400">Pending</p>
                            @endif
                        </div>
                    </div>
                @endforeach
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