@extends('layouts.app')

@section('title', 'Purchase Successful')

@section('content')
<div class="container mx-auto px-4 py-16 max-w-2xl">
    <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
        <div class="w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-4">
            <i class="ti ti-check text-4xl text-emerald-600"></i>
        </div>
        
        <h1 class="text-2xl font-bold" style="color: #1a1a2e;">Purchase Successful! </h1>
        <p class="text-sm mt-2" style="color: #6b7280;">You now own this book and can access it anytime.</p>
        
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mt-6">
            <p class="text-sm" style="color: #065f46;">
                <i class="ti ti-book"></i> 
                <strong>{{ $book->title ?? 'Book' }}</strong> has been added to your library.
            </p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 mt-6">
            <a href="{{ route('library.index') }}" 
               class="flex-1 py-3 rounded-xl font-semibold text-center transition-all duration-300 hover:scale-[1.02]"
               style="background: linear-gradient(135deg, #db570a, #e87a2a); color: white;">
                <i class="ti ti-library"></i> Go to Library
            </a>
           @if(isset($book->file_path) && file_exists(public_path('media/' . $book->file_path)))
    <a href="{{ route('book.download', $book->id) }}" 
       class="flex-1 py-3 rounded-xl font-semibold text-center transition-all duration-300 hover:scale-[1.02]"
       style="background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white;">
        <i class="ti ti-download"></i> Download Book
    </a>
@else
    <div class="flex-1 py-3 rounded-xl text-center text-sm" 
         style="background: rgba(0,0,0,0.05); color: #6b7280; border: 1px solid rgba(0,0,0,0.1);">
        <i class="ti ti-file"></i> File not available yet
    </div>
@endif
        </div>
    </div>
</div>
@endsection