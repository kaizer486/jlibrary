@extends('layouts.librarian')

@section('title', 'Add New Shelf')
@section('page-title', '🗄️ Add New Shelf')

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
                <i class="ti ti-layout-grid"></i> Add New Shelf
            </h1>
            <p class="text-slate-400 text-sm">Create a new shelf location in your library</p>
        </div>

        <form method="POST" action="{{ route('librarian.shelves.store') }}" class="p-6">
            @csrf

            <div class="space-y-6">
                
                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">
                            Shelf Name <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="e.g., Fiction Section A"
                               class="search-bar">
                        @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">
                            Shelf Code <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="code" value="{{ old('code') }}" required
                               placeholder="e.g., A-01, B-02, C-03"
                               class="search-bar font-mono">
                        <p class="text-xs text-slate-500 mt-1">Unique identifier for the shelf (e.g., A-01, B-02)</p>
                        @error('code') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Category</label>
                        <input type="text" name="category" value="{{ old('category') }}"
                               placeholder="e.g., Fiction, Science, History"
                               class="search-bar">
                        @error('category') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">
                            Status <span class="text-red-400">*</span>
                        </label>
                        <select name="status" class="search-bar">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>✅ Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>⚪ Inactive</option>
                            <option value="full" {{ old('status') == 'full' ? 'selected' : '' }}>🔴 Full</option>
                        </select>
                        @error('status') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Description</label>
                    <textarea name="description" rows="3" 
                              placeholder="Describe the shelf location, contents, or special notes"
                              class="search-bar">{{ old('description') }}</textarea>
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
                            <input type="text" name="floor" value="{{ old('floor') }}"
                                   placeholder="e.g., Ground, 1st, 2nd, Basement"
                                   class="search-bar">
                            @error('floor') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Section</label>
                            <input type="text" name="section" value="{{ old('section') }}"
                                   placeholder="e.g., East Wing, Main Hall, Reading Room"
                                   class="search-bar">
                            @error('section') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Column</label>
                            <input type="text" name="column" value="{{ old('column') }}"
                                   placeholder="e.g., Column 3, Left Wing"
                                   class="search-bar">
                            @error('column') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Row</label>
                            <input type="text" name="row" value="{{ old('row') }}"
                                   placeholder="e.g., Row 2, Top Shelf"
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
                            <input type="number" name="capacity" value="{{ old('capacity', 50) }}" required min="1"
                                   class="search-bar">
                            <p class="text-xs text-slate-500 mt-1">Maximum number of books this shelf can hold</p>
                            @error('capacity') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Current Count</label>
                            <input type="number" name="current_count" value="{{ old('current_count', 0) }}" min="0"
                                   class="search-bar bg-slate-800 cursor-not-allowed" readonly>
                            <p class="text-xs text-slate-500 mt-1">Auto-updated when books are added/removed</p>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex gap-3 pt-6 border-t border-slate-700">
                    <button type="submit" class="btn-library flex-1 justify-center">
                        <i class="ti ti-device-floppy"></i> Create Shelf
                    </button>
                    <a href="{{ route('librarian.shelves.index') }}" class="bg-slate-800 text-slate-400 px-6 py-2.5 rounded-lg hover:bg-slate-700 transition text-center border border-slate-700">
                        Cancel
                    </a>
                </div>

            </div>
        </form>
    </div>

    <!-- Code Helper -->
    <div class="mt-4 bg-slate-900 border border-slate-700 rounded-xl p-4">
        <h4 class="font-semibold text-slate-300 text-sm flex items-center gap-2">
            <i class="ti ti-lightbulb text-purple-400"></i> Shelf Code Suggestions
        </h4>
        <div class="grid grid-cols-3 md:grid-cols-6 gap-2 mt-2">
            <span class="text-xs bg-slate-800 border border-slate-700 rounded-lg px-3 py-1.5 text-center font-mono text-purple-300 hover:bg-slate-700 cursor-pointer" onclick="document.querySelector('[name=code]').value='A-01'">A-01</span>
            <span class="text-xs bg-slate-800 border border-slate-700 rounded-lg px-3 py-1.5 text-center font-mono text-purple-300 hover:bg-slate-700 cursor-pointer" onclick="document.querySelector('[name=code]').value='A-02'">A-02</span>
            <span class="text-xs bg-slate-800 border border-slate-700 rounded-lg px-3 py-1.5 text-center font-mono text-purple-300 hover:bg-slate-700 cursor-pointer" onclick="document.querySelector('[name=code]').value='B-01'">B-01</span>
            <span class="text-xs bg-slate-800 border border-slate-700 rounded-lg px-3 py-1.5 text-center font-mono text-purple-300 hover:bg-slate-700 cursor-pointer" onclick="document.querySelector('[name=code]').value='B-02'">B-02</span>
            <span class="text-xs bg-slate-800 border border-slate-700 rounded-lg px-3 py-1.5 text-center font-mono text-purple-300 hover:bg-slate-700 cursor-pointer" onclick="document.querySelector('[name=code]').value='C-01'">C-01</span>
            <span class="text-xs bg-slate-800 border border-slate-700 rounded-lg px-3 py-1.5 text-center font-mono text-purple-300 hover:bg-slate-700 cursor-pointer" onclick="document.querySelector('[name=code]').value='D-01'">D-01</span>
        </div>
    </div>

</div>

<script>
    // Auto-generate code suggestion based on name
    document.querySelector('[name="name"]').addEventListener('input', function() {
        const codeInput = document.querySelector('[name="code"]');
        if (!codeInput.value || codeInput.value === '') {
            const name = this.value.trim();
            if (name) {
                const prefix = name.substring(0, 1).toUpperCase();
                const random = String(Math.floor(Math.random() * 90) + 10).padStart(2, '0');
                codeInput.value = prefix + '-' + random;
            }
        }
    });
</script>

@endsection