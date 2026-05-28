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
        
        #sidebar {
            transition: transform 0.3s ease;
            transform: translateX(0);
            z-index: 100;
        }
        
        @media (max-width: 1024px) {
            #sidebar {
                transform: translateX(-100%);
            }
        }
        
        /* Chevron rotation */
        .rotate-180 {
            transform: rotate(180deg);
        }
        
        /* Notification animation */
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
    </style>
</head>
<body class="bg-gray-100">
    
    <!-- Sidebar for logged in users -->
    @auth
        @include('layouts.sidebar')
    @endauth
    
    <!-- Main Content -->
    <main class="{{ Auth::check() ? 'lg:ml-64' : '' }} min-h-screen">
        @auth
            <!-- Top Bar -->
            <div class="bg-white shadow-sm sticky top-0 z-20">
                <div class="px-4 py-3 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <button id="mobile-menu-toggle" class="lg:hidden text-gray-600 hover:text-purple-600">
                            <i class="ti ti-menu-2 text-2xl"></i>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <!-- ========== NOTIFICATION BELL (NEW) ========== -->
                        @php
                            $unreadNotifications = Auth::check() ? App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count() : 0;
                        @endphp
                        
                        <div class="relative">
                            <button id="notification-bell" class="relative p-2 text-gray-600 hover:text-purple-600 transition rounded-full hover:bg-gray-100">
                                <i class="ti ti-bell text-2xl"></i>
                                @if($unreadNotifications > 0)
                                    <span id="notification-badge" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold">
                                        {{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}
                                    </span>
                                @endif
                            </button>
                            
                            <!-- Notifications Dropdown -->
                            <div id="notifications-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden">
                                <div class="p-3 border-b bg-gradient-to-r from-purple-50 to-pink-50">
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
                    
<!-- Search Bar -->
<div class="hidden md:flex items-center relative">
    <i class="ti ti-search absolute left-3 text-gray-400"></i>
    <input type="text" id="live-search" placeholder="Search books..." 
           class="pl-9 pr-4 py-2 w-64 bg-gray-100 border-0 rounded-full text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
    <div id="live-search-results" class="hidden absolute top-full left-0 mt-2 w-80 bg-white rounded-xl shadow-lg z-50 max-h-96 overflow-y-auto"></div>
</div>

<script>
    // Live search functionality
    const searchInput = document.getElementById('live-search');
    const resultsDiv = document.getElementById('live-search-results');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                resultsDiv.classList.add('hidden');
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch(`/search/live?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            resultsDiv.innerHTML = data.map(book => `
                                <a href="/library/${book.id}" class="flex items-center gap-3 p-3 hover:bg-gray-50 border-b last:border-0 transition">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="ti ti-book text-purple-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">${book.title}</p>
                                        <p class="text-xs text-gray-500">${book.author}</p>
                                    </div>
                                    <i class="ti ti-arrow-right text-gray-400"></i>
                                </a>
                            `).join('');
                            resultsDiv.classList.remove('hidden');
                        } else {
                            resultsDiv.innerHTML = '<div class="p-4 text-center text-gray-500 text-sm">No books found</div>';
                            resultsDiv.classList.remove('hidden');
                        }
                    });
            }, 300);
        });
        
        document.addEventListener('click', function(e) {
            if (!searchInput?.contains(e.target) && !resultsDiv?.contains(e.target)) {
                resultsDiv.classList.add('hidden');
            }
        });
    }
</script>

                        <!-- Profile Button -->
                        <button id="profile-btn" class="flex items-center gap-3 focus:outline-none">
                            <!-- Wallet Badge -->
                            <div class="hidden md:flex items-center gap-1.5 bg-green-50 px-3 py-1.5 rounded-full">
                                <i class="ti ti-wallet text-green-600 text-sm"></i>
                                <span class="text-sm font-semibold text-green-700">TSh {{ number_format(Auth::user()->wallet_balance ?? 0, 2) }}</span>
                            </div>
                            <!-- Avatar -->
                            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center">
                                <i class="ti ti-user text-white text-sm"></i>
                            </div>
                            <span class="hidden md:inline text-sm text-gray-700">{{ Auth::user()->full_name }}</span>
                            <i id="dropdown-chevron" class="ti ti-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
                        </button>
                        
                        <!-- Profile Dropdown Menu -->
                       <!-- Profile Dropdown Menu -->
<div id="profile-dropdown" class="hidden absolute right-0 top-full mt-2 w-72 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden">
    <!-- User Info with Edit Button -->
    <div class="px-4 py-3 bg-gradient-to-r from-purple-50 to-pink-50 border-b">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-semibold text-gray-900">{{ Auth::user()->full_name }}</p>
                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="w-8 h-8 bg-white rounded-lg flex items-center justify-center hover:bg-purple-100 transition shadow-sm">
                <i class="ti ti-edit text-purple-600 text-sm"></i>
            </a>
        </div>
        <div class="flex items-center gap-1.5 mt-2 bg-green-50 px-2 py-1 rounded-full w-fit">
            <i class="ti ti-wallet text-green-600 text-xs"></i>
            <span class="text-xs font-semibold text-green-700">TSh {{ number_format(Auth::user()->wallet_balance ?? 0, 2) }}</span>
        </div>
    </div>

    <!-- Admin Panel (for admin AND super_admin) -->
    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'super_admin')
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-purple-50 border-b transition">
            <i class="ti ti-dashboard text-lg text-purple-600"></i>
            <span class="font-medium text-gray-700">Admin Panel</span>
            @if(Auth::user()->role === 'super_admin')
                <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded ml-auto">Super Admin</span>
            @else
                <span class="text-xs bg-purple-100 text-purple-600 px-2 py-0.5 rounded ml-auto">Admin</span>
            @endif
        </a>
    @endif

    <!-- Wallet -->
    <a href="{{ route('wallet.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition">
        <i class="ti ti-wallet text-lg text-amber-500"></i>
        <span>My Wallet</span>
        <span class="text-xs text-gray-400 ml-auto">TSh {{ number_format(Auth::user()->wallet_balance ?? 0, 2) }}</span>
    </a>

    <!-- Certificates -->
    <a href="{{ route('certificates.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition">
        <i class="ti ti-certificate text-lg text-green-500"></i>
        <span>My Certificates</span>
        <span class="text-xs text-gray-400 ml-auto">{{ Auth::user()->certificates()->count() ?? 0 }}</span>
    </a>

    <!-- Documents -->
    <a href="{{ route('documents.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition">
        <i class="ti ti-file-text text-lg text-blue-500"></i>
        <span>My Documents</span>
    </a>

    <!-- Bookmarks -->
    <a href="{{ route('bookmarks.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition">
        <i class="ti ti-bookmark text-lg text-purple-500"></i>
        <span>My Bookmarks</span>
        <span class="text-xs text-gray-400 ml-auto">{{ Auth::user()->bookmarks()->count() ?? 0 }}</span>
    </a>

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
        @endauth
        
       <div class="{{ Auth::check() ? 'p-4 md:p-6' : '' }}">
    @yield('content')
</div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Profile Dropdown
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

            // Mobile sidebar toggle
            const mobileToggle = document.getElementById('mobile-menu-toggle');
            const sidebar = document.getElementById('sidebar');
            
            if (mobileToggle && sidebar) {
                mobileToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                });
            }
            
            // ========== NOTIFICATION BELL FUNCTIONALITY ==========
            const notificationBell = document.getElementById('notification-bell');
            const notificationsDropdown = document.getElementById('notifications-dropdown');
            
            function loadNotifications() {
                fetch('{{ route("notifications.latest") }}')
                    .then(response => response.json())
                    .then(data => {
                        const list = document.getElementById('notifications-list');
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
                                    ${!notification.is_read ? '<span class="w-2 h-2 bg-purple-600 rounded-full mt-2"></span>' : ''}
                                </div>
                            </div>
                        `).join('');
                        
                        // Update badge count
                        const badge = document.getElementById('notification-badge');
                        if (data.unread_count > 0) {
                            if (badge) {
                                badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                                badge.classList.remove('hidden');
                            } else {
                                const newBadge = document.createElement('span');
                                newBadge.id = 'notification-badge';
                                newBadge.className = 'absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold';
                                newBadge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                                notificationBell.appendChild(newBadge);
                            }
                        } else {
                            if (badge) badge.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                        document.getElementById('notifications-list').innerHTML = `
                            <div class="p-4 text-center text-red-500 text-sm">
                                Error loading notifications
                            </div>
                        `;
                    });
            }
            
            function escapeHtml(text) {
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
            
            // Load unread count on page load
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