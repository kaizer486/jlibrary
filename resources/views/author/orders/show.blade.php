@extends('layouts.author')

@section('title', 'Order ' . $order->id)
@section('page-title', 'Order ' . $order->id)

@section('content')

<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-5xl">

        <!-- Back -->
        <div class="mb-6">
            <a href="{{ route('seller.orders') }}"
               class="text-slate-600 hover:text-slate-800 flex items-center gap-2 font-medium">
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
        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
                <i class="ti ti-alert-circle text-2xl text-red-500"></i>
                <p class="text-red-800 text-sm">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Header card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-orange-600 to-amber-600 px-6 py-4 border-b-2 border-orange-400/30">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="ti ti-package"></i> {{ $order->book->book_code ?? 'Order ' . $order->id }}
                        </h1>
                        <p class="text-orange-100 text-sm mt-1">
                            Placed {{ $order->created_at->timezone('Africa/Dar_es_Salaam')->format('F d, Y \a\t H:i') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        {!! $order->status_badge !!}
                        
                        <a href="{{ route('orders.invoice', $order->id) }}"
                           class="inline-flex items-center gap-1.5 bg-white/20 hover:bg-white/30 text-white backdrop-blur-sm border border-white/30 px-3 py-1.5 rounded-lg text-sm font-semibold transition shadow-sm"
                           title="Download Invoice">
                            <i class="ti ti-file-invoice text-base"></i> Invoice
                        </a>
                    </div>
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
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-2 font-semibold">Book</p>
                    <div class="flex gap-3">
                        <div class="w-16 h-20 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden border border-slate-200">
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
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-2 font-semibold">Buyer</p>
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
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
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

                    <!-- COMMISSION ROW -->
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-slate-500">Commission</span>
                        <span class="text-right">
                            @if($order->hasCommission())
                                <span class="font-semibold text-slate-700 block">
                                    TSh {{ number_format($order->platform_commission, 2) }}
                                </span>
                                <span class="mt-0.5 inline-block">{!! $order->commission_badge !!}</span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                                    <i class="ti ti-clock text-xs"></i> Awaiting
                                </span>
                            @endif
                        </span>
                    </div>

                    <!-- PAYOUT ROW -->
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="font-semibold text-slate-800">Your Payout</span>
                        <span class="font-bold text-emerald-700 text-lg">
                            TSh {{ number_format($order->ownerPayout(), 2) }}
                        </span>
                    </div>

                    <div class="flex justify-between pt-3 border-t-2 border-slate-200">
                        <span class="font-semibold text-slate-800">Order Total</span>
                        <span class="font-bold text-orange-600 text-lg">TSh {{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
                <h2 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-truck-delivery text-orange-600"></i> Delivery Details
                </h2>
                <div class="space-y-3 text-sm">
                    <div class="py-2 border-b border-slate-100">
                        <p class="text-xs text-slate-400 uppercase tracking-wide mb-1 font-semibold">Full Name</p>
                        <p class="font-medium">{{ $order->user->full_name ?? 'N/A' }}</p>
                    </div>
                    <div class="py-2 border-b border-slate-100">
                        <p class="text-xs text-slate-400 uppercase tracking-wide mb-1 font-semibold">Phone</p>
                        <p class="font-medium flex items-center gap-1">
                            <i class="ti ti-phone text-slate-400"></i> {{ $order->customer_phone }}
                        </p>
                    </div>
                    <div class="py-2">
                        <p class="text-xs text-slate-400 uppercase tracking-wide mb-1 font-semibold">Delivery Location</p>
                        <p class="font-medium flex items-start gap-1">
                            <i class="ti ti-map-pin text-slate-400 mt-0.5"></i>
                            <span>{{ $order->shipping_address }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buyer's notes (Moved up for better flow) -->
        @php
            $userNotes = '';
            if ($order->notes && preg_match('/\|\s*Notes:\s*(.+)$/', $order->notes, $m)) {
                $userNotes = trim($m[1]);
            }
        @endphp
        @if($userNotes)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 mb-6">
                <h2 class="font-semibold text-slate-800 mb-2 flex items-center gap-2">
                    <i class="ti ti-notes text-orange-600"></i> Buyer's Notes
                </h2>
                <p class="text-slate-700 text-sm leading-relaxed bg-gray-50 p-3 rounded-lg border border-slate-100">{{ $userNotes }}</p>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- PAYMENT STATUS CARD                        -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 mb-6">
            <h2 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <i class="ti ti-coin text-orange-600"></i> Payment Status
            </h2>

            <!-- Current state + action buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div class="flex items-center gap-3">
                    {!! $order->payment_badge !!}
                </div>

                <div class="flex items-center gap-2">
                    @if($order->canSellerMarkPaid())
                        <form method="POST"
                              action="{{ route('author.orders.mark-paid', $order->id) }}"
                              onsubmit="return confirm('Mark this order as PAID?\n\nThe buyer will be notified to confirm.')">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-5 py-2.5 rounded-xl transition shadow-sm hover:shadow-md">
                                <i class="ti ti-check"></i> Mark as Paid
                            </button>
                        </form>
                    @elseif($order->canSellerUnmarkPaid())
                        <form method="POST"
                              action="{{ route('author.orders.unmark-paid', $order->id) }}"
                              onsubmit="return confirm('Un-mark this payment?\n\nThe buyer will be notified.')">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-xl transition">
                                <i class="ti ti-arrow-back-up"></i> Unmark Paid
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Status explanation -->
            @if($order->isPaymentUnpaid())
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm text-slate-700">
                    <p class="font-semibold mb-1"><i class="ti ti-info-circle"></i> Waiting for buyer payment</p>
                    <p class="text-xs">
                        Once you receive the money from the buyer, click <strong>Mark as Paid</strong> above.
                        The buyer will then be asked to confirm on their side.
                    </p>
                </div>
            @elseif($order->isSellerMarkedPaid())
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-sm text-yellow-800">
                    <p class="font-semibold mb-1"><i class="ti ti-clock"></i> Awaiting buyer confirmation</p>
                    <p class="text-xs">
                        You've marked this payment as received. The buyer will confirm on their side.
                        If you made a mistake, click <strong>Unmark Paid</strong>.
                    </p>
                </div>
            @elseif($order->isBuyerConfirmed())
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-sm text-emerald-800">
                    <p class="font-semibold mb-1"><i class="ti ti-circle-check"></i> Payment fully settled</p>
                    <p class="text-xs">
                        The buyer confirmed the payment. This order's payment is now locked and cannot be un-marked.
                    </p>
                </div>
            @elseif($order->isPaymentDisputed())
                <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-sm text-red-800">
                    <p class="font-semibold mb-1"><i class="ti ti-alert-triangle"></i> Payment disputed by buyer</p>
                    <p class="text-xs mb-2">
                        The buyer has disputed your payment claim. This needs admin review before it can be resolved.
                    </p>
                    @if($order->payment_dispute_reason)
                        <div class="bg-white/60 rounded-lg p-2 mt-2 border border-red-100">
                            <p class="text-[10px] uppercase tracking-wide text-red-600 font-semibold mb-0.5">Buyer's reason</p>
                            <p class="text-xs">{{ $order->payment_dispute_reason }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Payment timeline -->
            <div class="mt-6 pt-6 border-t-2 border-slate-100">
                <p class="text-xs uppercase tracking-wide text-slate-400 font-semibold mb-4">Payment Timeline</p>
                <div class="space-y-4">
                    @foreach($order->payment_timeline as $step)
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                        {{ $step['done'] ? 'bg-emerald-500 text-white shadow-sm' : 'bg-slate-100 text-slate-400' }}">
                                <i class="ti {{ $step['icon'] }} text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0 pt-0.5">
                                <p class="font-medium text-sm {{ $step['done'] ? 'text-slate-800' : 'text-slate-400' }}">
                                    {{ $step['label'] }}
                                </p>
                                @if($step['timestamp'])
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ $step['timestamp']->timezone('Africa/Dar_es_Salaam')->format('M d, Y \a\t H:i') }}
                                        <span class="text-slate-400">· {{ $step['timestamp']->diffForHumans() }}</span>
                                    </p>
                                @else
                                    <p class="text-xs text-slate-400 mt-0.5">Pending</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Update status -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 mb-6">
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
                            <option value="pending"    {{ $order->status === 'pending'    ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing (Preparing)</option>
                            <option value="completed"  {{ $order->status === 'completed'  ? 'selected' : '' }}>Completed (Delivered)</option>
                            <option value="cancelled"  {{ $order->status === 'cancelled'  ? 'selected' : '' }}>Cancelled</option>
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
            <a href="{{ route('seller.orders') }}"
               class="flex-1 bg-white border border-slate-200/80 text-slate-700 py-3 rounded-xl font-medium text-center hover:bg-slate-50 transition shadow-sm">
                <i class="ti ti-arrow-left"></i> Back to Orders
            </a>
            
            <a href="{{ route('orders.invoice', $order->id) }}"
               class="flex-1 bg-purple-600 text-white py-3 rounded-xl font-semibold text-center hover:bg-purple-700 transition shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                <i class="ti ti-file-invoice text-lg"></i> Download Invoice
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