<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 z-40 h-screen w-64 bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 text-white transition-all duration-300 shadow-2xl overflow-y-auto border-r border-white/10">
    <!-- Logo -->
    <div class="flex items-center justify-between p-4 border-b border-white/10">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <i class="ti ti-book text-2xl text-purple-400"></i>
            <span class="text-xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">JLIBRARY</span>
        </a>
        <button id="close-sidebar" class="lg:hidden text-gray-400 hover:text-white">
            <i class="ti ti-x text-2xl"></i>
        </button>
    </div>

    <!-- User Info -->
    <div class="p-4 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center shadow-lg">
                <i class="ti ti-user text-white text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-sm text-white">{{ Auth::user()->full_name }}</p>
               <p class="text-xs text-gray-400">
    @if(Auth::user()->role === 'super_admin')
        👑 Super Administrator
    @elseif(Auth::user()->role === 'admin')
        🛡️ Administrator
    @else
        👤 Member
    @endif
</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 py-4">
        <div class="px-3 mb-2">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Main Menu</p>
        </div>
        
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ti ti-dashboard text-indigo-400 text-xl"></i>
            <span>Dashboard</span>
        </a>

        <!-- Library -->
        <a href="{{ route('library.index') }}" class="nav-item {{ (request()->routeIs('library.*') && !request()->routeIs('library.my-library')) ? 'active' : '' }}">
            <i class="ti ti-books text-blue-400 text-xl"></i>
            <span>Library</span>
        </a>

        <!-- My Library -->
        <a href="{{ route('library.my-library') }}" class="nav-item {{ request()->routeIs('library.my-library') ? 'active' : '' }}">
            <i class="ti ti-bookmark text-sky-400 text-xl"></i>
            <span>My Library</span>
        </a>

        <!-- AI Assistant -->
        <a href="{{ route('ai.chat') }}" class="nav-item {{ request()->routeIs('ai.*') ? 'active' : '' }}">
            <i class="ti ti-robot text-purple-400 text-xl"></i>
            <span>AI Assistant</span>
        </a>

        <!-- Community -->
        <a href="{{ route('community.index') }}" class="nav-item {{ request()->routeIs('community.*') ? 'active' : '' }}">
            <i class="ti ti-users text-green-400 text-xl"></i>
            <span>Community</span>
        </a>

        <!-- File Converter - Using VALID Tabler Icon -->
        <a href="{{ route('converter.index') }}" class="nav-item {{ request()->routeIs('converter.*') ? 'active' : '' }}">
            <i class="ti ti-arrows-exchange text-orange-400 text-xl"></i>
            <span>File Converter</span>
        </a>

        <!-- Marketplace -->
        <a href="{{ route('marketplace.index') }}" class="nav-item {{ request()->routeIs('marketplace.*') ? 'active' : '' }}">
            <i class="ti ti-shopping-cart text-pink-400 text-xl"></i>
            <span>Marketplace</span>
        </a>

        <!-- Certificates -->
        <a href="{{ route('certificates.index') }}" class="nav-item {{ request()->routeIs('certificates.*') ? 'active' : '' }}">
            <i class="ti ti-certificate text-yellow-400 text-xl"></i>
            <span>Certificates</span>
        </a>

        <!-- My Documents -->
        <a href="{{ route('documents.index') }}" class="nav-item {{ request()->routeIs('documents.*') ? 'active' : '' }}">
            <i class="ti ti-file-text text-teal-400 text-xl"></i>
            <span>My Documents</span>
        </a>

        <!-- Wallet -->
        <a href="{{ route('wallet.index') }}" class="nav-item {{ request()->routeIs('wallet.*') ? 'active' : '' }}">
            <i class="ti ti-wallet text-emerald-400 text-xl"></i>
            <span>Wallet</span>
        </a>
<!-- Referrals Link -->
<a href="{{ route('referrals.index') }}" 
   class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300
   {{ request()->routeIs('referrals.*') 
        ? 'bg-gradient-to-r from-purple-700 via-fuchsia-600 to-pink-500 text-white shadow-md' 
        : 'text-gray-600' }}">

    <!-- Left Icon -->
    <span class="text-lg">💰</span>

    <!-- Gradient Text -->
    <span class="bg-gradient-to-r from-pink-200 via-yellow-100 to-white bg-clip-text text-transparent">
        Refer & Earn
    </span>

    <!-- Right Icon -->
    <span class="text-lg">🎁</span>

    @php
        $pendingReferrals = Auth::check() 
            ? App\Models\Referral::where('referrer_id', Auth::id())
                ->where('status', 'pending')
                ->count() 
            : 0;
    @endphp

    @if($pendingReferrals > 0)
        <span class="ml-auto bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">
            {{ $pendingReferrals }}
        </span>
    @endif

</a>

       <!-- Admin Section (visible to admin AND super_admin) -->

@if(Auth::user()->role === 'admin' || Auth::user()->role === 'super_admin')
    <div class="px-3 mt-4 mb-2">
        <p class="text-xs text-gray-500 uppercase tracking-wider">Administration</p>
    </div>
    
    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="ti ti-dashboard text-red-400 text-xl"></i>
        <span>Admin Panel</span>
        @if(Auth::user()->role === 'super_admin')
        <span class="ml-auto text-[10px] bg-red-500 text-white px-1.5 py-0.5 rounded-full">Super</span>
        @endif
    </a>
    
    <a href="{{ route('admin.books.index') }}" class="nav-item {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
        <i class="ti ti-books text-blue-400 text-xl"></i>
        <span>Manage Books</span>
    </a>
    
    <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="ti ti-users text-cyan-400 text-xl"></i>
        <span>Manage Users</span>
    </a>

    <a href="{{ route('admin.marketplace.pending') }}" class="nav-item {{ request()->routeIs('admin.marketplace.*') ? 'active' : '' }}">
        <i class="ti ti-shopping-cart text-amber-400 text-xl"></i>
        <span>Pending Approvals</span>
    </a>
@endif

    </nav>

    <!-- Logout Button -->
    <div class="p-4 border-t border-white/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-700 transition-all duration-200">
                <i class="ti ti-logout text-gray-400 text-xl"></i>
                <span>Sign Out</span>
            </button>
        </form>
    </div>
</aside>

<!-- Mobile Open Button -->
<button id="open-sidebar" class="lg:hidden fixed bottom-6 right-6 z-50 bg-purple-600 text-white p-4 rounded-full shadow-lg hover:bg-purple-700 transition">
    <i class="ti ti-menu-2 text-2xl"></i>
</button>

<!-- Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-30 hidden lg:hidden"></div>

<style>
    .nav-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.625rem 0.75rem;
        margin: 0.25rem 0.5rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        color: #9ca3af;
    }
    .nav-item:hover {
        background-color: #374151;
        color: white;
    }
    .nav-item:hover i {
        color: white !important;
    }
    .nav-item.active {
        background: linear-gradient(135deg, #7c3aed, #db2777);
        color: white;
    }
    .nav-item.active i {
        color: white !important;
    }
    .nav-item i {
        width: 1.25rem;
    }
</style>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const openBtn = document.getElementById('open-sidebar');
    const closeBtn = document.getElementById('close-sidebar');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    if (openBtn) openBtn.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeSidebar();
    });
</script>