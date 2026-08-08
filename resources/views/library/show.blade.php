@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button -->
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('library.index') }}" class="inline-flex items-center text-jlibrary-600 hover:text-jlibrary-700">
            <i class="ti ti-arrow-left mr-1"></i> Back to Library
        </a>

        <div class="flex items-center gap-2">
            <x-bookmark-button :item="$book" type="book" size="md" />
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-8">
        <!-- Left Column - Book Cover -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden sticky top-24">
                <div class="h-64 bg-gradient-to-br from-jlibrary-500 to-jlibrary-700 flex items-center justify-center relative">
                    @if($book->cover_image)
                        <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                    @else
                        <i class="ti ti-book text-8xl text-white/50"></i>
                    @endif
                </div>

                <div class="p-6">
                    @if($book->is_paid)
                        <div class="mb-4">
                            <div class="text-2xl font-bold text-jlibrary-600">TSh {{ number_format($book->price, 0) }}</div>
                            <p class="text-gray-500 text-sm">One-time purchase. Lifetime access.</p>
                        </div>
                    @endif

                    <div class="space-y-3">
                        @auth
                           @if($hasAccess ?? false)
                                <a href="{{ route('library.read', $book->id) }}"
                                   class="block text-center bg-jlibrary-600 text-white px-4 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                                    <i class="ti ti-eye"></i> Read Now
                                </a>
                                <a href="{{ route('library.download', $book->id) }}"
                                   class="block text-center border border-jlibrary-600 text-jlibrary-600 px-4 py-2 rounded-lg hover:bg-jlibrary-600 hover:text-white transition">
                                    <i class="ti ti-download"></i> Download PDF
                                </a>
                            @else
                                <button onclick="showPurchaseModal({{ $book->id }}, {{ $book->price }}, '{{ addslashes($book->title) }}')"
                                        class="block text-center bg-gradient-to-r from-yellow-500 to-orange-500 text-white px-4 py-2 rounded-lg hover:shadow-lg transition w-full">
                                    <i class="ti ti-shopping-cart"></i> Purchase for TSh {{ number_format($book->price, 2) }}
                                </button>
                            @endif

                            <button onclick="shareBook()"
                                    class="block text-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition w-full">
                                <i class="ti ti-share mr-2"></i> Share This Book
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="block text-center bg-jlibrary-600 text-white px-4 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                                Login to Read
                            </a>
                            <a href="{{ route('register') }}" class="block text-center border border-jlibrary-600 text-jlibrary-600 px-4 py-2 rounded-lg hover:bg-jlibrary-600 hover:text-white transition">
                                Create Free Account
                            </a>
                        @endauth
                    </div>

                    @if(($progress ?? null) && $progress->progress_percent > 0)
                        <div class="mt-6 pt-4 border-t">
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Reading Progress</span>
                                <span>{{ $progress->progress_percent }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $progress->progress_percent }}%"></div>
                            </div>
                            @if($progress->status == 'completed')
                                <div class="mt-2 text-green-600 text-sm">
                                    <i class="ti ti-certificate"></i> Completed!
                                    <a href="#" class="underline">Get Certificate</a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Book Details -->
        <div class="md:col-span-2">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $book->title }}</h1>
                    <p class="text-gray-600 text-lg mb-4">by {{ $book->author }}</p>
                </div>
            </div>

            <!-- ⭐ RATINGS SECTION -->
            <div class="flex items-center flex-wrap gap-4 mb-6">
                <div class="flex items-center gap-2">
                    <div class="flex items-center" id="average-stars">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($book->averageRating()))
                                <i class="ti ti-star-filled text-yellow-400 text-2xl"></i>
                            @else
                                <i class="ti ti-star text-gray-300 text-2xl"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="text-2xl font-bold text-gray-800">
                        {{ number_format($book->averageRating(), 1) }}
                    </span>
                    <span class="text-gray-500 text-sm">
                        ({{ $book->ratingCount() }} ratings)
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-4 text-sm text-gray-500 mb-6">
                <span><i class="ti ti-file-text"></i> {{ $book->total_pages }} pages</span>
                <span><i class="ti ti-download"></i> {{ number_format($book->downloads) }} downloads</span>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold mb-3">Description</h2>
                <p class="text-gray-700 leading-relaxed">{{ $book->description ?? 'No description available for this book.' }}</p>
            </div>

            <div class="bg-gray-50 rounded-xl p-6 mb-6">
                <h2 class="text-xl font-semibold mb-3">About the Author</h2>
                <p class="text-gray-700">{{ $book->author }} is a renowned author in this field.</p>
            </div>

            <!-- ============================================ -->
            <!-- ⭐ REVIEWS SECTION                            -->
            <!-- ============================================ -->
            <div class="mt-8" id="reviews-section">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-message-circle-2"></i>
                    Reviews & Comments
                    <span class="text-sm font-normal text-gray-500" id="review-count">
                        ({{ $book->reviews()->count() }} reviews)
                    </span>
                </h3>

                @auth
                    @if(!$book->hasUserReviewed())
                        <div class="bg-gray-50 rounded-xl p-5 mb-6" id="review-form-container">
                            <h4 class="font-semibold text-gray-800 mb-3">Rate & review this book</h4>
                            <form action="{{ route('books.review', $book) }}" method="POST" id="reviewForm">
                                @csrf

                                <!-- ⭐ FIXED STAR RATING -->
                              <div class="flex items-center gap-1 mb-1" id="new-rating-stars">
    @for($i = 1; $i <= 5; $i++)
        <button type="button"
                style="cursor:pointer;outline:none;background:transparent;border:0;padding:0;line-height:1;-webkit-tap-highlight-color:transparent;"
                aria-label="Rate {{ $i }} stars"
                onmouseenter="starHover({{ $i }})"
                onmouseleave="starReset()"
                onclick="starClick({{ $i }})">
            <i id="star-{{ $i }}" class="ti ti-star text-3xl text-gray-300 transition-all duration-150"></i>
        </button>
    @endfor
</div>
                                <p class="text-xs text-gray-400 mb-3" id="rating-hint">Tap a star to rate</p>
                                <input type="hidden" name="rating" id="rating-input" value="">

                                <textarea name="review" id="reviewText" rows="4"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                          placeholder="Share your thoughts about this book... (optional)"></textarea>

                                <button type="submit" class="mt-3 bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                                    <i class="ti ti-send mr-1"></i> Post
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-green-50 rounded-xl p-4 mb-6 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <i class="ti ti-check-circle text-green-500"></i>
                                <span class="text-sm text-gray-600">You rated this</span>
                                <div class="flex items-center gap-0.5" id="user-rating-display">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $book->userRating())
                                            <i class="ti ti-star-filled text-yellow-400 text-sm"></i>
                                        @else
                                            <i class="ti ti-star text-gray-300 text-sm"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <form action="{{ route('books.review.delete', $book) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-500 hover:text-red-700" onclick="return confirm('Delete your rating and review?')">
                                    <i class="ti ti-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth

                <!-- Reviews List -->
                <div class="space-y-4" id="reviews-list">
                    @forelse($book->reviews()->with('user')->latest()->get() as $review)
                        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 review-item" id="review-{{ $review->id }}">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-semibold text-gray-800">{{ $review->user->full_name ?? $review->user->name ?? 'Anonymous' }}</span>
                                        <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($review->rating)
                                        <div class="flex items-center gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="ti ti-star-filled text-yellow-400 text-sm"></i>
                                                @else
                                                    <i class="ti ti-star text-gray-300 text-sm"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    @endif
                                </div>

                                <button onclick="toggleHelpful({{ $review->id }}, this)"
                                        class="text-sm text-gray-400 hover:text-purple-600 transition flex items-center gap-1.5 px-2 py-1 rounded-lg helpful-btn {{ (auth()->check() && $review->isHelpfulByUser(auth()->id())) ? 'liked' : '' }}"
                                        data-review-id="{{ $review->id }}">
                                    <i class="ti ti-thumb-up {{ (auth()->check() && $review->isHelpfulByUser(auth()->id())) ? 'text-purple-600 font-bold' : '' }}"></i>
                                    <span class="helpful-count-{{ $review->id }}">{{ $review->helpful_count ?? 0 }}</span>
                                </button>
                            </div>
                            @if($review->review)
                                <p class="text-gray-600 leading-relaxed review-text">{{ $review->review }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="bg-gray-50 rounded-xl p-8 text-center" id="no-reviews">
                            <i class="ti ti-message-circle-2 text-4xl text-gray-300 mb-2 block"></i>
                            <p class="text-gray-500">No reviews yet. Be the first to review this book!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== PURCHASE MODAL ========== -->
<div id="purchaseModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full mx-auto overflow-hidden shadow-2xl animate-modal-pop">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4 text-white">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold" id="modalTitle">Complete Purchase</h3>
                <button onclick="closePurchaseModal()" class="text-white/80 hover:text-white transition">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6" id="purchaseModalContent">
            <div class="text-center py-8">
                <i class="ti ti-loader-2 animate-spin text-3xl text-purple-600"></i>
                <p class="text-gray-500 mt-2">Loading...</p>
            </div>
        </div>
    </div>
</div>

<!-- ========== SUCCESS MODAL ========== -->
<div id="successModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-md w-full mx-auto overflow-hidden shadow-2xl text-center animate-modal-pop">
        <div class="p-6">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ti ti-circle-check text-4xl text-green-500"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Purchase Complete! 🎉</h3>
            <p class="text-gray-600 mb-4" id="successMessage">Book added to your library.</p>
            <div class="flex gap-3">
                <button onclick="closeSuccessAndRead()" class="flex-1 bg-purple-600 text-white py-3 rounded-xl font-semibold hover:bg-purple-700 transition">
                    <i class="ti ti-book-open"></i> Read Now
                </button>
                <button onclick="closeSuccessAndDownload()" class="flex-1 border border-purple-600 text-purple-600 py-3 rounded-xl font-semibold hover:bg-purple-50 transition">
                    <i class="ti ti-download"></i> Download
                </button>
            </div>
            <p class="text-xs text-gray-400 mt-4">
                <i class="ti ti-mail"></i> Receipt sent to your email
            </p>
        </div>
    </div>
</div>

<style>
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.animate-modal-pop { animation: modalPop 0.2s ease-out; }

.helpful-btn { cursor: pointer; transition: all 0.2s ease; }
.helpful-btn:hover { background: #f3f4f6; }
.helpful-btn.liked { color: #7c3aed !important; background: #ede9fe; }
.helpful-btn.liked i { font-weight: 900; }

/* ⭐ Star rating cursor & interaction */
.star-btn {
    cursor: pointer;
    outline: none;
    -webkit-tap-highlight-color: transparent;
}
.star-btn:focus-visible {
    outline: 2px solid #a78bfa;
    outline-offset: 2px;
    border-radius: 4px;
}
.star-btn i {
    display: inline-block;
    pointer-events: none; /* clicks pass through <i> to the <button> */
}
</style>

@push('scripts')
<script>
/* ⭐ INLINE STAR RATING — no DOMContentLoaded needed */
let selRating = 0;
const rInput = document.getElementById('rating-input');
const rHint  = document.getElementById('rating-hint');

function starPaint(val) {
    for (let i = 1; i <= 5; i++) {
        const el = document.getElementById('star-' + i);
        if (!el) continue;
        if (i <= val) {
            el.classList.remove('ti-star','text-gray-300');
            el.classList.add('ti-star-filled','text-yellow-400');
        } else {
            el.classList.remove('ti-star-filled','text-yellow-400');
            el.classList.add('ti-star','text-gray-300');
        }
    }
}
function starHover(n) { starPaint(n); }
function starReset()  { starPaint(selRating); }
function starClick(n) {
    selRating = n;
    if (rInput) rInput.value = n;
    starPaint(n);
    if (rHint) {
        rHint.textContent = n + ' star' + (n > 1 ? 's' : '') + ' selected';
        rHint.classList.add('text-yellow-600');
    }
    const el = document.getElementById('star-' + n);
    if (el) {
        el.style.transform = 'scale(1.3)';
        setTimeout(() => el.style.transform = '', 120);
    }
}

/* ============================================
   REVIEW FORM AJAX SUBMIT
   ============================================ */
(function() {
    const reviewForm = document.getElementById('reviewForm');
    if (!reviewForm) return;

    reviewForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const ratingValue = rInput ? rInput.value : '';
        if (!ratingValue || parseInt(ratingValue, 10) < 1) {
            alert('Please tap a star to rate this book first.');
            return;
        }

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        submitBtn.innerHTML = '<i class="ti ti-loader-2 animate-spin"></i> Submitting...';
        submitBtn.disabled = true;

        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Server error');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Thanks for your rating!');
                setTimeout(() => location.reload(), 800);
            } else {
                alert(data.message || 'Failed to submit review.');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
})();

/* ============================================
   SHARE FUNCTION
   ============================================ */
function shareBook() {
    const shareData = {
        title: '{{ addslashes($book->title) }}',
        text: 'Check out this book: {{ addslashes($book->title) }} by {{ addslashes($book->author) }}',
        url: window.location.href
    };
    if (navigator.share) {
        navigator.share(shareData).catch(() => {});
    } else {
        navigator.clipboard.writeText(window.location.href).then(() => {
            showToast('Link copied to clipboard! Share it with your friends.');
        }).catch(() => {
            const textArea = document.createElement('textarea');
            textArea.value = window.location.href;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showToast('Link copied to clipboard!');
        });
    }
}

/* ============================================
   TOAST NOTIFICATION
   ============================================ */
function showToast(message) {
    const existing = document.querySelector('.kimi-toast');
    if (existing) existing.remove();
    const toast = document.createElement('div');
    toast.className = 'kimi-toast fixed bottom-4 right-4 bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    toast.innerHTML = '<i class="ti ti-check mr-1"></i> ' + message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.5s';
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}

/* ============================================
   HELPFUL / LIKE FUNCTION
   ============================================ */
function toggleHelpful(reviewId, button) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        alert('Please refresh the page and try again.');
        return;
    }
    button.style.opacity = '0.6';
    button.style.pointerEvents = 'none';

    fetch(`/reviews/${reviewId}/helpful`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const countSpan = document.querySelector('.helpful-count-' + reviewId);
            if (countSpan) countSpan.textContent = data.helpful_count;
            button.classList.toggle('liked', data.liked);
            const icon = button.querySelector('i');
            if (icon) {
                icon.classList.toggle('text-purple-600', data.liked);
                icon.classList.toggle('font-bold', data.liked);
            }
        } else {
            alert(data.message || 'Something went wrong');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        button.style.opacity = '1';
        button.style.pointerEvents = 'auto';
    });
}

/* ============================================
   PURCHASE MODAL FUNCTIONS
   ============================================ */
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
            <i class="ti ti-loader-2 animate-spin text-3xl text-purple-600"></i>
            <p class="text-gray-500 mt-2">Loading purchase info...</p>
        </div>
    `;
    fetch('/wallet/balance')
        .then(res => res.json())
        .then(walletData => {
            const walletBalance = walletData.balance || 0;
            const shortfall = bookPrice - walletBalance;
            const hasSufficientFunds = walletBalance >= bookPrice;
            content.innerHTML = `
                <div class="flex gap-4 mb-6 pb-4 border-b">
                    <div class="w-20 h-24 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="ti ti-book text-3xl text-purple-600"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-800 text-lg">${escapeHtml(bookTitle)}</h4>
                        <p class="text-sm text-gray-500">Access for lifetime • Downloadable PDF</p>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Book Price</span>
                        <span class="font-semibold text-gray-800">TSh ${Number(bookPrice).toLocaleString()}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Wallet Balance</span>
                        <span class="font-semibold ${hasSufficientFunds ? 'text-green-600' : 'text-amber-600'}">
                            TSh ${Number(walletBalance).toLocaleString()}
                        </span>
                    </div>
                    <div class="border-t pt-2 mt-2">
                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-800">${hasSufficientFunds ? 'Total to Pay' : 'Amount Needed'}</span>
                            <span class="font-bold ${hasSufficientFunds ? 'text-green-600' : 'text-red-600'} text-lg">
                                TSh ${Number(hasSufficientFunds ? bookPrice : shortfall).toLocaleString()}
                            </span>
                        </div>
                    </div>
                </div>
                ${hasSufficientFunds ? `
                    <button onclick="confirmPurchaseWithWallet()"
                            class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition flex items-center justify-center gap-2">
                        <i class="ti ti-shopping-cart-check"></i>
                        Confirm Purchase (TSh ${Number(bookPrice).toLocaleString()})
                    </button>
                ` : `
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
                        <div class="flex items-center gap-2 text-amber-700 mb-2">
                            <i class="ti ti-alert-circle"></i>
                            <span class="font-semibold">Insufficient Balance</span>
                        </div>
                        <p class="text-sm text-amber-600">You need TSh ${Number(shortfall).toLocaleString()} more to complete this purchase.</p>
                    </div>
                    <button onclick="topUpAndComplete(${shortfall})"
                            class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition flex items-center justify-center gap-2 mb-3">
                        <i class="ti ti-plus-circle"></i>
                        Add TSh ${Number(shortfall).toLocaleString()} & Complete Purchase
                    </button>
                    <div class="relative my-3">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">OR</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <button onclick="payWithMobileMoney('mpesa', ${shortfall})"
                                class="w-full border border-green-500 text-green-600 py-2.5 rounded-xl font-medium hover:bg-green-50 transition flex items-center justify-center gap-2">
                            <i class="ti ti-device-mobile"></i> Pay with M-Pesa
                        </button>
                        <button onclick="payWithMobileMoney('tigopesa', ${shortfall})"
                                class="w-full border border-blue-500 text-blue-600 py-2.5 rounded-xl font-medium hover:bg-blue-50 transition flex items-center justify-center gap-2">
                            <i class="ti ti-device-mobile"></i> Pay with TigoPesa
                        </button>
                        <button onclick="payWithMobileMoney('halopesa', ${shortfall})"
                                class="w-full border border-red-500 text-red-600 py-2.5 rounded-xl font-medium hover:bg-red-50 transition flex items-center justify-center gap-2">
                            <i class="ti ti-device-mobile"></i> Pay with HaloPesa
                        </button>
                    </div>
                `}
                <div class="mt-6 pt-4 border-t text-center">
                    <div class="flex justify-center gap-4 text-xs text-gray-400">
                        <span>🔒 256-bit SSL</span>
                        <span>✓ Fraud Protection</span>
                        <span>📧 Receipt via Email</span>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            content.innerHTML = `
                <div class="text-center py-8">
                    <i class="ti ti-circle-x text-3xl text-red-500"></i>
                    <p class="text-gray-500 mt-2">Error loading wallet info</p>
                    <button onclick="closePurchaseModal()" class="mt-4 text-purple-600">Close</button>
                </div>
            `;
        });
}

function confirmPurchaseWithWallet() {
    const content = document.getElementById('purchaseModalContent');
    content.innerHTML = `
        <div class="text-center py-8">
            <i class="ti ti-loader-2 animate-spin text-3xl text-purple-600"></i>
            <p class="text-gray-500 mt-2">Processing purchase...</p>
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
            content.innerHTML = `
                <div class="text-center py-8">
                    <i class="ti ti-circle-x text-3xl text-red-500"></i>
                    <p class="text-red-600 mt-2">${escapeHtml(data.message)}</p>
                    <button onclick="closePurchaseModal()" class="mt-4 text-purple-600">Close</button>
                </div>
            `;
        }
    })
    .catch(error => {
        content.innerHTML = `
            <div class="text-center py-8">
                <i class="ti ti-circle-x text-3xl text-red-500"></i>
                <p class="text-gray-500 mt-2">Network error. Please try again.</p>
                <button onclick="closePurchaseModal()" class="mt-4 text-purple-600">Close</button>
            </div>
        `;
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
                <i class="ti ti-device-mobile text-5xl text-purple-600"></i>
            </div>
            <h4 class="font-bold text-gray-800 mb-2">Enter Phone Number</h4>
            <input type="tel" id="mobile-phone" placeholder="0712 345 678"
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl mb-4 text-center text-lg">
            <button onclick="processMobileMoneyPayment('${gateway}', ${amount})"
                    class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-xl font-semibold">
                Pay TSh ${Number(amount).toLocaleString()}
            </button>
            <button onclick="showPurchaseModal(${currentBook.id}, ${currentBook.price}, '${escapeHtml(currentBook.title)}')"
                    class="w-full mt-2 text-gray-500 py-2 rounded-xl">Back</button>
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
            <i class="ti ti-loader-2 animate-spin text-3xl text-purple-600"></i>
            <p class="text-gray-500 mt-2">Processing ${gateway.toUpperCase()} payment...</p>
            <p class="text-xs text-gray-400 mt-2">Check your phone for the STK push</p>
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
                    <i class="ti ti-circle-check text-5xl text-green-500 mb-3"></i>
                    <p class="text-gray-700 mb-2">STK Push Sent!</p>
                    <p class="text-sm text-gray-500 mb-4">Check your phone and enter PIN to complete.</p>
                    <div id="payment-status" class="text-sm text-gray-500 mb-3">Waiting for confirmation...</div>
                    <button onclick="checkPaymentAndComplete('${data.payment_id}')" class="w-full bg-purple-600 text-white py-2 rounded-lg">
                        I've Completed Payment
                    </button>
                </div>
            `;
            let attempts = 0;
            const interval = setInterval(() => {
                attempts++;
                const statusDiv = document.getElementById('payment-status');
                if (statusDiv) statusDiv.innerHTML = `Checking... (${attempts})`;
                fetch(`/payment/status/${data.payment_id}`)
                    .then(res => res.json())
                    .then(statusData => {
                        if (statusData.status === 'completed') {
                            clearInterval(interval);
                            if (statusDiv) statusDiv.innerHTML = '✅ Payment confirmed! Completing purchase...';
                            setTimeout(() => confirmPurchaseWithWallet(), 1000);
                        } else if (attempts > 15) {
                            clearInterval(interval);
                            if (statusDiv) statusDiv.innerHTML = '⏰ Still waiting? Click the button above.';
                        }
                    });
            }, 3000);
        } else {
            content.innerHTML = `
                <div class="text-center py-8">
                    <i class="ti ti-circle-x text-3xl text-red-500"></i>
                    <p class="text-red-600 mt-2">${escapeHtml(data.message)}</p>
                    <button onclick="showPurchaseModal(${currentBook.id}, ${currentBook.price}, '${escapeHtml(currentBook.title)}')" class="mt-4 text-purple-600">Try Again</button>
                </div>
            `;
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
    if (successDownloadUrl) {
        window.location.href = successDownloadUrl;
    } else if (successRedirectUrl) {
        window.location.href = successRedirectUrl;
    }
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