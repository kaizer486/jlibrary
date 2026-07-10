@extends('layouts.super-admin')

@section('title', 'Override Subscription')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('super-admin.subscriptions.show', $subscription) }}" class="text-purple-400 hover:text-purple-300 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Subscription
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="ti ti-settings"></i> Override Subscription
            </h2>
            <p class="text-purple-200 text-sm">{{ $subscription->institution->name }}</p>
        </div>
        
        <form method="POST" action="{{ route('super-admin.subscriptions.override', $subscription) }}" class="p-6">
            @csrf
            
            <div class="space-y-4">
                <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-4 mb-4">
                    <p class="text-sm text-yellow-400 flex items-center gap-2">
                        <i class="ti ti-alert-triangle"></i>
                        Current Plan: <strong class="text-white capitalize">{{ $subscription->plan }}</strong>
                        ({{ ucfirst($subscription->status) }})
                    </p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        New Plan <span class="text-red-400">*</span>
                    </label>
                    <select name="plan" required class="search-bar w-full">
                        <option value="basic">📘 Basic</option>
                        <option value="premium">📚 Premium</option>
                        <option value="enterprise">🏢 Enterprise</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Period <span class="text-red-400">*</span>
                    </label>
                    <select name="period" required class="search-bar w-full">
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="semi_annual">Semi-Annual</option>
                        <option value="annual">Annual</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Amount Paid <span class="text-red-400">*</span>
                    </label>
                    <input type="number" name="amount" step="0.01" required 
                           placeholder="Enter amount paid"
                           class="search-bar w-full">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Payment Method
                    </label>
                    <select name="payment_method" class="search-bar w-full">
                        <option value="">Select Method</option>
                        <option value="mpesa">📱 M-Pesa</option>
                        <option value="tigopesa">📱 TigoPesa</option>
                        <option value="halopesa">📱 HaloPesa</option>
                        <option value="pesapal">💳 PesaPal</option>
                        <option value="bank">🏦 Bank Transfer</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Note / Reason for Override
                    </label>
                    <textarea name="note" rows="3" class="search-bar w-full" placeholder="Why is this being overridden?"></textarea>
                </div>
                
                <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-4">
                    <p class="text-sm text-amber-400 flex items-center gap-2">
                        <i class="ti ti-info-circle"></i>
                        This will cancel the current subscription and create a new one with the selected plan.
                        The institution will have immediate access to the new plan.
                    </p>
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 hover:shadow-lg text-white px-6 py-3 rounded-xl transition font-semibold">
                        <i class="ti ti-check"></i> Override Subscription
                    </button>
                    <a href="{{ route('super-admin.subscriptions.show', $subscription) }}" 
                       class="px-6 py-3 border border-slate-600 rounded-xl hover:bg-slate-800 transition text-center text-slate-300">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection