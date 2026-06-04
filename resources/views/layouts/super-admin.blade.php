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
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- SUPER ADMIN SIDEBAR -->
        <aside class="w-64 bg-gray-900 text-white flex-shrink-0 overflow-y-auto">
            <div class="p-4 border-b border-gray-800">
                <div class="flex items-center gap-2">
                    <i class="ti ti-crown text-yellow-400 text-2xl"></i>
                    <span class="text-xl font-bold">SUPER ADMIN</span>
                </div>
                <p class="text-xs text-yellow-500 mt-1">Full Platform Control</p>
            </div>
            
            <nav class="p-4 space-y-1">
                <!-- SUPER DASHBOARD -->
                <a href="{{ route('super-admin.dashboard') }}" class="nav-item {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                    <i class="ti ti-crown text-yellow-400 text-xl"></i>
                    <span>Super Dashboard</span>
                </a>

                <!-- SUPER ADMIN BOOKS -->
                <a href="{{ route('super-admin.books.index') }}" class="nav-item {{ request()->routeIs('super-admin.books.*') ? 'active' : '' }}">
                    <i class="ti ti-books text-blue-400 text-xl"></i>
                    <span>Manage Books</span>
                </a>

                <!-- SUPER ADMIN USERS -->
                <a href="{{ route('super-admin.users.index') }}" class="nav-item {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}">
                    <i class="ti ti-users text-cyan-400 text-xl"></i>
                    <span>Manage Users</span>
                </a>

                <!-- SUPER ADMIN INSTITUTIONS -->
                <a href="{{ route('super-admin.institutions.index') }}" class="nav-item {{ request()->routeIs('super-admin.institutions.*') ? 'active' : '' }}">
                    <i class="ti ti-building text-indigo-400 text-xl"></i>
                    <span>Institutions</span>
                </a>

                <!-- SUPER ADMIN MARKETPLACE -->
                <a href="{{ route('super-admin.marketplace.index') }}" class="nav-item {{ request()->routeIs('super-admin.marketplace.*') ? 'active' : '' }}">
                    <i class="ti ti-shopping-cart text-amber-400 text-xl"></i>
                    <span>Marketplace</span>
                </a>

                <!-- SUPER ADMIN APPLICATIONS -->
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

                <!-- SUPER ADMIN PAYMENTS -->
                <a href="{{ route('super-admin.payments.index') }}" class="nav-item {{ request()->routeIs('super-admin.payments.*') ? 'active' : '' }}">
                    <i class="ti ti-wallet text-green-400 text-xl"></i>
                    <span>Payments</span>
                </a>

                <!-- SUPER ADMIN ANALYTICS -->
                <a href="{{ route('super-admin.analytics.index') }}" class="nav-item {{ request()->routeIs('super-admin.analytics.*') ? 'active' : '' }}">
                    <i class="ti ti-chart-bar text-yellow-400 text-xl"></i>
                    <span>Analytics</span>
                </a>

                <hr class="my-3 border-gray-800">

                <!-- Back to User Site -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-800 transition">
                    <i class="ti ti-arrow-left"></i> Back to User Site
                </a>
                
                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-red-400 hover:bg-red-900/20 transition">
                        <i class="ti ti-logout"></i> Logout
                    </button>
                </form>
            </nav>
        </aside>
        
        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto bg-gray-50">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm sticky top-0 z-20 border-b">
                <div class="px-6 py-3 flex justify-between items-center">
                    <h1 class="text-xl font-semibold text-gray-800">@yield('title', 'Super Admin Dashboard')</h1>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-yellow-500 to-red-500 flex items-center justify-center">
                            <i class="ti ti-crown text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">{{ Auth::user()->full_name }}</p>
                            <p class="text-xs text-red-500 font-semibold">Super Admin</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>