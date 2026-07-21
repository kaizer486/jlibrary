@extends('layouts.app')

@section('content')
<div class="container mx-auto px-2 py-3 md:px-4 md:py-6">
    <!-- Header -->
    <div class="mb-3 md:mb-6">
        <h1 class="text-lg md:text-3xl font-bold text-gray-900 mb-0.5 md:mb-1">📚 Global Library</h1>
        <p class="text-[10px] md:text-sm text-gray-600">Discover thousands of books from all libraries and institutions</p>
    </div>
    
    <!-- Search and Filter Bar -->
    <div class="bg-white rounded-lg md:rounded-xl shadow-sm p-2 md:p-4 mb-3 md:mb-6">
        <form method="GET" action="{{ route('library.index') }}" class="flex flex-col gap-1.5 md:gap-3">
            <div class="flex flex-col md:flex-row gap-1.5 md:gap-3 items-start md:items-center">
                <div class="w-full md:flex-1">
                    <input type="text" name="search" placeholder="Search..." 
                           value="{{ request('search') }}"
                           class="w-full px-2 md:px-4 py-1 md:py-2 text-[10px] md:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500 focus:border-transparent">
                </div>
                
                <div class="w-full md:flex-1 md:max-w-xs">
                    <select name="category" class="w-full px-2 md:px-4 py-1 md:py-2 text-[10px] md:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500 bg-white">
                        <option value="">📚 All Categories</option>
                        <optgroup label="🔬 Technology & Science">
                            <option value="Computer Science & Information Technology" {{ request('category') == 'Computer Science & Information Technology' ? 'selected' : '' }}>Computer Science & IT</option>
                            <option value="Artificial Intelligence & Data Science" {{ request('category') == 'Artificial Intelligence & Data Science' ? 'selected' : '' }}>AI & Data Science</option>
                            <option value="Engineering & Technology" {{ request('category') == 'Engineering & Technology' ? 'selected' : '' }}>Engineering & Technology</option>
                            <option value="Mathematics & Statistics" {{ request('category') == 'Mathematics & Statistics' ? 'selected' : '' }}>Mathematics & Statistics</option>
                            <option value="Physical Sciences" {{ request('category') == 'Physical Sciences' ? 'selected' : '' }}>Physical Sciences</option>
                            <option value="Biological Sciences" {{ request('category') == 'Biological Sciences' ? 'selected' : '' }}>Biological Sciences</option>
                        </optgroup>
                        <optgroup label="🏥 Health & Medicine">
                            <option value="Health & Medical Sciences" {{ request('category') == 'Health & Medical Sciences' ? 'selected' : '' }}>Health & Medical Sciences</option>
                            <option value="Public Health" {{ request('category') == 'Public Health' ? 'selected' : '' }}>Public Health</option>
                            <option value="Agriculture & Veterinary Sciences" {{ request('category') == 'Agriculture & Veterinary Sciences' ? 'selected' : '' }}>Agriculture & Veterinary</option>
                            <option value="Environmental & Earth Sciences" {{ request('category') == 'Environmental & Earth Sciences' ? 'selected' : '' }}>Environmental & Earth Sciences</option>
                        </optgroup>
                        <optgroup label="💼 Business & Finance">
                            <option value="Business & Management" {{ request('category') == 'Business & Management' ? 'selected' : '' }}>Business & Management</option>
                            <option value="Economics & Finance" {{ request('category') == 'Economics & Finance' ? 'selected' : '' }}>Economics & Finance</option>
                            <option value="Accounting" {{ request('category') == 'Accounting' ? 'selected' : '' }}>Accounting</option>
                            <option value="Marketing" {{ request('category') == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                            <option value="Entrepreneurship" {{ request('category') == 'Entrepreneurship' ? 'selected' : '' }}>Entrepreneurship</option>
                        </optgroup>
                        <optgroup label="📖 Social Sciences & Humanities">
                            <option value="Law" {{ request('category') == 'Law' ? 'selected' : '' }}>Law</option>
                            <option value="Education" {{ request('category') == 'Education' ? 'selected' : '' }}>Education</option>
                            <option value="Social Sciences" {{ request('category') == 'Social Sciences' ? 'selected' : '' }}>Social Sciences</option>
                            <option value="Psychology" {{ request('category') == 'Psychology' ? 'selected' : '' }}>Psychology</option>
                            <option value="Political Science & Public Administration" {{ request('category') == 'Political Science & Public Administration' ? 'selected' : '' }}>Political Science</option>
                            <option value="Humanities" {{ request('category') == 'Humanities' ? 'selected' : '' }}>Humanities</option>
                            <option value="Philosophy" {{ request('category') == 'Philosophy' ? 'selected' : '' }}>Philosophy</option>
                            <option value="Languages & Linguistics" {{ request('category') == 'Languages & Linguistics' ? 'selected' : '' }}>Languages & Linguistics</option>
                            <option value="Literature" {{ request('category') == 'Literature' ? 'selected' : '' }}>Literature</option>
                            <option value="History & Archaeology" {{ request('category') == 'History & Archaeology' ? 'selected' : '' }}>History & Archaeology</option>
                            <option value="Geography & Tourism" {{ request('category') == 'Geography & Tourism' ? 'selected' : '' }}>Geography & Tourism</option>
                            <option value="Religion & Theology" {{ request('category') == 'Religion & Theology' ? 'selected' : '' }}>Religion & Theology</option>
                        </optgroup>
                        <optgroup label="🎨 Arts & Design">
                            <option value="Arts, Design & Music" {{ request('category') == 'Arts, Design & Music' ? 'selected' : '' }}>Arts, Design & Music</option>
                            <option value="Architecture & Urban Planning" {{ request('category') == 'Architecture & Urban Planning' ? 'selected' : '' }}>Architecture & Urban Planning</option>
                        </optgroup>
                        <optgroup label="📚 General Reading">
                            <option value="Children's Books" {{ request('category') == "Children's Books" ? 'selected' : '' }}>Children's Books</option>
                            <option value="Fiction" {{ request('category') == 'Fiction' ? 'selected' : '' }}>Fiction</option>
                            <option value="Non-Fiction" {{ request('category') == 'Non-Fiction' ? 'selected' : '' }}>Non-Fiction</option>
                            <option value="Biographies & Memoirs" {{ request('category') == 'Biographies & Memoirs' ? 'selected' : '' }}>Biographies & Memoirs</option>
                            <option value="Self-Help & Personal Development" {{ request('category') == 'Self-Help & Personal Development' ? 'selected' : '' }}>Self-Help & Personal Development</option>
                            <option value="Leadership" {{ request('category') == 'Leadership' ? 'selected' : '' }}>Leadership</option>
                        </optgroup>
                        <optgroup label="📝 Academic & Research">
                            <option value="Research & Academic Publications" {{ request('category') == 'Research & Academic Publications' ? 'selected' : '' }}>Research & Academic</option>
                            <option value="Journals & Conference Proceedings" {{ request('category') == 'Journals & Conference Proceedings' ? 'selected' : '' }}>Journals & Proceedings</option>
                            <option value="Theses & Dissertations" {{ request('category') == 'Theses & Dissertations' ? 'selected' : '' }}>Theses & Dissertations</option>
                        </optgroup>
                        <optgroup label="📋 Government & Reference">
                            <option value="Government Publications" {{ request('category') == 'Government Publications' ? 'selected' : '' }}>Government Publications</option>
                            <option value="Policies, Acts & Regulations" {{ request('category') == 'Policies, Acts & Regulations' ? 'selected' : '' }}>Policies & Regulations</option>
                            <option value="Reports & White Papers" {{ request('category') == 'Reports & White Papers' ? 'selected' : '' }}>Reports & White Papers</option>
                            <option value="Reference Books" {{ request('category') == 'Reference Books' ? 'selected' : '' }}>Reference Books</option>
                            <option value="Open Educational Resources (OER)" {{ request('category') == 'Open Educational Resources (OER)' ? 'selected' : '' }}>Open Educational Resources</option>
                            <option value="Newspapers & Magazines" {{ request('category') == 'Newspapers & Magazines' ? 'selected' : '' }}>Newspapers & Magazines</option>
                            <option value="Encyclopedias & Dictionaries" {{ request('category') == 'Encyclopedias & Dictionaries' ? 'selected' : '' }}>Encyclopedias & Dictionaries</option>
                        </optgroup>
                    </select>
                </div>
                
                <button type="submit" class="w-full md:w-auto bg-jlibrary-600 text-white px-3 md:px-5 py-1 md:py-2 text-[10px] md:text-sm rounded-lg hover:bg-jlibrary-700 transition whitespace-nowrap">
                    <i class="ti ti-search"></i> <span class="hidden sm:inline">Search</span>
                </button>
                
                @if(request('search') || request('category'))
                    <a href="{{ route('library.index') }}" class="w-full md:w-auto bg-gray-200 text-gray-700 px-3 md:px-4 py-1 md:py-2 text-[10px] md:text-sm rounded-lg hover:bg-gray-300 transition whitespace-nowrap text-center">
                        <i class="ti ti-x"></i> <span class="hidden sm:inline">Clear</span>
                    </a>
                @endif
            </div>
        </form>
    </div>
    
    <!-- Results Count -->
    <div class="mb-2 md:mb-4 text-[10px] md:text-sm text-gray-500">
        Found <span class="font-semibold text-gray-700">{{ $books->total() }}</span> books
        @if(request('category'))
            <span class="font-medium text-jlibrary-600">in "{{ request('category') }}"</span>
        @endif
    </div>
    
    <!-- Books Grid -->
    @if($books->count() > 0)
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-1 md:gap-2 lg:gap-3">
            @foreach($books as $book)
                @php
                    $bookUrl = $book->institution_id 
                        ? url('/institution/' . $book->institution_id . '/library/' . $book->id)
                        : route('library.show', $book->id);
                @endphp
                
                <div class="bg-white rounded-md md:rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group border border-gray-200 hover:border-jlibrary-400 max-w-[90px] md:max-w-[180px] lg:max-w-[200px] xl:max-w-[220px] mx-auto w-full">
                    <a href="{{ $bookUrl }}" class="block">
                        <div class="relative w-full aspect-[2/3] max-h-[100px] md:max-h-[200px] lg:max-h-[220px] bg-gradient-to-br from-jlibrary-500 to-jlibrary-700 flex items-center justify-center overflow-hidden">
                            @if($book->cover_image)
                                <img src="{{ url('media/' . $book->cover_image) }}" 
                                     alt="{{ $book->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <i class="ti ti-book text-lg md:text-4xl text-white/40 group-hover:scale-110 transition-transform duration-300"></i>
                            @endif
                            
                            <!-- Hover Overlay - Hidden on mobile -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 hidden md:flex items-end justify-center pb-2.5">
                                <span class="text-white text-[11px] font-semibold bg-jlibrary-600/80 px-3 py-1 rounded-full">
                                    <i class="ti ti-eye"></i> View
                                </span>
                            </div>
                            
                            <!-- Institution Badge - Simplified for mobile -->
                            <div class="absolute bottom-0.5 left-0.5 bg-black/70 text-white px-0.5 md:px-1.5 py-0.5 rounded text-[5px] md:text-[9px] max-w-[20px] md:max-w-[65px] truncate">
                                @if($book->institution_id && $book->institution)
                                    <i class="ti ti-building text-[5px] md:text-[9px]"></i> 
                                    <span class="hidden sm:inline">{{ Str::limit($book->institution->name ?? '', 6) }}</span>
                                @else
                                    🌐
                                @endif
                            </div>
                            
                            <!-- Bookmark Button - Smaller on mobile -->
                            <div class="absolute top-0.5 right-0.5 z-10 scale-75 md:scale-100">
                                <x-bookmark-button :item="$book" type="book" size="xs" />
                            </div>
                        </div>
                    </a>
                    
                    <!-- Book Info - Compact -->
                    <div class="p-0.5 md:p-2 text-center">
                        <a href="{{ $bookUrl }}" class="block">
                            <h3 class="font-semibold text-[6px] md:text-xs text-gray-800 mb-0 line-clamp-1 hover:text-jlibrary-600 transition">{{ Str::limit($book->title, 8) }}</h3>
                        </a>
                        <p class="text-[5px] md:text-[10px] text-gray-500 mb-0 line-clamp-1 hidden sm:block">{{ $book->author ?? 'Unknown' }}</p>
                        
                        <div class="flex items-center justify-center gap-0.5 md:gap-2 text-[5px] md:text-[10px] mt-0.5 md:mt-1">
                            <span class="flex items-center gap-0.5 text-blue-600 font-medium">
                                <i class="ti ti-eye text-blue-500 text-[6px] md:text-[11px]"></i> 
                                <span class="hidden md:inline">{{ number_format($book->views_count ?? 0) }}</span>
                            </span>
                            <span class="text-gray-300 text-[5px] md:text-[10px] hidden md:inline">|</span>
                            <span class="flex items-center gap-0.5 text-green-600 font-medium">
                                <i class="ti ti-download text-green-500 text-[6px] md:text-[11px]"></i> 
                                <span class="hidden md:inline">{{ number_format($book->downloads ?? 0) }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-3 md:mt-6">
            {{ $books->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-6 md:p-12 text-center">
            <i class="ti ti-books text-3xl md:text-6xl text-gray-400 mb-2 md:mb-4 block"></i>
            <h3 class="text-base md:text-xl font-semibold mb-1 md:mb-2">No books found</h3>
            <p class="text-xs md:text-sm text-gray-500">Try adjusting your search or filter criteria</p>
            <a href="{{ route('library.index') }}" class="inline-block mt-2 md:mt-4 text-xs md:text-sm text-jlibrary-600 hover:text-jlibrary-700">Clear filters</a>
        </div>
    @endif
</div>
@endsection