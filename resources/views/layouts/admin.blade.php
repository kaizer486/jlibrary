<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - JLIBRARY</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        body {
            background: #f3f4f6;
            min-height: 100vh;
        }
        
        /* Sidebar - Fixed position on desktop */
        #admin-sidebar {
            width: 280px;
            flex-shrink: 0;
            height: 100vh;
            overflow-y: auto;
            background: #111827;
            color: white;
            transition: transform 0.3s ease;
            z-index: 100;
            position: sticky;
            top: 0;
        }
        
        @media (max-width: 1024px) {
            #admin-sidebar {
                position: fixed;
                height: 100vh;
                top: 0;
                left: 0;
                transform: translateX(-100%);
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
        
        /* Main content wrapper */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            height: 100vh;
            overflow: hidden;
        }
        
        /* Scrollable content area */
        .scrollable-content {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }
        
        @media (min-width: 640px) {
            .scrollable-content {
                padding: 1.25rem;
            }
        }
        
        @media (min-width: 768px) {
            .scrollable-content {
                padding: 1.5rem;
            }
        }
        
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
        .nav-item:hover i { color: white !important; }
        .nav-item.active {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }
        .nav-item.active i { color: white !important; }
        
        /* Submenu items */
        .nav-sub-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0.75rem 0.5rem 2.5rem;
            margin: 0.1rem 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            color: #9ca3af;
            font-size: 0.875rem;
            text-decoration: none;
        }
        .nav-sub-item:hover {
            background-color: #374151;
            color: white;
        }
        .nav-sub-item.active {
            background: rgba(99, 102, 241, 0.2);
            color: #818cf8;
        }
        .nav-sub-item.active i { color: #818cf8 !important; }
        
        .nav-section-title {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #6b7280;
            padding: 0.75rem 0.75rem 0.25rem;
            font-weight: 600;
        }
        
        /* Mobile improvements */
        @media (max-width: 640px) {
            .top-bar-mobile {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .admin-info-mobile {
                display: none !important;
            }
        }

        /* Badge styles */
        .badge-orange {
            background: #ea580c;
            color: white;
            font-size: 0.65rem;
            padding: 0.1rem 0.5rem;
            border-radius: 9999px;
            margin-left: auto;
        }
        
        .badge-yellow {
            background: #ca8a04;
            color: white;
            font-size: 0.65rem;
            padding: 0.1rem 0.5rem;
            border-radius: 9999px;
            margin-left: auto;
        }
        
        .badge-red {
            background: #dc2626;
            color: white;
            font-size: 0.65rem;
            padding: 0.1rem 0.5rem;
            border-radius: 9999px;
            margin-left: auto;
        }
        
        .badge-green {
            background: #16a34a;
            color: white;
            font-size: 0.65rem;
            padding: 0.1rem 0.5rem;
            border-radius: 9999px;
            margin-left: auto;
        }
        
        .badge-purple {
            background: #7c3aed;
            color: white;
            font-size: 0.65rem;
            padding: 0.1rem 0.5rem;
            border-radius: 9999px;
            margin-left: auto;
        }
        
        /* Role badge in top bar */
        .role-badge {
            font-size: 0.6rem;
            padding: 0.15rem 0.6rem;
            border-radius: 9999px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .role-badge.super-admin {
            background: #dc2626;
            color: white;
        }
        .role-badge.admin {
            background: #7c3aed;
            color: white;
        }
        .role-badge.institution-admin {
            background: #2563eb;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay" onclick="closeSidebar()"></div>
    
    <div class="flex h-screen overflow-hidden">
        <!-- ========================================== -->
        <!-- ADMIN SIDEBAR                              -->
        <!-- ========================================== -->
        <aside id="admin-sidebar">

        <!-- Brand -->
        <div class="p-4 border-b border-gray-800 sticky top-0 bg-gray-900 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
                        <i class="ti ti-crown text-red-400 text-2xl"></i>
                        <span class="text-xl font-bold text-red-400">SUPER ADMIN</span>
                    @elseif(auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
                        <i class="ti ti-shield text-indigo-400 text-2xl"></i>
                        <span class="text-xl font-bold text-indigo-400">ADMIN</span>
                    @elseif(auth()->user()->isInstitutionAdmin() || auth()->user()->hasRole('institution_admin'))
                        <i class="ti ti-building text-blue-400 text-2xl"></i>
                        <span class="text-xl font-bold text-blue-400">INSTITUTION</span>
                    @else
                        <i class="ti ti-user text-gray-400 text-2xl"></i>
                        <span class="text-xl font-bold text-gray-400">DASHBOARD</span>
                    @endif
                </div>
                <!-- Close button for mobile -->
                <button id="close-sidebar-mobile" class="lg:hidden text-gray-400 hover:text-white">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
            <p class="text-xs mt-1 
                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
                    text-red-400
                @elseif(auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
                    text-indigo-400
                @elseif(auth()->user()->isInstitutionAdmin() || auth()->user()->hasRole('institution_admin'))
                    text-blue-400
                @else
                    text-gray-400
                @endif
            ">
                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
                    Full Platform Control
                @elseif(auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
                    Platform Management
                @elseif(auth()->user()->isInstitutionAdmin() || auth()->user()->hasRole('institution_admin'))
                    Institution Management
                @else
                    User Dashboard
                @endif
            </p>
        </div>           
        <nav class="p-4 space-y-1">
            <!-- ========================================== -->
            <!-- DASHBOARD                                -->
            <!-- ========================================== -->
            @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin') || auth()->user()->isAdmin() || auth()->user()->hasRole('admin') || auth()->user()->isInstitutionAdmin() || auth()->user()->hasRole('institution_admin'))
                <div class="px-3 mt-4 mb-2">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Dashboard</p>
                </div>
                
                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
                    <a href="{{ route('super-admin.dashboard') }}" class="nav-item {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                        <i class="ti ti-crown text-red-400 text-xl"></i>
                        <span>Super Dashboard</span>
                    </a>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="ti ti-shield text-indigo-400 text-xl"></i>
                        <span>Admin Dashboard</span>
                    </a>
                @endif

                @if(auth()->user()->isInstitutionAdmin() || auth()->user()->hasRole('institution_admin'))
                    <a href="{{ route('institution.dashboard') }}" class="nav-item {{ request()->routeIs('institution.dashboard') ? 'active' : '' }}">
                        <i class="ti ti-building text-blue-400 text-xl"></i>
                        <span>Institution Dashboard</span>
                    </a>
                @endif
            @endif

            <!-- ========================================== -->
            <!-- PLATFORM MANAGEMENT - Admin & Super Admin -->
            <!-- ========================================== -->
            @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin') || auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
                <div class="nav-section-title mt-4">
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
                        🔒 Platform Management
                    @else
                        ⚙️ Platform Management
                    @endif
                </div>

                <!-- Books -->
                <a href="{{ route('admin.books.index') }}" class="nav-item {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
                    <i class="ti ti-books text-blue-400 text-xl"></i>
                    <span>Manage Books</span>
                </a>

                <!-- Users -->
                <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="ti ti-users text-cyan-400 text-xl"></i>
                    <span>Manage Users</span>
                </a>

                <!-- Institutions -->
                <a href="{{ route('admin.institutions.index') }}" 
                   class="nav-item {{ request()->routeIs('admin.institutions.*') ? 'active' : '' }}">
                    <i class="ti ti-building text-indigo-400 text-xl"></i>
                    <span>Institutions</span>
                </a>

              

                <!-- Applications -->
                <a href="{{ route('admin.applications.index') }}" class="nav-item {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}">
                    <i class="ti ti-files text-yellow-400 text-xl"></i>
                    <span>Applications</span>
                    @php
                        $pendingAppCount = \App\Models\Application::where('status', 'pending')->count();
                    @endphp
                    @if($pendingAppCount > 0)
                        <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingAppCount }}</span>
                    @endif
                </a>

                <!-- Quotes -->
                <a href="{{ route('admin.quotes.index') }}" class="nav-item {{ request()->routeIs('admin.quotes.*') ? 'active' : '' }}">
                    <i class="ti ti-quote text-purple-400 text-xl"></i>
                    <span>Manage Quotes</span>
                </a>

                <!-- ========================================== -->
                <!-- ANALYTICS - Admin & Super Admin          -->
                <!-- ========================================== -->
                <div class="nav-section-title mt-4">📊 Analytics</div>

                <a href="{{ route('admin.analytics') }}" class="nav-item {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                    <i class="ti ti-chart-bar text-yellow-400 text-xl"></i>
                    <span>Analytics</span>
                </a>
            @endif

            <!-- ========================================== -->
            <!-- INSTITUTION MANAGEMENT - Institution Admin -->
            <!-- ========================================== -->
            @if(auth()->user()->isInstitutionAdmin() || auth()->user()->hasRole('institution_admin'))
                <div class="nav-section-title mt-4">🏢 Institution Management</div>

                <!-- Members -->
                <a href="{{ route('institution.members.index') }}" class="nav-item {{ request()->routeIs('institution.members.*') ? 'active' : '' }}">
                    <i class="ti ti-users text-cyan-400 text-xl"></i>
                    <span>Members</span>
                </a>

                <!-- Books -->
                <a href="{{ route('institution.books.index') }}" class="nav-item {{ request()->routeIs('institution.books.*') ? 'active' : '' }}">
                    <i class="ti ti-books text-blue-400 text-xl"></i>
                    <span>Books</span>
                </a>

                <!-- Withdrawals -->
                <a href="{{ route('institution.withdrawals.index') }}" class="nav-item {{ request()->routeIs('institution.withdrawals.*') ? 'active' : '' }}">
                    <i class="ti ti-wallet text-green-400 text-xl"></i>
                    <span>Withdrawals</span>
                </a>

                <!-- Join Requests -->
                <a href="{{ route('institution.join-requests.index') }}" class="nav-item {{ request()->routeIs('institution.join-requests.*') ? 'active' : '' }}">
                    <i class="ti ti-user-check text-yellow-400 text-xl"></i>
                    <span>Join Requests</span>
                </a>

                <!-- Subscription -->
                <a href="{{ route('institution.subscription.index') }}" class="nav-item {{ request()->routeIs('institution.subscription.*') ? 'active' : '' }}">
                    <i class="ti ti-crown text-purple-400 text-xl"></i>
                    <span>Subscription</span>
                    @if(Auth::user()->institution && Auth::user()->institution->isSubscriptionActive())
                        <span class="ml-auto text-xs bg-green-500/20 text-green-400 px-2 py-0.5 rounded-full">
                            {{ Auth::user()->institution->getDaysLeft() }} days
                        </span>
                    @endif
                </a>

                <!-- Institution Quotes -->
                <a href="{{ route('institution.quotes.index') }}" class="nav-item {{ request()->routeIs('institution.quotes.*') ? 'active' : '' }}">
                    <i class="ti ti-quote text-pink-400 text-xl"></i>
                    <span>Institution Quotes</span>
                </a>
            @endif

            <!-- ========================================== -->
            <!-- NAVIGATION                              -->
            <!-- ========================================== -->
            <hr class="my-3 border-gray-800">

            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-800 transition">
                <i class="ti ti-arrow-left"></i> <span class="hidden sm:inline">Back to User Site</span>
            </a>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-red-400 hover:bg-red-900/20 transition">
                    <i class="ti ti-logout"></i> <span class="hidden sm:inline">Logout</span>
                </button>
            </form>
        </nav>           
        </aside>
        
        <!-- ========================================== -->
        <!-- MAIN CONTENT                              -->
        <!-- ========================================== -->
        <div class="main-wrapper">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm sticky top-0 z-20 border-b flex-shrink-0">
                <div class="px-4 sm:px-6 py-3 flex justify-between items-center top-bar-mobile">
                    <div class="flex items-center gap-3">
                        <!-- Mobile menu toggle -->
                        <button id="open-sidebar-mobile" class="lg:hidden text-gray-600 hover:text-purple-600">
                            <i class="ti ti-menu-2 text-2xl"></i>
                        </button>
                        <h1 class="text-lg sm:text-xl font-semibold text-gray-800 truncate">@yield('title', 'Admin Dashboard')</h1>
                    </div>
                    
                    <div class="flex items-center gap-2 sm:gap-3">
                        <!-- User Info -->
                        <div class="hidden sm:flex items-center gap-2 admin-info-mobile">
                            <div class="w-8 h-8 rounded-full 
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
                                    bg-gradient-to-r from-red-500 to-red-600
                                @elseif(auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
                                    bg-gradient-to-r from-indigo-500 to-purple-600
                                @elseif(auth()->user()->isInstitutionAdmin() || auth()->user()->hasRole('institution_admin'))
                                    bg-gradient-to-r from-blue-500 to-cyan-500
                                @else
                                    bg-gradient-to-r from-gray-500 to-gray-600
                                @endif
                                flex items-center justify-center flex-shrink-0">
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
                                    <i class="ti ti-crown text-white text-sm"></i>
                                @elseif(auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
                                    <i class="ti ti-shield text-white text-sm"></i>
                                @elseif(auth()->user()->isInstitutionAdmin() || auth()->user()->hasRole('institution_admin'))
                                    <i class="ti ti-building text-white text-sm"></i>
                                @else
                                    <i class="ti ti-user text-white text-sm"></i>
                                @endif
                            </div>
                            <div class="hidden md:block">
                                <p class="text-sm font-medium text-gray-700">{{ Auth::user()->full_name ?? 'Admin' }}</p>
                                @if(Auth::user()->isSuperAdmin() || Auth::user()->hasRole('super_admin'))
                                    <span class="role-badge super-admin">👑 Super Admin</span>
                                @elseif(Auth::user()->isAdmin() || Auth::user()->hasRole('admin'))
                                    <span class="role-badge admin">🛡️ Admin</span>
                                @elseif(Auth::user()->isInstitutionAdmin() || Auth::user()->hasRole('institution_admin'))
                                    <span class="role-badge institution-admin">🏢 Institution Admin</span>
                                @else
                                    <span class="role-badge admin">Admin</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Mobile avatar -->
                        <div class="sm:hidden w-8 h-8 rounded-full 
                            @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
                                bg-gradient-to-r from-red-500 to-red-600
                            @elseif(auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
                                bg-gradient-to-r from-indigo-500 to-purple-600
                            @elseif(auth()->user()->isInstitutionAdmin() || auth()->user()->hasRole('institution_admin'))
                                bg-gradient-to-r from-blue-500 to-cyan-500
                            @else
                                bg-gradient-to-r from-gray-500 to-gray-600
                            @endif
                            flex items-center justify-center flex-shrink-0">
                            @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
                                <i class="ti ti-crown text-white text-sm"></i>
                            @elseif(auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
                                <i class="ti ti-shield text-white text-sm"></i>
                            @elseif(auth()->user()->isInstitutionAdmin() || auth()->user()->hasRole('institution_admin'))
                                <i class="ti ti-building text-white text-sm"></i>
                            @else
                                <i class="ti ti-user text-white text-sm"></i>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Page Content - Scrollable -->
            <div class="scrollable-content">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
                        <i class="ti ti-check-circle text-green-500"></i>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
                        <i class="ti ti-alert-circle text-red-500"></i>
                        {{ session('error') }}
                    </div>
                @endif
                
                @if(session('warning'))
                    <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg flex items-center gap-2">
                        <i class="ti ti-alert-triangle text-yellow-500"></i>
                        {{ session('warning') }}
                    </div>
                @endif
                
                @if(session('info'))
                    <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg flex items-center gap-2">
                        <i class="ti ti-info-circle text-blue-500"></i>
                        {{ session('info') }}
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
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

            // Make closeSidebar available globally for onclick
            window.closeSidebar = closeSidebar;

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

            // Close on window resize to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    closeSidebar();
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>