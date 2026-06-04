@extends('layouts.admin')

@section('title', 'Librarian Dashboard')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-3 mb-2">
        <i class="ti ti-book text-purple-600 text-3xl"></i>
        <h1 class="text-3xl font-bold text-gray-900">Librarian Dashboard</h1>
    </div>
    <p class="text-gray-600">Manage books for <strong>{{ $institution->name }}</strong></p>
</div>

<!-- Stats -->
<div class="grid md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl p-6 border-l-4 border-blue-500 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Books in Library</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($totalBooks) }}</p>
            </div>
            <i class="ti ti-books text-4xl text-blue-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-6 border-l-4 border-green-500 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Your Institution</p>
                <p class="text-xl font-semibold text-gray-800">{{ $institution->name }}</p>
            </div>
            <i class="ti ti-building text-4xl text-green-500"></i>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid md:grid-cols-2 gap-4 mb-8">
    <a href="{{ route('admin.books.create') }}" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl p-4 text-center hover:shadow-lg transition">
        <i class="ti ti-plus text-2xl mb-1 block"></i>
        <span class="font-semibold">Add New Book</span>
    </a>
    <a href="{{ route('admin.books.index') }}" class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl p-4 text-center hover:shadow-lg transition">
        <i class="ti ti-books text-2xl mb-1 block"></i>
        <span class="font-semibold">Manage Books</span>
    </a>
</div>

<!-- Recent Books -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">📚 Recently Added Books</h2>
    <div class="space-y-3">
        @forelse($recentBooks as $book)
        <div class="flex items-center justify-between py-2 border-b">
            <div>
                <p class="font-medium text-gray-900">{{ $book->title }}</p>
                <p class="text-sm text-gray-500">by {{ $book->author }}</p>
            </div>
            <a href="{{ route('admin.books.edit', $book) }}" class="text-purple-600 hover:text-purple-700">Edit</a>
        </div>
        @empty
        <p class="text-gray-500 text-center py-4">No books added yet</p>
        @endforelse
    </div>
</div>
@endsection