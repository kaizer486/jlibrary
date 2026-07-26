<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JLIBRARY - Learn. Share. Grow Together.</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'jlibrary': {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        },
                    },
                }
            }
        }
    </script>
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: #f3f4f6;
            min-height: 100vh;
        }
        
        /* ========================================== */
        /* SIDEBAR - FIXED MOBILE                    */
        /* ========================================== */
        #sidebar {
            transition: transform 0.3s ease;
            transform: translateX(0);
            z-index: 1000;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            overflow-y: auto;
        }
        
        @media (max-width: 1024px) {
            #sidebar {
                transform: translateX(-100%);
            }
            
            #sidebar.open {
                transform: translateX(0);
            }
        }
        
        /* Overlay */
        #sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 999;
            backdrop-filter: blur(4px);
        }
        
        #sidebar-overlay.active {
            display: block;
        }
        
        /* ========================================== */
        /* PROFILE DROPDOWN - FIXED MOBILE           */
        /* ========================================== */
        #profile-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            width: 280px;
            max-height: 80vh;
            overflow-y: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            z-index: 1001;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        @media (max-width: 640px) {
            #profile-dropdown {
                position: fixed;
                right: 10px;
                left: 10px;
                top: 70px;
                width: auto;
                max-height: 70vh;
            }
        }
        
        /* ========================================== */
        /* NOTIFICATIONS DROPDOWN - FIXED MOBILE     */
        /* ========================================== */
        #notifications-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            width: 380px;
            max-height: 70vh;
            overflow-y: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            z-index: 1001;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        @media (max-width: 640px) {
            #notifications-dropdown {
                position: fixed;
                right: 10px;
                left: 10px;
                top: 70px;
                width: auto;
                max-height: 70vh;
            }
        }
        
        /* ========================================== */
        /* SEARCH RESULTS - FIXED MOBILE             */
        /* ========================================== */
        #global-search-results {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            width: 480px;
            max-height: 60vh;
            overflow-y: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            z-index: 1001;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        @media (max-width: 640px) {
            #global-search-results {
                position: fixed;
                right: 10px;
                left: 10px;
                top: 70px;
                width: auto;
                max-height: 60vh;
            }
        }
        
        /* ========================================== */
        /* OTHER STYLES                              */
        /* ========================================== */
        .institution-badge {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .btn-green {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        
        .btn-green:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.35);
            color: white;
        }
        
        .badge-approved {
            background: #10B981;
            color: white;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .badge-pending {
            background: #F59E0B;
            color: white;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .badge-rejected {
            background: #EF4444;
            color: white;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .rotate-180 {
            transform: rotate(180deg);
        }
        
        @keyframes ring {
            0% { transform: rotate(0); }
            25% { transform: rotate(15deg); }
            50% { transform: rotate(-15deg); }
            75% { transform: rotate(5deg); }
            100% { transform: rotate(0); }
        }
        
        .animate-ring {
            animation: ring 0.5s ease-in-out;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }
        
        .animate-float {
            animation: float 4s ease-in-out infinite;
        }
        
        /* Mobile top bar fixes */
        @media (max-width: 640px) {
            .top-bar-mobile {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .mobile-hidden {
                display: none !important;
            }
        }
        
        /* Search result item hover */
        .search-result-item:hover {
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.05), rgba(236, 72, 153, 0.05));
        }
        
        /* Scrollbar styling */
        #global-search-results::-webkit-scrollbar,
        #notifications-dropdown::-webkit-scrollbar,
        #profile-dropdown::-webkit-scrollbar {
            width: 4px;
        }
        
        #global-search-results::-webkit-scrollbar-track,
        #notifications-dropdown::-webkit-scrollbar-track,
        #profile-dropdown::-webkit-scrollbar-track {
            background: transparent;
        }
        
        #global-search-results::-webkit-scrollbar-thumb,
        #notifications-dropdown::-webkit-scrollbar-thumb,
        #profile-dropdown::-webkit-scrollbar-thumb {
            background: #c4b5fd;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-gray-100">
    
    <!-- ========================================== -->
    <!-- SIDEBAR OVERLAY                           -->
    <!-- ========================================== -->
    <div id="sidebar-overlay"></div>
    
    <!-- ========================================== -->
    <!-- SIDEBAR                                   -->
    <!-- ========================================== -->
    @auth
        @include('layouts.sidebar')
    @endauth
    
    <!-- ========================================== -->
    <!-- MAIN CONTENT                              -->
    <!-- ========================================== -->
    <main class="{{ Auth::check() ? 'lg:ml-[280px]' : '' }} min-h-screen">
        @auth
            <!-- Top Bar -->
            <div class="bg-white shadow-sm sticky top-0 z-50">
                <div class="px-3 sm:px-4 py-2 sm:py-3 flex justify-between items-center top-bar-mobile">
                    <!-- Left Section -->
                    <div class="flex items-center gap-2 sm:gap-3">
                        <button id="mobile-menu-toggle" class="lg:hidden text-gray-600 hover:text-purple-600 p-1">
                            <i class="ti ti-menu-2 text-2xl"></i>
                        </button>
                        <h1 class="text-lg sm:text-xl font-semibold text-gray-800 truncate">@yield('title', 'Dashboard')</h1>
                    </div>
                    
                    <!-- Right Section -->
                    <div class="flex items-center gap-1 sm:gap-3">
                        <!-- ========== NOTIFICATION BELL ========== -->
                        @php
                            $unreadNotifications = Auth::check() ? App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count() : 0;
                        @endphp
                        
                        <div class="relative">
                            <button id="notification-bell" class="relative p-1.5 sm:p-2 text-gray-600 hover:text-purple-600 transition rounded-full hover:bg-gray-100">
                                <i class="ti ti-bell text-xl sm:text-2xl"></i>
                                @if($unreadNotifications > 0)
                                    <span id="notification-badge" class="absolute -top-0.5 -right-0.5 w-4 h-4 sm:w-5 sm:h-5 bg-red-500 text-white text-[10px] sm:text-xs rounded-full flex items-center justify-center font-bold">
                                        {{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}
                                    </span>
                                @endif
                            </button>
                            
                            <!-- Notifications Dropdown -->
                            <div id="notifications-dropdown" class="hidden">
                                <div class="p-3 border-b bg-gradient-to-r from-purple-50 to-pink-50 sticky top-0 z-10">
                                    <div class="flex justify-between items-center">
                                        <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                                            <i class="ti ti-bell text-purple-600"></i>
                                            Notifications
                                        </h4>
                                        <a href="{{ route('notifications.index') }}" class="text-purple-600 text-xs hover:underline">View All</a>
                                    </div>
                                </div>
                                <div id="notifications-list" class="max-h-96 overflow-y-auto">
                                    <div class="p-4 text-center text-gray-500 text-sm">
                                        <i class="ti ti-loader-2 animate-spin text-2xl mb-2 block"></i>
                                        Loading...
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ========== GLOBAL SEARCH ========== -->
                        <div class="hidden md:flex items-center relative">
                            <i class="ti ti-search absolute left-3 text-gray-400"></i>
                            <input type="text" 
                                   id="global-search" 
                                   placeholder="Search..." 
                                   autocomplete="off"
                                   class="pl-9 pr-4 py-1.5 sm:py-2 w-32 sm:w-48 lg:w-64 bg-gray-100 border-0 rounded-full text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none focus:bg-white transition">
                            <div id="global-search-results" class="hidden"></div>
                        </div>
                        
                        <!-- ========== PROFILE BUTTON ========== -->
                        <div class="relative">
                            <button id="profile-btn" class="flex items-center gap-1 sm:gap-2 focus:outline-none">
                                <!-- Wallet Badge - Hidden on mobile -->
                                <div class="hidden sm:flex items-center gap-1 bg-green-50 px-2 py-1 rounded-full">
                                    <i class="ti ti-wallet text-green-600 text-xs"></i>
                                    <span class="text-xs font-semibold text-green-700">TSh {{ number_format(Auth::user()->wallet_balance ?? 0, 2) }}</span>
                                </div>
                              <!-- Avatar -->
<div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center flex-shrink-0 overflow-hidden">
    @if(Auth::user()->avatar)
        <img src="{{ url('media/' . Auth::user()->avatar) }}" class="w-full h-full object-cover" alt="Avatar">
    @else
        <i class="ti ti-user text-white text-xs sm:text-sm"></i>
    @endif
</div>
                                <span class="hidden md:inline text-sm text-gray-700">{{ Auth::user()->full_name }}</span>
                                <i id="dropdown-chevron" class="ti ti-chevron-down text-gray-400 text-xs transition-transform duration-200 hidden sm:inline"></i>
                            </button>
                            
                            <!-- ========== PROFILE DROPDOWN ========== -->
                            <div id="profile-dropdown" class="hidden">
                                <!-- User Info -->
                                <div class="px-4 py-3 bg-gradient-to-r from-purple-50 to-pink-50 border-b">
                                    <div class="flex items-center justify-between">
                                        <div class="min-w-0 flex-1">
                                            <p class="font-semibold text-gray-900 truncate">{{ Auth::user()->full_name }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                        </div>
                                        <a href="{{ route('profile.edit') }}" class="w-8 h-8 bg-white rounded-lg flex items-center justify-center hover:bg-purple-100 transition shadow-sm flex-shrink-0 ml-2">
                                            <i class="ti ti-edit text-purple-600 text-sm"></i>
                                        </a>
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-2 bg-green-50 px-2 py-1 rounded-full w-fit">
                                        <i class="ti ti-wallet text-green-600 text-xs"></i>
                                        <span class="text-xs font-semibold text-green-700">TSh {{ number_format(Auth::user()->wallet_balance ?? 0, 2) }}</span>
                                    </div>
                                    <div class="mt-2">
                                        <span class="text-xs px-2 py-0.5 rounded-full inline-block 
    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin')) bg-red-100 text-red-700
    @elseif(auth()->user()->isMediaTeam() || auth()->user()->hasRole('media_team')) bg-yellow-100 text-yellow-700
    @elseif(auth()->user()->isAdmin() || auth()->user()->hasRole('admin')) bg-purple-100 text-purple-700
    @elseif(auth()->user()->isAnyInstitutionAdmin() || auth()->user()->hasRole('institution_admin')) bg-blue-100 text-blue-700
    @elseif(auth()->user()->hasAnyRole(['author', 'seller'])) bg-purple-100 text-purple-700
    @elseif(auth()->user()->hasRole('librarian')) bg-amber-100 text-amber-700
    @elseif(auth()->user()->hasRole('instructor')) bg-cyan-100 text-cyan-700
    @else bg-gray-100 text-gray-700 @endif">
    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
        👑 Super Admin
    @elseif(auth()->user()->isMediaTeam() || auth()->user()->hasRole('media_team'))
        🎨 Media Team
    @elseif(auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
        🛡️ Admin
    @elseif(auth()->user()->isAnyInstitutionAdmin() || auth()->user()->hasRole('institution_admin'))
        🏢 Institution Admin
    @elseif(auth()->user()->hasAnyRole(['author', 'seller']))
        ✍️ Author & Seller
    @elseif(auth()->user()->hasRole('librarian'))
        📚 Librarian
    @elseif(auth()->user()->hasRole('instructor'))
        👨‍🏫 Instructor
    @else
        👤 Member
    @endif
</span>
                                    </div>                                    
                                </div>

                                <!-- ADMIN ACCESS -->
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('super_admin'))
                                    <a href="{{ route('super-admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-red-50 border-b transition">
                                        <i class="ti ti-crown text-lg text-yellow-500"></i>
                                        <span class="font-medium text-gray-700">Super Dashboard</span>
                                        <span class="text-xs bg-red-500 text-white px-2 py-0.5 rounded ml-auto">Super</span>
                                    </a>
                                @elseif(auth()->user()->isMediaTeam() || auth()->user()->hasRole('media_team'))
                                    <a href="{{ route('super-admin.media.dashboard') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-yellow-50 border-b transition">
                                        <i class="ti ti-palette text-lg text-yellow-500"></i>
                                        <span class="font-medium text-gray-700">Media Dashboard</span>
                                        <span class="text-xs bg-yellow-500 text-white px-2 py-0.5 rounded ml-auto">Media</span>
                                    </a>
                                @elseif(auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-purple-50 border-b transition">
                                        <i class="ti ti-shield text-lg text-purple-600"></i>
                                        <span class="font-medium text-gray-700">Admin Panel</span>
                                        <span class="text-xs bg-purple-100 text-purple-600 px-2 py-0.5 rounded ml-auto">Admin</span>
                                    </a>
                                @endif


                                <!-- INSTITUTION ADMIN -->
                                @if(!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin() && (auth()->user()->isAnyInstitutionAdmin() || auth()->user()->hasRole('institution_admin')))
                                    <a href="{{ route('institution.dashboard') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 border-b transition">
                                        <i class="ti ti-building text-lg text-blue-500"></i>
                                        <span class="font-medium text-gray-700">Institution Panel</span>
                                        <span class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded ml-auto">Admin</span>
                                    </a>
                                @endif

                              <!-- AUTHOR & SELLER -->
@if(auth()->user()->hasAnyRole(['author', 'seller']))
    <a href="{{ route('author.dashboard') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-purple-50 border-b transition">
        <i class="ti ti-edit text-lg text-purple-500"></i>
        <span class="font-medium text-gray-700">Author & Seller Studio</span>
        <span class="text-xs bg-purple-100 text-purple-600 px-2 py-0.5 rounded ml-auto">Studio</span>
    </a>
@endif

                                <!-- LIBRARIAN -->
                                @if(!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin() && auth()->user()->hasRole('librarian'))
                                    <a href="{{ route('librarian.dashboard') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-amber-50 border-b transition">
                                        <i class="ti ti-library text-lg text-amber-500"></i>
                                        <span class="font-medium text-gray-700">Librarian Panel</span>
                                        <span class="text-xs bg-amber-100 text-amber-600 px-2 py-0.5 rounded ml-auto">Librarian</span>
                                    </a>
                                @endif

                                <!-- INSTRUCTOR -->
                                @if(!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin() && auth()->user()->hasRole('instructor'))
                                    <a href="{{ route('instructor.dashboard') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-cyan-50 border-b transition">
                                        <i class="ti ti-school text-lg text-cyan-500"></i>
                                        <span class="font-medium text-gray-700">Instructor Panel</span>
                                        <span class="text-xs bg-cyan-100 text-cyan-600 px-2 py-0.5 rounded ml-auto">Instructor</span>
                                    </a>
                                @endif

                                <hr class="my-1">

                                <!-- Regular User Links -->
                                <a href="{{ route('wallet.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition">
                                    <i class="ti ti-wallet text-lg text-amber-500"></i>
                                    <span>My Wallet</span>
                                    <span class="text-xs text-gray-400 ml-auto">TSh {{ number_format(Auth::user()->wallet_balance ?? 0, 2) }}</span>
                                </a>

                                <a href="{{ route('certificates.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition">
                                    <i class="ti ti-certificate text-lg text-green-500"></i>
                                    <span>My Certificates</span>
                                    <span class="text-xs text-gray-400 ml-auto">{{ Auth::user()->certificates()->count() ?? 0 }}</span>
                                </a>

                                <a href="{{ route('documents.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition">
                                    <i class="ti ti-file-text text-lg text-blue-500"></i>
                                    <span>My Documents</span>
                                </a>

                                <a href="{{ route('bookmarks.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition">
                                    <i class="ti ti-bookmark text-lg text-purple-500"></i>
                                    <span>My Bookmarks</span>
                                    <span class="text-xs text-gray-400 ml-auto">{{ Auth::user()->bookmarks()->count() ?? 0 }}</span>
                                </a>

                                @if(auth()->user()->institution_id)
                                    <a href="{{ route('my.institution') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition">
                                        <i class="ti ti-building text-lg text-indigo-500"></i>
                                        <span>My Institution</span>
                                    </a>
                                @endif

                                <hr class="my-1">

                                <!-- Logout -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 transition text-left">
                                        <i class="ti ti-logout text-lg"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endauth
        
        <!-- Page Content -->
        <div class="{{ Auth::check() ? 'p-3 sm:p-4 md:p-6' : '' }}">
            @yield('content')
        </div>
    </main>

    <!-- ========================================== -->
    <!-- JAVASCRIPT                                -->
    <!-- ========================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ==========================================
            // SIDEBAR TOGGLE
            // ==========================================
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const openBtn = document.getElementById('mobile-menu-toggle');
            const closeBtn = document.getElementById('close-sidebar');

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
                openBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    openSidebar();
                });
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    closeSidebar();
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeSidebar();
                });
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeSidebar();
                }
            });

            // ==========================================
            // PROFILE DROPDOWN
            // ==========================================
            const profileBtn = document.getElementById('profile-btn');
            const profileDropdown = document.getElementById('profile-dropdown');
            const chevron = document.getElementById('dropdown-chevron');

            if (profileBtn && profileDropdown) {
                profileBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('hidden');
                    if (chevron) {
                        chevron.classList.toggle('rotate-180');
                    }
                });

                document.addEventListener('click', function () {
                    profileDropdown.classList.add('hidden');
                    if (chevron) {
                        chevron.classList.remove('rotate-180');
                    }
                });

                profileDropdown.addEventListener('click', function (e) {
                    e.stopPropagation();
                });
            }

            // ==========================================
            // NOTIFICATIONS
            // ==========================================
            const notificationBell = document.getElementById('notification-bell');
            const notificationsDropdown = document.getElementById('notifications-dropdown');

            function loadNotifications() {
                const list = document.getElementById('notifications-list');
                if (!list) return;
                
                fetch('{{ route("notifications.latest") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.notifications.length === 0) {
                            list.innerHTML = `
                                <div class="p-6 text-center">
                                    <i class="ti ti-bell-off text-3xl text-gray-300 mb-2 block"></i>
                                    <p class="text-gray-500 text-sm">No notifications</p>
                                    <p class="text-gray-400 text-xs mt-1">When you have activities, they'll appear here</p>
                                </div>
                            `;
                            return;
                        }
                        
                        list.innerHTML = data.notifications.map(notification => `
                            <div class="border-b border-gray-100 p-3 hover:bg-gray-50 transition ${notification.is_read ? '' : 'bg-purple-50'}">
                                <div class="flex items-start gap-2">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">${escapeHtml(notification.title)}</p>
                                        <p class="text-xs text-gray-500 mt-1">${escapeHtml(notification.message)}</p>
                                        <p class="text-xs text-gray-400 mt-1">${notification.created_at}</p>
                                    </div>
                                    ${!notification.is_read ? '<span class="w-2 h-2 bg-purple-600 rounded-full mt-2 flex-shrink-0"></span>' : ''}
                                </div>
                            </div>
                        `).join('');
                        
                        const badge = document.getElementById('notification-badge');
                        if (data.unread_count > 0) {
                            if (badge) {
                                badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                                badge.classList.remove('hidden');
                            }
                        } else {
                            if (badge) badge.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                        list.innerHTML = `
                            <div class="p-4 text-center text-red-500 text-sm">
                                Error loading notifications
                            </div>
                        `;
                    });
            }

            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            if (notificationBell && notificationsDropdown) {
                notificationBell.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notificationsDropdown.classList.toggle('hidden');
                    if (!notificationsDropdown.classList.contains('hidden')) {
                        loadNotifications();
                        notificationBell.classList.add('animate-ring');
                        setTimeout(() => notificationBell.classList.remove('animate-ring'), 500);
                    }
                });
                
                document.addEventListener('click', function() {
                    notificationsDropdown.classList.add('hidden');
                });
                
                notificationsDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            // ==========================================
            // GLOBAL SEARCH - COMPLETE FIX
            // ==========================================
            const searchInput = document.getElementById('global-search');
            const resultsContainer = document.getElementById('global-search-results');
            let searchTimeout;
            let isSearching = false;

            if (searchInput && resultsContainer) {
                // Input event with debounce
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();
                    
                    if (query.length < 2) {
                        resultsContainer.classList.add('hidden');
                        return;
                    }
                    
                    searchTimeout = setTimeout(() => {
                        performSearch(query);
                    }, 300);
                });

                // Focus event
                searchInput.addEventListener('focus', function() {
                    if (this.value.trim().length >= 2 && !isSearching) {
                        resultsContainer.classList.remove('hidden');
                    }
                });

                // Click outside to close
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                        resultsContainer.classList.add('hidden');
                    }
                });

                // Keyboard shortcuts
                document.addEventListener('keydown', function(e) {
                    // Ctrl+K or Cmd+K to focus search
                    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                        e.preventDefault();
                        searchInput.focus();
                        searchInput.select();
                    }
                    // Escape to close results
                    if (e.key === 'Escape') {
                        resultsContainer.classList.add('hidden');
                        searchInput.blur();
                    }
                });
            }

           async function performSearch(query) {
    if (!resultsContainer) return;
    isSearching = true;
    
    try {
        console.log('Searching for:', query);
        
        const response = await fetch(`/api/global-search?q=${encodeURIComponent(query)}`);
        
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        console.log('Search results:', data);
        
        if (!data.results || data.results.length === 0) {
            resultsContainer.innerHTML = `
                <div class="p-8 text-center">
                    <i class="ti ti-search-off text-4xl text-gray-300 mb-3 block"></i>
                    <p class="text-gray-500 text-sm font-medium">No results found</p>
                    <p class="text-gray-400 text-xs mt-1">Try searching for books, courses, or documents</p>
                </div>
            `;
        } else {
            // Group results by type
            const grouped = {};
            data.results.forEach(item => {
                if (!grouped[item.type]) grouped[item.type] = [];
                grouped[item.type].push(item);
            });

            const typeLabels = {
                book: '📚 Books',
                chat: '💬 AI Chats',
                certificate: '🎓 Certificates',
                quiz: '📝 Quizzes',
                group: '👥 Community Groups',
                marketplace: '🛒 Marketplace',
                document: '📄 Documents',
                transaction: '💰 Transactions',
                referral: '🎁 Referrals',
                conversion: '🔄 File Conversions',
                institution: '🏛️ Institutions',
                shelf: '📚 Shelves',
                category: '🏷️ Categories',
                borrowing: '📖 Borrowings',
                purchase: '🛍️ Purchases',
                notification: '🔔 Notifications',
                withdrawal: '💳 Withdrawals',
                author: ' Authors'
            };

            const typeColors = {
                book: 'from-purple-500 to-indigo-500',
                chat: 'from-green-500 to-emerald-500',
                certificate: 'from-pink-500 to-rose-500',
                quiz: 'from-indigo-500 to-blue-500',
                group: 'from-blue-500 to-cyan-500',
                marketplace: 'from-orange-500 to-red-500',
                document: 'from-cyan-500 to-teal-500',
                transaction: 'from-amber-500 to-yellow-500',
                referral: 'from-green-500 to-teal-500',
                conversion: 'from-purple-500 to-pink-500',
                institution: 'from-violet-500 to-purple-500',
                shelf: 'from-blue-500 to-indigo-500',
                category: 'from-gray-500 to-slate-500',
                borrowing: 'from-amber-500 to-orange-500',
                purchase: 'from-emerald-500 to-green-500',
                notification: 'from-red-500 to-pink-500',
                withdrawal: 'from-rose-500 to-red-500',
                author: 'from-cyan-500 to-blue-500'
            };

            let html = '';
            
            // Priority order for display
            const priorityTypes = ['book', 'institution', 'group', 'marketplace', 'quiz', 'certificate', 'document'];
            
            const sortedKeys = Object.keys(grouped).sort((a, b) => {
                const indexA = priorityTypes.indexOf(a);
                const indexB = priorityTypes.indexOf(b);
                if (indexA === -1 && indexB === -1) return 0;
                if (indexA === -1) return 1;
                if (indexB === -1) return -1;
                return indexA - indexB;
            });

            for (const type of sortedKeys) {
                const items = grouped[type];
                const label = typeLabels[type] || type.charAt(0).toUpperCase() + type.slice(1) + 's';
                const color = typeColors[type] || 'from-gray-400 to-gray-500';
                
                html += `
                    <div class="border-b border-gray-100 last:border-0">
                        <div class="px-4 py-2 bg-gray-50/80 sticky top-0 z-10">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">${label}</p>
                        </div>
                        <div>
                            ${items.slice(0, 5).map(item => `
                                <a href="${item.url}" 
                                   class="search-result-item flex items-center gap-3 px-4 py-2.5 hover:bg-purple-50/50 transition group cursor-pointer border-b border-gray-50 last:border-0">
                                    <div class="w-8 h-8 bg-gradient-to-br ${color} rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                        <i class="${item.icon || 'ti ti-search'} text-white text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 group-hover:text-purple-600 truncate">
                                            ${escapeHtml(item.title)}
                                        </p>
                                        <p class="text-xs text-gray-400 truncate">${escapeHtml(item.subtitle || '')}</p>
                                    </div>
                                    ${item.badge ? `<span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full flex-shrink-0">${escapeHtml(item.badge)}</span>` : ''}
                                    <i class="ti ti-chevron-right text-gray-300 group-hover:text-purple-500 text-sm flex-shrink-0 ml-1"></i>
                                </a>
                            `).join('')}
                            ${items.length > 5 ? `
                                <div class="px-4 py-2 text-center border-t border-gray-50">
                                    <span class="text-xs text-gray-400">+${items.length - 5} more results</span>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            }

            // View All link
            html += `
                <div class="p-3 text-center border-t border-gray-200 bg-gray-50/80 sticky bottom-0 rounded-b-xl">
                    <a href="/global-search?q=${encodeURIComponent(query)}" 
                       class="text-sm text-purple-600 hover:text-purple-800 font-medium flex items-center justify-center gap-1 group">
                        View all results 
                        <i class="ti ti-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            `;

            resultsContainer.innerHTML = html;
        }
        
        resultsContainer.classList.remove('hidden');
        
    } catch (error) {
        console.error('Search error:', error);
        resultsContainer.innerHTML = `
            <div class="p-8 text-center">
                <i class="ti ti-alert-circle text-3xl text-red-400 mb-3 block"></i>
                <p class="text-gray-700 text-sm font-medium">Search failed</p>
                <p class="text-gray-400 text-xs mt-1">${error.message || 'Please try again'}</p>
            </div>
        `;
        resultsContainer.classList.remove('hidden');
    } finally {
        isSearching = false;
    }
}

            // ==========================================
            // LOAD UNREAD NOTIFICATION COUNT
            // ==========================================
            @auth
            fetch('{{ route("notifications.unread-count") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notification-badge');
                    if (data.count > 0) {
                        if (badge) {
                            badge.textContent = data.count > 9 ? '9+' : data.count;
                            badge.classList.remove('hidden');
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
            @endauth
        });
    </script>
    
    @stack('scripts')
</body>
</html>