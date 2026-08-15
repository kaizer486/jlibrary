@extends('layouts.library')

@section('title', $book->title . ' - ' . $institution->name)

@section('content')

<!-- ========================================== -->
<!-- HERO SECTION                               -->
<!-- ========================================== -->
<div class="library-hero" style="min-height: 200px;">
    <div class="hero-content">
        <div class="flex flex-col items-center text-center gap-2">
            <div>
                <div class="flex items-center justify-center gap-2 mb-2">
                    <i class="ti ti-book text-purple-200 text-2xl"></i>
                    <h1 class="text-2xl md:text-3xl font-bold text-white font-playfair">
                        {{ $book->title }}
                    </h1>
                </div>
                <p class="text-purple-100/80 text-sm md:text-base">
                    by {{ $book->author ?? 'Unknown' }}
                </p>
                <div class="mt-2 flex flex-wrap gap-3 justify-center text-sm text-white/60">
                    @if($book->category)
                        <span><i class="ti ti-category"></i> {{ $book->category }}</span>
                    @endif
                    @if($book->shelf_number)
                        <span><i class="ti ti-shelf"></i> Shelf {{ $book->shelf_number }}</span>
                    @endif
                    @if($book->total_pages)
                        <span><i class="ti ti-file-text"></i> {{ $book->total_pages }} pages</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- BACK BUTTON                                -->
<!-- ========================================== -->
<div class="max-w-7xl mx-auto px-4 mt-4">
    <a href="{{ route('institution.public.index', $institution->id) }}" class="inline-flex items-center text-white/60 hover:text-white text-sm transition">
        <i class="ti ti-arrow-left mr-1"></i> Back to Library
    </a>
</div>

<!-- ========================================== -->
<!-- BOOK DETAILS                               -->
<!-- ========================================== -->
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="grid md:grid-cols-3 gap-8">
        <!-- Left Column - Book Cover -->
        <div class="md:col-span-1">
            <div class="bg-white/5 rounded-xl overflow-hidden backdrop-blur-sm border border-white/10 sticky top-24">
                <div class="aspect-[2/3] bg-gradient-to-br from-purple-900/40 to-indigo-900/40 flex items-center justify-center relative">
                    @if($book->cover_image)
                        <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                    @else
                        <i class="ti ti-book text-8xl text-white/20"></i>
                    @endif
                    
                    @if($book->is_paid)
                        <div class="absolute top-3 right-3 badge-paid px-3 py-1 rounded-lg">
                            TSh {{ number_format($book->price) }}
                        </div>
                    @else
                        <div class="absolute top-3 right-3 badge-free px-3 py-1 rounded-lg">
                            FREE
                        </div>
                    @endif
                </div>

                <div class="p-6">
                    <!-- Actions -->
                    <div class="space-y-3">
                        @auth
                            @if($hasAccess ?? false)
                                <a href="{{ route('institution.public.read', [$institution->id, $book->id]) }}"
                                   class="block text-center bg-purple-600 text-white px-4 py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                                    <i class="ti ti-eye"></i> Read Now
                                </a>
                                <a href="{{ route('institution.public.download', [$institution->id, $book->id]) }}"
                                   class="block text-center border border-purple-400/40 text-purple-300 px-4 py-3 rounded-lg hover:bg-purple-600/20 transition font-semibold">
                                    <i class="ti ti-download"></i> Download PDF
                                </a>
                            @elseif($book->is_paid)
                                <button onclick="showPurchaseModal({{ $book->id }}, {{ $book->price }}, '{{ addslashes($book->title) }}')"
                                        class="block text-center bg-gradient-to-r from-yellow-500 to-orange-500 text-white px-4 py-3 rounded-lg hover:shadow-lg transition font-semibold w-full">
                                    <i class="ti ti-shopping-cart"></i> Purchase for TSh {{ number_format($book->price) }}
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="block text-center bg-purple-600 text-white px-4 py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                                    <i class="ti ti-login"></i> Login to Read
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block text-center bg-purple-600 text-white px-4 py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                                <i class="ti ti-login"></i> Login to Read
                            </a>
                            <a href="{{ route('register') }}" class="block text-center border border-purple-400/40 text-purple-300 px-4 py-3 rounded-lg hover:bg-purple-600/20 transition font-semibold">
                                <i class="ti ti-user-plus"></i> Create Free Account
                            </a>
                        @endauth

                        <button onclick="shareBook()"
                                class="block text-center bg-blue-600/30 hover:bg-blue-600/50 text-blue-300 px-4 py-3 rounded-lg transition font-semibold w-full">
                            <i class="ti ti-share mr-2"></i> Share This Book
                        </button>
                    </div>

                    @if(isset($progress) && $progress && $progress->progress_percent > 0)
                        <div class="mt-6 pt-4 border-t border-white/10">
                            <div class="flex justify-between text-sm text-white/60 mb-1">
                                <span>Reading Progress</span>
                                <span>{{ $progress->progress_percent }}%</span>
                            </div>
                            <div class="w-full bg-white/10 rounded-full h-2">
                                <div class="bg-purple-500 h-2 rounded-full transition-all duration-500" style="width: {{ $progress->progress_percent }}%"></div>
                            </div>
                            @if($progress->status == 'completed')
                                <div class="mt-2 text-emerald-400 text-sm">
                                    <i class="ti ti-certificate"></i> Completed!
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Book Details -->
        <div class="md:col-span-2">
            <!-- Description -->
            <div class="bg-white/5 rounded-xl p-6 border border-white/10 backdrop-blur-sm mb-6">
                <h2 class="text-xl font-semibold text-white mb-3">Description</h2>
                <p class="text-white/70 leading-relaxed">{{ $book->description ?? 'No description available for this book.' }}</p>
            </div>

            <!-- Book Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white/5 rounded-xl p-4 text-center border border-white/5">
                    <i class="ti ti-eye text-purple-400 text-xl block mb-1"></i>
                    <span class="text-white/80 text-sm">{{ number_format($book->views_count ?? 0) }}</span>
                    <p class="text-white/40 text-xs">Views</p>
                </div>
                <div class="bg-white/5 rounded-xl p-4 text-center border border-white/5">
                    <i class="ti ti-download text-purple-400 text-xl block mb-1"></i>
                    <span class="text-white/80 text-sm">{{ number_format($book->downloads ?? 0) }}</span>
                    <p class="text-white/40 text-xs">Downloads</p>
                </div>
                <div class="bg-white/5 rounded-xl p-4 text-center border border-white/5">
                    <i class="ti ti-star text-yellow-400 text-xl block mb-1"></i>
                    <span class="text-white/80 text-sm">{{ number_format($book->averageRating(), 1) }}</span>
                    <p class="text-white/40 text-xs">Rating</p>
                </div>
                <div class="bg-white/5 rounded-xl p-4 text-center border border-white/5">
                    <i class="ti ti-message-circle text-purple-400 text-xl block mb-1"></i>
                    <span class="text-white/80 text-sm">{{ $book->reviews()->count() }}</span>
                    <p class="text-white/40 text-xs">Reviews</p>
                </div>
            </div>

            <!-- Book Details -->
            <div class="bg-white/5 rounded-xl p-6 border border-white/10 backdrop-blur-sm mb-6">
                <h2 class="text-xl font-semibold text-white mb-3">Book Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div class="flex justify-between py-2 border-b border-white/5">
                        <span class="text-white/40">Author</span>
                        <span class="text-white/80">{{ $book->author ?? 'Unknown' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-white/5">
                        <span class="text-white/40">Category</span>
                        <span class="text-white/80">{{ $book->category ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-white/5">
                        <span class="text-white/40">Pages</span>
                        <span class="text-white/80">{{ $book->total_pages ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-white/5">
                        <span class="text-white/40">ISBN</span>
                        <span class="text-white/80">{{ $book->isbn ?? 'N/A' }}</span>
                    </div>
                    @if($book->shelf_number)
                        <div class="flex justify-between py-2 border-b border-white/5">
                            <span class="text-white/40">Shelf</span>
                            <span class="text-white/80">{{ $book->shelf_number }}</span>
                        </div>
                    @endif
                    @if($book->publication_year)
                        <div class="flex justify-between py-2 border-b border-white/5">
                            <span class="text-white/40">Year</span>
                            <span class="text-white/80">{{ $book->publication_year }}</span>
                        </div>
                    @endif
                    @if($book->publisher)
                        <div class="flex justify-between py-2 border-b border-white/5">
                            <span class="text-white/40">Publisher</span>
                            <span class="text-white/80">{{ $book->publisher }}</span>
                        </div>
                    @endif
                    @if($book->language)
                        <div class="flex justify-between py-2 border-b border-white/5">
                            <span class="text-white/40">Language</span>
                            <span class="text-white/80">{{ $book->language }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="bg-white/5 rounded-xl p-6 border border-white/10 backdrop-blur-sm">
                <h2 class="text-xl font-semibold text-white mb-3 flex items-center gap-2">
                    <i class="ti ti-message-circle-2"></i>
                    Reviews
                    <span class="text-sm font-normal text-white/40">({{ $book->reviews()->count() }})</span>
                </h2>

                @auth
                    @if(!$book->hasUserReviewed())
                        <div class="bg-white/5 rounded-lg p-4 mb-4">
                            <h4 class="font-semibold text-white/80 mb-2">Rate this book</h4>
                            <form action="{{ route('books.review', ['book' => $book->id]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $book->id }}">
                                <div class="flex items-center gap-1 mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" onclick="setRating({{ $i }})" class="text-2xl rating-star" id="star-{{ $i }}">
                                            <i class="ti ti-star text-white/30"></i>
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="rating-input" value="">
                                <textarea name="review" rows="3" placeholder="Share your thoughts..." class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white placeholder-white/30 focus:outline-none focus:border-purple-500"></textarea>
                                <button type="submit" class="mt-2 bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition text-sm">
                                    <i class="ti ti-send"></i> Post Review
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-lg p-4 mb-4 flex justify-between items-center">
                            <div class="flex items-center gap-2 text-emerald-400">
                                <i class="ti ti-check-circle"></i>
                                <span>You rated this</span>
                                <div class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $book->userRating())
                                            <i class="ti ti-star-filled text-yellow-400 text-sm"></i>
                                        @else
                                            <i class="ti ti-star text-white/20 text-sm"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <form action="{{ route('books.review.delete', $book) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-400 hover:text-red-300" onclick="return confirm('Delete your review?')">
                                    <i class="ti ti-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth

                <!-- Reviews List -->
                <div class="space-y-4">
                    @forelse($book->reviews()->with('user')->latest()->take(10)->get() as $review)
                        <div class="bg-white/5 rounded-lg p-4 border border-white/5">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-white/90 text-sm">{{ $review->user->name ?? 'Anonymous' }}</span>
                                        <span class="text-white/30 text-xs">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($review->rating)
                                        <div class="flex items-center gap-0.5 mt-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="ti ti-star-filled text-yellow-400 text-xs"></i>
                                                @else
                                                    <i class="ti ti-star text-white/20 text-xs"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    @endif
                                </div>
                                <button onclick="toggleHelpful({{ $review->id }}, this)"
                                        class="text-white/40 hover:text-purple-400 transition text-sm flex items-center gap-1">
                                    <i class="ti ti-thumb-up"></i>
                                    <span class="helpful-count-{{ $review->id }}">{{ $review->helpful_count ?? 0 }}</span>
                                </button>
                            </div>
                            @if($review->review)
                                <p class="text-white/60 text-sm mt-2">{{ $review->review }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-white/30 py-8">
                            <i class="ti ti-message-circle-2 text-3xl block mb-2"></i>
                            No reviews yet
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Purchase Modal -->
<div id="purchaseModal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 p-4">
    <div class="bg-gray-900 rounded-2xl max-w-lg w-full mx-auto overflow-hidden shadow-2xl border border-white/10">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Complete Purchase</h3>
                <button onclick="closePurchaseModal()" class="text-white/80 hover:text-white transition">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6" id="purchaseModalContent">
            <div class="text-center py-8">
                <i class="ti ti-loader-2 animate-spin text-3xl text-purple-400"></i>
                <p class="text-white/50 mt-2">Loading...</p>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 p-4">
    <div class="bg-gray-900 rounded-2xl max-w-md w-full mx-auto overflow-hidden shadow-2xl text-center border border-white/10">
        <div class="p-6">
            <div class="w-20 h-20 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ti ti-circle-check text-4xl text-emerald-400"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Purchase Complete! 🎉</h3>
            <p class="text-white/60 mb-4">Book added to your library.</p>
            <div class="flex gap-3">
                <button onclick="closeSuccessAndRead()" class="flex-1 bg-purple-600 text-white py-3 rounded-xl font-semibold hover:bg-purple-700 transition">
                    <i class="ti ti-book-open"></i> Read Now
                </button>
                <button onclick="closeSuccessAndDownload()" class="flex-1 border border-purple-400/40 text-purple-300 py-3 rounded-xl font-semibold hover:bg-purple-600/20 transition">
                    <i class="ti ti-download"></i> Download
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.rating-star { cursor: pointer; transition: all 0.2s ease; }
.rating-star:hover { transform: scale(1.2); }
.rating-star.active i { color: #fbbf24 !important; }
</style>

@push('scripts')
<script>
let selectedRating = 0;

function setRating(val) {
    selectedRating = val;
    document.getElementById('rating-input').value = val;
    for (let i = 1; i <= 5; i++) {
        const star = document.getElementById('star-' + i);
        const icon = star.querySelector('i');
        if (i <= val) {
            icon.className = 'ti ti-star-filled text-yellow-400';
        } else {
            icon.className = 'ti ti-star text-white/30';
        }
    }
}

function toggleHelpful(reviewId, button) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) return;

    fetch(`/reviews/${reviewId}/helpful`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const countSpan = document.querySelector('.helpful-count-' + reviewId);
            if (countSpan) countSpan.textContent = data.helpful_count;
            button.classList.toggle('text-purple-400');
        }
    })
    .catch(() => {});
}

function shareBook() {
    if (navigator.share) {
        navigator.share({
            title: '{{ addslashes($book->title) }}',
            text: 'Check out this book: {{ addslashes($book->title) }} by {{ addslashes($book->author) }}',
            url: window.location.href
        }).catch(() => {});
    } else {
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link copied to clipboard!');
        });
    }
}

// Purchase Modal Functions
let currentBook = null;
let successRedirectUrl = null;
let successDownloadUrl = null;

function showPurchaseModal(bookId, bookPrice, bookTitle) {
    currentBook = { id: bookId, price: bookPrice, title: bookTitle };
    const modal = document.getElementById('purchaseModal');
    const content = document.getElementById('purchaseModalContent');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    content.innerHTML = `
        <div class="text-center py-8">
            <i class="ti ti-loader-2 animate-spin text-3xl text-purple-400"></i>
            <p class="text-white/50 mt-2">Loading purchase info...</p>
        </div>
    `;
    fetch('/wallet/balance')
        .then(res => res.json())
        .then(walletData => {
            const walletBalance = walletData.balance || 0;
            const hasSufficientFunds = walletBalance >= bookPrice;
            content.innerHTML = `
                <div class="flex gap-4 mb-6 pb-4 border-b border-white/10">
                    <div class="w-20 h-24 bg-purple-900/30 rounded-xl flex items-center justify-center border border-purple-500/20">
                        <i class="ti ti-book text-3xl text-purple-400"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-white text-lg">${escapeHtml(bookTitle)}</h4>
                        <p class="text-sm text-white/40">Access for lifetime • Downloadable PDF</p>
                    </div>
                </div>
                <div class="bg-white/5 rounded-xl p-4 mb-6 border border-white/10">
                    <div class="flex justify-between mb-2">
                        <span class="text-white/60">Book Price</span>
                        <span class="font-semibold text-white">TSh ${Number(bookPrice).toLocaleString()}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-white/60">Wallet Balance</span>
                        <span class="font-semibold ${hasSufficientFunds ? 'text-emerald-400' : 'text-amber-400'}">
                            TSh ${Number(walletBalance).toLocaleString()}
                        </span>
                    </div>
                    <div class="border-t border-white/10 pt-2 mt-2">
                        <div class="flex justify-between">
                            <span class="font-semibold text-white">${hasSufficientFunds ? 'Total to Pay' : 'Amount Needed'}</span>
                            <span class="font-bold ${hasSufficientFunds ? 'text-emerald-400' : 'text-red-400'} text-lg">
                                TSh ${Number(hasSufficientFunds ? bookPrice : bookPrice - walletBalance).toLocaleString()}
                            </span>
                        </div>
                    </div>
                </div>
                ${hasSufficientFunds ? `
                    <button onclick="confirmPurchaseWithWallet()"
                            class="w-full bg-gradient-to-r from-emerald-500 to-green-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition flex items-center justify-center gap-2">
                        <i class="ti ti-shopping-cart-check"></i>
                        Confirm Purchase (TSh ${Number(bookPrice).toLocaleString()})
                    </button>
                ` : `
                    <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-4 mb-4">
                        <div class="flex items-center gap-2 text-amber-400 mb-2">
                            <i class="ti ti-alert-circle"></i>
                            <span class="font-semibold">Insufficient Balance</span>
                        </div>
                        <p class="text-sm text-amber-400/70">You need TSh ${Number(bookPrice - walletBalance).toLocaleString()} more to complete this purchase.</p>
                    </div>
                    <button onclick="topUpAndComplete(${bookPrice - walletBalance})"
                            class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition flex items-center justify-center gap-2 mb-3">
                        <i class="ti ti-plus-circle"></i>
                        Add TSh ${Number(bookPrice - walletBalance).toLocaleString()} & Complete Purchase
                    </button>
                    <div class="relative my-3">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-white/10"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-gray-900 text-white/40">OR</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <button onclick="payWithMobileMoney('mpesa', ${bookPrice - walletBalance})"
                                class="w-full border border-green-500/50 text-green-400 py-2.5 rounded-xl font-medium hover:bg-green-500/10 transition flex items-center justify-center gap-2">
                            <i class="ti ti-device-mobile"></i> Pay with M-Pesa
                        </button>
                        <button onclick="payWithMobileMoney('tigopesa', ${bookPrice - walletBalance})"
                                class="w-full border border-blue-500/50 text-blue-400 py-2.5 rounded-xl font-medium hover:bg-blue-500/10 transition flex items-center justify-center gap-2">
                            <i class="ti ti-device-mobile"></i> Pay with TigoPesa
                        </button>
                    </div>
                `}
                <div class="mt-6 pt-4 border-t border-white/10 text-center">
                    <div class="flex justify-center gap-4 text-xs text-white/30">
                        <span>🔒 256-bit SSL</span>
                        <span>✓ Fraud Protection</span>
                        <span>📧 Receipt via Email</span>
                    </div>
                </div>
            `;
        });
}

function confirmPurchaseWithWallet() {
    const content = document.getElementById('purchaseModalContent');
    content.innerHTML = `
        <div class="text-center py-8">
            <i class="ti ti-loader-2 animate-spin text-3xl text-purple-400"></i>
            <p class="text-white/50 mt-2">Processing purchase...</p>
        </div>
    `;
    fetch(`/books/${currentBook.id}/purchase-wallet`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closePurchaseModal();
            showSuccessModal(data.redirect_url, data.download_url);
        } else {
            alert(data.message || 'Purchase failed');
        }
    })
    .catch(() => {
        alert('Network error. Please try again.');
    });
}

function topUpAndComplete(amount) {
    closePurchaseModal();
    window.location.href = `/payment/methods?amount=${amount}&book_id=${currentBook.id}&redirect=library`;
}

function payWithMobileMoney(gateway, amount) {
    const content = document.getElementById('purchaseModalContent');
    content.innerHTML = `
        <div class="text-center py-4">
            <div class="mb-4">
                <i class="ti ti-device-mobile text-5xl text-purple-400"></i>
            </div>
            <h4 class="font-bold text-white mb-2">Enter Phone Number</h4>
            <input type="tel" id="mobile-phone" placeholder="0712 345 678"
                   class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl mb-4 text-center text-lg text-white">
            <button onclick="processMobileMoneyPayment('${gateway}', ${amount})"
                    class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-xl font-semibold">
                Pay TSh ${Number(amount).toLocaleString()}
            </button>
            <button onclick="showPurchaseModal(${currentBook.id}, ${currentBook.price}, '${escapeHtml(currentBook.title)}')"
                    class="w-full mt-2 text-white/40 py-2 rounded-xl">Back</button>
        </div>
    `;
}

function processMobileMoneyPayment(gateway, amount) {
    const phone = document.getElementById('mobile-phone')?.value;
    if (!phone) {
        alert('Please enter your phone number');
        return;
    }
    const content = document.getElementById('purchaseModalContent');
    content.innerHTML = `
        <div class="text-center py-8">
            <i class="ti ti-loader-2 animate-spin text-3xl text-purple-400"></i>
            <p class="text-white/50 mt-2">Processing ${gateway.toUpperCase()} payment...</p>
            <p class="text-xs text-white/30 mt-2">Check your phone for the STK push</p>
        </div>
    `;
    fetch('/payment/initiate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
            gateway: gateway,
            amount: amount,
            phone: phone,
            book_id: currentBook.id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            content.innerHTML = `
                <div class="text-center py-4">
                    <i class="ti ti-circle-check text-5xl text-emerald-400 mb-3"></i>
                    <p class="text-white/70 mb-2">STK Push Sent!</p>
                    <p class="text-sm text-white/40 mb-4">Check your phone and enter PIN to complete.</p>
                    <div id="payment-status" class="text-sm text-white/40 mb-3">Waiting for confirmation...</div>
                    <button onclick="checkPaymentAndComplete('${data.payment_id}')" class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition">
                        I've Completed Payment
                    </button>
                </div>
            `;
            let attempts = 0;
            const interval = setInterval(() => {
                attempts++;
                const statusDiv = document.getElementById('payment-status');
                if (statusDiv) statusDiv.textContent = `Checking... (${attempts})`;
                fetch(`/payment/status/${data.payment_id}`)
                    .then(res => res.json())
                    .then(statusData => {
                        if (statusData.status === 'completed') {
                            clearInterval(interval);
                            if (statusDiv) statusDiv.textContent = '✅ Payment confirmed! Completing purchase...';
                            setTimeout(() => confirmPurchaseWithWallet(), 1000);
                        } else if (attempts > 15) {
                            clearInterval(interval);
                            if (statusDiv) statusDiv.textContent = '⏰ Still waiting? Click the button above.';
                        }
                    });
            }, 3000);
        } else {
            alert(data.message || 'Payment initiation failed');
        }
    });
}

function checkPaymentAndComplete(paymentId) {
    fetch(`/payment/status/${paymentId}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'completed') {
                confirmPurchaseWithWallet();
            } else {
                alert('Payment still pending. Please wait or check your phone.');
            }
        });
}

function showSuccessModal(redirectUrl, downloadUrl) {
    successRedirectUrl = redirectUrl;
    successDownloadUrl = downloadUrl;
    const modal = document.getElementById('successModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeSuccessAndRead() {
    const modal = document.getElementById('successModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    if (successRedirectUrl) window.location.href = successRedirectUrl;
}

function closeSuccessAndDownload() {
    const modal = document.getElementById('successModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    if (successDownloadUrl) window.location.href = successDownloadUrl;
}

function closePurchaseModal() {
    const modal = document.getElementById('purchaseModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
@endpush

@endsection