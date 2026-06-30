@extends('layouts.app')

@section('title', 'Purchase Book')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-3xl">

        <div class="mb-6">
            <a href="{{ route('library.public.show', [$institution->id, $book->id]) }}" class="text-purple-300 hover:text-purple-200 transition inline-flex items-center gap-2">
                <i class="ti ti-arrow-left"></i> Back to Book
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                    <i class="ti ti-shopping-cart"></i> Complete Purchase
                </h1>
            </div>

            <div class="p-6">
                <!-- Book Summary -->
                <div class="flex gap-4 p-4 bg-gray-50 rounded-xl mb-6">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-24 h-32 object-cover rounded-lg">
                    @else
                        <div class="w-24 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="ti ti-book text-4xl text-gray-400"></i>
                        </div>
                    @endif
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-800 text-lg">{{ $book->title }}</h3>
                        <p class="text-sm text-gray-500">by {{ $book->author ?? 'Unknown' }}</p>
                        <p class="text-2xl font-bold text-green-600 mt-2">TSh {{ number_format($book->price, 2) }}</p>
                        <p class="text-xs text-gray-400">{{ $book->institution->name }}</p>
                    </div>
                </div>

                <!-- Payment Methods -->
                <form method="POST" action="{{ route('book.purchase.process') }}">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">

                    <h3 class="font-semibold text-gray-800 mb-4">Select Payment Method</h3>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-6">
                        <label class="border rounded-lg p-3 cursor-pointer hover:border-purple-500 transition has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50">
                            <input type="radio" name="payment_method" value="mpesa" checked class="hidden">
                            <div class="text-center">
                                <i class="ti ti-phone text-2xl text-green-600"></i>
                                <p class="text-sm font-medium">M-Pesa</p>
                            </div>
                        </label>

                        <label class="border rounded-lg p-3 cursor-pointer hover:border-purple-500 transition has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50">
                            <input type="radio" name="payment_method" value="tigopesa" class="hidden">
                            <div class="text-center">
                                <i class="ti ti-phone text-2xl text-blue-600"></i>
                                <p class="text-sm font-medium">TigoPesa</p>
                            </div>
                        </label>

                        <label class="border rounded-lg p-3 cursor-pointer hover:border-purple-500 transition has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50">
                            <input type="radio" name="payment_method" value="halopesa" class="hidden">
                            <div class="text-center">
                                <i class="ti ti-phone text-2xl text-orange-600"></i>
                                <p class="text-sm font-medium">HaloPesa</p>
                            </div>
                        </label>

                        <label class="border rounded-lg p-3 cursor-pointer hover:border-purple-500 transition has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50">
                            <input type="radio" name="payment_method" value="bank" class="hidden">
                            <div class="text-center">
                                <i class="ti ti-building-bank text-2xl text-blue-700"></i>
                                <p class="text-sm font-medium">Bank Transfer</p>
                            </div>
                        </label>

                        <label class="border rounded-lg p-3 cursor-pointer hover:border-purple-500 transition has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50">
                            <input type="radio" name="payment_method" value="pesapal" class="hidden">
                            <div class="text-center">
                                <i class="ti ti-credit-card text-2xl text-purple-600"></i>
                                <p class="text-sm font-medium">PesaPal</p>
                            </div>
                        </label>
                    </div>

                    <!-- Wallet Balance -->
                    <div class="bg-gray-50 rounded-xl p-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Your Wallet Balance</span>
                            <span class="text-2xl font-bold text-green-600">TSh {{ number_format(Auth::user()->wallet_balance ?? 0, 2) }}</span>
                        </div>
                        @if((Auth::user()->wallet_balance ?? 0) < $book->price)
                            <div class="mt-2 text-red-500 text-sm">
                                <i class="ti ti-alert-circle"></i> Insufficient balance. Please top up your wallet.
                            </div>
                        @endif
                    </div>

                    <!-- Summary -->
                    <div class="border-t pt-4 mb-6">
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold">TSh {{ number_format($book->price, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-t">
                            <span class="text-gray-600">Total</span>
                            <span class="text-2xl font-bold text-green-600">TSh {{ number_format($book->price, 2) }}</span>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-4 rounded-xl hover:shadow-lg transition font-semibold text-lg"
                            @if((Auth::user()->wallet_balance ?? 0) < $book->price) disabled @endif>
                        <i class="ti ti-lock"></i> Pay Now
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection