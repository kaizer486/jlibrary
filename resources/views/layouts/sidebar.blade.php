<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 z-[1000] h-screen w-[280px] bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 text-white transition-all duration-300 shadow-2xl overflow-y-auto border-r border-white/10">
    <!-- Logo -->
    <div class="flex items-center justify-between p-4 border-b border-white/10">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
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
                    <img src="{{ url('media/' . Auth::user()->avatar) }}" class="w-full h-full object-cover" alt="Avatar">
                @else
                    <i class="ti ti-user text-white text-lg"></i>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm text-white truncate">{{ Auth::user()->full_name }}</p>
                <p class="text-xs text-gray-400">
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
                        👑 Super Administrator
                    @elseif(auth()->user()->isMediaTeam() || auth()->user()->hasRole('media_team'))
                        🎨 Media Team
                    @elseif(auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
                        🛡️ Administrator
                    @elseif(auth()->user()->isAnyInstitutionAdmin() || auth()->user()->hasRole('institution_admin'))
                        🏢 Institution Admin
                    @elseif(auth()->user()->role === 'school_admin' || auth()->user()->hasRole('school_admin'))
                        🏫 School Admin
                    @elseif(auth()->user()->role === 'college_admin' || auth()->user()->hasRole('college_admin'))
                        🎓 College Admin
                    @elseif(auth()->user()->role === 'university_admin' || auth()->user()->hasRole('university_admin'))
                        🏛️ University Admin
                    @elseif(auth()->user()->role === 'library_admin' || auth()->user()->hasRole('library_admin'))
                        📚 Library Admin
                    @elseif(auth()->user()->role === 'bookstore_admin' || auth()->user()->hasRole('bookstore_admin'))
                        📖 Bookstore Admin
                    @elseif(auth()->user()->role === 'publisher_admin' || auth()->user()->hasRole('publisher_admin'))
                        📰 Publisher Admin
                    @elseif(auth()->user()->role === 'researcher' || auth()->user()->hasRole('researcher'))
                        🔬 Researcher
                    @elseif(auth()->user()->hasAnyRole(['author', 'seller']))
                        ✍️ Author & Seller
                    @elseif(auth()->user()->role === 'librarian' || auth()->user()->hasRole('librarian'))
                        📚 Librarian
                    @elseif(auth()->user()->role === 'instructor' || auth()->user()->hasRole('instructor'))
                        👨‍🏫 Instructor
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
        
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ti ti-dashboard text-indigo-400 text-xl"></i>
            <span>Dashboard</span>
        </a>

        <!-- REGULAR USER MENUS -->
        <a href="{{ route('library.index') }}" class="nav-item {{ (request()->routeIs('library.*') && !request()->routeIs('library.my-library')) ? 'active' : '' }}">
            <i class="ti ti-books text-blue-400 text-xl"></i>
            <span>Library</span>
        </a>

        <a href="{{ route('ai.chat') }}" class="nav-item {{ request()->routeIs('ai.*') ? 'active' : '' }}">
            <i class="ti ti-robot text-purple-400 text-xl"></i>
            <span>AI Assistant</span>
        </a>

        <!-- ========================================== -->
        <!-- INSTITUTION SECTION - PERSISTENT STATE     -->
        <!-- ========================================== -->
        <div x-data="{ 
                open: localStorage.getItem('institutionOpen') === 'true',
                toggle() {
                    this.open = !this.open;
                    localStorage.setItem('institutionOpen', this.open);
                }
            }" 
            x-init="localStorage.setItem('institutionOpen', open)"
            class="px-6 mt-4 mb-2">
            
            <button @click="toggle()" class="w-full flex items-center justify-between text-xs text-gray-300 uppercase tracking-wider hover:text-white transition group">
                <span class="flex items-center gap-2">
                    <i class="ti ti-building-community text-indigo-400 text-sm group-hover:text-indigo-300 transition"></i>
                    <span class="font-medium">Institution</span>
                </span>
                <i class="ti ti-chevron-down text-gray-400 text-xs transition-transform duration-200 group-hover:text-white" :class="{'rotate-180': open}"></i>
            </button>
            
            <div x-show="open" x-collapse.duration.300ms class="mt-2 space-y-1">
                <a href="{{ route('my.institution') }}" 
                   class="nav-item {{ request()->routeIs('my.institution') ? 'active' : '' }} hover:bg-white/5">
                    <i class="ti ti-building text-indigo-400 text-xl"></i>
                    <span class="text-gray-300">My Institution</span>
                </a>
                
                <a href="{{ route('discover.institutions') }}" 
                   class="nav-item {{ request()->routeIs('discover.institutions') ? 'active' : '' }} hover:bg-white/5">
                    <i class="ti ti-building-community text-cyan-400 text-xl"></i>
                    <span class="text-gray-300">Discover</span>
                </a>
                
                <a href="{{ route('institution.create-request') }}" 
                   class="nav-item {{ request()->routeIs('institution.create-request') ? 'active' : '' }} hover:bg-white/5">
                    <i class="ti ti-file-plus text-emerald-400 text-xl"></i>
                    <span class="text-gray-300">Register Institution</span>
                </a>
            </div>
        </div>

        <a href="{{ route('community.index') }}" class="nav-item {{ request()->routeIs('community.*') ? 'active' : '' }}">
            <i class="ti ti-users text-green-400 text-xl"></i>
            <span>Community</span>
        </a>

        <a href="{{ route('converter.index') }}" class="nav-item {{ request()->routeIs('converter.*') ? 'active' : '' }}">
            <i class="ti ti-arrows-exchange text-orange-400 text-xl"></i>
            <span>File Converter</span>
        </a>

        <a href="{{ route('certificates.index') }}" class="nav-item {{ request()->routeIs('certificates.*') ? 'active' : '' }}">
            <i class="ti ti-certificate text-yellow-400 text-xl"></i>
            <span>Certificates</span>
        </a>

        <a href="{{ route('documents.index') }}" class="nav-item {{ request()->routeIs('documents.*') ? 'active' : '' }}">
            <i class="ti ti-file-text text-teal-400 text-xl"></i>
            <span>My Documents</span>
        </a>

        <a href="{{ route('wallet.index') }}" class="nav-item {{ request()->routeIs('wallet.*') ? 'active' : '' }}">
            <i class="ti ti-wallet text-emerald-400 text-xl"></i>
            <span>Wallet</span>
        </a>

        <a href="{{ route('referrals.index') }}" class="nav-item {{ request()->routeIs('referrals.*') ? 'active' : '' }}">
            <i class="ti ti-gift text-pink-400 text-xl"></i>
            <span>Refer & Earn</span>
            @php
                $pendingReferrals = Auth::check() ? App\Models\Referral::where('referrer_id', Auth::id())->where('status', 'pending')->count() : 0;
            @endphp
            @if($pendingReferrals > 0)
                <span class="ml-auto bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingReferrals }}</span>
            @endif
        </a>

        <!-- AUTHOR & SELLER ACCESS -->
@if(auth()->user()->hasAnyRole(['author', 'seller']))
    <div class="px-3 mt-4 mb-2">
        <p class="text-xs text-gray-500 uppercase tracking-wider">Author & Seller Studio</p>
    </div>
    
    <a href="{{ route('author.dashboard') }}" 
       class="nav-item {{ request()->routeIs('author.dashboard') ? 'active' : '' }}">
        <i class="ti ti-dashboard text-purple-400 text-xl"></i>
        <span>Studio Dashboard</span>
    </a>
   
@endif

        <!-- ========================================== -->
        <!-- ADMIN/SUPER ADMIN/MEDIA TEAM ACCESS        -->
        <!-- ========================================== -->
        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isMediaTeam() || auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('media_team'))
            <div class="px-3 mt-4 mb-2">
                <p class="text-xs text-gray-500 uppercase tracking-wider">Admin Access</p>
            </div>
            
            @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
                <a href="{{ route('super-admin.dashboard') }}" class="nav-item {{ request()->routeIs('super-admin.*') ? 'active' : '' }}">
                    <i class="ti ti-crown text-yellow-400 text-xl"></i>
                    <span>Super Dashboard</span>
                </a>
            @endif

            @if(auth()->user()->isMediaTeam() || auth()->user()->hasRole('media_team'))
                <a href="{{ route('super-admin.media.dashboard') }}" class="nav-item {{ request()->routeIs('super-admin.media.dashboard') ? 'active' : '' }}">
                    <i class="ti ti-palette text-yellow-400 text-xl"></i>
                    <span>Media Dashboard</span>
                </a>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                    <i class="ti ti-shield text-purple-400 text-xl"></i>
                    <span>Admin Panel</span>
                    <span class="ml-auto text-[10px] bg-purple-500/20 text-purple-300 px-2 py-0.5 rounded-full">Manage</span>
                </a>
            @endif
        @endif

        <!-- INSTITUTION ADMIN ACCESS -->
        @if(auth()->user()->isAnyInstitutionAdmin() || auth()->user()->hasRole('institution_admin'))
            <div class="px-3 mt-4 mb-2">
                <p class="text-xs text-gray-500 uppercase tracking-wider">Institution Access</p>
            </div>
            <a href="{{ route('institution.dashboard') }}" class="nav-item {{ request()->routeIs('institution.dashboard') ? 'active' : '' }}">
                <i class="ti ti-building text-blue-400 text-xl"></i>
                <span>Institution Panel</span>
                <span class="ml-auto text-[10px] bg-blue-500/20 text-blue-300 px-2 py-0.5 rounded-full">Manage</span>
            </a>
        @endif

        <!-- LIBRARIAN ACCESS -->
        @if(auth()->user()->role === 'librarian' || auth()->user()->hasRole('librarian'))
            <div class="px-3 mt-4 mb-2">
                <p class="text-xs text-gray-500 uppercase tracking-wider">Library Management</p>
            </div>
            <a href="{{ route('librarian.dashboard') }}" class="nav-item {{ request()->routeIs('librarian.dashboard') ? 'active' : '' }}">
                <i class="ti ti-library text-blue-400 text-xl"></i>
                <span>Librarian Dashboard</span>
            </a>
        @endif

        <!-- INSTRUCTOR ACCESS -->
        @if(auth()->user()->role === 'instructor' || auth()->user()->hasRole('instructor'))
            <div class="px-3 mt-4 mb-2">
                <p class="text-xs text-gray-500 uppercase tracking-wider">Teaching</p>
            </div>
            <a href="{{ route('instructor.dashboard') }}" class="nav-item {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}">
                <i class="ti ti-school text-green-400 text-xl"></i>
                <span>Instructor Dashboard</span>
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
<button id="open-sidebar" class="lg:hidden fixed bottom-6 right-6 z-[999] bg-purple-600 text-white p-4 rounded-full shadow-lg hover:bg-purple-700 transition">
    <i class="ti ti-menu-2 text-2xl"></i>
</button>

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
        text-decoration: none;
    }
    .nav-item:hover {
        background-color: #374151;
        color: white;
    }
    .nav-item:hover i {
        color: white !important;
    }
    .nav-item.active {
        background: linear-gradient(135deg, #ed993a, #db4227);
        color: white;
    }
    .nav-item.active i {
        color: white !important;
    }
    .nav-item i {
        width: 1.25rem;
        flex-shrink: 0;
    }
    
    @media (max-width: 1024px) {
        #sidebar {
            transform: translateX(-100%);
        }
        #sidebar.open {
            transform: translateX(0);
        }
    }
</style>