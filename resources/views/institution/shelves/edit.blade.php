@extends('layouts.librarian')

@section('title', 'Edit Shelf')
@section('page-title', '✏️ Edit Shelf')

@section('content')

<div class="max-w-4xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('librarian.shelves.index') }}" class="text-purple-400 hover:text-purple-300 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Shelves
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-700 rounded-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-purple-900/30 to-pink-900/30 px-6 py-4 border-b border-slate-700">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="ti ti-edit"></i> Edit Shelf
            </h1>
            <p class="text-slate-400 text-sm">Update shelf details - {{ $shelf->code }}</p>
        </div>

        <form method="POST" action="{{ route('librarian.shelves.update', $shelf) }}" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                
                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">
                            Shelf Name <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $shelf->name) }}" required
                               class="search-bar">
                        @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">
                            Shelf Code <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="code" value="{{ old('code', $shelf->code) }}" required
                               class="search-bar font-mono">
                        <p class="text-xs text-slate-500 mt-1">Unique identifier for the shelf</p>
                        @error('code') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Category</label>
                        <input type="text" name="category" value="{{ old('category', $shelf->category) }}"
                               class="search-bar">
                        @error('category') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">
                            Status <span class="text-red-400">*</span>
                        </label>
                        <select name="status" class="search-bar">
                            <option value="active" {{ old('status', $shelf->status) == 'active' ? 'selected' : '' }}>✅ Active</option>
                            <option value="inactive" {{ old('status', $shelf->status) == 'inactive' ? 'selected' : '' }}>⚪ Inactive</option>
                            <option value="full" {{ old('status', $shelf->status) == 'full' ? 'selected' : '' }}>🔴 Full</option>
                        </select>
                        @error('status') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Description</label>
                    <textarea name="description" rows="3" 
                              class="search-bar">{{ old('description', $shelf->description) }}</textarea>
                    @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Location -->
                <div class="border-t border-slate-700 pt-6">
                    <h3 class="font-semibold text-white mb-4 flex items-center gap-2">
                        <i class="ti ti-map-pin text-purple-400"></i> Physical Location
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Floor</label>
                            <input type="text" name="floor" value="{{ old('floor', $shelf->floor) }}"
                                   placeholder="e.g., Ground, 1st, 2nd"
                                   class="search-bar">
                            @error('floor') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Section</label>
                            <input type="text" name="section" value="{{ old('section', $shelf->section) }}"
                                   placeholder="e.g., East Wing, Main Hall"
                                   class="search-bar">
                            @error('section') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Column</label>
                            <input type="text" name="column" value="{{ old('column', $shelf->column) }}"
                                   placeholder="e.g., Column 3"
                                   class="search-bar">
                            @error('column') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Row</label>
                            <input type="text" name="row" value="{{ old('row', $shelf->row) }}"
                                   placeholder="e.g., Row 2"
                                   class="search-bar">
                            @error('row') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Capacity -->
                <div class="border-t border-slate-700 pt-6">
                    <h3 class="font-semibold text-white mb-4 flex items-center gap-2">
                        <i class="ti ti-cpu text-purple-400"></i> Capacity
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">
                                Capacity <span class="text-red-400">*</span>
                            </label>
                            <input type="number" name="capacity" value="{{ old('capacity', $shelf->capacity) }}" required min="1"
                                   class="search-bar">
                            <p class="text-xs text-slate-500 mt-1">Maximum number of books this shelf can hold</p>
                            @error('capacity') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Current Count</label>
                            <input type="number" name="current_count" value="{{ old('current_count', $shelf->current_count) }}" min="0"
                                   class="search-bar">
                            <p class="text-xs text-slate-500 mt-1">Current number of books on this shelf</p>
                            @error('current_count') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Books on this shelf -->
                <div class="border-t border-slate-700 pt-6">
                    <h3 class="font-semibold text-white mb-4 flex items-center gap-2">
                        <i class="ti ti-books text-purple-400"></i> Books on this Shelf ({{ $shelf->books()->count() }})
                    </h3>
                    
                    @if($shelf->books()->count() > 0)
                        <div class="bg-slate-800 rounded-xl p-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach($shelf->books()->limit(10)->get() as $book)
                                    <span class="text-xs bg-slate-700 border border-slate-600 rounded-full px-3 py-1 text-slate-300">
                                        {{ Str::limit($book->title, 20) }}
                                    </span>
                                @endforeach
                                @if($shelf->books()->count() > 10)
                                    <span class="text-xs text-slate-500">+{{ $shelf->books()->count() - 10 }} more</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-slate-500">No books on this shelf yet.</p>
                    @endif
                </div>

                <!-- Submit -->
                <div class="flex gap-3 pt-6 border-t border-slate-700">
                    <button type="submit" class="btn-library flex-1 justify-center">
                        <i class="ti ti-device-floppy"></i> Update Shelf
                    </button>
                    <a href="{{ route('librarian.shelves.index') }}" class="bg-slate-800 text-slate-400 px-6 py-2.5 rounded-lg hover:bg-slate-700 transition text-center border border-slate-700">
                        Cancel
                    </a>
                </div>

            </div>
        </form>
    </div>

</div>

@endsection