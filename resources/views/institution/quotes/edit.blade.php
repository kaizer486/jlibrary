@extends('layouts.institution')

@section('title', 'Edit Institution Quote')

@section('content')

@php
    // ==========================================
    // SECURITY CHECKS
    // ==========================================
    
    // Check if user belongs to an institution
    if (!auth()->user()->institution_id) {
        abort(403, 'You do not belong to any institution.');
    }
    
    // Check if institution exists
    if (!isset($institution) || !$institution) {
        abort(404, 'Institution not found.');
    }
    
    // Check if user has access to this institution
    if (auth()->user()->institution_id != $institution->id) {
        abort(403, 'You do not have access to this institution.');
    }
    
    // Check if quote exists and belongs to this institution
    if (!isset($quote) || !$quote) {
        abort(404, 'Quote not found.');
    }
    
    if ($quote->institution_id != $institution->id) {
        abort(403, 'This quote does not belong to your institution.');
    }
    
    // Check if user has permission to edit this quote
    if (!auth()->user()->can('update', $quote)) {
        abort(403, 'You do not have permission to edit this quote.');
    }
@endphp

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('institution.quotes.index') }}" class="text-purple-600 hover:text-purple-700 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Quotes
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <h1 class="text-xl font-bold text-white">✏️ Edit Institution Quote</h1>
            <p class="text-blue-100 text-sm">Update quote for {{ $institution->name }}</p>
        </div>
        
        <!-- ========================================== -->
        <!-- QUOTE PREVIEW                               -->
        <!-- ========================================== -->
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 m-6 border border-purple-200">
            <p class="text-sm text-gray-600 mb-2 flex items-center gap-2">
                <i class="ti ti-eye text-purple-600"></i> Preview:
            </p>
            <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                <p class="text-gray-800 italic text-lg" id="preview_text">
                    "{{ old('quote_text', $quote->quote_text) }}"
                </p>
                <p class="text-gray-500 text-sm mt-2" id="preview_author">
                    — {{ old('author', $quote->author ?? 'Anonymous') }}
                </p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('institution.quotes.update', $quote) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Quote Text <span class="text-red-500">*</span>
                    </label>
                    <textarea name="quote_text" id="quote_text" rows="4" required 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                              placeholder="Enter an inspiring quote for your institution members...">{{ old('quote_text', $quote->quote_text) }}</textarea>
                    @error('quote_text') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Author</label>
                    <input type="text" name="author" id="author" value="{{ old('author', $quote->author) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="e.g., Albert Einstein, Nelson Mandela...">
                    @error('author') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white">
                            @foreach($categories ?? [] as $key => $label)
                                <option value="{{ $key }}" {{ old('category', $quote->category) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white">
                            <option value="active" {{ old('status', $quote->status) == 'active' ? 'selected' : '' }}>✅ Active</option>
                            <option value="inactive" {{ old('status', $quote->status) == 'inactive' ? 'selected' : '' }}>❌ Inactive</option>
                            <option value="draft" {{ old('status', $quote->status) == 'draft' ? 'selected' : '' }}>📝 Draft</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Schedule Date (Optional)</label>
                    <input type="date" name="scheduled_date" value="{{ old('scheduled_date', $quote->scheduled_date) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <p class="text-xs text-gray-400 mt-1">Leave empty to show randomly</p>
                    @error('scheduled_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            
            <div class="flex gap-3 mt-8 pt-6 border-t border-gray-200">
                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold">
                    <i class="ti ti-device-floppy"></i> Update Quote
                </button>
                <a href="{{ route('institution.quotes.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- SCRIPTS FOR LIVE PREVIEW                   -->
<!-- ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const quoteText = document.getElementById('quote_text');
    const author = document.getElementById('author');
    const previewText = document.getElementById('preview_text');
    const previewAuthor = document.getElementById('preview_author');
    
    if (quoteText) {
        quoteText.addEventListener('input', function() {
            const text = this.value || 'Your quote will appear here';
            previewText.textContent = '"' + text + '"';
        });
    }
    
    if (author) {
        author.addEventListener('input', function() {
            const name = this.value || 'Anonymous';
            previewAuthor.textContent = '— ' + name;
        });
    }
});
</script>

@endsection