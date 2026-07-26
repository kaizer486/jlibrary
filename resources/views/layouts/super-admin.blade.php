<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') - JLIBRARY</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        body {
            background: #f3f4f6;
            min-height: 100vh;
        }
        
        /* Sidebar - Mobile responsive */
        #super-sidebar {
            transition: transform 0.3s ease;
            transform: translateX(0);
            z-index: 100;
            width: 280px;
        }
        
        @media (max-width: 1024px) {
            #super-sidebar {
                transform: translateX(-100%);
                position: fixed;
                height: 100vh;
                top: 0;
                left: 0;
                overflow-y: auto;
            }
            
            #super-sidebar.open {
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
            background: linear-gradient(135deg, #eab308, #ef4444);
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
            background: rgba(234, 179, 8, 0.2);
            color: #eab308;
        }
        .nav-sub-item.active i { color: #eab308 !important; }
        
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
            
            .super-admin-info-mobile {
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
        .role-badge.media-team {
            background: #7c3aed;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay" onclick="closeSidebar()"></div>
    
    <div class="flex h-screen overflow-hidden">
        <!-- ========================================== -->
        <!-- SUPER ADMIN SIDEBAR                        -->
        <!-- ========================================== -->
        <aside id="super-sidebar" class="bg-gray-900 text-white flex-shrink-0 overflow-y-auto">

        <!-- Brand -->
<div class="p-4 border-b border-gray-800 sticky top-0 bg-gray-900 z-10">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
                <i class="ti ti-crown text-yellow-400 text-2xl"></i>
                <span class="text-xl font-bold text-yellow-400">SUPER ADMIN</span>
            @elseif(auth()->user()->isMediaTeam() || auth()->user()->hasRole('media_team'))
                <i class="ti ti-palette text-yellow-400 text-2xl"></i>
                <span class="text-xl font-bold text-yellow-400">MEDIA TEAM</span>
            @elseif(auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
                <i class="ti ti-shield text-blue-400 text-2xl"></i>
                <span class="text-xl font-bold text-blue-400">ADMIN</span>
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
            text-yellow-500
        @elseif(auth()->user()->isMediaTeam() || auth()->user()->hasRole('media_team'))
            text-yellow-500
        @elseif(auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
            text-blue-400
        @else
            text-gray-400
        @endif
    ">
        @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
            Full Platform Control
        @elseif(auth()->user()->isMediaTeam() || auth()->user()->hasRole('media_team'))
            Content Management
        @elseif(auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
            Platform Management
        @else
            User Dashboard
        @endif
    </p>
</div>           
<nav class="p-4 space-y-1">
   <!-- ========================================== -->
<!-- DASHBOARD                                -->
<!-- ========================================== -->
@if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin') || auth()->user()->isMediaTeam() || auth()->user()->hasRole('media_team') || auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
    <div class="px-3 mt-4 mb-2">
        <p class="text-xs text-gray-500 uppercase tracking-wider">Dashboard</p>
    </div>
    
    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
        <a href="{{ route('super-admin.dashboard') }}" class="nav-item {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
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
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="ti ti-shield text-blue-400 text-xl"></i>
            <span>Admin Dashboard</span>
        </a>
    @endif
@endif
    <!-- ========================================== -->
    <!-- CONTENT MANAGEMENT - 🎨 Media Team Access -->
    <!-- ========================================== -->
    @if(auth()->user()->isMediaTeam() || auth()->user()->hasRole('media_team') || auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
        <div class="nav-section-title">🎨 Content Management</div>

        <!-- Hero Slides -->
        <a href="{{ route('super-admin.hero-slides.index') }}" class="nav-item {{ request()->routeIs('super-admin.hero-slides.*') ? 'active' : '' }}">
            <i class="ti ti-slideshow text-purple-400 text-xl"></i>
            <span>Hero Slides</span>
            @php
                $slideCount = \App\Models\HeroSlide::count();
            @endphp
            @if($slideCount > 0)
                <span class="ml-auto text-xs text-gray-400">{{ $slideCount }}</span>
            @endif
        </a>

        <!-- News Items -->
        <a href="{{ route('super-admin.news-items.index') }}" class="nav-item {{ request()->routeIs('super-admin.news-items.*') ? 'active' : '' }}">
            <i class="ti ti-news text-blue-400 text-xl"></i>
            <span>News & Updates</span>
            @php
                $newsCount = \App\Models\NewsItem::count();
            @endphp
            @if($newsCount > 0)
                <span class="ml-auto text-xs text-gray-400">{{ $newsCount }}</span>
            @endif
        </a>

        <!-- Founders -->
        <a href="{{ route('super-admin.founders.index') }}" class="nav-item {{ request()->routeIs('super-admin.founders.*') ? 'active' : '' }}">
            <i class="ti ti-users text-pink-400 text-xl"></i>
            <span>Founders</span>
            @php
                $founderCount = \App\Models\Founder::count();
            @endphp
            @if($founderCount > 0)
                <span class="ml-auto text-xs text-gray-400">{{ $founderCount }}</span>
            @endif
        </a>

        <!-- Site Settings -->
        <a href="{{ route('super-admin.site-settings.index') }}" class="nav-item {{ request()->routeIs('super-admin.site-settings.*') ? 'active' : '' }}">
            <i class="ti ti-settings text-gray-400 text-xl"></i>
            <span>Site Settings</span>
        </a>
    @endif

    <!-- ========================================== -->
    <!-- PLATFORM MANAGEMENT - 🔒 Super Admin Only -->
    <!-- ========================================== -->
    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
        <div class="nav-section-title mt-4">🔒 Platform Management</div>

        <!-- Books -->
        <a href="{{ route('super-admin.books.index') }}" class="nav-item {{ request()->routeIs('super-admin.books.*') ? 'active' : '' }}">
            <i class="ti ti-books text-blue-400 text-xl"></i>
            <span>Manage Books</span>
        </a>

        <!-- Users -->
        <a href="{{ route('super-admin.users.index') }}" class="nav-item {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}">
            <i class="ti ti-users text-cyan-400 text-xl"></i>
            <span>Manage Users</span>
        </a>

        <!-- Institutions -->
        <a href="{{ route('super-admin.institutions.index') }}" 
           class="nav-item {{ request()->routeIs('super-admin.institutions.index') || request()->routeIs('super-admin.institutions.show') || request()->routeIs('super-admin.institutions.edit') || request()->routeIs('super-admin.institutions.create') ? 'active' : '' }}">
            <i class="ti ti-building text-indigo-400 text-xl"></i>
            <span>Institutions</span>
        </a>

        <!-- Institution Requests -->
        <a href="{{ route('super-admin.institution-requests.index') }}" 
           class="nav-item {{ request()->routeIs('super-admin.institution-requests.*') ? 'active' : '' }}">
            <i class="ti ti-file-plus text-orange-400 text-xl"></i>
            <span>Institution Requests</span>
            @php
                $pendingRequests = \App\Models\InstitutionCreationRequest::where('status', 'pending')->count() ?? 0;
            @endphp
            @if($pendingRequests > 0)
                <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingRequests }}</span>
            @endif
        </a>

        <!-- Subscriptions -->
        <a href="{{ route('super-admin.institutions.subscriptions.index') }}"
           class="nav-item {{ request()->routeIs('super-admin.institutions.subscriptions.index') || request()->routeIs('super-admin.institutions.subscriptions.*') ? 'active' : '' }}">
            <i class="ti ti-building text-purple-400 text-xl"></i>
            <span>Subscriptions</span>
            @php
                $expiringSoon = \App\Models\Institution::whereHas('subscriptions', function($q) {
                    $q->where('status', 'active')
                        ->where('ends_at', '<=', now()->addDays(7))
                        ->where('ends_at', '>', now());
                })->count();
            @endphp
            @if($expiringSoon > 0)
                <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $expiringSoon }}</span>
            @endif
        </a>

<!-- Communities -->
<a href="{{ route('super-admin.communities.index') }}" 
   class="nav-item {{ request()->routeIs('super-admin.communities.*') ? 'active' : '' }}"
   id="communities-link"
   onclick="hideCommunityBadge()">
    <i class="ti ti-users text-teal-400 text-xl"></i>
    <span>Communities</span>
    @php
        $communityCount = \App\Models\CommunityGroup::count();
    @endphp
    @if($communityCount > 0)
        <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full" id="community-badge">
            {{ $communityCount }}
        </span>
    @endif
</a>

        <!-- Applications -->
        <a href="{{ route('super-admin.applications.index') }}" class="nav-item {{ request()->routeIs('super-admin.applications.*') ? 'active' : '' }}">
            <i class="ti ti-files text-yellow-400 text-xl"></i>
            <span>Applications</span>
            @php
                $pendingAppCount = \App\Models\Application::where('status', 'pending')->count();
            @endphp
            @if($pendingAppCount > 0)
                <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingAppCount }}</span>
            @endif
        </a>

        <!-- Payments -->
        <a href="{{ route('super-admin.payments.index') }}" class="nav-item {{ request()->routeIs('super-admin.payments.*') ? 'active' : '' }}">
            <i class="ti ti-wallet text-green-400 text-xl"></i>
            <span>Payments</span>
        </a>

        <!-- Quotes -->
        <a href="{{ route('super-admin.quotes.index') }}" class="nav-item {{ request()->routeIs('super-admin.quotes.*') ? 'active' : '' }}">
            <i class="ti ti-quote text-purple-400 text-xl"></i>
            <span>Manage Quotes</span>
        </a>

        <!-- ========================================== -->
        <!-- ANALYTICS - 📊 Super Admin Only          -->
        <!-- ========================================== -->
        <div class="nav-section-title mt-4">📊 Analytics</div>

        <a href="{{ route('super-admin.analytics.index') }}" class="nav-item {{ request()->routeIs('super-admin.analytics.*') ? 'active' : '' }}">
            <i class="ti ti-chart-bar text-yellow-400 text-xl"></i>
            <span>Analytics</span>
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
        <main class="flex-1 overflow-y-auto bg-gray-50">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm sticky top-0 z-20 border-b">
                <div class="px-4 sm:px-6 py-3 flex justify-between items-center top-bar-mobile">
                    <div class="flex items-center gap-3">
                        <!-- Mobile menu toggle -->
                        <button id="open-sidebar-mobile" class="lg:hidden text-gray-600 hover:text-purple-600">
                            <i class="ti ti-menu-2 text-2xl"></i>
                        </button>
                        <h1 class="text-lg sm:text-xl font-semibold text-gray-800 truncate">@yield('title', 'Super Admin Dashboard')</h1>
                    </div>
                    
                    <div class="flex items-center gap-2 sm:gap-3">
                        <!-- User Info -->
                        <div class="hidden sm:flex items-center gap-2 super-admin-info-mobile">
                          <div class="w-8 h-8 rounded-full 
    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
        bg-gradient-to-r from-yellow-500 to-red-500
    @elseif(auth()->user()->isMediaTeam() || auth()->user()->hasRole('media_team'))
        bg-gradient-to-r from-purple-500 to-pink-500
    @elseif(auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
        bg-gradient-to-r from-blue-500 to-cyan-500
    @else
        bg-gradient-to-r from-gray-500 to-gray-600
    @endif
    flex items-center justify-center flex-shrink-0">
    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
        <i class="ti ti-crown text-white text-sm"></i>
    @elseif(auth()->user()->isMediaTeam() || auth()->user()->hasRole('media_team'))
        <i class="ti ti-palette text-white text-sm"></i>
    @elseif(auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
        <i class="ti ti-shield text-white text-sm"></i>
    @else
        <i class="ti ti-user text-white text-sm"></i>
    @endif
</div>
                            <div class="hidden md:block">
                                <p class="text-sm font-medium text-gray-700">{{ Auth::user()->full_name ?? 'Super Admin' }}</p>
                                @if(Auth::user()->isSuperAdmin())
                                    <span class="role-badge super-admin">Super Admin</span>
                                @else
                                    <span class="role-badge media-team">Media Team</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Mobile avatar -->
                        <div class="sm:hidden w-8 h-8 rounded-full bg-gradient-to-r from-yellow-500 to-red-500 flex items-center justify-center flex-shrink-0">
                            <i class="ti ti-crown text-white text-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Page Content -->
            <div class="p-3 sm:p-4 md:p-6">
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
        </main>
    </div>

    <!-- ========== JAVASCRIPT ========== -->
    <script>
        // Hide community badge when clicked
        function hideCommunityBadge() {
            const badge = document.getElementById('community-badge');
            if (badge) {
                badge.style.display = 'none';
                // Store in session storage so it stays hidden
                sessionStorage.setItem('community_badge_hidden', 'true');
            }
        }

        // Check if badge should be hidden on page load
        document.addEventListener('DOMContentLoaded', function() {
            const badge = document.getElementById('community-badge');
            if (badge && sessionStorage.getItem('community_badge_hidden') === 'true') {
                badge.style.display = 'none';
            }
            
            // If on communities page, always hide the badge
            @if(request()->routeIs('super-admin.communities.*'))
                if (badge) {
                    badge.style.display = 'none';
                    sessionStorage.setItem('community_badge_hidden', 'true');
                }
            @endif
        });

        // Sidebar functions
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('super-sidebar');
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