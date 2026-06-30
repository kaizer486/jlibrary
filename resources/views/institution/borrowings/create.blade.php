@extends('layouts.librarian')

@section('title', 'New Borrowing')
@section('page-title', '📖 New Borrowing')

@section('content')

<div class="max-w-3xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('institution.borrowings.index') }}" class="text-purple-400 hover:text-purple-300 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Borrowings
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-700 rounded-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-purple-900/30 to-pink-900/30 px-6 py-4 border-b border-slate-700">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="ti ti-book-plus"></i> Borrow Book
            </h1>
            <p class="text-slate-400 text-sm">Borrow a book for a library member</p>
        </div>

        <form method="POST" action="{{ route('institution.borrowings.store') }}" class="p-6">
            @csrf

            <div class="space-y-6">
                
                <!-- Book Selection -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Book <span class="text-red-400">*</span>
                    </label>
                    @if($book)
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
                            <p class="text-white font-medium">{{ $book->title }}</p>
                            <p class="text-slate-400 text-sm">by {{ $book->author ?? 'Unknown' }}</p>
                        </div>
                    @else
                        <select name="book_id" class="search-bar" required>
                            <option value="">Select a book...</option>
                            @foreach($books as $bookItem)
                                <option value="{{ $bookItem->id }}" {{ old('book_id') == $bookItem->id ? 'selected' : '' }}>
                                    {{ $bookItem->title }} by {{ $bookItem->author ?? 'Unknown' }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('book_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- User Selection -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Member <span class="text-red-400">*</span>
                    </label>
                    <select name="user_id" class="search-bar" required>
                        <option value="">Select a member...</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ old('user_id') == $member->id ? 'selected' : '' }}>
                                {{ $member->full_name }} ({{ $member->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Due Date -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Due Date <span class="text-red-400">*</span>
                    </label>
                    <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(14)->format('Y-m-d')) }}" 
                           class="search-bar" required min="{{ now()->addDay()->format('Y-m-d') }}">
                    <p class="text-xs text-slate-500 mt-1">Default: 14 days from today</p>
                    @error('due_date') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="search-bar" placeholder="Additional notes...">{{ old('notes') }}</textarea>
                    @error('notes') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Submit -->
                <div class="flex gap-3 pt-6 border-t border-slate-700">
                    <button type="submit" class="btn-library flex-1 justify-center">
                        <i class="ti ti-device-floppy"></i> Borrow Book
                    </button>
                    <a href="{{ route('institution.borrowings.index') }}" class="bg-slate-800 text-slate-400 px-6 py-2.5 rounded-lg hover:bg-slate-700 transition text-center border border-slate-700">
                        Cancel
                    </a>
                </div>

            </div>
        </form>
    </div>

</div>

@endsection