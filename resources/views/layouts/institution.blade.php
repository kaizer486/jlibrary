<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - JLIBRARY</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-item:hover { background-color: #7c3aed; color: white; }
        .sidebar-item.active { background-color: #7c3aed; color: white; }
        .search-results {
            position: absolute;
            top: 100%;
            right: 0;
            width: 320px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            z-index: 50;
            max-height: 400px;
            overflow-y: auto;
            margin-top: 8px;
        }
        .search-results a:hover { background-color: #f3f4f6; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white flex-shrink-0 overflow-y-auto">
            <div class="p-4 border-b border-gray-800">
                <div class="flex items-center gap-2">
                    <i class="ti ti-book text-2xl"></i>
                    <span class="text-xl font-bold">JLIBRARY</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Admin Panel</p>
              @if(auth()->check() && auth()->user()->hasRole('super_admin'))
                    <div class="mt-2 inline-block bg-red-600/20 text-red-400 text-xs px-2 py-0.5 rounded-full">
                        👑 Super Admin Access
                    </div>
                @endif
            </div>
            
           
            <nav class="p-4 space-y-1">
  <!-- ========================================== -->
<!-- INSTITUTION ADMIN MENU (only for institution_admin) -->
<!-- ========================================== -->
@hasrole('institution_admin')
    <div class="px-3 mt-4 mb-2">
        <p class="text-xs text-gray-500 uppercase tracking-wider">Institution</p>
    </div>
    <a href="{{ route('institution.dashboard') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('institution.dashboard') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
        <i class="ti ti-dashboard"></i> Dashboard
    </a>
    <a href="{{ route('institution.members.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('institution.members.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
        <i class="ti ti-users"></i> Members
    </a>
    <a href="{{ route('institution.books.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition">
        <i class="ti ti-books"></i> Books
    </a>
    <a href="{{ route('institution.withdrawals.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition">
        <i class="ti ti-wallet"></i> Withdrawals
    </a>
    <!-- Institution Quotes - CORRECT ROUTE -->
    <a href="{{ route('institution.quotes.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('institution.quotes.*') ? 'active' : 'text-gray-300 hover:bg-gray-800' }}">
        <i class="ti ti-quote"></i> Institution Quotes
    </a>
@endhasrole


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
        
        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto bg-gray-50">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm sticky top-0 z-20 border-b">
                <div class="flex items-center justify-between px-6 py-3">
                    <div>
                        <h1 class="text-gray-800 text-xl font-semibold">@yield('title', 'Admin Dashboard')</h1>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <!-- Search Bar -->
                        <div class="relative">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="ti ti-search text-gray-400 text-sm"></i>
                                </div>
                                <input type="text" id="admin-search" 
                                       placeholder="Search users, books..." 
                                       class="w-80 pl-9 pr-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg border border-gray-200 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 placeholder-gray-400">
                                
                                <div id="search-results" class="search-results hidden">
                                    <div class="p-2">
                                        <div class="text-xs text-gray-500 px-3 py-2">Loading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Admin Info -->
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-purple-600 flex items-center justify-center">
                                <i class="ti ti-user text-white text-sm"></i>
                            </div>
                            <div class="hidden md:block">
                                <p class="text-sm font-medium text-gray-700">{{ Auth::user()->full_name }}</p>
                                <p class="text-xs">
                                    @if(auth()->user()->hasRole('super_admin'))
                                        <span class="text-red-500 font-semibold">👑 Super Admin</span>
                                    @elseif(auth()->user()->hasRole('institution_admin'))
                                        <span class="text-blue-500 font-semibold">🏢 Institution Admin</span>
                                    @else
                                        <span class="text-gray-400">Administrator</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Page Content -->
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        const searchInput = document.getElementById('admin-search');
        const resultsDiv = document.getElementById('search-results');
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
                    fetch(`/admin/search?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.users.length === 0 && data.books.length === 0) {
                                resultsDiv.innerHTML = `<div class="p-4 text-center text-gray-500 text-sm">No results found for "${query}"</div>`;
                                resultsDiv.classList.remove('hidden');
                                return;
                            }
                            
                            let html = '';
                            
                            if (data.users.length > 0) {
                                html += '<div class="px-3 py-2 text-xs font-semibold text-gray-500 border-b">USERS</div>';
                                data.users.forEach(user => {
                                    html += `<a href="/admin/users/${user.id}/edit" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 transition border-b last:border-0">
                                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center"><i class="ti ti-user text-purple-600 text-sm"></i></div>
                                        <div><p class="text-sm font-medium text-gray-800">${user.full_name}</p><p class="text-xs text-gray-500">${user.email}</p></div>
                                    </a>`;
                                });
                            }
                            
                            if (data.books.length > 0) {
                                html += '<div class="px-3 py-2 text-xs font-semibold text-gray-500 border-b">BOOKS</div>';
                                data.books.forEach(book => {
                                    html += `<a href="/admin/books/${book.id}/edit" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 transition border-b last:border-0">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center"><i class="ti ti-book text-blue-600 text-sm"></i></div>
                                        <div><p class="text-sm font-medium text-gray-800">${book.title}</p><p class="text-xs text-gray-500">by ${book.author}</p></div>
                                    </a>`;
                                });
                            }
                            
                            resultsDiv.innerHTML = html;
                            resultsDiv.classList.remove('hidden');
                        });
                }, 300);
            });
            
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
                    resultsDiv.classList.add('hidden');
                }
            });
        }
    </script>
    
    @stack('scripts')
</body>
</html>