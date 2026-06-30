@extends('layouts.librarian')

@section('title', 'Shelf Management')
@section('page-title', '🗄️ Shelf Management')

@section('content')

<div class="max-w-7xl mx-auto">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p class="text-slate-400 text-sm">Organize your library shelves</p>
        </div>
        <a href="{{ route('institution.shelves.create') }}" class="btn-library">
            <i class="ti ti-plus"></i> Add Shelf
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-purple-500">
            <p class="text-2xl font-bold text-white">{{ $stats['total'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">📚 Total Shelves</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-emerald-500">
            <p class="text-2xl font-bold text-emerald-400">{{ $stats['active'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">✅ Active</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-red-500">
            <p class="text-2xl font-bold text-red-400">{{ $stats['full'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">🔴 Full</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-slate-500">
            <p class="text-2xl font-bold text-slate-400">{{ $stats['inactive'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">⚪ Inactive</p>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Search by name, code, or category..." 
                       value="{{ request('search') }}"
                       class="search-bar">
            </div>
            <select name="status" class="search-bar w-auto">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>✅ Active</option>
                <option value="full" {{ request('status') == 'full' ? 'selected' : '' }}>🔴 Full</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>⚪ Inactive</option>
            </select>
            <button type="submit" class="btn-library">
                <i class="ti ti-search"></i> Filter
            </button>
            <a href="{{ route('institution.shelves.index') }}" class="bg-slate-800 text-slate-400 px-4 py-2 rounded-lg hover:bg-slate-700 transition border border-slate-700">
                <i class="ti ti-x"></i> Clear
            </a>
        </form>
    </div>

    <!-- Shelves Grid -->
    @if($shelves->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($shelves as $shelf)
                @php
                    $bookCount = $shelf->books()->count();
                    $percentage = $shelf->capacity > 0 ? round(($bookCount / $shelf->capacity) * 100) : 0;
                    $displayBooks = min($bookCount, 20);
                @endphp
                
                <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden hover:shadow-lg transition 
                    @if($shelf->status === 'full') border-red-500/30
                    @elseif($shelf->status === 'active') border-emerald-500/30
                    @else border-slate-700 @endif">
                    
                    <div class="p-4">
                        <!-- Header -->
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-white text-lg">{{ $shelf->name }}</h3>
                                <p class="text-sm text-slate-400">Code: <span class="font-mono font-semibold text-purple-400">{{ $shelf->code }}</span></p>
                            </div>
                            {!! $shelf->status_badge !!}
                        </div>
                        
                        @if($shelf->category)
                            <p class="text-sm text-purple-300 mt-1">
                                <i class="ti ti-tag text-purple-400"></i> {{ $shelf->category }}
                            </p>
                        @endif
                        
                        <!-- Location -->
                        @if($shelf->getFullLocationAttribute() && $shelf->getFullLocationAttribute() !== ' |  |  | ')
                            <div class="mt-2 flex flex-wrap gap-1">
                                @if($shelf->floor)
                                    <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded-full">🏢 {{ $shelf->floor }}</span>
                                @endif
                                @if($shelf->section)
                                    <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded-full">📂 {{ $shelf->section }}</span>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Stats -->
                        <div class="mt-3 flex items-center justify-between text-sm">
                            <span class="text-slate-300">
                                <i class="ti ti-books text-purple-400"></i> {{ $bookCount }} books
                            </span>
                            <span class="text-slate-300">
                                <i class="ti ti-users text-purple-400"></i> {{ $shelf->current_count }}/{{ $shelf->capacity }}
                            </span>
                        </div>
                        
                        <!-- Visual Book Count (Mini Books) -->
                        <div class="mt-2 flex items-center gap-0.5 flex-wrap">
                            @for($i = 0; $i < $displayBooks; $i++)
                                <span class="text-[10px] text-purple-400">📕</span>
                            @endfor
                            @if($bookCount > 20)
                                <span class="text-[10px] text-slate-500 ml-1">+{{ $bookCount - 20 }}</span>
                            @endif
                            @if($bookCount == 0)
                                <span class="text-[10px] text-slate-600">Empty shelf</span>
                            @endif
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mt-2 w-full bg-slate-800 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full transition-all duration-500 
                                @if($percentage >= 90) bg-red-500
                                @elseif($percentage >= 70) bg-yellow-500
                                @else bg-emerald-500 @endif" 
                                style="width: {{ $percentage }}%"></div>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 text-right">{{ $percentage }}% capacity</p>
                        
                        @if($shelf->description)
                            <p class="text-sm text-slate-400 mt-2 line-clamp-2">{{ $shelf->description }}</p>
                        @endif
                        
                        <!-- Actions -->
                        <div class="mt-3 flex gap-2 pt-3 border-t border-slate-700">
                            <a href="{{ route('institution.shelves.show', $shelf) }}" class="text-purple-400 hover:text-purple-300 text-sm font-medium transition">
                                <i class="ti ti-eye"></i> View
                            </a>
                            <a href="{{ route('institution.shelves.edit', $shelf) }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium transition">
                                <i class="ti ti-edit"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('institution.shelves.destroy', $shelf) }}" 
                                  onsubmit="return confirm('Delete this shelf?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 text-sm font-medium transition">
                                    <i class="ti ti-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $shelves->links() }}</div>
    @else
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-12 text-center">
            <i class="ti ti-books text-6xl text-slate-600 mb-4 block"></i>
            <h3 class="text-xl font-semibold text-white/60 mb-2">No Shelves Found</h3>
            <p class="text-slate-400">Start organizing your library by creating shelves.</p>
            <a href="{{ route('institution.shelves.create') }}" class="inline-block mt-4 btn-library">
                <i class="ti ti-plus"></i> Create First Shelf
            </a>
        </div>
    @endif
</div>

@endsection