<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Librarian Panel - JLIBRARY</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        .sidebar-item:hover { background-color: #7c3aed; color: white; }
        .sidebar-item.active { background-color: #7c3aed; color: white; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Librarian Sidebar -->
        <aside class="w-64 bg-gray-900 text-white flex-shrink-0 overflow-y-auto">
            <div class="p-4 border-b border-gray-800">
                <div class="flex items-center gap-2">
                    <i class="ti ti-book text-2xl"></i>
                    <span class="text-xl font-bold">JLIBRARY</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Librarian Panel</p>
            </div>
            
            <nav class="p-4 space-y-1">
                <a href="{{ route('librarian.dashboard') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('librarian.dashboard') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="ti ti-dashboard"></i> Dashboard
                </a>
                <a href="{{ route('admin.books.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.books.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="ti ti-books"></i> Manage Books
                </a>
                
                <hr class="my-3 border-gray-800">
                
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-800 transition">
                    <i class="ti ti-arrow-left"></i> Back to Site
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-red-400 hover:bg-red-900/20 transition">
                        <i class="ti ti-logout"></i> Logout
                    </button>
                </form>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50">
            <div class="bg-white shadow-sm sticky top-0 z-20 border-b">
                <div class="px-6 py-3">
                    <div class="flex justify-between items-center">
                        <h1 class="text-xl font-semibold text-gray-800">@yield('title', 'Librarian Dashboard')</h1>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 flex items-center justify-center">
                                <i class="ti ti-book text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ Auth::user()->full_name }}</p>
                                <p class="text-xs text-blue-500 font-semibold">Librarian</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>