@extends('layouts.super-admin')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Back -->
    <a href="{{ route('super-admin.hardcopy-orders.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-800 font-medium">
        <i class="ti ti-arrow-left"></i> Back to Hardcopy Orders
    </a>

    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-xl font-bold flex items-center gap-2">
                        <i class="ti ti-package"></i> Order #{{ $order->id }}
                    </h1>
                    <p class="text-purple-100 text-sm mt-1">
                        Placed {{ $order->created_at->format('F d, Y \a\t H:i') }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    {!! $order->status_badge !!}
                    
                    {{-- ADDED: Invoice Download Button for Admin --}}
                    <a href="{{ route('orders.invoice', $order->id) }}"
                       class="inline-flex items-center gap-1 bg-white/20 hover:bg-white/30 text-white backdrop-blur-sm border border-white/30 px-3 py-1.5 rounded-lg text-sm font-medium transition"
                       title="Download Invoice">
                        <i class="ti ti-file-invoice"></i> Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Order info + Owner contact -->
    <div class="grid md:grid-cols-2 gap-6">

        <!-- Order details -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="ti ti-receipt text-purple-600"></i> Order Details
            </h2>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Book</span>
                    <span class="font-semibold text-right text-gray-800">{{ $order->book->title ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Quantity</span>
                    <span class="font-medium">× {{ $order->quantity }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Unit Price</span>
                    <span class="font-medium">TSh {{ number_format($order->price_per_unit, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Total</span>
                    <span class="font-bold text-emerald-600">TSh {{ number_format($order->total, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Payment</span>
                    <span class="font-medium">
                        @if($order->payment_method === 'contact')
                            <i class="ti ti-phone"></i> Contact on delivery
                        @else
                            {{ ucfirst($order->payment_method) }}
                        @endif
                    </span>
                </div>
            </div>

            @if($order->shipping_address)
                <div class="pt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Delivery Location</p>
                    <p class="text-sm text-gray-700">
                        <i class="ti ti-map-pin text-orange-500"></i> {{ $order->shipping_address }}
                    </p>
                </div>
            @endif

            @if($order->notes && preg_match('/\|\s*Notes:\s*(.+)$/', $order->notes, $m))
                <div class="pt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Buyer's Notes</p>
                    <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded-lg">{{ trim($m[1]) }}</p>
                </div>
            @endif
        </div>

        <!-- Owner + Buyer contact -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="ti ti-user-check text-purple-600"></i> Contacts
            </h2>

            <!-- Book Owner -->
            <div class="bg-purple-50 rounded-xl p-4 border border-purple-100">
                <p class="text-xs text-purple-600 uppercase mb-1 font-bold">
                    {{ $order->author_id ? 'Author (Book Owner)' : 'Institution (Book Owner)' }}
                </p>
                <p class="font-bold text-gray-800 text-lg">{{ $order->owner_name }}</p>

                @if($order->author && $order->author->email)
                    <a href="mailto:{{ $order->author->email }}"
                       class="text-sm text-purple-700 hover:text-purple-900 mt-2 inline-block">
                        <i class="ti ti-mail text-xs"></i> {{ $order->author->email }}
                    </a>
                @endif

                @if($order->author && $order->author->mpesa_phone)
                    <a href="tel:{{ $order->author->mpesa_phone }}"
                       class="text-sm text-purple-700 hover:text-purple-900 mt-1 inline-block ml-3">
                        <i class="ti ti-phone text-xs"></i> {{ $order->author->mpesa_phone }}
                    </a>
                @endif
            </div>

            <!-- Buyer -->
            <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                <p class="text-xs text-blue-600 uppercase mb-1 font-bold">Buyer</p>
                <p class="font-bold text-gray-800">{{ $order->user->full_name ?? 'Guest' }}</p>

                @if($order->user && $order->user->email)
                    <a href="mailto:{{ $order->user->email }}"
                       class="text-sm text-blue-700 hover:text-blue-900 mt-2 inline-block">
                        <i class="ti ti-mail text-xs"></i> {{ $order->user->email }}
                    </a>
                @endif

                @if($order->customer_phone)
                    <a href="tel:{{ $order->customer_phone }}"
                       class="text-sm text-blue-700 hover:text-blue-900 mt-1 inline-block ml-3">
                        <i class="ti ti-phone text-xs"></i> {{ $order->customer_phone }}
                    </a>
                @endif
            </div>

            <div class="bg-orange-50 rounded-xl p-3 text-xs text-orange-800 flex items-start gap-2 border border-orange-100">
                <i class="ti ti-info-circle mt-0.5 flex-shrink-0"></i>
                <span>Contact the owner to negotiate the platform commission for this order. Save the agreed amount below.</span>
            </div>
        </div>
    </div>
    
    <!-- ========================================== -->
    <!-- PAYMENT CONFIRMATION TIMELINE              -->
    <!-- ========================================== -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="ti ti-coin text-purple-600"></i> Payment Confirmation
            </h2>
            {!! $order->payment_badge !!}
        </div>

        <!-- Current state summary -->
        @if($order->isPaymentUnpaid())
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm text-slate-700 mb-4">
                <p class="font-semibold mb-1"><i class="ti ti-clock"></i> Waiting for payment</p>
                <p class="text-xs">
                    The buyer has not yet paid the seller. No payment has been marked.
                </p>
            </div>
        @elseif($order->isSellerMarkedPaid())
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-sm text-yellow-800 mb-4">
                <p class="font-semibold mb-1"><i class="ti ti-clock"></i> Seller marked paid — awaiting buyer confirmation</p>
                <p class="text-xs">
                    The seller marked this payment as received on
                    <strong>{{ $order->payment_received_at->format('M d, Y \a\t H:i') }}</strong>.
                    The buyer has not yet confirmed.
                </p>
            </div>
        @elseif($order->isBuyerConfirmed())
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-sm text-emerald-800 mb-4">
                <p class="font-semibold mb-1"><i class="ti ti-circle-check"></i> Payment fully settled</p>
                <p class="text-xs">
                    Both parties agreed. Buyer confirmed on
                    <strong>{{ $order->payment_confirmed_by_buyer_at->format('M d, Y \a\t H:i') }}</strong>.
                </p>
            </div>
        @elseif($order->isPaymentDisputed())
            <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-sm text-red-800 mb-4">
                <p class="font-semibold mb-1"><i class="ti ti-alert-triangle"></i> Payment disputed — needs review</p>
                <p class="text-xs mb-2">
                    The buyer disputed the seller's payment claim. Review the reason below and contact both parties to resolve.
                </p>
                @if($order->payment_dispute_reason)
                    <div class="bg-white/60 rounded-lg p-2 border border-red-100">
                        <p class="text-[10px] uppercase tracking-wide text-red-600 font-semibold mb-0.5">Buyer's reason</p>
                        <p class="text-xs text-red-900">{{ $order->payment_dispute_reason }}</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Timeline -->
        <div class="space-y-3">
            @foreach($order->payment_timeline as $step)
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0
                                {{ $step['done'] ? 'bg-emerald-500 text-white shadow-sm' : 'bg-gray-100 text-gray-400' }}">
                        <i class="ti {{ $step['icon'] }}"></i>
                    </div>
                    <div class="flex-1 min-w-0 pt-1">
                        <p class="font-medium text-sm {{ $step['done'] ? 'text-gray-800' : 'text-gray-400' }}">
                            {{ $step['label'] }}
                        </p>
                        @if($step['timestamp'])
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $step['timestamp']->format('M d, Y \a\t H:i') }}
                                <span class="text-gray-400">· {{ $step['timestamp']->diffForHumans() }}</span>
                            </p>
                        @else
                            <p class="text-xs text-gray-400 mt-0.5">Pending</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Contact actions -->
        @if($order->isSellerMarkedPaid() || $order->isPaymentDisputed())
            <div class="mt-5 pt-5 border-t border-gray-100">
                <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold mb-2">
                    <i class="ti ti-phone"></i> Follow up
                </p>
                <div class="grid sm:grid-cols-2 gap-2">
                    @if($order->user && $order->user->email)
                        <a href="mailto:{{ $order->user->email }}?subject=Order #{{ $order->id }} - Payment confirmation"
                           class="flex items-center justify-center gap-2 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 text-xs font-medium px-3 py-2 rounded-lg transition">
                            <i class="ti ti-mail"></i> Email Buyer
                        </a>
                    @endif
                    @if($order->customer_phone)
                        <a href="tel:{{ $order->customer_phone }}"
                           class="flex items-center justify-center gap-2 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 text-xs font-medium px-3 py-2 rounded-lg transition">
                            <i class="ti ti-phone"></i> Call Buyer ({{ $order->customer_phone }})
                        </a>
                    @endif
                    @if($order->author && $order->author->email)
                        <a href="mailto:{{ $order->author->email }}?subject=Order #{{ $order->id }} - Payment issue"
                           class="flex items-center justify-center gap-2 bg-purple-50 hover:bg-purple-100 border border-purple-200 text-purple-700 text-xs font-medium px-3 py-2 rounded-lg transition">
                            <i class="ti ti-mail"></i> Email Seller
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Danger zone: Delete order (admin only) -->
        <div class="mt-5 pt-5 border-t border-red-100">
            <form method="POST"
                  action="{{ route('super-admin.hardcopy-orders.delete', $order->id) }}"
                  onsubmit="return confirm('DELETE order #{{ $order->id }} permanently?\n\nThis cannot be undone. The stock will be restored and both parties will be notified.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 border border-red-300 text-red-700 font-semibold px-4 py-2.5 rounded-xl transition text-sm">
                    <i class="ti ti-trash"></i> Delete Order #{{ $order->id }}
                </button>
                <p class="text-[10px] text-red-600 text-center mt-2">
                    <i class="ti ti-alert-triangle"></i>
                    Restores stock. Buyer and seller will be notified.
                </p>
            </form>
        </div>
    </div>
    
    <!-- Commission form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="ti ti-coin text-purple-600"></i> Commission &amp; Payout
        </h2>

        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 rounded-xl p-3 text-sm text-green-800 flex items-center gap-2">
                <i class="ti ti-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-3 text-sm text-red-800">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('super-admin.hardcopy-orders.update-commission', $order->id) }}">
            @csrf

            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Platform Commission (TSh) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="platform_commission" step="0.01" min="0"
                           max="{{ $order->total }}"
                           value="{{ old('platform_commission', $order->platform_commission) }}"
                           placeholder="0.00" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    <p class="text-xs text-gray-400 mt-1">
                        Max: TSh {{ number_format($order->total, 2) }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Commission Status <span class="text-red-500">*</span>
                    </label>
                    <select name="commission_status" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        <option value="pending"    {{ $order->commission_status === 'pending'    ? 'selected' : '' }}>Pending</option>
                        <option value="negotiated" {{ $order->commission_status === 'negotiated' ? 'selected' : '' }}>Negotiated</option>
                        <option value="collected"  {{ $order->commission_status === 'collected'  ? 'selected' : '' }}>Collected</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Negotiation Notes (internal)
                </label>
                <textarea name="commission_notes" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                          placeholder="e.g. Agreed on 15% commission over call on Nov 15, confirmed via WhatsApp">{{ old('commission_notes', $order->commission_notes) }}</textarea>
            </div>

            <!-- Preview -->
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-4 grid grid-cols-3 gap-3 text-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Total</p>
                    <p class="text-sm font-semibold text-gray-800">TSh {{ number_format($order->total, 0) }}</p>
                </div>
                <div>
                    <p class="text-xs text-purple-600 mb-1">Commission</p>
                    <p class="text-sm font-bold text-purple-700">
                        TSh {{ number_format($order->commissionAmount(), 0) }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-emerald-600 mb-1">Owner Payout</p>
                    <p class="text-sm font-bold text-emerald-700">
                        TSh {{ number_format($order->ownerPayout(), 0) }}
                    </p>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition flex items-center justify-center gap-2">
                <i class="ti ti-device-floppy"></i> Save Commission
            </button>
        </form>
    </div>

</div>
@endsection