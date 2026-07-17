@extends('layouts.app')

@section('title', 'My Purchases')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-6xl">

        <div class="mb-6">
            <div class="flex items-center gap-2 mb-2">
                <i class="ti ti-shopping-cart text-purple-400 text-3xl"></i>
                <h1 class="text-2xl md:text-3xl font-bold text-white">My Purchases</h1>
            </div>
            <div class="w-16 h-1 bg-yellow-400 rounded-full mb-2"></div>
            <p class="text-gray-300">Books you have purchased</p>
        </div>

        @if($purchases->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($purchases as $purchase)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition">
                        <div class="h-40 bg-gradient-to-r from-indigo-100 to-purple-100 flex items-center justify-center">
                           @if($purchase->book->cover_image)
    <img src="{{ url('media/' . $purchase->book->cover_image) }}" alt="{{ $purchase->book->title }}" class="h-full w-full object-cover">
@else
    <i class="ti ti-book text-6xl text-purple-400"></i>
@endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-800">{{ Str::limit($purchase->book->title, 40) }}</h3>
                            <p class="text-sm text-gray-500">{{ $purchase->book->author ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-400 mt-1">Purchased: {{ $purchase->created_at->format('M d, Y') }}</p>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-sm font-bold text-green-600">TSh {{ number_format($purchase->amount, 2) }}</span>
                                <div class="flex gap-2">
                                    <a href="{{ route('library.public.show', [$purchase->book->institution_id, $purchase->book->id]) }}" 
                                       class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                        Read
                                    </a>
                                    @if($purchase->book->file_path)
                                        <a href="{{ route('book.download', $purchase->book->id) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Download
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">{{ $purchases->links() }}</div>
        @else
            <div class="bg-white/10 rounded-2xl p-12 text-center border border-white/20">
                <i class="ti ti-shopping-cart text-5xl text-gray-400 mb-3 block"></i>
                <h3 class="text-xl font-semibold text-white mb-2">No Purchases Yet</h3>
                <p class="text-gray-300">You haven't purchased any books yet.</p>
                <a href="{{ route('library.public.index', auth()->user()->institution_id ?? 1) }}" 
                   class="mt-4 inline-block bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                    Browse Books
                </a>
            </div>
        @endif

    </div>
</div>
@endsection