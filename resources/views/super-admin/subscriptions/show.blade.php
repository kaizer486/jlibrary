@extends('layouts.super-admin')

@section('title', 'Subscription Details')

@section('content')
<div class="mb-6">
    <a href="{{ route('super-admin.subscriptions.index', request()->query()) }}" class="text-purple-400 hover:text-purple-300 transition inline-flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Back to Subscriptions
    </a>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <!-- ========================================== -->
    <!-- SUBSCRIPTION INFO                         -->
    <!-- ========================================== -->
    <div class="lg:col-span-2">
        <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="ti ti-subscription"></i> Subscription Details
                    </h2>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold 
                        @if($subscription->status === 'active') bg-emerald-500/20 text-emerald-400
                        @elseif($subscription->status === 'pending') bg-yellow-500/20 text-yellow-400
                        @elseif($subscription->status === 'expired') bg-red-500/20 text-red-400
                        @else bg-slate-500/20 text-slate-400 @endif">
                        {{ ucfirst($subscription->status) }}
                    </span>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-slate-400">Institution</p>
                        <p class="text-white font-semibold">{{ $institution->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Plan</p>
                        <p class="text-white font-semibold capitalize">{{ $subscription->plan }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Amount</p>
                        <p class="text-white font-semibold">TSh {{ number_format($subscription->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Payment Method</p>
                        <p class="text-white font-semibold capitalize">{{ $subscription->payment_method ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Starts At</p>
                        <p class="text-white font-semibold">{{ $subscription->starts_at ? $subscription->starts_at->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Ends At</p>
                        <p class="text-white font-semibold">{{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y') : 'N/A' }}</p>
                        @if($subscription->ends_at && $subscription->status === 'active')
                            <p class="text-xs {{ $subscription->ends_at->diffInDays(now()) < 7 ? 'text-red-400' : 'text-emerald-400' }}">
                                {{ $subscription->ends_at->diffInDays(now()) }} days remaining
                            </p>
                        @endif
                    </div>
                </div>
                
                @if($subscription->transaction_reference)
                <div class="mt-4 p-3 bg-slate-800/50 rounded-lg">
                    <p class="text-xs text-slate-400">Transaction Reference</p>
                    <p class="text-white font-mono text-sm">{{ $subscription->transaction_reference }}</p>
                </div>
                @endif
                
                @if($subscription->mpesa_response_description)
                <div class="mt-4 p-3 bg-slate-800/50 rounded-lg">
                    <p class="text-xs text-slate-400">Notes</p>
                    <p class="text-white text-sm">{{ $subscription->mpesa_response_description }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- ========================================== -->
        <!-- SUBSCRIPTION HISTORY                      -->
        <!-- ========================================== -->
        <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden mt-6">
            <div class="px-6 py-4 border-b border-slate-700">
                <h3 class="font-semibold text-white flex items-center gap-2">
                    <i class="ti ti-history text-purple-400"></i> Subscription History
                </h3>
            </div>
            <div class="p-4">
                @if($institutionSubscriptions->count() > 0)
                    <div class="space-y-2">
                        @foreach($institutionSubscriptions as $sub)
                        <div class="flex items-center justify-between p-3 bg-slate-800/50 rounded-lg">
                            <div>
                                <p class="text-white font-medium capitalize">{{ $sub->plan }}</p>
                                <p class="text-xs text-slate-400">TSh {{ number_format($sub->amount, 2) }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                    @if($sub->status === 'active') bg-emerald-500/20 text-emerald-400
                                    @elseif($sub->status === 'pending') bg-yellow-500/20 text-yellow-400
                                    @elseif($sub->status === 'expired') bg-red-500/20 text-red-400
                                    @else bg-slate-500/20 text-slate-400 @endif">
                                    {{ ucfirst($sub->status) }}
                                </span>
                                <p class="text-xs text-slate-500 mt-1">{{ $sub->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-slate-400 text-center py-4">No history available</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- ========================================== -->
    <!-- ACTIONS PANEL                             -->
    <!-- ========================================== -->
    <div class="lg:col-span-1">
        <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden sticky top-4">
            <div class="px-6 py-4 border-b border-slate-700">
                <h3 class="font-semibold text-white flex items-center gap-2">
                    <i class="ti ti-tools text-purple-400"></i> Actions
                </h3>
            </div>
            <div class="p-6 space-y-3">
                @if($subscription->status === 'pending')
                    <form method="POST" action="{{ route('super-admin.subscriptions.activate', $subscription) }}">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-xl transition font-semibold flex items-center justify-center gap-2">
                            <i class="ti ti-check"></i> Activate Subscription
                        </button>
                    </form>
                @endif
                
                @if($subscription->status === 'active')
                    <a href="{{ route('super-admin.subscriptions.override', $subscription) }}" 
                       class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2.5 rounded-xl transition font-semibold flex items-center justify-center gap-2 block text-center">
                        <i class="ti ti-settings"></i> Override Plan
                    </a>
                    
                    <form method="POST" action="{{ route('super-admin.subscriptions.cancel', $subscription) }}">
                        @csrf
                        <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2.5 rounded-xl transition font-semibold flex items-center justify-center gap-2" onclick="return confirm('Cancel this subscription?')">
                            <i class="ti ti-ban"></i> Cancel Subscription
                        </button>
                    </form>
                @endif
                
                @if($subscription->status !== 'expired' && $subscription->status !== 'cancelled')
                    <form method="POST" action="{{ route('super-admin.subscriptions.expire', $subscription) }}">
                        @csrf
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-xl transition font-semibold flex items-center justify-center gap-2" onclick="return confirm('Mark this subscription as expired?')">
                            <i class="ti ti-clock"></i> Mark as Expired
                        </button>
                    </form>
                @endif
                
                <div class="border-t border-slate-700 pt-3 mt-3">
                    <a href="{{ route('super-admin.institutions.show', $institution) }}" 
                       class="w-full bg-slate-700 hover:bg-slate-600 text-white py-2.5 rounded-xl transition font-semibold flex items-center justify-center gap-2 block text-center">
                        <i class="ti ti-building"></i> View Institution
                    </a>
                </div>
            </div>
        </div>
        
        <!-- ========================================== -->
        <!-- QUICK STATS                               -->
        <!-- ========================================== -->
        <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden mt-6">
            <div class="px-6 py-4 border-b border-slate-700">
                <h3 class="font-semibold text-white flex items-center gap-2">
                    <i class="ti ti-chart-bar text-purple-400"></i> Institution Stats
                </h3>
            </div>
            <div class="p-4 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 text-sm">Total Subscriptions</span>
                    <span class="text-white font-semibold">{{ $institutionSubscriptions->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 text-sm">Total Users</span>
                    <span class="text-white font-semibold">{{ $institution->users()->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 text-sm">Total Books</span>
                    <span class="text-white font-semibold">{{ $institution->books()->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 text-sm">Wallet Balance</span>
                    <span class="text-white font-semibold">TSh {{ number_format($institution->wallet->balance ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection