<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - JLIBRARY</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        body {
            background: #f3f4f6;
            min-height: 100vh;
        }
        
        /* Sidebar - Mobile responsive */
        #admin-sidebar {
            transition: transform 0.3s ease;
            transform: translateX(0);
            z-index: 100;
            width: 280px;
        }
        
        @media (max-width: 1024px) {
            #admin-sidebar {
                transform: translateX(-100%);
                position: fixed;
                height: 100vh;
                top: 0;
                left: 0;
                overflow-y: auto;
            }
            
            #admin-sidebar.open {
                transform: translateX(0);
            }
        }
        
        /* Sidebar overlay */
        #sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99;
        }
        
        #sidebar-overlay.active {
            display: block;
        }
        
        .sidebar-item:hover { 
            background-color: #7c3aed; 
            color: white; 
        }
        .sidebar-item.active { 
            background-color: #7c3aed; 
            color: white; 
        }
        
        /* Search results */
        .search-results {
            position: absolute;
            top: 100%;
            right: 0;
            width: 320px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            z-index: 50;
            max-height: 400px;
            overflow-y: auto;
            margin-top: 8px;
        }
        
        @media (max-width: 640px) {
            .search-results {
                width: calc(100vw - 2rem);
                right: 1rem;
                left: 1rem;
            }
        }
        
        .search-results a:hover { 
            background-color: #f3f4f6; 
        }
        
        /* Mobile top bar improvements */
        @media (max-width: 640px) {
            .top-bar-mobile {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .admin-info-mobile {
                display: none !important;
            }
            
            .search-bar-mobile {
                display: none !important;
            }
            
            #admin-profile-dropdown {
                width: calc(100vw - 2rem);
                right: 1rem;
                left: 1rem;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay" onclick="closeSidebar()"></div>
    
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="admin-sidebar" class="bg-gray-900 text-white flex-shrink-0 overflow-y-auto">
            <div class="p-4 border-b border-gray-800 sticky top-0 bg-gray-900 z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="ti ti-book text-2xl"></i>
                        <span class="text-xl font-bold">JLIBRARY</span>
                    </div>
                    <!-- Close button for mobile -->
                    <button id="close-sidebar-mobile" class="lg:hidden text-gray-400 hover:text-white">
                        <i class="ti ti-x text-2xl"></i>
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1">Admin Panel</p>
                @if(auth()->user()?->hasRole('super_admin'))
                    <div class="mt-2 inline-block bg-red-600/20 text-red-400 text-xs px-2 py-0.5 rounded-full">
                        👑 Super Admin Access
                    </div>
                @endif
            </div>
            
            <nav class="p-4 space-y-1">
                <!-- ========================================== -->
                <!-- SUPER ADMIN MENU -->
                <!-- ========================================== -->
                @hasrole('super_admin')
                    <div class="px-3 mt-4 mb-2">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Super Admin</p>
                    </div>
                    <a href="{{ route('super-admin.dashboard') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('super-admin.dashboard') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-crown"></i> Super Dashboard
                    </a>
                    <a href="{{ route('admin.books.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.books.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-books"></i> Books
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-users"></i> Users
                    </a>
                    <a href="{{ route('admin.institutions.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.institutions.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-building"></i> Institutions
                    </a>
                    <a href="{{ route('admin.marketplace.pending') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.marketplace.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-shopping-cart"></i> Marketplace
                    </a>
                    <a href="{{ route('admin.analytics') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.analytics') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-chart-bar"></i> Analytics
                    </a>
                    <a href="{{ route('admin.payments.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition">
                        <i class="ti ti-wallet"></i> Payments
                    </a>
                    <a href="{{ route('admin.applications.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.applications.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-files"></i> Applications
                        @php
                            $pendingCount = App\Models\Application::where('status', 'pending')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.quotes.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition">
                        <i class="ti ti-quote"></i> Manage Quotes
                    </a>
                @endhasrole

                <!-- ========================================== -->
                <!-- ADMIN MENU -->
                <!-- ========================================== -->
                @hasrole('admin')
                    <div class="px-3 mt-4 mb-2">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Administration</p>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-dashboard"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.books.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.books.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-books"></i> Books
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-users"></i> Users
                    </a>
                    <a href="{{ route('admin.institutions.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.institutions.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-building"></i> Institutions
                    </a>
                    <a href="{{ route('admin.marketplace.pending') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.marketplace.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-shopping-cart"></i> Marketplace
                    </a>
                    <a href="{{ route('admin.analytics') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.analytics') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-chart-bar"></i> Analytics
                    </a>
                    <a href="{{ route('admin.payments.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition">
                        <i class="ti ti-wallet"></i> Payments
                    </a>
                    <a href="{{ route('admin.applications.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.applications.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-files"></i> Applications
                        @php
                            $pendingCount = App\Models\Application::where('status', 'pending')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.quotes.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition">
                        <i class="ti ti-quote"></i> Manage Quotes
                    </a>
                @endhasrole

                <!-- ========================================== -->
                <!-- INSTITUTION ADMIN MENU -->
                <!-- ========================================== -->
                @hasanyrole('institution_admin|school_admin|college_admin|university_admin|library_admin|bookstore_admin|publisher_admin|researcher')
                    <div class="px-3 mt-4 mb-2">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">
                            @php
                                $user = auth()->user();
                            @endphp
                            
                            @if($user->hasRole('school_admin'))
                                🏫 School Admin
                            @elseif($user->hasRole('college_admin'))
                                🎓 College Admin
                            @elseif($user->hasRole('university_admin'))
                                🏛️ University Admin
                            @elseif($user->hasRole('library_admin'))
                                📚 Library Admin
                            @elseif($user->hasRole('bookstore_admin'))
                                📖 Bookstore Admin
                            @elseif($user->hasRole('publisher_admin'))
                                📰 Publisher Admin
                            @elseif($user->hasRole('researcher'))
                                🔬 Researcher
                            @else
                                🏢 Institution Admin
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('institution.dashboard') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('institution.dashboard') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-dashboard"></i> Dashboard
                    </a>
                    <a href="{{ route('institution.members.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('institution.members.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-users"></i> Members
                    </a>
                    <a href="{{ route('institution.books.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('institution.books.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-books"></i> Books
                    </a>
                    <a href="{{ route('institution.withdrawals.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('institution.withdrawals.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-wallet"></i> Withdrawals
                    </a>
                    <a href="{{ route('institution.quotes.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('institution.quotes.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-quote"></i> Institution Quotes
                    </a>
                    <a href="{{ route('institution.join-requests.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('institution.join-requests.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-user-check"></i> Join Requests
                    </a>
                    <a href="{{ route('institution.subscription.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('institution.subscription.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                        <i class="ti ti-crown"></i> Subscription
                        @if(Auth::user()->institution && Auth::user()->institution->isSubscriptionActive())
                            <span class="ml-auto text-xs bg-green-500/20 text-green-400 px-2 py-0.5 rounded-full">
                                {{ Auth::user()->institution->getDaysLeft() }} days
                            </span>
                        @endif
                    </a>
                @endhasanyrole

                <hr class="my-3 border-gray-800">
                
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-800 transition">
                    <i class="ti ti-arrow-left"></i> <span class="hidden sm:inline">Back to Dashboard</span>
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-red-400 hover:bg-red-900/20 transition">
                        <i class="ti ti-logout"></i> <span class="hidden sm:inline">Logout</span>
                    </button>
                </form>
            </nav>    
        </aside>
        
        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto bg-gray-50">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm sticky top-0 z-20 border-b">
                <div class="flex items-center justify-between px-4 sm:px-6 py-3 top-bar-mobile">
                    <div class="flex items-center gap-3">
                        <!-- Mobile menu toggle -->
                        <button id="open-sidebar-mobile" class="lg:hidden text-gray-600 hover:text-purple-600">
                            <i class="ti ti-menu-2 text-2xl"></i>
                        </button>
                        <h1 class="text-gray-800 text-lg sm:text-xl font-semibold truncate">@yield('title', 'Admin Dashboard')</h1>
                    </div>
                    
                    <div class="flex items-center gap-2 sm:gap-4">
                        <!-- Search Bar - Hidden on mobile -->
                        <div class="relative hidden sm:block">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="ti ti-search text-gray-400 text-sm"></i>
                                </div>
                                <input type="text" id="admin-search" 
                                       placeholder="Search users, books..." 
                                       class="w-48 lg:w-80 pl-9 pr-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg border border-gray-200 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 placeholder-gray-400">
                                
                                <div id="search-results" class="search-results hidden">
                                    <div class="p-2">
                                        <div class="text-xs text-gray-500 px-3 py-2">Loading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Admin Info - Hidden on mobile -->
                        <div class="hidden sm:flex items-center gap-2 admin-info-mobile">
                            <div class="w-8 h-8 rounded-full bg-purple-600 flex items-center justify-center flex-shrink-0">
                                <i class="ti ti-user text-white text-sm"></i>
                            </div>
                            <div class="hidden md:block">
                                <p class="text-sm font-medium text-gray-700">{{ Auth::user()->full_name }}</p>
                                <p class="text-xs">
                                    @php
                                        $user = auth()->user();
                                    @endphp
                                    
                                    @if($user->hasRole('super_admin'))
                                        <span class="text-red-500 font-semibold">👑 Super Admin</span>
                                    @elseif($user->hasRole('admin'))
                                        <span class="text-purple-500 font-semibold">🛡️ Admin</span>
                                    @elseif($user->hasRole('institution_admin'))
                                        <span class="text-blue-500 font-semibold">🏢 Institution Admin</span>
                                    @else
                                        <span class="text-gray-400">Admin</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <!-- Mobile avatar (smaller) -->
                        <div class="sm:hidden w-8 h-8 rounded-full bg-purple-600 flex items-center justify-center flex-shrink-0">
                            <i class="ti ti-user text-white text-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Page Content -->
            <div class="p-3 sm:p-4 md:p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- ========== JAVASCRIPT ========== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('admin-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const openBtn = document.getElementById('open-sidebar-mobile');
            const closeBtn = document.getElementById('close-sidebar-mobile');

            function openSidebar() {
                if (sidebar) {
                    sidebar.classList.add('open');
                }
                if (overlay) {
                    overlay.classList.add('active');
                }
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                if (sidebar) {
                    sidebar.classList.remove('open');
                }
                if (overlay) {
                    overlay.classList.remove('active');
                }
                document.body.style.overflow = '';
            }

            if (openBtn) {
                openBtn.addEventListener('click', openSidebar);
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', closeSidebar);
            }

            // Close on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeSidebar();
                }
            });

            // ==========================================
            // SEARCH FUNCTIONALITY
            // ==========================================
            const searchInput = document.getElementById('admin-search');
            const resultsDiv = document.getElementById('search-results');
            let searchTimeout;

            if (searchInput && resultsDiv) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();
                    
                    if (query.length < 2) {
                        resultsDiv.classList.add('hidden');
                        return;
                    }
                    
                    searchTimeout = setTimeout(() => {
                        fetch(`/admin/search?q=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.users.length === 0 && data.books.length === 0) {
                                    resultsDiv.innerHTML = `<div class="p-4 text-center text-gray-500 text-sm">No results found for "${query}"</div>`;
                                    resultsDiv.classList.remove('hidden');
                                    return;
                                }
                                
                                let html = '';
                                
                                if (data.users.length > 0) {
                                    html += '<div class="px-3 py-2 text-xs font-semibold text-gray-500 border-b">USERS</div>';
                                    data.users.forEach(user => {
                                        html += `<a href="/admin/users/${user.id}/edit" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 transition border-b last:border-0">
                                            <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center"><i class="ti ti-user text-purple-600 text-sm"></i></div>
                                            <div><p class="text-sm font-medium text-gray-800">${user.full_name}</p><p class="text-xs text-gray-500">${user.email}</p></div>
                                        </a>`;
                                    });
                                }
                                
                                if (data.books.length > 0) {
                                    html += '<div class="px-3 py-2 text-xs font-semibold text-gray-500 border-b">BOOKS</div>';
                                    data.books.forEach(book => {
                                        html += `<a href="/admin/books/${book.id}/edit" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 transition border-b last:border-0">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center"><i class="ti ti-book text-blue-600 text-sm"></i></div>
                                            <div><p class="text-sm font-medium text-gray-800">${book.title}</p><p class="text-xs text-gray-500">by ${book.author}</p></div>
                                        </a>`;
                                    });
                                }
                                
                                resultsDiv.innerHTML = html;
                                resultsDiv.classList.remove('hidden');
                            });
                    }, 300);
                });
                
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
                        resultsDiv.classList.add('hidden');
                    }
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>