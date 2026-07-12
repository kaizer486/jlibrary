<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JLIBRARY - @yield('title', 'Librarian Panel')</title>
    
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
        #librarian-sidebar {
            transition: transform 0.3s ease;
            transform: translateX(0);
            z-index: 100;
            width: 280px;
        }
        
        @media (max-width: 1024px) {
            #librarian-sidebar {
                transform: translateX(-100%);
                position: fixed;
                height: 100vh;
                top: 0;
                left: 0;
                overflow-y: auto;
            }
            
            #librarian-sidebar.open {
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
            
            .librarian-info-mobile {
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

        /* Library Cards */
        .library-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            color: #1f2937;
        }
        
        .library-card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-color: #d1d5db;
        }
        
        .library-stat {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px 20px;
            color: #1f2937;
            border-left: 4px solid #8b5cf6;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        
        .library-stat .number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1f2937;
        }
        
        .library-stat .label {
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 500;
        }

        /* Search Bar */
        .search-bar {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 18px;
            color: #1f2937;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .search-bar::placeholder {
            color: #9ca3af;
        }
        
        .search-bar:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
            outline: none;
        }
        
        .search-bar option {
            background: #ffffff;
            color: #1f2937;
        }

        /* Buttons */
        .btn-library {
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            color: white;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-library:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(139, 92, 246, 0.3);
            color: white;
            text-decoration: none;
        }
        
        .btn-library-outline {
            background: transparent;
            color: #7c3aed;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            border: 2px solid rgba(139, 92, 246, 0.25);
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-library-outline:hover {
            background: rgba(139, 92, 246, 0.06);
            border-color: #8b5cf6;
            color: #5b21b6;
            text-decoration: none;
        }

        /* Notification Bell */
        .notification-bell {
            position: relative;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
            cursor: pointer;
            color: #6b7280;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid #e5e7eb;
        }
        
        .notification-bell:hover {
            background: #f3f4f6;
            color: #374151;
            border-color: #d1d5db;
        }
        
        .notification-bell .badge {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            background: #ef4444;
            color: white;
            font-size: 0.6rem;
            font-weight: 700;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
            border: 2px solid #ffffff;
        }
        
        .notification-bell .badge.hidden {
            display: none;
        }
        
        /* Notification Dropdown */
        .notifications-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            width: 380px;
            max-height: 420px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            z-index: 100;
            overflow: hidden;
            display: none;
        }
        
        .notifications-dropdown.open {
            display: block;
        }
        
        .notifications-dropdown .dropdown-header {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f9fafb;
        }
        
        .notifications-dropdown .dropdown-header h4 {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.85rem;
        }
        
        .notifications-dropdown .dropdown-header a {
            font-size: 0.7rem;
            color: #8b5cf6;
            transition: color 0.3s ease;
            text-decoration: none;
        }
        
        .notifications-dropdown .dropdown-header a:hover {
            color: #7c3aed;
        }
        
        .notifications-dropdown .dropdown-body {
            max-height: 350px;
            overflow-y: auto;
        }
        
        .notifications-dropdown .dropdown-body::-webkit-scrollbar {
            width: 4px;
        }
        .notifications-dropdown .dropdown-body::-webkit-scrollbar-thumb {
            background: rgba(139, 92, 246, 0.3);
            border-radius: 10px;
        }
        
        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            transition: background 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            display: block;
        }
        
        .notification-item:hover {
            background: #f9fafb;
        }
        
        .notification-item.unread {
            background: rgba(139, 92, 246, 0.04);
            border-left: 3px solid #8b5cf6;
        }
        
        .notification-item .notification-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: rgba(139, 92, 246, 0.1);
        }
        
        .notification-item .notification-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: #1f2937;
        }
        
        .notification-item .notification-message {
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 2px;
        }
        
        .notification-item .notification-time {
            font-size: 0.6rem;
            color: #9ca3af;
            margin-top: 4px;
        }
        
        .notification-empty {
            padding: 24px;
            text-align: center;
            color: #9ca3af;
        }
        
        .notification-empty i {
            font-size: 2rem;
            display: block;
            margin-bottom: 8px;
            color: #e5e7eb;
        }

        /* Profile Dropdown */
        .profile-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 6px 12px 6px 6px;
            border-radius: 50px;
            transition: all 0.3s ease;
            background: #ffffff;
            border: 1px solid #e5e7eb;
        }
        
        .profile-btn:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }
        
        .profile-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        
        .profile-info .name {
            font-size: 0.85rem;
            font-weight: 600;
            color: #1f2937;
        }
        
        .profile-info .role {
            font-size: 0.7rem;
            color: #8b5cf6;
            font-weight: 500;
        }
        
        .profile-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            min-width: 220px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            z-index: 100;
            overflow: hidden;
        }
        
        .profile-dropdown.open {
            display: block;
        }
        
        .profile-dropdown .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #4b5563;
            text-decoration: none;
            transition: background 0.3s ease;
            font-size: 0.85rem;
        }
        
        .profile-dropdown .dropdown-item:hover {
            background: #f9fafb;
            color: #1f2937;
        }
        
        .profile-dropdown .dropdown-item i {
            width: 20px;
            color: #8b5cf6;
        }
        
        .profile-dropdown .dropdown-item.logout {
            color: #dc2626;
            border-top: 1px solid #f3f4f6;
        }
        
        .profile-dropdown .dropdown-item.logout i {
            color: #dc2626;
        }
        
        .profile-dropdown .dropdown-item.logout:hover {
            background: #fef2f2;
            color: #dc2626;
        }
        
        .profile-dropdown .dropdown-item .badge {
            margin-left: auto;
            font-size: 0.6rem;
            background: rgba(139, 92, 246, 0.1);
            color: #8b5cf6;
            padding: 1px 8px;
            border-radius: 999px;
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: #4b5563;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 4px;
        }
        
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 90;
        }
        
        .sidebar-overlay.active {
            display: block;
        }

        @media (max-width: 1024px) {
            #librarian-sidebar {
                transform: translateX(-100%);
            }
            #librarian-sidebar.open {
                transform: translateX(0);
            }
            .librarian-main {
                margin-left: 0;
                padding: 16px;
            }
            .mobile-toggle {
                display: block;
            }
            .librarian-topbar {
                padding: 12px 16px;
            }
            .librarian-topbar .page-title {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 640px) {
            .librarian-topbar .page-title span {
                display: none;
            }
            .profile-info .name {
                font-size: 0.75rem;
            }
            .profile-info .role {
                display: none;
            }
            .notifications-dropdown {
                width: 320px;
                right: -60px;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay" onclick="closeSidebar()"></div>
    
    <div class="flex h-screen overflow-hidden">
        <!-- ========================================== -->
        <!-- LIBRARIAN SIDEBAR                         -->
        <!-- ========================================== -->
       <aside id="librarian-sidebar" class="bg-gray-900 text-white flex-shrink-0 overflow-y-auto">
    <!-- Brand -->
    <div class="p-4 border-b border-gray-800 sticky top-0 bg-gray-900 z-10">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="ti ti-library text-purple-400 text-2xl"></i>
                <span class="text-xl font-bold">LIBRARIAN</span>
            </div>
            <button id="close-sidebar-mobile" class="lg:hidden text-gray-400 hover:text-white">
                <i class="ti ti-x text-2xl"></i>
            </button>
        </div>
        <p class="text-xs text-purple-400 mt-1">Library Management Panel</p>
    </div>
    
    <nav class="p-4 space-y-1">
        <!-- ========================================== -->
        <!-- DASHBOARD                                -->
        <!-- ========================================== -->
        <a href="{{ route('institution.dashboard') }}" class="nav-item {{ request()->routeIs('institution.dashboard*') ? 'active' : '' }}">
            <i class="ti ti-dashboard text-blue-400 text-xl"></i>
            <span>Dashboard</span>
        </a>

        <!-- ========================================== -->
        <!-- SUBSCRIPTION - SHOW FOR ALL TYPES         -->
        <!-- ========================================== -->
        <div class="nav-section-title">Subscription</div>
        
        <a href="{{ route('institution.subscription.index') }}" class="nav-item {{ request()->routeIs('institution.subscription*') ? 'active' : '' }}">
            <i class="ti ti-credit-card text-purple-400 text-xl"></i>
            <span>Subscription</span>
            
            @php
                $user = auth()->user();
                $institution = $user?->institution;
                
                if ($institution) {
                    $hasActiveSub = $institution->hasActiveSubscription();
                    $daysLeft = $institution->getSubscriptionDaysLeft();
                } else {
                    $hasActiveSub = false;
                    $daysLeft = 0;
                }
            @endphp
            
            @if(!$hasActiveSub)
                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">Expired</span>
            @elseif($daysLeft <= 7)
                <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $daysLeft }}d</span>
            @elseif($daysLeft <= 30)
                <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $daysLeft }}d</span>
            @endif
        </a>

        <!-- ========================================== -->
        <!-- LIBRARY MANAGEMENT                       -->
        <!-- ========================================== -->
        <div class="nav-section-title">Library Management</div>

        @php
            $institutionType = auth()->user()->institution?->type ?? 'library';
            $isBookstore = $institutionType === 'bookstore';
        @endphp

        @if($isBookstore)
            <!-- Bookstore Menu -->
            <a href="{{ route('institution.books.index') }}" class="nav-item {{ request()->routeIs('institution.books*') ? 'active' : '' }}">
                <i class="ti ti-package text-green-400 text-xl"></i>
                <span>Inventory Management</span>
            </a>
            
            <a href="{{ route('institution.shelves.index') }}" class="nav-item {{ request()->routeIs('institution.shelves*') ? 'active' : '' }}">
                <i class="ti ti-category text-yellow-400 text-xl"></i>
                <span>Categories & Sections</span>
            </a>
            
            <a href="{{ route('institution.members.index') }}" class="nav-item {{ request()->routeIs('institution.members*') ? 'active' : '' }}">
                <i class="ti ti-users text-blue-400 text-xl"></i>
                <span>Customers</span>
            </a>
            
            <a href="{{ route('institution.orders.index') }}" class="nav-item {{ request()->routeIs('institution.orders*') ? 'active' : '' }}">
                <i class="ti ti-shopping-cart text-pink-400 text-xl"></i>
                <span>Orders & Sales</span>
            </a>
        @else
            <!-- Library Menu -->
            <a href="{{ route('institution.books.index') }}" class="nav-item {{ request()->routeIs('institution.books*') ? 'active' : '' }}">
                <i class="ti ti-books text-blue-400 text-xl"></i>
                <span>Books Management</span>
            </a>
            
            <a href="{{ route('institution.shelves.index') }}" class="nav-item {{ request()->routeIs('institution.shelves*') ? 'active' : '' }}">
                <i class="ti ti-layout-grid text-green-400 text-xl"></i>
                <span>Shelves & Locations</span>
            </a>
            
            <a href="{{ route('institution.members.index') }}" class="nav-item {{ request()->routeIs('institution.members*') ? 'active' : '' }}">
                <i class="ti ti-users text-cyan-400 text-xl"></i>
                <span>Members Directory</span>
            </a>

            <a href="{{ route('institution.borrowings.index') }}" class="nav-item {{ request()->routeIs('institution.borrowings*') ? 'active' : '' }}">
                <i class="ti ti-bookmark text-pink-400 text-xl"></i>
                <span>Borrowings</span>
            </a>

            <a href="{{ route('institution.join-requests.index') }}" class="nav-item {{ request()->routeIs('institution.join-requests*') ? 'active' : '' }}">
                <i class="ti ti-user-plus text-yellow-400 text-xl"></i>
                <span>Join Requests</span>
                @php
                    $pendingJoinRequests = App\Models\JoinRequest::where('institution_id', auth()->user()->institution_id ?? 0)
                        ->where('status', 'pending')
                        ->count();
                @endphp
                @if($pendingJoinRequests > 0)
                    <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingJoinRequests }}</span>
                @endif
            </a>
        @endif

        <!-- ========================================== -->
        <!-- ANALYTICS                               -->
        <!-- ========================================== -->
        <div class="nav-section-title mt-4">Analytics</div>

        <a href="{{ route('institution.reports.index') }}" class="nav-item {{ request()->routeIs('institution.reports*') ? 'active' : '' }}">
            <i class="ti ti-chart-bar text-yellow-400 text-xl"></i>
            <span>Reports & Analytics</span>
        </a>

        <!-- ========================================== -->
        <!-- SETTINGS                                -->
        <!-- ========================================== -->
        <div class="nav-section-title mt-4">Settings</div>

        <a href="{{ route('institution.settings') }}" class="nav-item {{ request()->routeIs('institution.settings*') ? 'active' : '' }}">
            <i class="ti ti-settings text-gray-400 text-xl"></i>
            <span>Settings</span>
        </a>

        <!-- ========================================== -->
        <!-- NAVIGATION                              -->
        <!-- ========================================== -->
        <hr class="my-3 border-gray-800">

        <a href="{{ route('institution.public.index', auth()->user()->institution_id ?? 1) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-800 transition">
            <i class="ti ti-arrow-left"></i> <span class="hidden sm:inline">Back to Library</span>
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
                        <h1 class="text-lg sm:text-xl font-semibold text-gray-800 truncate">@yield('title', 'Librarian Dashboard')</h1>
                    </div>
                    
                    <div class="flex items-center gap-2 sm:gap-3">
                        <!-- Librarian Info -->
                        <div class="hidden sm:flex items-center gap-2 librarian-info-mobile">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center flex-shrink-0">
                                <i class="ti ti-library text-white text-sm"></i>
                            </div>
                            <div class="hidden md:block">
                                <p class="text-sm font-medium text-gray-700">{{ Auth::user()->full_name ?? 'Librarian' }}</p>
                                <p class="text-xs text-purple-500 font-semibold">Librarian</p>
                            </div>
                        </div>
                        
                        <!-- Mobile avatar -->
                        <div class="sm:hidden w-8 h-8 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center flex-shrink-0">
                            <i class="ti ti-library text-white text-sm"></i>
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
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('librarian-sidebar');
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