@extends('layouts.app')

@section('title', 'My Royalties')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-5xl">
        
        <div class="mb-6">
            <a href="{{ route('author.dashboard') }}" class="text-purple-300 hover:text-purple-200 flex items-center gap-2 mb-4">
                <i class="ti ti-arrow-left"></i> Back to Dashboard
            </a>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="ti ti-wallet"></i> My Royalties
            </h1>
            <p class="text-gray-300 text-sm mt-1">Track your earnings from book sales</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-purple-500">
                <p class="text-gray-500 text-sm">Total Royalties</p>
                <p class="text-2xl font-bold text-purple-600">TSh {{ number_format($totalRoyalties ?? 0, 2) }}</p>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-green-500">
                <p class="text-gray-500 text-sm">Paid Out</p>
                <p class="text-2xl font-bold text-green-600">TSh {{ number_format($totalWithdrawn ?? 0, 2) }}</p>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-yellow-500">
                <p class="text-gray-500 text-sm">Available Balance</p>
                <p class="text-2xl font-bold text-yellow-600">TSh {{ number_format($available ?? 0, 2) }}</p>
            </div>
        </div>

        <!-- Royalties Table -->
        @if($royalties->count() > 0)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-lg font-semibold text-gray-800">💰 Royalty History</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Book</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buyer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sale Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Your Royalty (10%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($royalties as $royalty)
                            @php
                                $book = \App\Models\Book::find($royalty->payable_id);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $royalty->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-800">{{ $book->title ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $royalty->user->full_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-blue-600">TSh {{ number_format($royalty->amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-green-600">TSh {{ number_format($royalty->amount * 0.10, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-6">
            {{ $royalties->links() }}
        </div>
        
        @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="ti ti-wallet text-6xl text-gray-400 mb-4 block"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Royalties Yet</h3>
            <p class="text-gray-500">Royalties will appear here when your books are purchased.</p>
        </div>
        @endif

    </div>
</div>
@endsection