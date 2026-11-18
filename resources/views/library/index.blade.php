@extends('layouts.app')

@section('content')
<div class="container mx-auto px-2 py-3 md:px-4 md:py-6">
    <!-- Header -->
    <div class="mb-3 md:mb-6">
        <div class="flex items-start justify-between gap-3 mb-2">
            <div class="flex-1 min-w-0">
                <h1 class="text-lg md:text-3xl font-bold text-gray-900 mb-0.5 md:mb-1">
                    @if(request('format') === 'hardcopy')
                        🏪 JLIBRARY MARKET
                    @else
                        📚 Global Library
                    @endif
                </h1>
                <p class="text-[10px] md:text-sm text-gray-600">
                    @if(request('format') === 'hardcopy')
                        Browse physical books from institutions and authors across the platform and Select any book to order
                    @else
                        Discover thousands of books from all libraries and institutions
                    @endif
                </p>
            </div>
            @if(request('format') === 'hardcopy')
                <a href="{{ route('library.index', request()->except('format', 'page')) }}"
                   class="inline-flex items-center gap-1.5 md:gap-2 bg-jlibrary-600 hover:bg-jlibrary-700 active:bg-jlibrary-800 text-white font-semibold px-3 md:px-5 py-2 md:py-2.5 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 whitespace-nowrap flex-shrink-0">
                    <i class="ti ti-arrow-left text-base md:text-lg"></i>
                    <span class="text-[10px] md:text-sm">Back to Library</span>
                </a>
            @endif
        </div>
    </div>
    
    <!-- Search and Filter Bar -->
    <div class="bg-white rounded-lg md:rounded-xl shadow-sm p-2 md:p-4 mb-3 md:mb-6">
        <form method="GET" action="{{ route('library.index') }}" class="flex flex-col gap-1.5 md:gap-3">
            <div class="flex flex-col md:flex-row gap-1.5 md:gap-3 items-start md:items-center">
                <a href="{{ request('format') === 'hardcopy' 
                            ? route('library.index', request()->except('format', 'page')) 
                            : route('library.index', array_merge(request()->except('page'), ['format' => 'hardcopy'])) }}"
                   class="w-full md:w-auto inline-flex items-center justify-center gap-1 px-3 md:px-4 py-1 md:py-2 text-[10px] md:text-sm rounded-lg transition whitespace-nowrap border font-medium
                          {{ request('format') === 'hardcopy'
                              ? 'bg-jlibrary-600 text-white border-jlibrary-600 hover:bg-jlibrary-700'
                              : 'bg-white text-jlibrary-600 border-jlibrary-300 hover:bg-jlibrary-50' }}">
                    <i class="ti ti-building-store"></i>
                    <span class="hidden sm:inline">JLIBRARY MARKET</span>
                    <span class="sm:hidden">Market</span>
                </a>

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

                @if(request('search') || request('category') || request('format'))
                    <a href="{{ route('library.index') }}" class="w-full md:w-auto bg-gray-200 text-gray-700 px-3 md:px-4 py-1 md:py-2 text-[10px] md:text-sm rounded-lg hover:bg-gray-300 transition whitespace-nowrap text-center">
                        <i class="ti ti-x"></i> <span class="hidden sm:inline">Clear</span>
                    </a>
                @endif
            </div>
        </form>
    </div>
    
    <!-- Results Count -->
    <div class="mb-2 md:mb-4 text-[10px] md:text-sm text-gray-500">
        {{ $books->total() }} {{ Str::plural('book', $books->total()) }}
        @if(request('category'))
            <span class="text-gray-400">·</span>
            <span class="font-medium text-jlibrary-600">{{ request('category') }}</span>
        @endif
    </div>
    
    <!-- Books Grid -->
    @if($books->count() > 0)
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-1 md:gap-2 lg:gap-3">
            @foreach($books as $book)
                @php
                    if ($book->institution_id) {
                        $bookUrl = route('institution.public.show', [
                            'institutionId' => $book->institution_id,
                            'book' => $book->id
                        ]);
                    } else {
                        $bookUrl = route('library.show', $book->id);
                    }
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
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 hidden md:flex items-end justify-center pb-2.5">
                                <span class="text-white text-[11px] font-semibold bg-jlibrary-600/80 px-3 py-1 rounded-full">
                                    <i class="ti ti-eye"></i> View
                                </span>
                            </div>
                            
                            <div class="absolute bottom-0.5 left-0.5 bg-black/70 text-white px-0.5 md:px-1.5 py-0.5 rounded text-[5px] md:text-[9px] max-w-[20px] md:max-w-[65px] truncate">
                                @if($book->institution_id && $book->institution)
                                    <i class="ti ti-building text-[5px] md:text-[9px]"></i> 
                                    <span class="hidden sm:inline">{{ Str::limit($book->institution->name ?? '', 6) }}</span>
                                @else
                                    🌐
                                @endif
                            </div>
                            
                            <div class="absolute top-0.5 right-0.5 z-10 scale-75 md:scale-100">
                                <x-bookmark-button :item="$book" type="book" size="xs" />
                            </div>

                            {{-- CART BUTTON (hardcopy only) --}}
                            @if(request('format') === 'hardcopy' && $book->offersHardcopy())
                                <button type="button"
                                        onclick="event.preventDefault(); event.stopPropagation(); addToCart({{ $book->id }}, '{{ addslashes($book->title) }}', {{ $book->effectiveHardcopyPrice() }}, '{{ $book->cover_image ? url('media/' . $book->cover_image) : '' }}', {{ (int) ($book->stock_quantity ?? 0) }})"
                                        class="absolute bottom-1 right-1 z-10 bg-jlibrary-600 hover:bg-jlibrary-700 text-white text-[8px] md:text-xs font-semibold px-1.5 md:px-2 py-1 rounded-full shadow-lg transition"
                                        title="Add to cart">
                                    <i class="ti ti-shopping-cart-plus text-[10px] md:text-sm"></i>
                                </button>
                            @endif
                        </div>
                    </a>
                    
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
        
        <div class="mt-3 md:mt-6">
            {{ $books->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-6 md:p-12 text-center">
            <i class="ti ti-books text-3xl md:text-6xl text-gray-400 mb-2 md:mb-4 block"></i>
            <h3 class="text-base md:text-xl font-semibold mb-1 md:mb-2">No books found</h3>
            <p class="text-xs md:text-sm text-gray-500">
                @if(request('format') === 'hardcopy')
                    No hardcopy books match your search. Try clearing filters.
                @else
                    Try adjusting your search or filter criteria
                @endif
            </p>
            <a href="{{ route('library.index') }}" class="inline-block mt-2 md:mt-4 text-xs md:text-sm text-jlibrary-600 hover:text-jlibrary-700">Clear filters</a>
        </div>
    @endif
</div>

<!-- ========================================== -->
<!-- FLOATING CART WIDGET                       -->
<!-- ========================================== -->
<div id="cart-widget" 
     style="display: none;"
     class="fixed bottom-6 right-6 z-[1500] items-center gap-2 bg-gradient-to-r from-jlibrary-600 to-purple-700 hover:from-jlibrary-700 hover:to-purple-800 text-white px-5 py-3 rounded-full shadow-2xl cursor-pointer transition-all hover:scale-105"
     onclick="openCartDrawer()">
    <i class="ti ti-shopping-cart text-xl"></i>
    <span class="font-bold">Cart</span>
    <span id="cart-badge" class="bg-white text-jlibrary-700 text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">0</span>
</div>

<!-- CART DRAWER -->
<div id="cart-drawer-backdrop" 
     class="fixed inset-0 bg-black/50 z-[1600] hidden"
     onclick="closeCartDrawer()"></div>

<div id="cart-drawer"
     class="fixed top-0 right-0 h-full w-full sm:w-[420px] bg-white shadow-2xl z-[1700] transform translate-x-full transition-transform duration-300 flex flex-col">
    
    <div class="bg-gradient-to-r from-jlibrary-600 to-purple-700 px-5 py-4 flex items-center justify-between">
        <h3 class="text-white font-bold text-lg flex items-center gap-2">
            <i class="ti ti-shopping-cart"></i> Your Cart
        </h3>
        <button onclick="closeCartDrawer()" class="text-white/80 hover:text-white">
            <i class="ti ti-x text-2xl"></i>
        </button>
    </div>

    <div id="cart-drawer-content" class="flex-1 overflow-y-auto p-4"></div>

    <div class="border-t p-4 bg-slate-50">
        <div class="flex justify-between items-center mb-3">
            <span class="text-sm text-slate-600">Total:</span>
            <span id="cart-total-display" class="font-bold text-lg text-jlibrary-700">TSh 0</span>
        </div>
        <button onclick="proceedToCheckout()"
                class="w-full bg-gradient-to-r from-jlibrary-600 to-purple-700 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition">
            <i class="ti ti-credit-card"></i> Proceed to Checkout
        </button>
        <button onclick="clearCart()" 
                class="w-full mt-2 text-xs text-red-500 hover:text-red-700 py-1">
            Clear cart
        </button>
    </div>
</div>

<script>
const CART_KEY = 'jlibrary_cart';

function getCart() {
    try { return JSON.parse(localStorage.getItem(CART_KEY) || '[]'); }
    catch (e) { return []; }
}

function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
    updateCartUI();
}

function addToCart(bookId, title, price, cover, stock) {
    const cart = getCart();
    const existing = cart.find(item => item.id === bookId);

    if (existing) {
        if (existing.qty >= stock) {
            showCartToast('Stock limit reached', true);
            return;
        }
        existing.qty += 1;
    } else {
        cart.push({ id: bookId, title, price, cover, stock, qty: 1 });
    }

    saveCart(cart);
    showCartToast('"' + title + '" added to cart');
}

function removeFromCart(bookId) {
    saveCart(getCart().filter(item => item.id !== bookId));
    if (window.cartDrawerOpen) { renderCartDrawer(); updateCartTotalDisplay(); }
}

function updateQty(bookId, delta) {
    const cart = getCart();
    const item = cart.find(i => i.id === bookId);
    if (!item) return;
    item.qty = Math.max(1, Math.min(item.stock, item.qty + delta));
    saveCart(cart);
    if (window.cartDrawerOpen) { renderCartDrawer(); updateCartTotalDisplay(); }
}

function clearCart() {
    saveCart([]);
    if (window.cartDrawerOpen) renderCartDrawer();
}

function cartTotal() {
    return getCart().reduce((sum, i) => sum + (i.price * i.qty), 0);
}

function cartCount() {
    return getCart().reduce((sum, i) => sum + i.qty, 0);
}

function updateCartUI() {
    const badge = document.getElementById('cart-badge');
    const widget = document.getElementById('cart-widget');
    const count = cartCount();

    if (widget) widget.style.display = count > 0 ? 'flex' : 'none';
    if (badge) badge.textContent = count;
    updateCartTotalDisplay();
}

function openCartDrawer() {
    document.getElementById('cart-drawer').classList.remove('translate-x-full');
    document.getElementById('cart-drawer-backdrop').classList.remove('hidden');
    window.cartDrawerOpen = true;
    renderCartDrawer();
    updateCartTotalDisplay();
}

function closeCartDrawer() {
    document.getElementById('cart-drawer').classList.add('translate-x-full');
    document.getElementById('cart-drawer-backdrop').classList.add('hidden');
    window.cartDrawerOpen = false;
}

function renderCartDrawer() {
    const content = document.getElementById('cart-drawer-content');
    if (!content) return;

    const cart = getCart();
    if (cart.length === 0) {
        content.innerHTML = `
            <div class="text-center py-16 text-gray-500">
                <i class="ti ti-shopping-cart-off text-5xl mb-3 block text-gray-300"></i>
                <p class="font-medium">Your cart is empty</p>
                <p class="text-xs mt-1">Add hardcopy books to get started</p>
            </div>`;
        return;
    }

    content.innerHTML = cart.map(item => `
        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg mb-2">
            <div class="w-12 h-16 bg-orange-100 rounded flex items-center justify-center flex-shrink-0 overflow-hidden">
                ${item.cover 
                    ? `<img src="${item.cover}" class="w-full h-full object-cover">` 
                    : '<i class="ti ti-book text-orange-500"></i>'}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-medium text-sm text-slate-800 line-clamp-1">${escapeHtml(item.title)}</p>
                <p class="text-xs text-jlibrary-600 font-semibold">TSh ${Number(item.price).toLocaleString()}</p>
                <div class="flex items-center gap-2 mt-1">
                    <button onclick="updateQty(${item.id}, -1)" class="w-5 h-5 rounded bg-slate-200 hover:bg-slate-300 text-xs font-bold flex items-center justify-center">−</button>
                    <span class="text-xs font-medium w-5 text-center">${item.qty}</span>
                    <button onclick="updateQty(${item.id}, 1)" class="w-5 h-5 rounded bg-slate-200 hover:bg-slate-300 text-xs font-bold flex items-center justify-center">+</button>
                </div>
            </div>
            <button onclick="removeFromCart(${item.id})" class="text-red-500 hover:text-red-700">
                <i class="ti ti-trash text-sm"></i>
            </button>
        </div>
    `).join('');
}

function updateCartTotalDisplay() {
    const el = document.getElementById('cart-total-display');
    if (el) el.textContent = 'TSh ' + cartTotal().toLocaleString();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showCartToast(message, isError = false) {
    const el = document.createElement('div');
    el.className = `fixed bottom-24 right-4 z-[2000] px-4 py-2 rounded-lg shadow-lg text-white text-sm font-medium ${isError ? 'bg-red-600' : 'bg-emerald-600'}`;
    el.innerHTML = `<i class="ti ${isError ? 'ti-alert-circle' : 'ti-check'} mr-1"></i>${message}`;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 2500);
}

function proceedToCheckout() {
    if (getCart().length === 0) return;
    window.location.href = '{{ route("library.bulk-checkout") }}';
}

document.addEventListener('DOMContentLoaded', updateCartUI);
</script>
@endsection