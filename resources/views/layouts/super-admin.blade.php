<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Super Admin - JLIBRARY</title>
    
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
        
        /* Submenu */
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
    </style>
</head>
<body class="bg-gray-100">
    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay" onclick="closeSidebar()"></div>
    
    <div class="flex h-screen overflow-hidden">
        <!-- SUPER ADMIN SIDEBAR -->
        <aside id="super-sidebar" class="bg-gray-900 text-white flex-shrink-0 overflow-y-auto">
            <div class="p-4 border-b border-gray-800 sticky top-0 bg-gray-900 z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="ti ti-crown text-yellow-400 text-2xl"></i>
                        <span class="text-xl font-bold">SUPER ADMIN</span>
                    </div>
                    <!-- Close button for mobile -->
                    <button id="close-sidebar-mobile" class="lg:hidden text-gray-400 hover:text-white">
                        <i class="ti ti-x text-2xl"></i>
                    </button>
                </div>
                <p class="text-xs text-yellow-500 mt-1">Full Platform Control</p>
            </div>
            
            <nav class="p-4 space-y-1">
                <!-- ========================================== -->
                <!-- DASHBOARD                                -->
                <!-- ========================================== -->
                <a href="{{ route('super-admin.dashboard') }}" class="nav-item {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                    <i class="ti ti-crown text-yellow-400 text-xl"></i>
                    <span>Super Dashboard</span>
                </a>

                <!-- ========================================== -->
                <!-- CONTENT MANAGEMENT                       -->
                <!-- ========================================== -->
                <div class="nav-section-title">Content Management</div>

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

                <!-- ========================================== -->
                <!-- PLATFORM MANAGEMENT                      -->
                <!-- ========================================== -->
                <div class="nav-section-title mt-4">Platform Management</div>

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
                <a href="{{ route('super-admin.institutions.index') }}" class="nav-item {{ request()->routeIs('super-admin.institutions.*') ? 'active' : '' }}">
                    <i class="ti ti-building text-indigo-400 text-xl"></i>
                    <span>Institutions</span>
                </a>

                <!-- Institution Requests -->
                <a href="{{ route('super-admin.institution-requests.index') }}" class="nav-item {{ request()->routeIs('super-admin.institution-requests.*') ? 'active' : '' }}">
                    <i class="ti ti-file-plus text-orange-400 text-xl"></i>
                    <span>Institution Requests</span>
                    @php
                        $pendingRequests = \App\Models\InstitutionCreationRequest::where('status', 'pending')->count() ?? 0;
                    @endphp
                    @if($pendingRequests > 0)
                        <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingRequests }}</span>
                    @endif
                </a>

                <!-- Marketplace -->
                <a href="{{ route('super-admin.marketplace.index') }}" class="nav-item {{ request()->routeIs('super-admin.marketplace.*') ? 'active' : '' }}">
                    <i class="ti ti-shopping-cart text-amber-400 text-xl"></i>
                    <span>Marketplace</span>
                </a>

                <!-- Applications -->
                <a href="{{ route('super-admin.applications.index') }}" class="nav-item {{ request()->routeIs('super-admin.applications.*') ? 'active' : '' }}">
                    <i class="ti ti-files text-yellow-400 text-xl"></i>
                    <span>Applications</span>
                    @php
                        $pendingCount = App\Models\Application::where('status', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
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
                <!-- ANALYTICS                               -->
                <!-- ========================================== -->
                <div class="nav-section-title mt-4">Analytics</div>

                <a href="{{ route('super-admin.analytics.index') }}" class="nav-item {{ request()->routeIs('super-admin.analytics.*') ? 'active' : '' }}">
                    <i class="ti ti-chart-bar text-yellow-400 text-xl"></i>
                    <span>Analytics</span>
                </a>

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
        
        <!-- MAIN CONTENT -->
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
                        <!-- Super Admin Info -->
                        <div class="hidden sm:flex items-center gap-2 super-admin-info-mobile">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-yellow-500 to-red-500 flex items-center justify-center flex-shrink-0">
                                <i class="ti ti-crown text-white text-sm"></i>
                            </div>
                            <div class="hidden md:block">
                                <p class="text-sm font-medium text-gray-700">{{ Auth::user()->full_name }}</p>
                                <p class="text-xs text-red-500 font-semibold">Super Admin</p>
                            </div>
                        </div>
                        
                        <!-- Mobile avatar -->
                        <div class="sm:hidden w-8 h-8 rounded-full bg-gradient-to-r from-yellow-500 to-red-500 flex items-center justify-center flex-shrink-0">
                            <i class="ti ti-crown text-white text-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-3 sm:p-4 md:p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- ========== JAVASCRIPT ========== -->
    <script>
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
        });
    </script>

    @stack('scripts')
</body>
</html>