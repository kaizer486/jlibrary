@extends('layouts.app')

@section('title', 'Purchase Book')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Purchase Book</h1>
        
        <div class="flex gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
            @if($book->cover_image)
               <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-24 h-32 object-cover rounded-lg">
            @else
                <div class="w-24 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                    <i class="ti ti-book text-3xl text-gray-400"></i>
                </div>
            @endif
            <div>
                <h2 class="text-xl font-semibold text-gray-800">{{ $book->title }}</h2>
                <p class="text-gray-600">by {{ $book->author ?? 'Unknown' }}</p>
                <p class="text-2xl font-bold text-yellow-600 mt-2">TSh {{ number_format($book->price, 2) }}</p>
            </div>
        </div>

        <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-sm text-blue-700">
                <i class="ti ti-info-circle"></i> 
                Wallet Balance: <strong>TSh {{ number_format($walletBalance, 2) }}</strong>
            </p>
        </div>

        <form method="POST" action="{{ route('book.purchase.process') }}">
            @csrf
            <input type="hidden" name="book_id" value="{{ $book->id }}">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Payment Method</label>
                <div class="grid grid-cols-2 gap-3">
                    @if($walletBalance >= $book->price)
                        <label class="border rounded-lg p-3 cursor-pointer hover:bg-gray-50 transition bg-green-50 border-green-300">
                            <input type="radio" name="payment_method" value="wallet" checked>
                            <span class="ml-2 font-semibold text-green-700">💰 Wallet (TSh {{ number_format($walletBalance, 2) }})</span>
                        </label>
                    @endif
                    <label class="border rounded-lg p-3 cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="payment_method" value="mpesa" {{ $walletBalance < $book->price ? 'checked' : '' }}>
                        <span class="ml-2">📱 M-Pesa</span>
                    </label>
                    <label class="border rounded-lg p-3 cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="payment_method" value="tigopesa">
                        <span class="ml-2">📱 TigoPesa</span>
                    </label>
                    <label class="border rounded-lg p-3 cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="payment_method" value="halopesa">
                        <span class="ml-2">📱 HaloPesa</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-lg transition">
                <i class="ti ti-shopping-cart"></i> Complete Purchase - TSh {{ number_format($book->price, 2) }}
            </button>
        </form>
    </div>
</div>
@endsection