@extends('layouts.author')

@section('title', 'My Listings')
@section('page-title', 'My Listings')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">All Books</h2>
            <p class="text-sm text-gray-500">Manage your marketplace listings</p>
        </div>
        <a href="{{ route('author.books.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
            <i class="ti ti-plus"></i> Add New Book
        </a>
    </div>
    
    @if($books->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b">
                        <th class="pb-3 font-semibold">Book</th>
                        <th class="pb-3 font-semibold">Price</th>
                        <th class="pb-3 font-semibold">Sales</th>
                        <th class="pb-3 font-semibold">Status</th>
                        <th class="pb-3 font-semibold">Date</th>
                        <th class="pb-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($books as $book)
                        <tr class="border-b last:border-0 hover:bg-gray-50 transition">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    @if($book->cover_image)
                                        <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-10 h-14 object-cover rounded">
                                    @else
                                        <div class="w-10 h-14 bg-gray-200 rounded flex items-center justify-center">
                                            <i class="ti ti-book text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $book->title }}</p>
                                        <p class="text-xs text-gray-500">by {{ $book->author }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 font-semibold text-green-600">TSh {{ number_format($book->price, 2) }}</td>
                            <td class="py-3 text-gray-600">{{ $book->sales_count ?? 0 }}</td>
                            <td class="py-3">
                                <span class="text-xs px-2 py-1 rounded-full 
                                    @if($book->status === 'approved') bg-green-100 text-green-700
                                    @elseif($book->status === 'pending') bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($book->status) }}
                                </span>
                            </td>
                            <td class="py-3 text-sm text-gray-500">{{ $book->created_at->format('M d, Y') }}</td>
                            <td class="py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('author.books.show', $book) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="{{ route('author.books.edit', $book) }}" class="text-orange-600 hover:text-orange-800" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $books->links() }}</div>
    @else
        <div class="text-center py-12">
            <i class="ti ti-books text-4xl text-gray-300 block mb-3"></i>
            <h3 class="text-lg font-semibold text-gray-600">No Books Listed</h3>
            <p class="text-gray-400 text-sm">Start selling your books on the marketplace</p>
            <a href="{{ route('author.books.create') }}" class="inline-block mt-4 text-purple-600 hover:text-purple-700 font-medium">
                Upload Your First Book →
            </a>
        </div>
    @endif
</div>
@endsection