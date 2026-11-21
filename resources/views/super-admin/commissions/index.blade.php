@extends('layouts.super-admin')

@section('title', 'Commissions')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ tab: 'requests' }">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
              
                Commission Management
            </h1>
            <p class="text-sm text-gray-500 mt-1">Set platform rates, track earnings, and verify seller payments.</p>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm">
            <i class="ti ti-circle-check text-lg"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm">
            <i class="ti ti-alert-triangle text-lg"></i> {{ session('warning') }}
        </div>
    @endif

    <!-- Top Stats & Compact Settings -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        
        <!-- Pending -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="ti ti-clock text-2xl"></i>
            </div>
            <div>
                <p class="text-[11px] text-gray-500 uppercase tracking-wide font-bold">Pending Requests</p>
                <p class="text-2xl font-bold text-gray-800">{{ $pendingRequests->total() }}</p>
            </div>
        </div>

        <!-- Collected -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="ti ti-wallet text-2xl"></i>
            </div>
            <div>
                <p class="text-[11px] text-gray-500 uppercase tracking-wide font-bold">Total Collected</p>
                <p class="text-2xl font-bold text-gray-800">TSh {{ number_format($history->where('commission_status', 'collected')->sum('platform_commission'), 0) }}</p>
            </div>
        </div>

        <!-- Global Setting (Professional Compact) -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-chart-pie text-2xl"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-500 uppercase tracking-wide font-bold">Global Commission Rate</p>
                    <p class="text-xl font-bold text-gray-800">{{ $percentage }}%</p>
                </div>
            </div>
            
            <!-- Inline Form -->
            <form action="{{ route('super-admin.commissions.update-setting') }}" method="POST" class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Update Rate</label>
                <div class="flex items-center gap-2">
                    <div class="relative w-24">
                        <input type="number" name="percentage" step="0.1" min="0" max="100" 
                               value="{{ $percentage }}" required
                               class="w-full pl-3 pr-8 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 font-bold text-gray-800 text-lg">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 font-bold">%</span>
                        </div>
                    </div>
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 rounded-lg transition shadow-sm flex items-center justify-center gap-2 font-semibold text-sm">
                        <i class="ti ti-device-floppy text-lg"></i> Save
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- Tabs -->
    <div class="bg-white border border-gray-200 p-1.5 rounded-2xl inline-flex shadow-sm">
        <button @click="tab = 'requests'" 
                :class="tab === 'requests' ? 'bg-orange-50 text-orange-700 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                class="px-5 py-2.5 text-sm font-bold rounded-xl transition flex items-center gap-2">
            <i class="ti ti-receipt text-base"></i> Pending Requests
            @if($pendingRequests->total() > 0)
                <span class="bg-orange-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingRequests->total() }}</span>
            @endif
        </button>
        <button @click="tab = 'history'" 
                :class="tab === 'history' ? 'bg-orange-50 text-orange-700 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                class="px-5 py-2.5 text-sm font-bold rounded-xl transition flex items-center gap-2">
            <i class="ti ti-history text-base"></i> Payment History
        </button>
    </div>

    <!-- TAB 1: Pending Requests -->
    <div x-show="tab === 'requests'" x-cloak class="space-y-4">
        @forelse($pendingRequests as $order)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:border-orange-200 transition overflow-hidden">
                <div class="p-5 flex flex-col md:flex-row md:items-center gap-6">
                    
                    <!-- Seller & Book Info -->
                    <div class="flex-1 flex items-center gap-4">
                        <div class="w-12 h-14 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0 border border-slate-200">
                            @if($order->book && $order->book->cover_image)
                                <img src="{{ url('media/' . $order->book->cover_image) }}" class="w-full h-full object-cover rounded-lg">
                            @else
                                <i class="ti ti-book text-slate-400 text-xl"></i>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs text-orange-600 font-bold uppercase tracking-wide mb-1">Pending Verification</p>
                            <h3 class="font-bold text-gray-800 text-lg">{{ $order->author->full_name ?? 'N/A' }}</h3>
                            <p class="text-sm text-gray-500">{{ $order->book->title ?? 'Book' }} ({{ $order->book->book_code ?? '—' }})</p>
                        </div>
                    </div>

                    <!-- Payment Details Box -->
                    <div class="bg-slate-50 rounded-xl p-4 flex-1 border border-slate-100 grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-1">Amount Due</p>
                            <p class="font-bold text-orange-600 text-lg">TSh {{ number_format($order->platform_commission, 0) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-1">Method / Ref</p>
                            <p class="text-xs font-bold text-slate-800 uppercase">{{ str_replace('_', ' ', $order->commission_payment_method) }}</p>
                            <p class="text-xs font-mono text-slate-600">{{ $order->commission_transaction_id }}</p>
                        </div>
                        <div class="col-span-2 border-t border-slate-200 pt-2">
                            <p class="text-[10px] text-gray-500">
                               <i class="ti ti-calendar"></i> Paid on: {{ $order->commission_paid_at ? \Carbon\Carbon::parse($order->commission_paid_at)->timezone('Africa/Dar_es_Salaam')->format('M d, Y H:i') : '—' }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex md:flex-col gap-2 flex-shrink-0">
                        <form action="{{ route('super-admin.commissions.verify', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" name="action" value="approve" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                                <i class="ti ti-check"></i> Approve
                            </button>
                        </form>
                        <form action="{{ route('super-admin.commissions.verify', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" name="action" value="reject" class="w-full bg-white text-red-600 border border-red-200 hover:bg-red-50 px-6 py-2.5 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2">
                                <i class="ti ti-x"></i> Reject
                            </button>
                        </form>
                        <a href="{{ route('super-admin.hardcopy-orders.show', $order->id) }}" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-center px-6 py-2.5 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2">
                            <i class="ti ti-eye"></i> View Order
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ti ti-shield-check text-3xl text-emerald-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">All Caught Up!</h3>
                <p class="text-gray-500">There are no pending commission requests to verify.</p>
            </div>
        @endforelse
        
        @if($pendingRequests->hasPages())
            <div class="mt-4">{{ $pendingRequests->links() }}</div>
        @endif
    </div>

    <!-- TAB 2: History -->
    <div x-show="tab === 'history'" x-cloak>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="text-left px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Order / Book</th>
                            <th class="text-left px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Seller</th>
                            <th class="text-left px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Amount</th>
                            <th class="text-left px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="text-left px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                            <th class="text-right px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($history as $order)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-slate-800">{{ $order->book->title ?? 'Book' }}</p>
                                    <p class="text-xs text-slate-400 font-mono">{{ $order->book->book_code ?? '—' }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ $order->author->full_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 font-bold text-slate-800">TSh {{ number_format($order->platform_commission, 2) }}</td>
                                <td class="px-6 py-4">
                                    @if($order->commission_status === 'collected')
                                        <span class="inline-flex items-center gap-1 text-[11px] bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-md font-bold uppercase">
                                            <i class="ti ti-check"></i> Collected
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] bg-red-50 text-red-700 border border-red-200 px-2.5 py-1 rounded-md font-bold uppercase">
                                            <i class="ti ti-x"></i> Rejected
                                        </span>
                                    @endif
                                </td>
                               <td class="px-6 py-4 text-xs font-medium text-slate-500">{{ $order->commission_paid_at ? \Carbon\Carbon::parse($order->commission_paid_at)->timezone('Africa/Dar_es_Salaam')->format('M d, Y') : '—' }}</td>
                                
                                <!-- ACTION BUTTONS -->
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        <a href="{{ route('super-admin.hardcopy-orders.show', $order->id) }}" 
                                           class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-200 transition bg-slate-100" 
                                           title="View Order">
                                            <i class="ti ti-eye"></i>
                                        </a>

                                        <form action="{{ route('super-admin.commissions.delete-log', $order->id) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this log permanently?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-red-500 hover:bg-red-100 transition bg-red-50" title="Delete Log">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    <i class="ti ti-folder-open text-3xl text-slate-300 mb-2 block"></i>
                                    <p>No commission history found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($history->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">{{ $history->links() }}</div>
            @endif
        </div>
    </div>

</div>
@endsection