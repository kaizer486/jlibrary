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
            background: #020617;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        
        /* Sidebar */
        #librarian-sidebar {
            transition: transform 0.3s ease;
            transform: translateX(0);
            z-index: 100;
            background: #0f172a;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 280px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #7c3aed transparent;
            border-right: 1px solid rgba(124, 58, 237, 0.15);
        }
        
        #librarian-sidebar::-webkit-scrollbar {
            width: 4px;
        }
        #librarian-sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        #librarian-sidebar::-webkit-scrollbar-thumb {
            background: #7c3aed;
            border-radius: 10px;
        }
        
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 10px;
            color: white;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            position: relative;
        }
        
        .sidebar-link:hover {
            background: rgba(124, 58, 237, 0.12);
            color: #e2e8f0;
        }
        
        /* ==========================================
   SEARCH BAR - Dark Theme
   ========================================== */
.search-bar {
    background: #1e293b !important;  /* Dark background */
    border: 1px solid #334155;
    border-radius: 12px;
    padding: 12px 18px;
    color: #f8fafc !important;  /* White text */
    width: 100%;
    transition: all 0.3s ease;
}

.search-bar::placeholder {
    color: #64748b;  /* Gray placeholder */
}

.search-bar:focus {
    border-color: #a855f7;
    box-shadow: 0 0 30px rgba(139, 92, 246, 0.08);
    outline: none;
    background: #0f172a;
}

.search-bar option {
    background: #1e293b;
    color: #f8fafc;
}

.search-bar option:hover {
    background: #334155;
}
        .sidebar-link.active {
            background: rgba(124, 58, 237, 0.15);
            color: #a78bfa;
        }
        
        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 24px;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            border-radius: 0 4px 4px 0;
        }
        
        .sidebar-link i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(124, 58, 237, 0.12);
            margin: 12px 16px;
        }
        
        .back-to-user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 10px;
            border-radius: 10px;
            background: rgba(124, 58, 237, 0.1);
            color: #a78bfa;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            border: 1px solid rgba(124, 58, 237, 0.15);
            margin: 8px 16px;
        }
        
        .back-to-user:hover {
            background: rgba(124, 58, 237, 0.2);
            color: #c4b5fd;
            border-color: rgba(124, 58, 237, 0.3);
        }
        
        /* Top Bar */
        .librarian-topbar {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(124, 58, 237, 0.12);
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 50;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .librarian-topbar .page-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #e2e8f0;
        }
        
        .librarian-topbar .page-title i {
            color: #a78bfa;
            margin-right: 8px;
        }
        
        /* Right Section */
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        /* Notification Bell */
        .notification-bell {
            position: relative;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
            cursor: pointer;
            color: #94a3b8;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(124, 58, 237, 0.15);
        }
        
        .notification-bell:hover {
            background: rgba(124, 58, 237, 0.12);
            color: #e2e8f0;
            border-color: rgba(124, 58, 237, 0.3);
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
            border: 2px solid #0f172a;
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
            background: #1e293b;
            border: 1px solid rgba(124, 58, 237, 0.15);
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
            z-index: 100;
            overflow: hidden;
            display: none;
        }
        
        .notifications-dropdown.open {
            display: block;
        }
        
        .notifications-dropdown .dropdown-header {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(124, 58, 237, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(15, 23, 42, 0.5);
        }
        
        .notifications-dropdown .dropdown-header h4 {
            font-weight: 600;
            color: white;
            font-size: 0.85rem;
        }
        
        .notifications-dropdown .dropdown-header a {
            font-size: 0.7rem;
            color: #a78bfa;
            transition: color 0.3s ease;
            text-decoration: none;
        }
        
        .notifications-dropdown .dropdown-header a:hover {
            color: #c4b5fd;
        }
        
        .notifications-dropdown .dropdown-body {
            max-height: 350px;
            overflow-y: auto;
        }
        
        .notifications-dropdown .dropdown-body::-webkit-scrollbar {
            width: 4px;
        }
        .notifications-dropdown .dropdown-body::-webkit-scrollbar-thumb {
            background: rgba(168, 85, 247, 0.3);
            border-radius: 10px;
        }
        
        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(124, 58, 237, 0.06);
            transition: background 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            display: block;
        }
        
        .notification-item:hover {
            background: rgba(124, 58, 237, 0.08);
        }
        
        .notification-item.unread {
            background: rgba(124, 58, 237, 0.06);
            border-left: 3px solid #a855f7;
        }
        
        .notification-item .notification-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .notification-item .notification-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
        }
        
        .notification-item .notification-message {
            font-size: 0.7rem;
            color: #94a3b8;
            margin-top: 2px;
        }
        
        .notification-item .notification-time {
            font-size: 0.6rem;
            color: #64748b;
            margin-top: 4px;
        }
        
        .notification-empty {
            padding: 24px;
            text-align: center;
            color: #64748b;
        }
        
        .notification-empty i {
            font-size: 2rem;
            display: block;
            margin-bottom: 8px;
            color: #334155;
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
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(124, 58, 237, 0.15);
        }
        
        .profile-btn:hover {
            background: rgba(124, 58, 237, 0.12);
            border-color: rgba(124, 58, 237, 0.3);
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
            color: #e2e8f0;
        }
        
        .profile-info .role {
            font-size: 0.7rem;
            color: #a78bfa;
            font-weight: 500;
        }
        
        .profile-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            min-width: 220px;
            background: #1e293b;
            border: 1px solid rgba(124, 58, 237, 0.15);
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
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
            padding: 12px 10px;
            color: #cbd5e1;
            text-decoration: none;
            transition: background 0.3s ease;
            font-size: 0.85rem;
        }
        
        .profile-dropdown .dropdown-item:hover {
            background: rgba(124, 58, 237, 0.1);
            color: white;
        }
        
        .profile-dropdown .dropdown-item i {
            width: 20px;
            color: #a78bfa;
        }
        
        .profile-dropdown .dropdown-item.logout {
            color: #f87171;
            border-top: 1px solid rgba(124, 58, 237, 0.1);
        }
        
        .profile-dropdown .dropdown-item.logout i {
            color: #f87171;
        }
        
        .profile-dropdown .dropdown-item.logout:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
        }
        
        .profile-dropdown .dropdown-item .badge {
            margin-left: auto;
            font-size: 0.6rem;
            background: rgba(168, 85, 247, 0.2);
            color: #a78bfa;
            padding: 1px 8px;
            border-radius: 999px;
        }
        
        /* Main Content */
        .librarian-main {
            margin-left: 280px;
            padding: 24px;
            min-height: 100vh;
            background: #020617;
        }
        
        /* Library Cards */
        .library-card {
            background: #111827;
            border: 1px solid #334155;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            color: #f8fafc;
        }
        
        .library-card:hover {
            box-shadow: 0 8px 30px rgba(139, 92, 246, 0.08);
            border-color: rgba(139, 92, 246, 0.2);
        }
        
        .library-stat {
            background: #111827;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 16px 20px;
            color: #f8fafc;
            border-left: 4px solid #a855f7;
        }
        
        .library-stat .number {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #a78bfa, #f472b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .library-stat .label {
            font-size: 0.8rem;
            color: #94a3b8;
            font-weight: 500;
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
            box-shadow: 0 8px 30px rgba(139, 92, 246, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .btn-library-outline {
            background: transparent;
            color: #a78bfa;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            border: 2px solid rgba(139, 92, 246, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-library-outline:hover {
            background: rgba(139, 92, 246, 0.1);
            border-color: #8b5cf6;
            color: #c4b5fd;
            text-decoration: none;
        }
        
        /* Badges */
        .badge-approved {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            padding: 2px 12px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 600;
            border: 1px solid rgba(16, 185, 129, 0.15);
        }
        
        .badge-pending {
            background: rgba(251, 191, 36, 0.15);
            color: #fbbf24;
            padding: 2px 12px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 600;
            border: 1px solid rgba(251, 191, 36, 0.15);
        }
        
        .badge-rejected {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            padding: 2px 12px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 600;
            border: 1px solid rgba(239, 68, 68, 0.15);
        }
        
        /* Responsive */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: #e2e8f0;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 4px;
        }
        
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
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
<body>

<!-- ========================================== -->
<!-- SIDEBAR OVERLAY (Mobile)                   -->
<!-- ========================================== -->
<div id="sidebar-overlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

<!-- ========================================== -->
<!-- SIDEBAR                                    -->
<!-- ========================================== -->
<aside id="librarian-sidebar">
    <!-- Profile Section -->
    <div class="p-4 border-b border-purple-900/20">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-lg font-bold flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->full_name ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white font-semibold text-sm truncate">{{ auth()->user()->full_name ?? 'User' }}</p>
            </div>
        </div>
        <div class="mt-2 flex items-center gap-2 text-xs text-purple-300/50">
            <i class="ti ti-building"></i>
            <span class="truncate text-blue">{{ auth()->user()->institution->name ?? 'No Institution' }}</span>
        </div>
    </div>


    <!-- Navigation -->
<nav class="p-3">
    <p class="text-xs uppercase tracking-wider text-purple-400/50 px-3 py-2 font-semibold">Main Menu</p>
    
    <a href="{{ route('institution.dashboard') }}" class="sidebar-link {{ request()->routeIs('institution.dashboard*') ? 'active' : '' }}">
        <i class="ti ti-dashboard"></i> Dashboard
    </a>
    
    <a href="{{ route('institution.books.index') }}" class="sidebar-link {{ request()->routeIs('institution.books*') ? 'active' : '' }}">
        <i class="ti ti-books"></i> Books Management
    </a>
    
    <a href="{{ route('institution.shelves.index') }}" class="sidebar-link {{ request()->routeIs('institution.shelves*') ? 'active' : '' }}">
        <i class="ti ti-layout-grid"></i> Shelves & Locations
    </a>
    
    <a href="{{ route('institution.members.index') }}" class="sidebar-link {{ request()->routeIs('institution.members*') ? 'active' : '' }}">
        <i class="ti ti-users"></i> Members Directory
    </a>

    <a href="{{ route('institution.borrowings.index') }}" class="sidebar-link {{ request()->routeIs('institution.borrowings*') ? 'active' : '' }}">
        <i class="ti ti-bookmark"></i> Borrowings
    </a>

    <a href="{{ route('institution.join-requests.index') }}" class="sidebar-link {{ request()->routeIs('institution.join-requests*') ? 'active' : '' }}">
        <i class="ti ti-user-plus"></i> Join Requests
        @php
            $pendingJoinRequests = App\Models\JoinRequest::where('institution_id', auth()->user()->institution_id)
                ->where('status', 'pending')
                ->count();
        @endphp
        @if($pendingJoinRequests > 0)
            <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingJoinRequests }}</span>
        @endif
    </a>
    
    <div class="sidebar-divider"></div>
    
    <p class="text-xs uppercase tracking-wider text-purple-400/50 px-3 py-2 font-semibold">Analytics</p>
    
    <a href="{{ route('institution.reports.index') }}" class="sidebar-link {{ request()->routeIs('institution.reports*') ? 'active' : '' }}">
        <i class="ti ti-chart-bar"></i> Reports & Analytics
    </a>
    
    <a href="{{ route('institution.settings') }}" class="sidebar-link {{ request()->routeIs('institution.settings*') ? 'active' : '' }}">
        <i class="ti ti-settings"></i> Settings
    </a>
    
    <div class="sidebar-divider"></div>
    
    <!-- Back to User Dashboard -->
    <a href="{{ route('institution.public.index', auth()->user()->institution_id ?? 1) }}" class="back-to-user">
        <i class="ti ti-arrow-left"></i> Back to User Dashboard
    </a>
</nav>
    <!-- Footer -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-purple-900/20">
        <p class="text-xs text-purple-400/30 text-center">JLIBRARY v1.0</p>
    </div>
</aside>

<!-- ========================================== -->
<!-- MAIN CONTENT                               -->
<!-- ========================================== -->
<div class="librarian-main">
    
    <!-- ========================================== -->
    <!-- TOP BAR                                    -->
    <!-- ========================================== -->
    <header class="librarian-topbar">
        <div class="flex items-center gap-3">
            <button class="mobile-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                <i class="ti ti-menu-2"></i>
            </button>
         <div class="page-title">
    <i class="ti ti-library"></i>
    <span>
        @if(auth()->user()->hasRole('librarian'))
            <span class="text-purple-400">{{ auth()->user()->institution->name ?? 'Library' }}</span>
            <span class="text-slate-500 mx-2">|</span>
            <span>Librarian Panel</span>
        @elseif(auth()->user()->hasRole('institution_admin'))
            <span class="text-purple-400">{{ auth()->user()->institution->name ?? 'Institution' }}</span>
            
            <span>Admin Panel</span>
        @else
            <span class="text-purple-400">{{ auth()->user()->institution->name ?? 'Admin' }}</span>
            <span class="text-slate-500 mx-2">|</span>
            <span>Panel</span>
        @endif
    </span>
</div>
        </div>
        
        <div class="topbar-right">
            <!-- ========================================== -->
            <!-- NOTIFICATION BELL                         -->
            <!-- ========================================== -->
            <div class="relative">
                <button id="notification-bell" class="notification-bell" onclick="toggleNotifications()">
                    <i class="ti ti-bell text-xl"></i>
                    <span id="notification-badge" class="badge hidden">0</span>
                </button>
                
                <!-- Dropdown -->
                <div id="notifications-dropdown" class="notifications-dropdown">
                    <div class="dropdown-header">
                        <h4>🔔 Notifications</h4>
                        <a href="{{ route('notifications.index') }}">View All</a>
                    </div>
                    <div id="notifications-list" class="dropdown-body">
                        <div class="notification-empty">
                            <i class="ti ti-loader-2 animate-spin"></i>
                            Loading...
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ========================================== -->
            <!-- PROFILE DROPDOWN                          -->
            <!-- ========================================== -->
            <div class="relative">
                <button onclick="toggleProfileDropdown()" class="profile-btn">
                    <div class="profile-avatar">
                        {{ strtoupper(substr(auth()->user()->full_name ?? 'L', 0, 1)) }}
                    </div>
                    <div class="profile-info hidden sm:block">
                        <p class="name">{{ auth()->user()->full_name ?? 'User' }}</p>
                        <p class="role">
                            @if(auth()->user()->hasRole('super_admin'))
                                👑 Super Admin
                            @elseif(auth()->user()->hasRole('institution_admin'))
                                🏢 Institution Admin
                            @elseif(auth()->user()->hasRole('librarian'))
                                📚 Librarian
                            @else
                                👤 Member
                            @endif
                        </p>
                    </div>
                    <i id="dropdown-chevron" class="ti ti-chevron-down text-purple-400 text-xs transition-transform duration-200"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div id="profile-dropdown" class="profile-dropdown">
                  
                    
                    <div class="dropdown-divider"></div>
                    
                    <a href="{{ route('institution.public.index', auth()->user()->institution_id ?? 1) }}" class="dropdown-item">
                        <i class="ti ti-arrow-left"></i> Back to User Dashboard
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="dropdown-item logout" style="padding: 0;">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-left" style="color: inherit;">
                            <i class="ti ti-logout"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>
    
    <!-- ========================================== -->
    <!-- PAGE CONTENT                               -->
    <!-- ========================================== -->
    @yield('content')
    
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT                                 -->
<!-- ========================================== -->
<script>
    // Toggle sidebar (mobile)
    function toggleSidebar() {
        const sidebar = document.getElementById('librarian-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    }
    
    // Toggle profile dropdown
    function toggleProfileDropdown() {
        const dropdown = document.getElementById('profile-dropdown');
        const chevron = document.getElementById('dropdown-chevron');
        dropdown.classList.toggle('open');
        if (chevron) {
            chevron.classList.toggle('rotate-180');
        }
    }
    
    // Close profile dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('profile-dropdown');
        const btn = document.querySelector('.profile-btn');
        if (dropdown && btn && !btn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
            const chevron = document.getElementById('dropdown-chevron');
            if (chevron) chevron.classList.remove('rotate-180');
        }
    });
    
    // ==========================================
    // NOTIFICATION BELL
    // ==========================================
    
    const bell = document.getElementById('notification-bell');
    const notifDropdown = document.getElementById('notifications-dropdown');
    const badge = document.getElementById('notification-badge');
    const userId = {{ auth()->id() }};
    
    function toggleNotifications() {
        notifDropdown.classList.toggle('open');
        if (!notifDropdown.classList.contains('open')) {
            return;
        }
        loadNotifications();
    }
    
    function loadNotifications() {
        const list = document.getElementById('notifications-list');
        list.innerHTML = `
            <div class="notification-empty">
                <i class="ti ti-loader-2 animate-spin"></i>
                Loading...
            </div>
        `;
        
        fetch('{{ route("notifications.latest") }}')
            .then(response => response.json())
            .then(data => {
                if (data.notifications.length === 0) {
                    list.innerHTML = `
                        <div class="notification-empty">
                            <i class="ti ti-bell-off"></i>
                            No notifications
                        </div>
                    `;
                    return;
                }
                
                list.innerHTML = data.notifications.map(n => `
                    <a href="${n.link || '#'}" class="notification-item ${n.is_read ? '' : 'unread'}">
                        <div class="flex items-start gap-3">
                            <div class="notification-icon bg-purple-500/20">
                                <i class="ti ${n.icon || 'ti-bell'} text-purple-400 text-sm"></i>
                            </div>
                            <div>
                                <div class="notification-title">${n.title}</div>
                                <div class="notification-message">${n.message}</div>
                                <div class="notification-time">${n.created_at}</div>
                            </div>
                        </div>
                    </a>
                `).join('');
                
                // Update badge
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            })
            .catch(() => {
                list.innerHTML = `
                    <div class="notification-empty">
                        <i class="ti ti-alert-circle"></i>
                        Error loading notifications
                    </div>
                `;
            });
    }
    
    // Load unread count on page load
    fetch('{{ route("notifications.unread-count") }}')
        .then(response => response.json())
        .then(data => {
            if (data.count > 0) {
                badge.textContent = data.count > 9 ? '9+' : data.count;
                badge.classList.remove('hidden');
            }
        });
    
    // Close notification dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (notifDropdown && !notifDropdown.contains(e.target) && !bell.contains(e.target)) {
            notifDropdown.classList.remove('open');
        }
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('librarian-sidebar');
        const toggle = document.querySelector('.mobile-toggle');
        const overlay = document.getElementById('sidebar-overlay');
        if (window.innerWidth <= 1024) {
            if (sidebar && !sidebar.contains(e.target) && !toggle?.contains(e.target)) {
                sidebar.classList.remove('open');
                if (overlay) overlay.classList.remove('active');
            }
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        const sidebar = document.getElementById('librarian-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (window.innerWidth > 1024) {
            sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('active');
        }
    });
</script>

@stack('scripts')
</body>
</html>