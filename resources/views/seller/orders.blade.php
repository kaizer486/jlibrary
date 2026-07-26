@extends('layouts.author')

@section('title', 'Orders')
@section('page-title', 'Marketplace Orders')

@section('content')
<div class="space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">Orders</h2>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Order ID</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Product</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Buyer</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">#{{ $order->id }}</td>
                        <td class="px-4 py-3">{{ $order->listing->title ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $order->buyer->full_name ?? 'Guest' }}</td>
                        <td class="px-4 py-3 font-semibold text-emerald-600">TSh {{ number_format($order->amount, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2.5 py-1 rounded-full 
                                {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 
                                   ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            <i class="ti ti-package-off text-4xl mb-2 block text-gray-300"></i>
                            <p>No orders yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($orders->hasPages())
        <div class="px-4 py-3 border-t">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection