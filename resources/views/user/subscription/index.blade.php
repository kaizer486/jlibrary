@extends('layouts.app')

@section('title', 'My Subscription')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="text-purple-300 hover:text-purple-200 transition inline-flex items-center gap-1">
                <i class="ti ti-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <!-- ========================================== -->
        <!-- SUBSCRIPTION STATUS CARD                   -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="ti ti-clock"></i> My Subscription
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold">Plan</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['plan_label'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold">Status</p>
                        <p class="text-2xl font-bold 
                            @if($stats['is_active']) text-green-600 
                            @else text-red-600 @endif">
                            {{ $stats['status_label'] }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold">Days Left</p>
                        <p class="text-2xl font-bold 
                            @if($stats['status_color'] == 'red') text-red-600 
                            @elseif($stats['status_color'] == 'yellow') text-yellow-600 
                            @elseif($stats['status_color'] == 'orange') text-orange-600 
                            @else text-green-600 @endif">
                            {{ $stats['days_left'] > 0 ? $stats['days_left'] . ' days' : 'Expired' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SUBSCRIPTION PLANS                        -->
        <!-- ========================================== -->
        <div class="grid md:grid-cols-3 gap-6 mb-6">
            @foreach($plans as $plan)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 
                    @if($plan->id == $stats['plan']) border-purple-500 
                    @else border-transparent @endif">
                    
                    @if($plan->id == $stats['plan'])
                        <div class="bg-purple-500 text-white text-center text-xs font-semibold py-1">
                            CURRENT PLAN
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800">{{ $plan->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $plan->description }}</p>
                        
                        <div class="mt-4">
                            @if($plan->monthly_price == 0)
                                <p class="text-3xl font-bold text-green-600">FREE</p>
                            @else
                                <p class="text-3xl font-bold text-gray-800">TSh {{ number_format($plan->monthly_price) }}</p>
                                <p class="text-sm text-gray-500">per month</p>
                                <p class="text-sm text-gray-500 mt-1">or TSh {{ number_format($plan->annual_price) }} / year</p>
                            @endif
                        </div>
                        
                        <ul class="mt-4 space-y-2">
                            @foreach($plan->features as $feature)
                                <li class="flex items-center gap-2 text-sm text-gray-600">
                                    <i class="ti ti-check text-green-500"></i> {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                        
                        @if($plan->id != $stats['plan'])
                            <form method="POST" action="{{ route('user.subscription.extend') }}" class="mt-6">
                                @csrf
                                <input type="hidden" name="plan" value="{{ $plan->id }}">
                                <select name="period" class="w-full px-3 py-2 border rounded-lg text-sm mb-2">
                                    <option value="monthly">Monthly</option>
                                    <option value="annual">Annual (Save 10%)</option>
                                </select>
                                <select name="payment_method" class="w-full px-3 py-2 border rounded-lg text-sm mb-2">
                                    <option value="mpesa">📱 M-Pesa</option>
                                    <option value="tigopesa">📱 TigoPesa</option>
                                    <option value="halopesa">📱 HaloPesa</option>
                                    <option value="bank">🏦 Bank Transfer</option>
                                    <option value="pesapal">💳 PesaPal</option>
                                </select>
                                <div class="flex items-center gap-2 mb-3">
                                    <input type="checkbox" name="auto_renew" value="1" checked class="rounded">
                                    <span class="text-sm text-gray-600">Auto-renew</span>
                                </div>
                                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white px-4 py-2 rounded-lg hover:shadow-lg transition font-semibold">
                                    Upgrade to {{ $plan->name }}
                                </button>
                            </form>
                        @else
                            @if($plan->id != 'free')
                                <form method="POST" action="{{ route('user.subscription.cancel') }}" class="mt-6">
                                    @csrf
                                    <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition" onclick="return confirm('Cancel your subscription?')">
                                        Cancel Subscription
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- ========================================== -->
        <!-- SUBSCRIPTION HISTORY                      -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="ti ti-history"></i> History
                </h2>
                <a href="{{ route('user.subscription.history') }}" class="text-sm text-white/70 hover:text-white">
                    View All →
                </a>
            </div>
            <div class="p-6">
                @if($history->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $entry)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $entry->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-sm font-semibold capitalize">{{ $entry->plan }}</td>
                                <td class="px-4 py-3 text-sm font-medium">TSh {{ number_format($entry->amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        @if($entry->status === 'active') bg-green-100 text-green-700
                                        @elseif($entry->status === 'cancelled') bg-gray-100 text-gray-700
                                        @else bg-yellow-100 text-yellow-700 @endif">
                                        {{ ucfirst($entry->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500 text-center py-8">No subscription history found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsectionv