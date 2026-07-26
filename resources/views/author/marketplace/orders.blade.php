@extends('layouts.author')

@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Orders</h2>
            <p class="text-sm text-gray-500">Track all orders for your books</p>
        </div>
    </div>
    
    @if($orders->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b">
                        <th class="pb-3 font-semibold">Order ID</th>
                        <th class="pb-3 font-semibold">Customer</th>
                        <th class="pb-3 font-semibold">Book</th>
                        <th class="pb-3 font-semibold">Amount</th>
                        <th class="pb-3 font-semibold">Status</th>
                        <th class="pb-3 font-semibold">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr class="border-b last:border-0 hover:bg-gray-50 transition">
                            <td class="py-3 font-medium text-gray-800">#{{ $order->id }}</td>
                            <td class="py-3 text-gray-600">{{ $order->user->full_name ?? 'Guest' }}</td>
                            <td class="py-3">
                                @foreach($order->items as $item)
                                    <span class="text-sm text-gray-600">{{ $item->book->title ?? 'Unknown' }}</span>
                                @endforeach
                            </td>
                            <td class="py-3 font-semibold text-green-600">TSh {{ number_format($order->total, 2) }}</td>
                            <td class="py-3">
                                <span class="text-xs px-2 py-1 rounded-full 
                                    @if($order->status === 'completed') bg-green-100 text-green-700
                                    @elseif($order->status === 'pending') bg-yellow-100 text-yellow-700
                                    @elseif($order->status === 'processing') bg-blue-100 text-blue-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="py-3 text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $orders->links() }}</div>
    @else
        <div class="text-center py-12">
            <i class="ti ti-package-off text-4xl text-gray-300 block mb-3"></i>
            <h3 class="text-lg font-semibold text-gray-600">No Orders Yet</h3>
            <p class="text-gray-400 text-sm">Orders will appear here when customers purchase your books</p>
        </div>
    @endif
</div>
@endsection