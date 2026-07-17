@extends('layouts.library')

@section('title', $book->title . ' - ' . $institution->name)

@section('content')

{{-- ========================================== --}}
{{-- PRICE LOGIC - DEFINED FIRST                --}}
{{-- ========================================== --}}
@php
    $isBookstore = isset($institution) && $institution->type === 'bookstore';
    $hasPrice = false;
    $displayPrice = 0;
    
    if ($isBookstore) {
        if (isset($book->price) && $book->price > 0) {
            $hasPrice = true;
            $displayPrice = $book->price;
        }
    } else {
        if (isset($book->is_paid) && $book->is_paid && isset($book->price) && $book->price > 0) {
            $hasPrice = true;
            $displayPrice = $book->price;
        }
    }
@endphp

<!-- ========================================== -->
<!-- INSTITUTION CONTEXT HEADER                 -->
<!-- ========================================== -->
<div class="mb-6 p-4 rounded-xl" style="background: linear-gradient(135deg, #db570a, #e87a2a, #f59e4c); box-shadow: 0 4px 20px rgba(219, 87, 10, 0.3);">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(255,255,255,0.25); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                    <i class="ti ti-building text-white text-xl"></i>
                </div>
                <div>
                    <span class="text-white font-bold text-lg">{{ $institution->name }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-white/30 text-white font-semibold ml-2">
                        {{ ucfirst($institution->type) }}
                    </span>
                </div>
            </div>
            <p class="text-white/90 text-sm mt-1 flex items-center gap-1 ml-12">
                <i class="ti ti-shopping-cart"></i> Purchase available
            </p>
        </div>
        <div>
            <a href="{{ route('institution.public.index', $institution->id) }}" 
               class="text-white hover:text-white/90 transition inline-flex items-center gap-2 text-sm bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg hover:bg-white/30 border border-white/20">
                <i class="ti ti-arrow-left"></i> Back to {{ $institution->name }}
            </a>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- SUCCESS / ERROR MESSAGES                   -->
<!-- ========================================== -->
@if(session('success'))
    <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/30 rounded-xl text-emerald-400 flex items-start gap-3 backdrop-blur-sm">
        <i class="ti ti-check-circle text-xl mt-0.5"></i>
        <div>
            <p class="font-semibold">Success!</p>
            <p class="text-sm text-emerald-400/80">{{ session('success') }}</p>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-4 bg-red-500/20 border border-red-500/30 rounded-xl text-red-400 flex items-start gap-3 backdrop-blur-sm">
        <i class="ti ti-alert-circle text-xl mt-0.5"></i>
        <div>
            <p class="font-semibold">Error!</p>
            <p class="text-sm text-red-400/80">{{ session('error') }}</p>
        </div>
    </div>
@endif

<!-- ========================================== -->
<!-- BOOK DETAILS - MAIN CONTENT               -->
<!-- ========================================== -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- ========================================== -->
    <!-- LEFT COLUMN - COVER & QUICK ACTIONS        -->
    <!-- ========================================== -->
    <div class="lg:col-span-1 space-y-4">
        
        <!-- Cover Image -->
        <div class="overflow-hidden rounded-2xl relative group" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.4); box-shadow: 0 8px 32px rgba(0,0,0,0.08);">
            @if($book->cover_image)
                <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-auto group-hover:scale-105 transition duration-700 ease-in-out">
            @else
                <div class="w-full aspect-[2/3] flex items-center justify-center" style="background: linear-gradient(135deg, #fef3c7, #fde68a);">
                    <i class="ti ti-book text-8xl" style="color: rgba(219, 87, 10, 0.3);"></i>
                </div>
            @endif
            
            <!-- Status Badge Overlay -->
            <div class="absolute top-4 right-4">
                <span class="px-3 py-1 rounded-full text-xs font-medium backdrop-blur-sm" style="background: rgba(0,0,0,0.5); color: white; border: 1px solid rgba(255,255,255,0.2);">
                    @if($hasPrice && $displayPrice > 0)
                        Paid
                    @else
                        Free
                    @endif
                </span>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-3 gap-2 p-3 rounded-2xl" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3);">
            <div class="text-center">
                <p class="text-lg font-bold" style="color: #1e3a5f;">{{ number_format($book->sold_count ?? 0) }}</p>
                <p class="text-xs" style="color: #6b7280;">Sold</p>
            </div>
            <div class="text-center">
                <p class="text-lg font-bold" style="color: #1e3a5f;">{{ number_format($book->stock_quantity ?? 0) }}</p>
                <p class="text-xs" style="color: #6b7280;">Stock</p>
            </div>
            <div class="text-center">
                <p class="text-lg font-bold" style="color: #1e3a5f;">{{ $book->pages ?? 0 }}</p>
                <p class="text-xs" style="color: #6b7280;">Pages</p>
            </div>
        </div>
        
        <!-- Price & Purchase Card -->
        <div class="p-4 rounded-2xl" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.4); box-shadow: 0 8px 32px rgba(0,0,0,0.08);">
            
            <!-- Price -->
            @if($hasPrice && $displayPrice > 0)
                <div class="mb-3 p-3 rounded-xl text-center" style="background: linear-gradient(135deg, rgba(219, 87, 10, 0.08), rgba(245, 158, 76, 0.08)); border: 2px solid rgba(219, 87, 10, 0.15);">
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color: #92400e;">Price</p>
                    <p class="text-3xl font-bold" style="color: #db570a;">TSh {{ number_format($displayPrice, 2) }}</p>
                    <p class="text-xs mt-1" style="color: #92400e;">One-time purchase. Lifetime access.</p>
                    @if($isBookstore && isset($book->stock_quantity) && $book->stock_quantity > 0)
                        <p class="text-xs mt-1 flex items-center justify-center gap-1" style="color: #065f46;">
                            <i class="ti ti-package"></i> {{ $book->stock_quantity }} in stock
                        </p>
                    @endif
                </div>
            @else
                <div class="mb-3 p-3 rounded-xl text-center" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(52, 211, 153, 0.08)); border: 2px solid rgba(16, 185, 129, 0.15);">
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color: #065f46;">Price</p>
                    <p class="text-3xl font-bold" style="color: #065f46;">FREE</p>
                    <p class="text-xs mt-1" style="color: #065f46;">
                        @if($isBookstore)
                            Free to download
                        @else
                            Free to read and download
                        @endif
                    </p>
                </div>
            @endif

            <!-- Read/Download Button -->
            @if(isset($book->file_path) && $book->file_path)
                @php
                    $canAccess = false;
                    if($institution->type === 'bookstore') {
                        $canAccess = true;
                    } elseif($institution->type === 'public') {
                        if(!$book->is_paid) {
                            $canAccess = true;
                        } elseif(auth()->check() && auth()->user()->isMemberOf($institution)) {
                            $canAccess = true;
                        }
                    } elseif($institution->type === 'school' || $institution->type === 'university') {
                        if(auth()->check() && auth()->user()->isMemberOf($institution)) {
                            $canAccess = true;
                        }
                    }
                @endphp

                @if($canAccess)
                    <a href="{{ url('media/' . $book->file_path) }}" target="_blank" 
                       class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-semibold transition-all duration-300 hover:scale-[1.02]" 
                       style="background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; box-shadow: 0 4px 16px rgba(124, 58, 237, 0.3);">
                        <i class="ti ti-file-pdf text-xl"></i> Read / Download
                    </a>
                @else
                    <div class="w-full px-4 py-3 rounded-xl text-center text-sm flex items-center justify-center gap-2" style="background: rgba(0,0,0,0.04); color: #6b7280; border: 1px dashed rgba(0,0,0,0.1);">
                        <i class="ti ti-lock"></i> 
                        @if(!auth()->check())
                            Login to access
                        @elseif(!auth()->user()->isMemberOf($institution))
                            @if($institution->type === 'public')
                                Join {{ $institution->name }}
                            @else
                                Request membership
                            @endif
                        @else
                            No permission
                        @endif
                    </div>
                @endif
            @else
                <div class="w-full px-4 py-3 rounded-xl text-center text-sm flex items-center justify-center gap-2" style="background: rgba(0,0,0,0.04); color: #6b7280; border: 1px dashed rgba(0,0,0,0.1);">
                    <i class="ti ti-file"></i> No digital version
                </div>
            @endif

            <!-- Borrow (Library Only) -->
            @if($institution->type !== 'bookstore')
                <div class="mt-3 pt-3 border-t" style="border-color: rgba(0,0,0,0.06);">
                    @auth
                        @php
                            $isMember = auth()->user()->isMemberOf($institution);
                            $hasPendingRequest = false;
                            $hasActiveBorrowing = false;
                            if($isMember) {
                                try {
                                    $hasPendingRequest = \App\Models\BorrowRequest::where('book_id', $book->id)
                                        ->where('user_id', auth()->id())
                                        ->where('status', 'pending')
                                        ->exists();
                                    $hasActiveBorrowing = \App\Models\Borrowing::where('book_id', $book->id)
                                        ->where('user_id', auth()->id())
                                        ->where('status', 'borrowed')
                                        ->exists();
                                } catch (\Exception $e) {
                                    $hasPendingRequest = false;
                                    $hasActiveBorrowing = false;
                                }
                            }
                        @endphp

                        @if($isMember)
                            @if($hasActiveBorrowing)
                                <div class="w-full text-center py-2.5 rounded-xl text-sm border flex items-center justify-center gap-2" style="background: rgba(59, 130, 246, 0.06); color: #2563eb; border-color: rgba(59, 130, 246, 0.15);">
                                    <i class="ti ti-book"></i> Already borrowed
                                </div>
                            @elseif($hasPendingRequest)
                                <div class="w-full text-center py-2.5 rounded-xl text-sm border flex items-center justify-center gap-2" style="background: rgba(245, 158, 11, 0.06); color: #d97706; border-color: rgba(245, 158, 11, 0.15);">
                                    <i class="ti ti-clock"></i> Request pending
                                </div>
                            @elseif($book->status === 'available' || $book->status === 'active')
                                <a href="{{ route('borrow.request.form', ['book_id' => $book->id, 'institution_id' => $institution->id]) }}" 
                                   class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 hover:scale-[1.02]" 
                                   style="background: #2563eb; color: white;">
                                    <i class="ti ti-bookmark"></i> Request to Borrow
                                </a>
                            @else
                                <div class="w-full text-center py-2.5 rounded-xl text-sm border flex items-center justify-center gap-2" style="background: rgba(220, 38, 38, 0.06); color: #dc2626; border-color: rgba(220, 38, 38, 0.15);">
                                    <i class="ti ti-x-circle"></i> Not available
                                </div>
                            @endif
                        @else
                            <div class="w-full text-center py-2.5 rounded-xl text-sm border flex items-center justify-center gap-2" style="background: rgba(245, 158, 11, 0.06); color: #d97706; border-color: rgba(245, 158, 11, 0.15);">
                                <i class="ti ti-user-plus"></i> Join to borrow
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" 
                           class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 hover:scale-[1.02]" 
                           style="background: #2563eb; color: white;">
                            <i class="ti ti-login"></i> Login to Borrow
                        </a>
                    @endauth
                </div>
            @endif

            <!-- Purchase -->
            @if($hasPrice && $displayPrice > 0)
                <div class="mt-3 pt-3 border-t" style="border-color: rgba(0,0,0,0.06);">
                    @auth
                        @php
                            $hasPurchased = auth()->user()->hasPurchasedBook($book->id);
                        @endphp
                        
                        @if($hasPurchased)
                            <div class="w-full text-center py-2.5 rounded-xl text-sm border flex items-center justify-center gap-2" style="background: rgba(16, 185, 129, 0.06); color: #065f46; border-color: rgba(16, 185, 129, 0.15);">
                                <i class="ti ti-check"></i> Already owned
                            </div>
                            @if(isset($book->file_path) && $book->file_path)
                                <a href="{{ route('book.download', $book->id) }}" 
                                   class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 hover:scale-[1.02] mt-2" 
                                   style="background: #2563eb; color: white;">
                                    <i class="ti ti-download"></i> Download
                                </a>
                            @endif
                        @else
                            <button onclick="openPurchaseModal({{ $book->id }})" 
                                    id="purchase-btn-{{ $book->id }}"
                                    class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-semibold transition-all duration-300 hover:scale-[1.02]" 
                                    style="background: linear-gradient(135deg, #db570a, #e87a2a); color: white; box-shadow: 0 4px 16px rgba(219, 87, 10, 0.3);">
                                <i class="ti ti-shopping-cart text-xl"></i> Buy Now - TSh {{ number_format($displayPrice, 2) }}
                            </button>
                            <div id="purchase-status-{{ $book->id }}" class="mt-2 text-center text-sm" style="color: #6b7280;"></div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" 
                           class="w-full flex items-center justify-center gap-2 py-3 rounded-xl font-semibold transition-all duration-300 hover:scale-[1.02]" 
                           style="background: linear-gradient(135deg, #db570a, #e87a2a); color: white; box-shadow: 0 4px 16px rgba(219, 87, 10, 0.3);">
                            <i class="ti ti-login"></i> Login to Buy
                        </a>
                    @endauth
                </div>
            @endif
        </div>
    </div>

    <!-- ========================================== -->
    <!-- RIGHT COLUMN - BOOK DETAILS               -->
    <!-- ========================================== -->
    <div class="lg:col-span-2">
        <div class="p-6 rounded-2xl" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.4); box-shadow: 0 8px 32px rgba(0,0,0,0.08);">
            
            <!-- Title & Author -->
            <div class="border-b border-gray-100 pb-4 mb-4">
                <h1 class="text-3xl font-bold" style="color: #1a1a2e;">{{ $book->title }}</h1>
                <p class="text-lg mt-1 flex items-center flex-wrap gap-2" style="color: #4b5563;">
                    by <span class="font-semibold" style="color: #1e3a5f;">{{ $book->author ?? 'Unknown' }}</span>
                    @if(isset($book->publication_year) && $book->publication_year)
                        <span class="text-sm" style="color: #6b7280;">• {{ $book->publication_year }}</span>
                    @endif
                </p>
                
                <!-- Badges -->
                <div class="flex flex-wrap gap-2 mt-3">
                    @if($book->category)
                        <span class="px-3 py-1 rounded-full text-xs font-medium flex items-center gap-1" style="background: rgba(124, 58, 237, 0.1); color: #5b21b6; border: 1px solid rgba(124, 58, 237, 0.15);">
                            <i class="ti ti-tag"></i> {{ $book->category }}
                        </span>
                    @endif
                    <span class="px-3 py-1 rounded-full text-xs font-medium flex items-center gap-1" style="background: rgba(16, 185, 129, 0.1); color: #065f46; border: 1px solid rgba(16, 185, 129, 0.15);">
                        <i class="ti ti-check"></i> {{ ucfirst($book->status ?? 'Available') }}
                    </span>
                    @if(isset($book->isbn) && $book->isbn)
                        <span class="px-3 py-1 rounded-full text-xs font-medium flex items-center gap-1" style="background: rgba(59, 130, 246, 0.1); color: #1d4ed8; border: 1px solid rgba(59, 130, 246, 0.15);">
                            <i class="ti ti-barcode"></i> {{ $book->isbn }}
                        </span>
                    @endif
                    @if($isBookstore)
                        <span class="px-3 py-1 rounded-full text-xs font-medium flex items-center gap-1" style="background: rgba(219, 87, 10, 0.1); color: #db570a; border: 1px solid rgba(219, 87, 10, 0.15);">
                            <i class="ti ti-shopping-cart"></i> Bookstore
                        </span>
                    @endif
                </div>
            </div>
            
            <!-- Book Info Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                @if(isset($book->publisher) && $book->publisher)
                    <div class="p-3 rounded-xl text-center" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05);">
                        <p class="text-xs" style="color: #6b7280;">Publisher</p>
                        <p class="text-sm font-medium truncate" style="color: #1a1a2e;">{{ $book->publisher }}</p>
                    </div>
                @endif
                @if(isset($book->publication_year) && $book->publication_year)
                    <div class="p-3 rounded-xl text-center" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05);">
                        <p class="text-xs" style="color: #6b7280;">Year</p>
                        <p class="text-sm font-medium" style="color: #1a1a2e;">{{ $book->publication_year }}</p>
                    </div>
                @endif
                @if(isset($book->pages) && $book->pages)
                    <div class="p-3 rounded-xl text-center" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05);">
                        <p class="text-xs" style="color: #6b7280;">Pages</p>
                        <p class="text-sm font-medium" style="color: #1a1a2e;">{{ $book->pages }}</p>
                    </div>
                @endif
                @if(isset($book->isbn) && $book->isbn)
                    <div class="p-3 rounded-xl text-center" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05);">
                        <p class="text-xs" style="color: #6b7280;">ISBN</p>
                        <p class="text-sm font-medium" style="color: #1a1a2e;">{{ $book->isbn }}</p>
                    </div>
                @endif
            </div>
            
            <!-- Description -->
            <div class="mb-4">
                <h3 class="font-semibold text-lg flex items-center gap-2 mb-2" style="color: #1a1a2e;">
                    <i class="ti ti-file-description" style="color: #db570a;"></i> About This Book
                </h3>
                <div class="p-4 rounded-xl" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05);">
                    <p class="leading-relaxed" style="color: #374151; font-size: 0.95rem;">
                        {{ $book->description ?? 'No description available for this book.' }}
                    </p>
                </div>
            </div>
            
            <!-- Additional Details -->
            <div class="mb-4">
                <h4 class="text-sm font-semibold mb-2 flex items-center gap-2" style="color: #4b5563;">
                    <i class="ti ti-info-circle" style="color: #db570a;"></i> Additional Details
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
                    @if(isset($book->language) && $book->language)
                        <div class="p-2 rounded-lg" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05);">
                            <span style="color: #6b7280;">Language:</span>
                            <span class="ml-1 font-medium" style="color: #1a1a2e;">{{ $book->language }}</span>
                        </div>
                    @endif
                    @if(isset($book->edition) && $book->edition)
                        <div class="p-2 rounded-lg" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05);">
                            <span style="color: #6b7280;">Edition:</span>
                            <span class="ml-1 font-medium" style="color: #1a1a2e;">{{ $book->edition }}</span>
                        </div>
                    @endif
                    @if(isset($book->format) && $book->format)
                        <div class="p-2 rounded-lg" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05);">
                            <span style="color: #6b7280;">Format:</span>
                            <span class="ml-1 font-medium" style="color: #1a1a2e;">{{ $book->format }}</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Shelf Location -->
            @if(isset($book->shelf_number) && $book->shelf_number)
                <div class="p-4 rounded-xl" style="background: rgba(124, 58, 237, 0.04); border: 1px solid rgba(124, 58, 237, 0.08);">
                    <p class="text-sm font-semibold flex items-center gap-2" style="color: #5b21b6;">
                        <i class="ti ti-map-pin"></i> Location
                    </p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mt-2">
                        @if($book->shelf_number)
                            <div>
                                <p class="text-xs" style="color: #6b7280;">Shelf</p>
                                <p class="font-semibold" style="color: #1a1a2e;">{{ $book->shelf_number }}</p>
                            </div>
                        @endif
                        @if(isset($book->shelf_name) && $book->shelf_name)
                            <div>
                                <p class="text-xs" style="color: #6b7280;">Shelf Name</p>
                                <p class="font-semibold" style="color: #1a1a2e;">{{ $book->shelf_name }}</p>
                            </div>
                        @endif
                        @if(isset($book->floor) && $book->floor)
                            <div>
                                <p class="text-xs" style="color: #6b7280;">Floor</p>
                                <p class="font-semibold" style="color: #1a1a2e;">{{ $book->floor }}</p>
                            </div>
                        @endif
                        @if(isset($book->section) && $book->section)
                            <div>
                                <p class="text-xs" style="color: #6b7280;">Section</p>
                                <p class="font-semibold" style="color: #1a1a2e;">{{ $book->section }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            
            <!-- Bookstore Details -->
            @if($isBookstore)
                <div class="mt-4 p-4 rounded-xl" style="background: rgba(219, 87, 10, 0.04); border: 1px solid rgba(219, 87, 10, 0.08);">
                    <h4 class="text-sm font-semibold flex items-center gap-2" style="color: #db570a;">
                        <i class="ti ti-shopping-cart"></i> Bookstore Details
                    </h4>
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <div>
                            <p class="text-xs" style="color: #6b7280;">Price</p>
                            <p class="font-medium" style="color: #1a1a2e;">TSh {{ number_format($book->price ?? 0, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs" style="color: #6b7280;">Stock</p>
                            <p class="font-medium" style="color: {{ isset($book->stock_quantity) && $book->stock_quantity <= 0 ? '#dc2626' : (isset($book->stock_quantity) && $book->stock_quantity <= 5 ? '#d97706' : '#059669') }};">
                                {{ $book->stock_quantity ?? 0 }} available
                            </p>
                        </div>
                        <div>
                            <p class="text-xs" style="color: #6b7280;">Sold</p>
                            <p class="font-medium" style="color: #1a1a2e;">{{ $book->sold_count ?? 0 }}</p>
                        </div>
                        <div>
                            <p class="text-xs" style="color: #6b7280;">Category</p>
                            <p class="font-medium" style="color: #1a1a2e;">{{ $book->category ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- RELATED BOOKS - SMALLER SIZE              -->
<!-- ========================================== -->
@if(isset($relatedBooks) && $relatedBooks->count() > 0)
    <div class="mt-8">
        <h2 class="text-xl font-bold mb-4 flex items-center gap-2" style="color: #1a1a2e;">
            <i class="ti ti-books" style="color: #db570a;"></i> You May Also Like
        </h2>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3">
            @foreach($relatedBooks as $related)
                <a href="{{ route('institution.public.show', [$institution->id, $related->id]) }}" 
                   class="p-2 hover:shadow-lg transition-all duration-300 group block rounded-xl hover:-translate-y-1" 
                   style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <div class="aspect-[2/3] rounded-lg overflow-hidden relative" style="background: rgba(124, 58, 237, 0.06);">
                        @if($related->cover_image)
                            <img src="{{ url('media/' . $related->cover_image) }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="ti ti-book text-2xl" style="color: rgba(124, 58, 237, 0.15);"></i>
                            </div>
                        @endif
                        @if($related->category)
                            <div class="absolute bottom-1 left-1 bg-black/60 backdrop-blur-sm text-white text-[0.5rem] px-1.5 py-0.5 rounded">
                                {{ Str::limit($related->category, 10) }}
                            </div>
                        @endif
                    </div>
                    <div class="mt-1.5">
                        <p class="text-xs font-semibold truncate group-hover:text-orange-600 transition" style="color: #1a1a2e;">{{ Str::limit($related->title, 20) }}</p>
                        <p class="text-[0.6rem] truncate" style="color: #6b7280;">{{ Str::limit($related->author ?? 'Unknown', 15) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif

<!-- ========================================== -->
<!-- LOCATION                                   -->
<!-- ========================================== -->
<div class="p-4 mt-8 rounded-2xl" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); box-shadow: 0 8px 32px rgba(0,0,0,0.08);">
    <h3 class="font-semibold flex items-center gap-2" style="color: #1a1a2e;">
        <i class="ti ti-map-pin" style="color: #db570a;"></i> {{ $institution->name }} - Location
    </h3>
    <p class="text-sm mt-1" style="color: #4b5563;">
        {{ $institution->address ?? 'Address not specified' }}
        @if($institution->city), {{ $institution->city }}@endif
        @if($institution->region), {{ $institution->region }}@endif
    </p>
    <div class="flex flex-wrap gap-4 mt-2 text-sm" style="color: #6b7280;">
        @if($institution->phone)
            <span class="flex items-center gap-1"><i class="ti ti-phone"></i> {{ $institution->phone }}</span>
        @endif
        @if($institution->email)
            <span class="flex items-center gap-1"><i class="ti ti-mail"></i> {{ $institution->email }}</span>
        @endif
        <span class="flex items-center gap-1"><i class="ti ti-building"></i> {{ ucfirst($institution->type) }} Institution</span>
    </div>
</div>

<!-- ========================================== -->
<!-- PURCHASE MODAL                             -->
<!-- ========================================== -->
<div id="purchase-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 24px; padding: 32px; max-width: 480px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 24px 80px rgba(0,0,0,0.3); animation: modalFadeIn 0.3s ease;">
        
        <!-- Modal Header -->
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold" style="color: #1a1a2e;">
                <i class="ti ti-shopping-cart" style="color: #db570a;"></i> Confirm Purchase
            </h3>
            <button onclick="closePurchaseModal()" class="text-gray-400 hover:text-gray-600 text-2xl transition w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100">
                &times;
            </button>
        </div>
        
        <!-- Book Info -->
        <div class="flex gap-3 mb-4 p-3 rounded-xl" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05);">
            @if($book->cover_image)
                <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-16 h-20 object-cover rounded-lg">
            @endif
            <div>
                <p class="font-semibold" style="color: #1a1a2e;">{{ $book->title }}</p>
                <p class="text-sm" style="color: #6b7280;">{{ $book->author ?? 'Unknown' }}</p>
                <p class="text-lg font-bold mt-1" style="color: #db570a;">TSh {{ number_format($displayPrice, 2) }}</p>
            </div>
        </div>
        
        <!-- Wallet Balance -->
        <div class="mb-4 p-3 rounded-xl flex items-center justify-between" style="background: rgba(59, 130, 246, 0.06); border: 1px solid rgba(59, 130, 246, 0.1);">
            <span class="text-sm font-medium" style="color: #1e3a5f;">
                <i class="ti ti-wallet"></i> Wallet Balance
            </span>
            <span class="text-sm font-bold" style="color: #1e3a5f;">
                TSh {{ number_format(auth()->user()->wallet_balance ?? 0, 2) }}
            </span>
        </div>
        
        <!-- Payment Methods -->
        <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #6b7280;">Select Payment Method</p>
        <div class="grid grid-cols-2 gap-2 mb-4">
            <button onclick="purchaseWithWallet({{ $book->id }})" 
                    id="wallet-pay-btn"
                    class="py-3 rounded-xl font-semibold transition-all duration-300 hover:scale-[1.02] flex items-center justify-center gap-2"
                    style="background: linear-gradient(135deg, #059669, #10b981); color: white;">
                <i class="ti ti-wallet"></i> Wallet
            </button>
            <button onclick="purchaseWithMpesa({{ $book->id }})" 
                    class="py-3 rounded-xl font-semibold transition-all duration-300 hover:scale-[1.02] flex items-center justify-center gap-2"
                    style="background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white;">
                <i class="ti ti-phone"></i> M-Pesa
            </button>
            <button onclick="purchaseWithTigoPesa({{ $book->id }})" 
                    class="py-3 rounded-xl font-semibold transition-all duration-300 hover:scale-[1.02] flex items-center justify-center gap-2"
                    style="background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white;">
                <i class="ti ti-phone"></i> TigoPesa
            </button>
            <button onclick="purchaseWithHaloPesa({{ $book->id }})" 
                    class="py-3 rounded-xl font-semibold transition-all duration-300 hover:scale-[1.02] flex items-center justify-center gap-2"
                    style="background: linear-gradient(135deg, #d97706, #b45309); color: white;">
                <i class="ti ti-phone"></i> HaloPesa
            </button>
            <button onclick="purchaseWithPesaPal({{ $book->id }})" 
                    class="py-3 rounded-xl font-semibold transition-all duration-300 hover:scale-[1.02] flex items-center justify-center gap-2 col-span-2"
                    style="background: linear-gradient(135deg, #db570a, #e87a2a); color: white;">
                <i class="ti ti-credit-card"></i> PesaPal
            </button>
        </div>
        
        <!-- Status Message -->
        <div id="modal-status" class="text-center text-sm" style="color: #6b7280;"></div>
    </div>
</div>

<style>
@keyframes modalFadeIn {
    from { opacity: 0; transform: scale(0.95) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.02); }
}
</style>

<!-- ========================================== -->
<!-- JAVASCRIPT                                 -->
<!-- ========================================== -->
@push('scripts')
<script>
function openPurchaseModal(bookId) {
    document.getElementById('purchase-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePurchaseModal() {
    document.getElementById('purchase-modal').style.display = 'none';
    document.body.style.overflow = '';
}

// Close modal on outside click
document.getElementById('purchase-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePurchaseModal();
    }
});

function purchaseWithWallet(bookId) {
    const status = document.getElementById('modal-status');
    const btn = document.getElementById('wallet-pay-btn');
    
    status.innerHTML = '<i class="ti ti-loader-2 animate-spin"></i> Processing...';
    btn.disabled = true;
    btn.style.opacity = '0.7';
    
    const url = '/book/purchase/wallet/' + bookId;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            status.innerHTML = '✅ ' + data.message + ' Redirecting...';
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            status.innerHTML = '❌ ' + data.message;
            btn.disabled = false;
            btn.style.opacity = '1';
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        status.innerHTML = '❌ Purchase failed. Please try again.';
        btn.disabled = false;
        btn.style.opacity = '1';
    });
}

function purchaseWithMpesa(bookId) {
    window.location.href = '/book/purchase/' + bookId + '?payment_method=mpesa';
}

function purchaseWithTigoPesa(bookId) {
    window.location.href = '/book/purchase/' + bookId + '?payment_method=tigopesa';
}

function purchaseWithHaloPesa(bookId) {
    window.location.href = '/book/purchase/' + bookId + '?payment_method=halopesa';
}

function purchaseWithPesaPal(bookId) {
    window.location.href = '/book/purchase/' + bookId + '?payment_method=pesapal';
}
</script>
@endpush

<style>
/* ========================================== */
/* COMPACT BOOK VIEW - OPTIMIZED              */
/* ========================================== */

/* Reduce card padding */
.library-card,
.glass-card {
    padding: 0.75rem !important;
}

/* Smaller titles */
.text-3xl {
    font-size: 1.35rem !important;
}

.text-2xl {
    font-size: 1.1rem !important;
}

.text-xl {
    font-size: 0.95rem !important;
}

.text-lg {
    font-size: 0.85rem !important;
}

/* Tighter spacing */
.p-6 {
    padding: 0.75rem !important;
}

.p-4 {
    padding: 0.5rem !important;
}

.p-3 {
    padding: 0.35rem !important;
}

.p-2 {
    padding: 0.25rem !important;
}

/* Reduced gaps */
.gap-8 {
    gap: 1rem !important;
}

.gap-6 {
    gap: 0.75rem !important;
}

.gap-4 {
    gap: 0.5rem !important;
}

.gap-3 {
    gap: 0.35rem !important;
}

.gap-2 {
    gap: 0.2rem !important;
}

/* Smaller margins */
.mb-6 {
    margin-bottom: 0.5rem !important;
}

.mb-4 {
    margin-bottom: 0.35rem !important;
}

.mb-3 {
    margin-bottom: 0.25rem !important;
}

.mt-4 {
    margin-top: 0.35rem !important;
}

.mt-3 {
    margin-top: 0.25rem !important;
}

.mt-2 {
    margin-top: 0.15rem !important;
}

/* Smaller borders */
.rounded-2xl {
    border-radius: 0.65rem !important;
}

.rounded-xl {
    border-radius: 0.45rem !important;
}

.rounded-lg {
    border-radius: 0.3rem !important;
}

/* Compact stats */
.compact-stats .stat-number {
    font-size: 1rem !important;
}

.compact-stats .stat-label {
    font-size: 0.5rem !important;
}

/* Price display */
.price-amount {
    font-size: 1.1rem !important;
}

.price-label {
    font-size: 0.5rem !important;
}

/* Badges */
.badge-sm {
    font-size: 0.55rem !important;
    padding: 0.1rem 0.4rem !important;
}

/* Description */
.description-text {
    font-size: 0.75rem !important;
    line-height: 1.4 !important;
}

/* Book info cards */
.info-card {
    padding: 0.3rem 0.4rem !important;
}

.info-card .label {
    font-size: 0.5rem !important;
}

.info-card .value {
    font-size: 0.7rem !important;
}

/* Buttons */
.btn-compact {
    padding: 0.3rem 0.6rem !important;
    font-size: 0.7rem !important;
}

/* Location card */
.location-card {
    padding: 0.4rem 0.6rem !important;
}

.location-card .title {
    font-size: 0.75rem !important;
}

.location-card .text {
    font-size: 0.65rem !important;
}

/* Modal */
.modal-compact {
    max-width: 380px !important;
    padding: 1rem !important;
}

/* Responsive */
@media (max-width: 768px) {
    .text-3xl {
        font-size: 1.1rem !important;
    }
    
    .p-6 {
        padding: 0.5rem !important;
    }
    
    .gap-8 {
        gap: 0.5rem !important;
    }
    
    .grid {
        gap: 0.5rem !important;
    }
    
    .library-card {
        padding: 0.5rem !important;
    }
}
</style>

@endsection