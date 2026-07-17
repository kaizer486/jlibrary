@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">📚 Global Library</h1>
        <p class="text-gray-600">Discover thousands of books from all libraries and institutions</p>
    </div>
    
    <!-- Search and Filter Bar -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-8">
        <form method="GET" action="{{ route('library.index') }}" class="flex flex-col gap-3">
            <div class="flex flex-col md:flex-row gap-3 items-start md:items-center">
                <!-- Search Input -->
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" placeholder="Search by title or author..." 
                           value="{{ request('search') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500 focus:border-transparent">
                </div>
                
                <!-- Category Filter -->
                <div class="flex-1 min-w-[150px] md:max-w-xs">
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500">
                        <option value="">📚 All Categories</option>
                        
                        <!-- Technology & Science -->
                        <optgroup label="🔬 Technology & Science">
                            <option value="Computer Science & Information Technology" {{ request('category') == 'Computer Science & Information Technology' ? 'selected' : '' }}>Computer Science & Information Technology</option>
                            <option value="Artificial Intelligence & Data Science" {{ request('category') == 'Artificial Intelligence & Data Science' ? 'selected' : '' }}>Artificial Intelligence & Data Science</option>
                            <option value="Engineering & Technology" {{ request('category') == 'Engineering & Technology' ? 'selected' : '' }}>Engineering & Technology</option>
                            <option value="Mathematics & Statistics" {{ request('category') == 'Mathematics & Statistics' ? 'selected' : '' }}>Mathematics & Statistics</option>
                            <option value="Physical Sciences" {{ request('category') == 'Physical Sciences' ? 'selected' : '' }}>Physical Sciences</option>
                            <option value="Biological Sciences" {{ request('category') == 'Biological Sciences' ? 'selected' : '' }}>Biological Sciences</option>
                        </optgroup>
                        
                        <!-- Health & Medicine -->
                        <optgroup label="🏥 Health & Medicine">
                            <option value="Health & Medical Sciences" {{ request('category') == 'Health & Medical Sciences' ? 'selected' : '' }}>Health & Medical Sciences</option>
                            <option value="Public Health" {{ request('category') == 'Public Health' ? 'selected' : '' }}>Public Health</option>
                            <option value="Agriculture & Veterinary Sciences" {{ request('category') == 'Agriculture & Veterinary Sciences' ? 'selected' : '' }}>Agriculture & Veterinary Sciences</option>
                            <option value="Environmental & Earth Sciences" {{ request('category') == 'Environmental & Earth Sciences' ? 'selected' : '' }}>Environmental & Earth Sciences</option>
                        </optgroup>
                        
                        <!-- Business & Finance -->
                        <optgroup label="💼 Business & Finance">
                            <option value="Business & Management" {{ request('category') == 'Business & Management' ? 'selected' : '' }}>Business & Management</option>
                            <option value="Economics & Finance" {{ request('category') == 'Economics & Finance' ? 'selected' : '' }}>Economics & Finance</option>
                            <option value="Accounting" {{ request('category') == 'Accounting' ? 'selected' : '' }}>Accounting</option>
                            <option value="Marketing" {{ request('category') == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                            <option value="Entrepreneurship" {{ request('category') == 'Entrepreneurship' ? 'selected' : '' }}>Entrepreneurship</option>
                        </optgroup>
                        
                        <!-- Social Sciences & Humanities -->
                        <optgroup label="📖 Social Sciences & Humanities">
                            <option value="Law" {{ request('category') == 'Law' ? 'selected' : '' }}>Law</option>
                            <option value="Education" {{ request('category') == 'Education' ? 'selected' : '' }}>Education</option>
                            <option value="Social Sciences" {{ request('category') == 'Social Sciences' ? 'selected' : '' }}>Social Sciences</option>
                            <option value="Psychology" {{ request('category') == 'Psychology' ? 'selected' : '' }}>Psychology</option>
                            <option value="Political Science & Public Administration" {{ request('category') == 'Political Science & Public Administration' ? 'selected' : '' }}>Political Science & Public Administration</option>
                            <option value="Humanities" {{ request('category') == 'Humanities' ? 'selected' : '' }}>Humanities</option>
                            <option value="Philosophy" {{ request('category') == 'Philosophy' ? 'selected' : '' }}>Philosophy</option>
                            <option value="Languages & Linguistics" {{ request('category') == 'Languages & Linguistics' ? 'selected' : '' }}>Languages & Linguistics</option>
                            <option value="Literature" {{ request('category') == 'Literature' ? 'selected' : '' }}>Literature</option>
                            <option value="History & Archaeology" {{ request('category') == 'History & Archaeology' ? 'selected' : '' }}>History & Archaeology</option>
                            <option value="Geography & Tourism" {{ request('category') == 'Geography & Tourism' ? 'selected' : '' }}>Geography & Tourism</option>
                            <option value="Religion & Theology" {{ request('category') == 'Religion & Theology' ? 'selected' : '' }}>Religion & Theology</option>
                        </optgroup>
                        
                        <!-- Arts & Design -->
                        <optgroup label="🎨 Arts & Design">
                            <option value="Arts, Design & Music" {{ request('category') == 'Arts, Design & Music' ? 'selected' : '' }}>Arts, Design & Music</option>
                            <option value="Architecture & Urban Planning" {{ request('category') == 'Architecture & Urban Planning' ? 'selected' : '' }}>Architecture & Urban Planning</option>
                        </optgroup>
                        
                        <!-- General Reading -->
                        <optgroup label="📚 General Reading">
                            <option value="Children's Books" {{ request('category') == "Children's Books" ? 'selected' : '' }}>Children's Books</option>
                            <option value="Fiction" {{ request('category') == 'Fiction' ? 'selected' : '' }}>Fiction</option>
                            <option value="Non-Fiction" {{ request('category') == 'Non-Fiction' ? 'selected' : '' }}>Non-Fiction</option>
                            <option value="Biographies & Memoirs" {{ request('category') == 'Biographies & Memoirs' ? 'selected' : '' }}>Biographies & Memoirs</option>
                            <option value="Self-Help & Personal Development" {{ request('category') == 'Self-Help & Personal Development' ? 'selected' : '' }}>Self-Help & Personal Development</option>
                            <option value="Leadership" {{ request('category') == 'Leadership' ? 'selected' : '' }}>Leadership</option>
                        </optgroup>
                        
                        <!-- Academic & Research -->
                        <optgroup label="📝 Academic & Research">
                            <option value="Research & Academic Publications" {{ request('category') == 'Research & Academic Publications' ? 'selected' : '' }}>Research & Academic Publications</option>
                            <option value="Journals & Conference Proceedings" {{ request('category') == 'Journals & Conference Proceedings' ? 'selected' : '' }}>Journals & Conference Proceedings</option>
                            <option value="Theses & Dissertations" {{ request('category') == 'Theses & Dissertations' ? 'selected' : '' }}>Theses & Dissertations</option>
                        </optgroup>
                        
                        <!-- Government & Reference -->
                        <optgroup label="📋 Government & Reference">
                            <option value="Government Publications" {{ request('category') == 'Government Publications' ? 'selected' : '' }}>Government Publications</option>
                            <option value="Policies, Acts & Regulations" {{ request('category') == 'Policies, Acts & Regulations' ? 'selected' : '' }}>Policies, Acts & Regulations</option>
                            <option value="Reports & White Papers" {{ request('category') == 'Reports & White Papers' ? 'selected' : '' }}>Reports & White Papers</option>
                            <option value="Reference Books" {{ request('category') == 'Reference Books' ? 'selected' : '' }}>Reference Books</option>
                            <option value="Open Educational Resources (OER)" {{ request('category') == 'Open Educational Resources (OER)' ? 'selected' : '' }}>Open Educational Resources (OER)</option>
                            <option value="Newspapers & Magazines" {{ request('category') == 'Newspapers & Magazines' ? 'selected' : '' }}>Newspapers & Magazines</option>
                            <option value="Encyclopedias & Dictionaries" {{ request('category') == 'Encyclopedias & Dictionaries' ? 'selected' : '' }}>Encyclopedias & Dictionaries</option>
                        </optgroup>
                    </select>
                </div>
                
                <!-- Search Button -->
                <button type="submit" class="bg-jlibrary-600 text-white px-6 py-2 rounded-lg hover:bg-jlibrary-700 transition whitespace-nowrap">
                    <i class="ti ti-search"></i> Search
                </button>
                
                <!-- Clear Filters -->
                @if(request('search') || request('category'))
                    <a href="{{ route('library.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition whitespace-nowrap">
                        <i class="ti ti-x"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>
    
    <!-- Results Count -->
    <div class="mb-4 text-sm text-gray-500">
        Found {{ $books->total() }} books
        @if(request('category'))
            <span class="font-medium text-jlibrary-600">in "{{ request('category') }}"</span>
        @endif
    </div>
    
    <!-- Books Grid -->
    @if($books->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($books as $book)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group">
                    <!-- Book Cover -->
                   @php
    // ✅ Determine the correct URL for the book
    if ($book->institution_id) {
        $bookUrl = url('/institution/' . $book->institution_id . '/library/' . $book->id);
    } else {
        $bookUrl = route('library.show', $book->id);
    }
@endphp

<a href="{{ $bookUrl }}" class="block">
    <div class="relative h-56 bg-gradient-to-br from-jlibrary-500 to-jlibrary-700 flex items-center justify-center">
       @if($book->cover_image)
    <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
@else
    <i class="ti ti-book text-6xl text-white/50"></i>
@endif
        
        <!-- Institution Badge - Only show if institution exists -->
        @if($book->institution_id && $book->institution)
            <div class="absolute bottom-2 left-2 bg-black/70 text-white px-2 py-1 rounded-lg text-xs max-w-[120px] truncate">
                <i class="ti ti-building"></i> {{ $book->institution->name ?? '' }}
            </div>
        @else
            <div class="absolute bottom-2 left-2 bg-purple-600/90 text-white px-2 py-1 rounded-lg text-xs">
                🌐 Global
            </div>
        @endif
        
        <!-- Bookmark Button -->
        <div class="absolute bottom-2 right-2 z-10">
            <x-bookmark-button :item="$book" type="book" size="sm" />
        </div>
    </div>
</a>              
                    <!-- Book Info -->
                    <div class="p-4">
                        <a href="{{ $bookUrl }}" class="block">
                            <h3 class="font-semibold text-lg text-gray-900 mb-1 line-clamp-1 hover:text-jlibrary-600 transition">{{ $book->title }}</h3>
                        </a>
                        <p class="text-gray-500 text-sm mb-2">{{ $book->author ?? 'Unknown' }}</p>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ Str::limit($book->description, 80) }}</p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 text-sm text-gray-500">
                                <span><i class="ti ti-download"></i> {{ number_format($book->downloads ?? 0) }}</span>
                                <span><i class="ti ti-eye"></i> {{ number_format($book->views_count ?? 0) }}</span>
                            </div>
                            <a href="{{ $bookUrl }}" 
                               class="bg-jlibrary-600 text-white px-4 py-1.5 rounded-lg hover:bg-jlibrary-700 transition text-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $books->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="ti ti-books text-6xl text-gray-400 mb-4 block"></i>
            <h3 class="text-xl font-semibold mb-2">No books found</h3>
            <p class="text-gray-500">Try adjusting your search or filter criteria</p>
            <a href="{{ route('library.index') }}" class="inline-block mt-4 text-jlibrary-600 hover:text-jlibrary-700">Clear filters</a>
        </div>
    @endif
</div>
@endsection