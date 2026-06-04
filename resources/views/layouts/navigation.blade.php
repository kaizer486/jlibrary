<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <!-- Dashboard - Role aware -->
                    @role('super_admin')
                        <x-nav-link :href="url('/super-admin/dashboard')" :active="request()->is('super-admin/dashboard')">
                            {{ __('Super Dashboard') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @endrole

                    <!-- Library - All users see -->
                    <x-nav-link :href="route('library.index')" :active="request()->routeIs('library.*')">
                        {{ __('Library') }}
                    </x-nav-link>

                    <!-- AI Assistant - All users see -->
                    <x-nav-link :href="route('ai.chat')" :active="request()->routeIs('ai.*')">
                        {{ __('AI Assistant') }}
                    </x-nav-link>

                    <!-- Marketplace - All users see -->
                    <x-nav-link :href="route('marketplace.index')" :active="request()->routeIs('marketplace.*')">
                        {{ __('Marketplace') }}
                    </x-nav-link>

                    <!-- Author Dashboard - Only authors -->
                    @role('author')
                        <x-nav-link :href="route('author.dashboard')" :active="request()->routeIs('author.dashboard')">
                            {{ __('Author Dashboard') }}
                        </x-nav-link>
                    @endrole

                    <!-- Instructor Dashboard - Only instructors -->
                    @role('instructor')
                        <x-nav-link :href="route('instructor.dashboard')" :active="request()->routeIs('instructor.dashboard')">
                            {{ __('Instructor Dashboard') }}
                        </x-nav-link>
                    @endrole

                    <!-- Librarian Dashboard - Only librarians -->
                    @role('librarian')
                        <x-nav-link :href="route('librarian.dashboard')" :active="request()->routeIs('librarian.dashboard')">
                            {{ __('Librarian Dashboard') }}
                        </x-nav-link>
                    @endrole

                    <!-- Admin Panel - Admin and Super Admin -->
                    @hasanyrole('admin|super_admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Admin Panel') }}
                        </x-nav-link>
                    @endhasanyrole
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->full_name ?? Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Role Badge -->
                        <div class="px-4 py-2 text-xs text-gray-500 border-b">
                            @role('super_admin')
                                👑 Super Administrator
                            @else
                                @role('admin')
                                    🛡️ Administrator
                                @else
                                    @role('institution_admin')
                                        🏢 Institution Admin
                                    @else
                                        @role('author')
                                            📚 Author
                                        @else
                                            @role('librarian')
                                                📖 Librarian
                                            @else
                                                @role('instructor')
                                                    👨‍🏫 Instructor
                                                @else
                                                    👤 Member
                                                @endrole
                                            @endrole
                                        @endrole
                                    @endrole
                                @endrole
                            @endrole
                        </div>

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('wallet.index')">
                            {{ __('Wallet') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('certificates.index')">
                            {{ __('Certificates') }}
                        </x-dropdown-link>

                        <!-- Admin Panel Link in Dropdown -->
                        @hasanyrole('admin|super_admin')
                            <x-dropdown-link :href="route('admin.dashboard')">
                                {{ __('Admin Panel') }}
                            </x-dropdown-link>
                        @endhasanyrole

                        <!-- Super Admin Dashboard Link -->
                        @role('super_admin')
                            <x-dropdown-link :href="route('super-admin.dashboard')">
                                👑 {{ __('Super Dashboard') }}
                            </x-dropdown-link>
                        @endrole

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('library.index')" :active="request()->routeIs('library.*')">
                {{ __('Library') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('ai.chat')" :active="request()->routeIs('ai.*')">
                {{ __('AI Assistant') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('marketplace.index')" :active="request()->routeIs('marketplace.*')">
                {{ __('Marketplace') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->full_name ?? Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                <div class="text-xs text-gray-400 mt-1">
                    @role('super_admin')
                        👑 Super Administrator
                    @else
                        @role('admin')
                            🛡️ Administrator
                        @else
                            @role('institution_admin')
                                🏢 Institution Admin
                            @else
                                @role('author')
                                    📚 Author
                                @else
                                    @role('librarian')
                                        📖 Librarian
                                    @else
                                        @role('instructor')
                                            👨‍🏫 Instructor
                                        @else
                                            👤 Member
                                        @endrole
                                    @endrole
                                @endrole
                            @endrole
                        @endrole
                    @endrole
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('wallet.index')">
                    {{ __('Wallet') }}
                </x-responsive-nav-link>

                @hasanyrole('admin|super_admin')
                    <x-responsive-nav-link :href="route('admin.dashboard')">
                        {{ __('Admin Panel') }}
                    </x-responsive-nav-link>
                @endhasanyrole

                @role('super_admin')
                    <x-responsive-nav-link :href="route('super-admin.dashboard')">
                        👑 {{ __('Super Dashboard') }}
                    </x-responsive-nav-link>
                @endrole

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>