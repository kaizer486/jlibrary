@extends('layouts.librarian')

@section('title', 'Borrowing Management')
@section('page-title', '📖 Borrowing Management')

@section('content')

<div class="max-w-7xl mx-auto">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p class="text-slate-400 text-sm">Manage book borrowings</p>
        </div>
        <a href="{{ route('institution.borrowings.create') }}" class="btn-library">
            <i class="ti ti-plus"></i> New Borrowing
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-purple-500">
            <p class="text-2xl font-bold text-white">{{ $stats['total'] }}</p>
            <p class="text-xs text-slate-400">📚 Total Borrowings</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-blue-500">
            <p class="text-2xl font-bold text-blue-400">{{ $stats['active'] }}</p>
            <p class="text-xs text-slate-400">📖 Active</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-red-500">
            <p class="text-2xl font-bold text-red-400">{{ $stats['overdue'] }}</p>
            <p class="text-xs text-slate-400">⚠️ Overdue</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-emerald-500">
            <p class="text-2xl font-bold text-emerald-400">{{ $stats['returned'] }}</p>
            <p class="text-xs text-slate-400">✅ Returned</p>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Search by book title..." 
                       value="{{ request('search') }}"
                       class="search-bar">
            </div>
            <select name="status" class="search-bar w-auto">
                <option value="">All Status</option>
                <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>📖 Borrowed</option>
                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>⚠️ Overdue</option>
                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>✅ Returned</option>
            </select>
            <select name="user_id" class="search-bar w-auto">
                <option value="">All Users</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" {{ request('user_id') == $member->id ? 'selected' : '' }}>
                        {{ $member->full_name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-library">
                <i class="ti ti-search"></i> Filter
            </button>
            <a href="{{ route('institution.borrowings.index') }}" class="bg-slate-800 text-slate-400 px-4 py-2 rounded-lg hover:bg-slate-700 transition border border-slate-700">
                <i class="ti ti-x"></i> Clear
            </a>
        </form>
    </div>

    <!-- Borrowings Table -->
    @if($borrowings->count() > 0)
        <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-800/50 text-left border-b border-slate-700">
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Book</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Borrower</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Borrowed</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Due Date</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($borrowings as $borrowing)
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-white">{{ Str::limit($borrowing->book->title, 30) }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-300">{{ $borrowing->user->full_name }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $borrowing->borrowed_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="{{ $borrowing->isOverdue() ? 'text-red-400 font-bold' : 'text-slate-400' }}">
                                        {{ $borrowing->due_date->format('M d, Y') }}
                                        @if($borrowing->isOverdue())
                                            <span class="text-red-400">({{ $borrowing->getDaysLeft() }} days overdue)</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    {!! $borrowing->status_badge !!}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($borrowing->isActive())
                                            <form method="POST" action="{{ route('institution.borrowings.return', $borrowing) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-emerald-400 hover:text-emerald-300 transition" title="Return Book">
                                                    <i class="ti ti-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('institution.borrowings.show', $borrowing) }}" class="text-purple-400 hover:text-purple-300 transition" title="View">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        @if($borrowing->isActive())
                                            <form method="POST" action="{{ route('institution.borrowings.destroy', $borrowing) }}" 
                                                  onsubmit="return confirm('Cancel this borrowing?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-300 transition" title="Cancel">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-6">
            {{ $borrowings->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-12 text-center">
            <i class="ti ti-book text-6xl text-slate-600 mb-4 block"></i>
            <h3 class="text-xl font-semibold text-white/60 mb-2">No Borrowings</h3>
            <p class="text-slate-500">No borrowing records found.</p>
        </div>
    @endif

</div>

@endsection