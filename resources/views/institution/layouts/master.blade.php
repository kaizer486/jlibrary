<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Institution Panel - {{ auth()->user()->institution->name ?? 'JLIBRARY' }}</title>
    
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
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }
        .nav-item.active i { color: white !important; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- INSTITUTION ADMIN SIDEBAR -->
        <aside class="w-64 bg-gray-900 text-white flex-shrink-0 overflow-y-auto">
            <div class="p-4 border-b border-gray-800">
                <div class="flex items-center gap-2">
                    <i class="ti ti-building text-blue-400 text-2xl"></i>
                    <span class="text-xl font-bold">INSTITUTION</span>
                </div>
                <p class="text-xs text-blue-400 mt-1">{{ auth()->user()->institution->name ?? 'Institution Panel' }}</p>
            </div>
            
            <nav class="p-4 space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('institution.dashboard') }}" class="nav-item {{ request()->routeIs('institution.dashboard') ? 'active' : '' }}">
                    <i class="ti ti-dashboard text-blue-400 text-xl"></i>
                    <span>Dashboard</span>
                </a>

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

                <!-- INSTITUTION QUOTES - SEPARATE -->
                <a href="{{ route('institution.quotes.index') }}" class="nav-item {{ request()->routeIs('institution.quotes.*') ? 'active' : '' }}">
                    <i class="ti ti-quote text-purple-400 text-xl"></i>
                    <span>Institution Quotes</span>
                </a>

                <hr class="my-3 border-gray-800">

                <!-- Back to User Site -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-800 transition">
                    <i class="ti ti-arrow-left"></i> Back to Dashboard
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
                    <h1 class="text-xl font-semibold text-gray-800">@yield('title', 'Institution Dashboard')</h1>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center">
                            <i class="ti ti-building text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">{{ Auth::user()->full_name }}</p>
                            <p class="text-xs text-blue-500 font-semibold">Institution Admin</p>
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