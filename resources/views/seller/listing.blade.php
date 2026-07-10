@extends('layouts.app')

@section('title', 'My Books')

@section('content')

<!-- ========================================== -->
<!-- LIGHT BISQUE BACKGROUND                    -->
<!-- ========================================== -->
<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div class="relative z-10 max-w-7xl mx-auto py-8">
    
    <!-- ========================================== -->
    <!-- HEADER                                     -->
    <!-- ========================================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <span class="bg-gradient-to-br from-orange-500 to-amber-500 p-2 rounded-xl shadow-lg shadow-orange-500/20">
                    <i class="ti ti-books text-white text-xl"></i>
                </span>
                My Books
            </h1>
            <p class="text-slate-600 text-sm mt-1">Manage your marketplace listings</p>
        </div>
        <a href="{{ route('marketplace.create') }}" class="bg-gradient-to-r from-orange-600 to-amber-600 hover:shadow-lg hover:shadow-orange-600/25 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-medium border-2 border-orange-400/30">
            <i class="ti ti-plus"></i> Add New Book
        </a>
    </div>

    <!-- ========================================== -->
    <!-- SUCCESS MESSAGE                            -->
    <!-- ========================================== -->
    @if(session('success'))
        <div class="bg-emerald-50 border-2 border-emerald-200/80 text-emerald-700 p-4 mb-6 rounded-xl shadow-sm flex items-center gap-3">
            <i class="ti ti-check-circle text-emerald-500 text-xl"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- BOOKS GRID                                 -->
    <!-- ========================================== -->
    @if($listings->count() > 0)
        <div class="grid md:grid-cols-3 gap-6">
            @foreach($listings as $listing)
            <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 overflow-hidden hover:shadow-lg hover:border-orange-300/60 transition group">
                <!-- Cover Image -->
                <div class="relative h-40 bg-gradient-to-r from-orange-100 to-amber-100">
                    @if($listing->cover_image)
                        <img src="{{ asset('storage/' . $listing->cover_image) }}" alt="{{ $listing->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="ti ti-book text-5xl text-orange-400/40"></i>
                        </div>
                    @endif
                    
                    <!-- Status Badge -->
                    <span class="absolute top-2 right-2 px-2.5 py-1 rounded-full text-xs font-medium border
                        {{ $listing->status === 'approved' ? 'bg-emerald-500 text-white border-emerald-400/30' : 'bg-yellow-500 text-white border-yellow-400/30' }}">
                        {{ ucfirst($listing->status) }}
                    </span>
                </div>
                
                <!-- Content -->
                <div class="p-4">
                    <h3 class="font-semibold text-slate-800">{{ $listing->title }}</h3>
                    <p class="text-sm text-slate-500">by {{ $listing->author }}</p>
                    <p class="text-lg font-bold text-orange-600 mt-2">TSh {{ number_format($listing->price, 2) }}</p>
                    
                    <div class="flex items-center gap-2 mt-3">
                        <span class="text-xs text-slate-500 flex items-center gap-1">
                            <i class="ti ti-shopping-cart text-orange-400 text-[10px]"></i>
                            {{ $listing->sales_count }} sales
                        </span>
                        <span class="text-xs text-slate-300">•</span>
                        <span class="text-xs text-slate-500 flex items-center gap-1">
                            <i class="ti ti-eye text-orange-400 text-[10px]"></i>
                            {{ $listing->views }} views
                        </span>
                    </div>
                    
                    <div class="flex gap-2 mt-3 pt-3 border-t-2 border-slate-200/60">
                        <a href="{{ route('marketplace.edit', $listing) }}" class="flex-1 text-center px-3 py-1.5 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100 transition text-sm font-medium border border-orange-200/60">
                            <i class="ti ti-edit"></i> Edit
                        </a>
                        <form action="{{ route('marketplace.destroy', $listing) }}" method="POST" onsubmit="return confirm('Delete this listing?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm font-medium border border-red-200/60">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-6">
            {{ $listings->links() }}
        </div>
        
    @else
        <!-- ========================================== -->
        <!-- EMPTY STATE CARD                           -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 p-16 text-center">
            <div class="w-24 h-24 bg-gradient-to-br from-orange-500 to-amber-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl shadow-orange-500/20">
                <i class="ti ti-package-off text-4xl text-white"></i>
            </div>
            <h3 class="text-xl font-semibold text-slate-800 mb-2">No Books Yet</h3>
            <p class="text-slate-500">You haven't uploaded any books to the marketplace.</p>
            <a href="{{ route('marketplace.create') }}" class="mt-4 inline-block bg-gradient-to-r from-orange-600 to-amber-600 text-white px-6 py-3 rounded-xl hover:shadow-lg hover:shadow-orange-600/25 transition font-medium border-2 border-orange-400/30">
                <i class="ti ti-plus"></i> Upload Your First Book
            </a>
        </div>
    @endif
</div>
@endsection