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
                    @role('super_admin')
                        👑 Super Administrator
                    @else
                        @role('admin')
                            🛡️ Administrator
                        @else
                            @role('institution_admin')
                                🏢 Institution Admin
                            @else
                                @role('author')
                                    📚 Author
                                @else
                                    @role('librarian')
                                        📖 Librarian
                                    @else
                                        @role('instructor')
                                            👨‍🏫 Instructor
                                        @else
                                            👤 Member
                                        @endrole
                                    @endrole
                                @endrole
                            @endrole
                        @endrole
                    @endrole
                </p>
            </div>
        </div>
    </div>

    <!-- INSTITUTION DISPLAY - Only shows if user belongs to an institution -->
    @if(Auth::user()->institution_id && Auth::user()->institution)
    <div class="mx-3 mb-3 p-3 bg-white/5 rounded-xl border border-white/10">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 bg-indigo-500/20 rounded-lg flex items-center justify-center">
                <i class="ti ti-building text-indigo-400 text-xs"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[10px] text-gray-400 uppercase tracking-wider">Your Institution</p>
                <p class="text-sm font-medium text-white truncate" title="{{ Auth::user()->institution->name }}">
                    {{ Str::limit(Auth::user()->institution->name, 25) }}
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Navigation -->
    <nav class="flex-1 py-4">
        <div class="px-3 mb-2">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Main Menu</p>
        </div>
        
        <!-- DASHBOARD -->
        @role('super_admin')
            <a href="{{ url('/super-admin/dashboard') }}" class="nav-item {{ request()->is('super-admin/dashboard') ? 'active' : '' }}">
                <i class="ti ti-crown text-yellow-400 text-xl"></i>
                <span>Super Dashboard</span>
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="ti ti-dashboard text-indigo-400 text-xl"></i>
                <span>Dashboard</span>
            </a>
        @endrole

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

        <!-- File Converter -->
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

        <!-- Members Directory -->
        @if(auth()->user()->institution_id)
        <a href="{{ route('institution.members.directory') }}" class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition">
            <i class="ti ti-users text-cyan-400 text-xl"></i>
            <span>Members Directory</span>
        </a>
        @endif

        <!-- Referrals Link -->
        <a href="{{ route('referrals.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('referrals.*') ? 'bg-gradient-to-r from-purple-700 via-fuchsia-600 to-pink-500 text-white shadow-md' : 'text-gray-600' }}">
            <span class="text-lg">💰</span>
            <span class="bg-gradient-to-r from-pink-200 via-yellow-100 to-white bg-clip-text text-transparent">Refer & Earn</span>
            <span class="text-lg">🎁</span>
            @php
                $pendingReferrals = Auth::check() ? App\Models\Referral::where('referrer_id', Auth::id())->where('status', 'pending')->count() : 0;
            @endphp
            @if($pendingReferrals > 0)
                <span class="ml-auto bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingReferrals }}</span>
            @endif
        </a>



            <!-- Institution Admin Section (only for institution_admin) -->
@hasrole('institution_admin')
    <div class="px-3 mt-4 mb-2">
        <p class="text-xs text-gray-500 uppercase tracking-wider">Institution Admin</p>
    </div>
    <a href="{{ route('institution.dashboard') }}" class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition">
        <i class="ti ti-building text-blue-400 text-xl"></i>
        <span>Institution Panel</span>
    </a>
@endhasrole

        <!-- ========================================== -->
        <!-- AUTHOR SECTION (only for authors) -->
        <!-- ========================================== -->
        @role('author')
        <div class="px-3 mt-4 mb-2">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Author Studio</p>
        </div>
        <a href="{{ route('author.dashboard') }}" class="nav-item {{ request()->routeIs('author.dashboard') ? 'active' : '' }}">
            <i class="ti ti-dashboard text-purple-400 text-xl"></i>
            <span>Author Dashboard</span>
        </a>
        <a href="{{ route('author.books.index') }}" class="nav-item {{ request()->routeIs('author.books.*') ? 'active' : '' }}">
            <i class="ti ti-books text-blue-400 text-xl"></i>
            <span>My Books</span>
        </a>
        <a href="{{ route('author.royalties.index') }}" class="nav-item {{ request()->routeIs('author.royalties.*') ? 'active' : '' }}">
            <i class="ti ti-wallet text-green-400 text-xl"></i>
            <span>Royalties</span>
        </a>
        @endrole

        <!-- ========================================== -->
        <!-- LIBRARIAN SECTION (only for librarians) -->
        <!-- ========================================== -->
        @role('librarian')
        <div class="px-3 mt-4 mb-2">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Library Management</p>
        </div>
        <a href="{{ route('librarian.dashboard') }}" class="nav-item {{ request()->routeIs('librarian.dashboard') ? 'active' : '' }}">
            <i class="ti ti-dashboard text-blue-400 text-xl"></i>
            <span>Librarian Dashboard</span>
        </a>
        <a href="{{ route('admin.books.index') }}" class="nav-item {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
            <i class="ti ti-books text-cyan-400 text-xl"></i>
            <span>Manage Books</span>
        </a>
        @endrole

        <!-- ========================================== -->
        <!-- INSTRUCTOR SECTION (only for instructors) -->
        <!-- ========================================== -->
        @role('instructor')
        <div class="px-3 mt-4 mb-2">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Teaching</p>
        </div>
        <a href="{{ route('instructor.dashboard') }}" class="nav-item {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}">
            <i class="ti ti-dashboard text-green-400 text-xl"></i>
            <span>Instructor Dashboard</span>
        </a>
        <a href="{{ route('instructor.courses.index') }}" class="nav-item {{ request()->routeIs('instructor.courses.*') ? 'active' : '' }}">
            <i class="ti ti-video text-cyan-400 text-xl"></i>
            <span>My Courses</span>
        </a>
        <a href="{{ route('quizzes.index') }}" class="nav-item {{ request()->routeIs('quizzes.*') ? 'active' : '' }}">
            <i class="ti ti-brain text-purple-400 text-xl"></i>
            <span>My Quizzes</span>
        </a>
        @endrole

        <!-- ========================================== -->
        <!-- ADMIN & SUPER ADMIN SECTION -->
        <!-- ========================================== -->
        @hasanyrole('admin|super_admin')
        <div class="px-3 mt-4 mb-2">
            <p class="text-xs text-gray-500 uppercase tracking-wider">
                @role('super_admin')👑 Super Admin Panel @else Administration @endrole
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="ti ti-dashboard text-red-400 text-xl"></i>
            <span>Dashboard</span>
            @role('super_admin')
                <span class="ml-auto text-[10px] bg-red-500 text-white px-1.5 py-0.5 rounded-full">Super</span>
            @endrole
        </a>

        <a href="{{ route('admin.books.index') }}" class="nav-item {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
            <i class="ti ti-books text-blue-400 text-xl"></i>
            <span>Manage Books</span>
        </a>

        <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="ti ti-users text-cyan-400 text-xl"></i>
            <span>Manage Users</span>
        </a>

        <a href="{{ route('admin.institutions.index') }}" class="nav-item {{ request()->routeIs('admin.institutions.*') ? 'active' : '' }}">
            <i class="ti ti-building text-indigo-400 text-xl"></i>
            <span>Institutions</span>
        </a>

        <a href="{{ route('admin.marketplace.pending') }}" class="nav-item {{ request()->routeIs('admin.marketplace.*') ? 'active' : '' }}">
            <i class="ti ti-shopping-cart text-amber-400 text-xl"></i>
            <span>Pending Approvals</span>
        </a>

        <a href="{{ route('admin.payments.index') }}" class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <i class="ti ti-wallet text-green-400 text-xl"></i>
            <span>Payments</span>
        </a>

        <a href="{{ route('admin.analytics') }}" class="nav-item {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
            <i class="ti ti-chart-bar text-yellow-400 text-xl"></i>
            <span>Analytics</span>
        </a>
        @endhasanyrole

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