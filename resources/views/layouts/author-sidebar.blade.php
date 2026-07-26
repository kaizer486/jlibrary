<aside id="sidebar" class="bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 text-white shadow-2xl flex flex-col border-r border-white/10">
    <!-- Logo -->
    <div class="p-4 border-b border-white/10 flex items-center justify-between">
        <a href="{{ route('author.dashboard') }}" class="flex items-center gap-2">
            <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" class="h-10 w-auto rounded-3xl">
            <span class="text-xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">JLIBRARY</span>
        </a>
        <button id="close-sidebar" class="lg:hidden text-gray-400 hover:text-white">
            <i class="ti ti-x text-2xl"></i>
        </button>
    </div>
    
    <!-- User Info -->
    <div class="p-4 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center shadow-lg overflow-hidden">
                @if(Auth::user()->avatar)
                    <img src="{{ url('media/' . Auth::user()->avatar) }}" class="w-full h-full object-cover">
                @else
                    <i class="ti ti-user text-white text-lg"></i>
                @endif
            </div>
            <div class="flex-1">
                <p class="font-semibold text-sm text-white">{{ Auth::user()->full_name }}</p>
                <p class="text-xs text-gray-400 font-medium">Author & Seller</p>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto p-3 space-y-1 sidebar-scroll">
        
        <!-- Dashboard -->
        <a href="{{ route('author.dashboard') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('author.dashboard') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="ti ti-dashboard text-lg"></i>
            <span class="font-medium">Dashboard</span>
        </a>
        
        <!-- My Books - Active for index, show, edit -->
        <a href="{{ route('author.books.index') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('author.books.index') || request()->routeIs('author.books.show') || request()->routeIs('author.books.edit') || request()->routeIs('author.books.update') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="ti ti-book text-lg"></i>
            <span class="font-medium">My Books</span>
            @php
                $bookCount = App\Models\Book::where('uploaded_by', Auth::id())->count();
            @endphp
            <span class="ml-auto text-xs bg-purple-600/30 px-2 py-0.5 rounded-full">{{ $bookCount }}</span>
        </a>
        
        <!-- Add New Book - Active ONLY for create -->
        <a href="{{ route('author.books.create') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('author.books.create') || request()->routeIs('author.books.store') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="ti ti-circle-plus text-lg"></i>
            <span class="font-medium">Add New Book</span>
        </a>
        
        <div class="border-t border-white/10 my-3"></div>
        
        <!-- Marketplace Section -->
        <div class="px-2 mb-2">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Marketplace</p>
        </div>

        <!-- My Listings -->
        <a href="{{ route('seller.listings') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('seller.listings') || request()->routeIs('marketplace.create') || request()->routeIs('marketplace.store') || request()->routeIs('marketplace.edit') || request()->routeIs('marketplace.update') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="ti ti-list text-lg"></i>
            <span class="font-medium">My Listings</span>
        </a>

        <!-- Orders -->
        <a href="{{ route('seller.orders') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('seller.orders') || request()->routeIs('seller.orders.show') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="ti ti-package text-lg"></i>
            <span class="font-medium">Orders</span>
            @php
                try {
                    $listingIds = App\Models\MarketplaceListing::where('seller_id', Auth::id())->pluck('id');
                    $pendingOrders = App\Models\MarketplaceOrder::whereIn('listing_id', $listingIds)
                        ->where('status', 'pending')->count();
                } catch (\Exception $e) {
                    $pendingOrders = 0;
                }
            @endphp
            @if($pendingOrders > 0)
                <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingOrders }}</span>
            @endif
        </a>

        <!-- Earnings -->
        <a href="{{ route('author.earnings') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('author.earnings') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="ti ti-wallet text-lg"></i>
            <span class="font-medium">Earnings</span>
        </a>
        
        <div class="border-t border-white/10 my-3"></div>
        
        <!-- Withdrawals -->
        <a href="{{ route('author.withdrawals.index') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('author.withdrawals.index') || request()->routeIs('author.withdrawals.create') || request()->routeIs('author.withdrawals.store') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="ti ti-cash text-lg"></i>
            <span class="font-medium">Withdrawals</span>
            @php
                $pendingWithdrawal = App\Models\WithdrawalRequest::where('user_id', Auth::id())->where('status', 'pending')->count();
            @endphp
            @if($pendingWithdrawal > 0)
                <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingWithdrawal }}</span>
            @endif
        </a>
        
        <div class="border-t border-white/10 my-3"></div>
        
        <!-- Back to Main -->
        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 text-gray-400 hover:bg-gray-700/50 hover:text-white">
            <i class="ti ti-arrow-left text-lg"></i>
            <span class="font-medium">Back to Main</span>
        </a>
    </nav>
    
    <!-- Logout -->
    <div class="p-4 border-t border-white/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-700 transition-all duration-200">
                <i class="ti ti-logout text-lg"></i>
                <span>Sign Out</span>
            </button>
        </form>
    </div>
</aside>

<style>
    .sidebar-scroll {
        scrollbar-width: auto;
        scrollbar-color: rgba(139, 92, 246, 0.6) rgba(31, 41, 55, 0.2);
    }
    
    .sidebar-scroll::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }
    
    .sidebar-scroll::-webkit-scrollbar-track {
        background: rgba(31, 41, 55, 0.3);
        border-radius: 10px;
        margin: 4px 0;
    }
    
    .sidebar-scroll::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, rgba(139, 92, 246, 0.8), rgba(236, 72, 153, 0.8));
        border-radius: 10px;
        border: 2px solid rgba(31, 41, 55, 0.5);
        min-height: 50px;
        transition: all 0.3s ease;
    }
    
    .sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, rgba(139, 92, 246, 1), rgba(236, 72, 153, 1));
        border-color: rgba(139, 92, 246, 0.3);
    }
</style>