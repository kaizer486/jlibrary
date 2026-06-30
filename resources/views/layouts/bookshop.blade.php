<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bookshop Panel - JLIBRARY</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-item:hover { background-color: #d97706; color: white; }
        .sidebar-item.active { background-color: #d97706; color: white; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white flex-shrink-0 overflow-y-auto">
            <div class="p-4 border-b border-gray-800">
                <div class="flex items-center gap-2">
                    <i class="ti ti-shopping-cart text-2xl"></i>
                    <span class="text-xl font-bold">JLIBRARY</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Bookshop Panel</p>
            </div>
            
            <nav class="p-4 space-y-1">
                <a href="{{ route('bookshop.books.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('bookshop.books.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="ti ti-books"></i> Books
                </a>
                <a href="{{ route('bookshop.books.create') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition">
                    <i class="ti ti-plus"></i> Add Book
                </a>
                
                <hr class="my-3 border-gray-800">
                
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-800 transition">
                    <i class="ti ti-arrow-left"></i> Back to Dashboard
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
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>