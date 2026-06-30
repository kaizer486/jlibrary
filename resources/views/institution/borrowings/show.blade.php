@extends('layouts.librarian')

@section('title', 'Borrowing Details')
@section('page-title', '📖 Borrowing Details')

@section('content')

<div class="max-w-3xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('institution.borrowings.index') }}" class="text-purple-400 hover:text-purple-300 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Borrowings
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-700 rounded-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-purple-900/30 to-pink-900/30 px-6 py-4 border-b border-slate-700">
            <h1 class="text-xl font-bold text-white">Borrowing Details</h1>
            <p class="text-slate-400 text-sm">#{{ $borrowing->id }} - {{ $borrowing->book->title }}</p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Book Info -->
                <div>
                    <h3 class="font-semibold text-white text-sm mb-3">Book Information</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between py-2 border-b border-slate-700">
                            <span class="text-slate-400">Title</span>
                            <span class="text-white">{{ $borrowing->book->title }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-700">
                            <span class="text-slate-400">Author</span>
                            <span class="text-white">{{ $borrowing->book->author ?? 'Unknown' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-700">
                            <span class="text-slate-400">Shelf</span>
                            <span class="text-white">{{ $borrowing->book->shelf_number ?? 'Not assigned' }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-slate-400">Status</span>
                            <span>{!! $borrowing->book->status_badge !!}</span>
                        </div>
                    </div>
                </div>

                <!-- Borrowing Info -->
                <div>
                    <h3 class="font-semibold text-white text-sm mb-3">Borrowing Information</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between py-2 border-b border-slate-700">
                            <span class="text-slate-400">Borrower</span>
                            <span class="text-white">{{ $borrowing->user->full_name }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-700">
                            <span class="text-slate-400">Email</span>
                            <span class="text-white">{{ $borrowing->user->email }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-700">
                            <span class="text-slate-400">Borrowed At</span>
                            <span class="text-white">{{ $borrowing->borrowed_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-700">
                            <span class="text-slate-400">Due Date</span>
                            <span class="text-white {{ $borrowing->isOverdue() ? 'text-red-400' : '' }}">
                                {{ $borrowing->due_date->format('M d, Y') }}
                                @if($borrowing->isOverdue())
                                    <span class="text-red-400">(Overdue)</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-700">
                            <span class="text-slate-400">Status</span>
                            <span>{!! $borrowing->status_badge !!}</span>
                        </div>
                        @if($borrowing->notes)
                            <div class="flex justify-between py-2">
                                <span class="text-slate-400">Notes</span>
                                <span class="text-white text-right">{{ $borrowing->notes }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 pt-6 border-t border-slate-700 flex gap-3">
                @if($borrowing->isActive())
                    <form method="POST" action="{{ route('institution.borrowings.return', $borrowing) }}" class="inline">
                        @csrf
                        <button type="submit" class="btn-library">
                            <i class="ti ti-check"></i> Return Book
                        </button>
                    </form>
                    <form method="POST" action="{{ route('institution.borrowings.destroy', $borrowing) }}" 
                          onsubmit="return confirm('Cancel this borrowing?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600/20 hover:bg-red-600/30 text-red-400 px-4 py-2.5 rounded-lg transition border border-red-500/20">
                            <i class="ti ti-x"></i> Cancel
                        </button>
                    </form>
                @endif
                <a href="{{ route('institution.borrowings.index') }}" class="bg-slate-800 text-slate-400 px-4 py-2.5 rounded-lg hover:bg-slate-700 transition border border-slate-700">
                    Back
                </a>
            </div>

            <!-- Processed By -->
            @if($borrowing->borrowed_by || $borrowing->returned_to)
                <div class="mt-4 pt-4 border-t border-slate-700 text-xs text-slate-500">
                    @if($borrowing->borrowed_by)
                        <p>Processed by: {{ $borrowing->borrowedBy?->full_name ?? 'Unknown' }}</p>
                    @endif
                    @if($borrowing->returned_to)
                        <p>Returned to: {{ $borrowing->returnedTo?->full_name ?? 'Unknown' }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

</div>

@endsection