@extends('layouts.app')

@section('title', 'Orders - Seller')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📦 Orders</h1>
        <a href="{{ route('seller.dashboard') }}" class="text-purple-600 hover:text-purple-700">
            <i class="ti ti-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    @if($orders->count() > 0)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Order ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Book</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Buyer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 font-medium text-gray-800">#{{ $order->id }}</td>
                                <td class="px-4 py-3">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $order->listing->title ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-400">by {{ $order->listing->author ?? 'Unknown' }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div>
                                        <p class="text-gray-800">{{ $order->buyer->full_name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-400">{{ $order->buyer->email ?? '' }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-semibold text-purple-600">TSh {{ number_format($order->amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        @if($order->status === 'completed') bg-green-100 text-green-700
                                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-700
                                        @elseif($order->status === 'processing') bg-blue-100 text-blue-700
                                        @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                                        @else bg-gray-100 text-gray-700 @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="ti ti-shopping-cart-off text-6xl text-gray-400 mb-4 block"></i>
            <h3 class="text-xl font-semibold mb-2">No Orders Yet</h3>
            <p class="text-gray-500">You haven't received any orders for your listings.</p>
            <a href="{{ route('marketplace.listings') }}" class="inline-block mt-4 text-purple-600 hover:text-purple-700">
                <i class="ti ti-arrow-left"></i> Back to Listings
            </a>
        </div>
    @endif
</div>
@endsection