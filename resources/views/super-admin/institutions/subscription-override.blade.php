@extends('layouts.super-admin')

@section('title', 'Override Subscription')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="ti ti-settings"></i> Override Subscription
            </h2>
            <p class="text-purple-200 text-sm">{{ $institution->name }}</p>
        </div>
        
        <form method="POST" action="{{ route('super-admin.institutions.activate-subscription', $institution) }}" class="p-6">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Select Plan <span class="text-red-500">*</span>
                    </label>
                    <select name="plan" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white">
                        <option value="basic">📘 Basic</option>
                        <option value="premium">📚 Premium</option>
                        <option value="enterprise">🏢 Enterprise</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Period <span class="text-red-500">*</span>
                    </label>
                    <select name="period" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white">
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="semi_annual">Semi-Annual</option>
                        <option value="annual">Annual</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Payment Method <span class="text-red-500">*</span>
                    </label>
                    <select name="payment_method" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white">
                        <option value="mpesa">📱 M-Pesa</option>
                        <option value="tigopesa">📱 TigoPesa</option>
                        <option value="halopesa">📱 HaloPesa</option>
                        <option value="pesapal">💳 PesaPal</option>
                        <option value="bank">🏦 Bank Transfer</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Amount Paid <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="amount_paid" step="0.01" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl">
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <p class="text-sm text-yellow-700 flex items-center gap-2">
                        <i class="ti ti-alert-triangle"></i>
                        This will override the current subscription and activate the new plan immediately.
                    </p>
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-3 rounded-xl hover:shadow-lg transition font-semibold">
                        <i class="ti ti-check"></i> Activate Subscription
                    </button>
                    <a href="{{ route('super-admin.institutions.show', $institution) }}" class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection