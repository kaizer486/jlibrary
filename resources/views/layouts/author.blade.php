<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Author Dashboard') - JLIBRARY</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f3f4f6; min-height: 100vh; }
        
        /* Sidebar */
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
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
        }
        
        #sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 999;
            backdrop-filter: blur(4px);
        }
        #sidebar-overlay.active { display: block; }
        
        /* Author specific styles */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .btn-author {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            color: white;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-author:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(124, 58, 237, 0.35);
        }
        
        .badge-draft { background: #9ca3af; color: white; }
        .badge-published { background: #10B981; color: white; }
        .badge-pending { background: #F59E0B; color: white; }
        .badge-rejected { background: #EF4444; color: white; }
        
        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c4b5fd; border-radius: 10px; }
    </style>
</head>
<body>
    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay"></div>
    
    <!-- Sidebar -->
    @include('layouts.author-sidebar')
    
    <!-- Main Content -->
    <main class="lg:ml-[280px] min-h-screen">
        <!-- Top Bar -->
        <div class="bg-white shadow-sm sticky top-0 z-50">
            <div class="px-4 py-3 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <button id="mobile-menu-toggle" class="lg:hidden text-gray-600 hover:text-purple-600">
                        <i class="ti ti-menu-2 text-2xl"></i>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Author Dashboard')</h1>
                </div>
                
                <div class="flex items-center gap-3">
                    <!-- Quick Actions -->
                    <a href="{{ route('author.books.create') }}" class="hidden sm:flex items-center gap-1 bg-purple-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-purple-700 transition">
                        <i class="ti ti-plus"></i>
                        <span>New Book</span>
                    </a>
                    
                    <!-- Profile -->
                    <div class="relative">
                        <button id="profile-btn" class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center overflow-hidden">
                                @if(Auth::user()->avatar)
                                    <img src="{{ url('media/' . Auth::user()->avatar) }}" class="w-full h-full object-cover">
                                @else
                                    <i class="ti ti-user text-white text-sm"></i>
                                @endif
                            </div>
                            <span class="hidden md:inline text-sm text-gray-700">{{ Auth::user()->full_name }}</span>
                            <i class="ti ti-chevron-down text-gray-400 text-xs hidden md:inline"></i>
                        </button>
                        
                        <div id="profile-dropdown" class="hidden absolute right-0 top-full mt-2 w-64 bg-white rounded-xl shadow-xl border z-50">
                            <div class="p-4 border-b">
                                <p class="font-semibold">{{ Auth::user()->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                <span class="inline-block mt-1 text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">✍️ Author</span>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50">
                                <i class="ti ti-user text-gray-500"></i>
                                <span>Profile</span>
                            </a>
                           <a href="{{ route('author.earnings') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50"></a>
                                <i class="ti ti-coin text-gray-500"></i>
                               <span>Earnings</span>
                            </a>
                            <a href="{{ route('author.withdrawals.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50">
                                <i class="ti ti-wallet text-gray-500"></i>
                                <span>Withdrawals</span>
                            </a>
                            <hr>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-red-600 hover:bg-red-50">
                                    <i class="ti ti-logout"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Page Content -->
        <div class="p-4 md:p-6">
            @yield('content')
        </div>
    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const openBtn = document.getElementById('mobile-menu-toggle');
            const closeBtn = document.getElementById('close-sidebar');
            
            function openSidebar() {
                if (sidebar) sidebar.classList.add('open');
                if (overlay) overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            
            function closeSidebar() {
                if (sidebar) sidebar.classList.remove('open');
                if (overlay) overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
            
            if (openBtn) openBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (overlay) overlay.addEventListener('click', closeSidebar);
            
            // Profile Dropdown
            const profileBtn = document.getElementById('profile-btn');
            const profileDropdown = document.getElementById('profile-dropdown');
            
            if (profileBtn && profileDropdown) {
                profileBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('hidden');
                });
                
                document.addEventListener('click', function() {
                    profileDropdown.classList.add('hidden');
                });
                
                profileDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>