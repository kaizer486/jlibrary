@extends('layouts.author')

@section('title', 'My Books')
@section('page-title', 'My Books')

@section('content')

<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-7xl">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="bg-gradient-to-br from-orange-500 to-amber-500 p-2 rounded-xl shadow-lg shadow-orange-500/20">
                        <i class="ti ti-books text-white text-xl"></i>
                    </span>
                    My Books
                </h1>
                <p class="text-slate-600 text-sm mt-1">Manage your published books</p>
            </div>
            <a href="{{ route('author.books.create') }}" class="bg-gradient-to-r from-orange-600 to-amber-600 hover:shadow-lg hover:shadow-orange-600/25 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-medium border-2 border-orange-400/30">
                <i class="ti ti-plus"></i> Upload New Book
            </a>
        </div>

        <!-- Books Table -->
        <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b-2 border-slate-200/60">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Book</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Category</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Price</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Stock</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Stats</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($books as $book)
                            <tr class="hover:bg-slate-50 transition">
                                <!-- Book -->
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-16 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden border border-slate-200">
                                            @if($book->cover_image)
                                                <img src="{{ url('media/' . $book->cover_image) }}"
                                                     alt="{{ $book->title }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <i class="ti ti-book text-orange-400 text-2xl"></i>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-slate-800 line-clamp-1">{{ $book->title }}</p>
                                            <p class="text-xs text-slate-500">by {{ $book->author }}</p>
                                            <p class="text-[10px] text-slate-400 mt-0.5">ID: #{{ $book->id }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category -->
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    {{ $book->category ?? '—' }}
                                </td>

                                <!-- Price -->
                                <td class="px-4 py-3 text-sm">
                                    @if($book->offersSoftcopy() && $book->offersHardcopy())
                                        <div class="space-y-0.5 text-xs">
                                            <p class="text-blue-600 font-medium"> TSh {{ number_format($book->effectiveSoftcopyPrice(), 0) }}</p>
                                            <p class="text-orange-600 font-medium"> TSh {{ number_format($book->effectiveHardcopyPrice(), 0) }}</p>
                                        </div>
                                    @elseif($book->offersHardcopy())
                                        <span class="text-orange-600 font-semibold">
                                             TSh {{ number_format($book->effectiveHardcopyPrice(), 2) }}
                                        </span>
                                    @elseif($book->offersSoftcopy())
                                        @if($book->effectiveSoftcopyPrice() > 0)
                                            <span class="text-blue-600 font-semibold">
                                                 TSh {{ number_format($book->effectiveSoftcopyPrice(), 2) }}
                                            </span>
                                        @else
                                            <span class="text-green-600 font-semibold">FREE</span>
                                        @endif
                                    @else
                                        <span class="text-slate-400 text-xs">—</span>
                                    @endif
                                </td>

                                <!-- Stock -->
                                <td class="px-4 py-3 text-sm">
                                    @if(in_array($book->book_type, ['hardcopy', 'both'], true))
                                        @php $stock = (int) ($book->stock_quantity ?? 0); @endphp
                                        <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full {{ $stock > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                            @if($stock > 0)
                                                <i class="ti ti-package text-[10px]"></i> {{ $stock }}
                                            @else
                                                <i class="ti ti-alert-triangle text-[10px]"></i> 0
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs">—</span>
                                    @endif
                                </td>

                                <!-- Stats -->
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex items-center gap-3 text-xs text-slate-500">
                                        <span title="Downloads">
                                            <i class="ti ti-download text-orange-500"></i> {{ number_format($book->downloads ?? 0) }}
                                        </span>
                                        <span title="Rating">
                                            <i class="ti ti-star text-yellow-400"></i> {{ $book->averageRating() ?? 0 }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-3">
                                    @if($book->status === 'approved')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">✅ Approved</span>
                                    @elseif($book->status === 'pending')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⏳ Pending</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">❌ Rejected</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('author.books.show', $book) }}" class="text-blue-600 hover:text-blue-800 transition" title="View">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('author.books.edit', $book) }}" class="text-orange-600 hover:text-orange-800 transition" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('author.books.destroy', $book) }}" class="inline" onsubmit="return confirm('Delete {{ addslashes($book->title) }} permanently?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 transition" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                                    <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-amber-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg shadow-orange-500/20">
                                        <i class="ti ti-books text-3xl text-white"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-slate-800 mb-1">No Books Yet</h3>
                                    <p class="text-slate-500 mb-4 text-sm">Upload your first book to start earning royalties.</p>
                                    <a href="{{ route('author.books.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-600 to-amber-600 text-white px-5 py-2.5 rounded-xl hover:shadow-lg transition font-medium">
                                        <i class="ti ti-plus"></i> Upload Your First Book
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($books->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $books->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

@push('styles')
<style>
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@endsection