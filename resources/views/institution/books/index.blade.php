@extends('layouts.admin')

@section('title', 'Institution Books')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📚 Institution Books</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $institution->name }}</p>
        </div>
    </div>
</div>

@if($books->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($books as $book)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $book->title }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $book->author }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @if($book->is_paid)
                            ${{ number_format($book->price, 2) }}
                        @else
                            <span class="text-green-600">FREE</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs {{ $book->status === 'approved' ? 'bg-green-100 text-green-700' : ($book->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ ucfirst($book->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">{{ $books->links() }}</div>
@else
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="ti ti-books text-6xl text-gray-400 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Books Found</h3>
    <p class="text-gray-500">No books have been added to this institution yet.</p>
</div>
@endif
@endsection