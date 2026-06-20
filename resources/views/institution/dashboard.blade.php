@php
    $layout = 'layouts.app';
    
    if (auth()->check()) {
        if (auth()->user()->role === 'superadmin' || auth()->user()->role === 'super_admin') {
            $layout = 'layouts.super-admin';
        } elseif (auth()->user()->role === 'institution_admin') {
            $layout = 'layouts.institution';
        } elseif (auth()->user()->role === 'admin') {
            $layout = 'layouts.admin';
        } elseif (auth()->user()->role === 'instructor') {
            $layout = 'layouts.instructor';
        } elseif (auth()->user()->role === 'librarian') {
            $layout = 'layouts.librarian';
        } else {
            $layout = 'layouts.app';
        }
    }
@endphp

@extends($layout)

@section('content')

@php
    // ==========================================
    // SECURITY CHECKS
    // ==========================================
    
    if (!auth()->user()->institution_id) {
        abort(403, 'You do not belong to any institution.');
    }
    
    if (!isset($institution) || !$institution) {
        abort(404, 'Institution not found.');
    }
    
    if (auth()->user()->institution_id != $institution->id) {
        abort(403, 'You do not have access to this institution.');
    }
@endphp

<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white">Institution Dashboard</h1>
            <div class="w-20 h-1 bg-yellow-400 rounded-full mt-2"></div>
            <p class="text-gray-300 mt-2">Welcome back, {{ Auth::user()->full_name }}</p>
        </div>
        
        <!-- ========================================== -->
        <!-- INSTITUTION STATUS BANNER                  -->
        <!-- ========================================== -->
        @if($institution->status === 'pending')
            <div class="bg-yellow-100 border-l-4 border-yellow-500 rounded-xl p-4 mb-8 shadow-md">
                <div class="flex items-center gap-3">
                    <i class="ti ti-clock text-yellow-600 text-xl"></i>
                    <div>
                        <p class="font-semibold text-yellow-800">⏳ Institution Pending Approval</p>
                        <p class="text-sm text-yellow-700">Your institution is awaiting approval from Super Admin. Some features may be limited.</p>
                    </div>
                </div>
            </div>
        @elseif($institution->status === 'suspended')
            <div class="bg-red-100 border-l-4 border-red-500 rounded-xl p-4 mb-8 shadow-md">
                <div class="flex items-center gap-3">
                    <i class="ti ti-alert-circle text-red-600 text-xl"></i>
                    <div>
                        <p class="font-semibold text-red-800">🚫 Institution Suspended</p>
                        <p class="text-sm text-red-700">Your institution has been suspended. Please contact Super Admin for more information.</p>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Institution Info Card -->
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl p-6 mb-8 text-white shadow-xl">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                    <i class="ti ti-building text-3xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold">{{ $institution->name }}</h2>
                    <p class="text-indigo-100">{{ $institution->city ?? 'Location not set' }} | {{ $institution->type_label ?? 'Institution' }}</p>
                    <p class="text-indigo-100 text-sm mt-1">Email: {{ $institution->email ?? 'Not set' }}</p>
                </div>
                <div class="ml-auto">
                    <span class="bg-white/20 px-4 py-2 rounded-full text-sm flex items-center gap-2">
                        <i class="ti ti-badge"></i>
                        {{ auth()->user()->getInstitutionRole() ?? 'Member' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- ADMIN ACTIONS (Only for Institution Admins) -->
        <!-- ========================================== -->
        @if(auth()->user()->isInstitutionAdmin())
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-5 mb-8 border border-purple-200 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <i class="ti ti-settings text-purple-600"></i> Admin Actions
                        </h3>
                        <p class="text-sm text-gray-500">Manage your institution resources</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('institution.members.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition shadow-md flex items-center gap-2">
                            <i class="ti ti-user-plus"></i> Add Member
                        </a>
                        <a href="{{ route('institution.books.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition shadow-md flex items-center gap-2">
                            <i class="ti ti-book-plus"></i> Add Book
                        </a>
                        <a href="{{ route('institution.quotes.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition shadow-md flex items-center gap-2">
                            <i class="ti ti-quote"></i> Add Quote
                        </a>
                        <a href="{{ route('institution.join-requests.index') }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-700 transition shadow-md flex items-center gap-2">
                            <i class="ti ti-user-check"></i> Join Requests
                            @if(($stats['pending_requests'] ?? 0) > 0)
                                <span class="bg-white text-yellow-600 text-xs font-bold rounded-full px-2 py-0.5">
                                    {{ $stats['pending_requests'] ?? 0 }}
                                </span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl p-5 shadow-md hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Members</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['total_members'] ?? 0 }}</p>
                    </div>
                    <i class="ti ti-users text-indigo-500 text-3xl"></i>
                </div>
                <a href="{{ route('institution.members.index') }}" class="text-sm text-purple-600 mt-2 inline-block hover:underline">Manage Members →</a>
            </div>
            
            <div class="bg-white rounded-xl p-5 shadow-md hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Books</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['total_books'] ?? 0 }}</p>
                    </div>
                    <i class="ti ti-books text-indigo-500 text-3xl"></i>
                </div>
                <a href="{{ route('institution.books.index') }}" class="text-sm text-purple-600 mt-2 inline-block hover:underline">Manage Books →</a>
            </div>
            
            <div class="bg-white rounded-xl p-5 shadow-md hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Institution Admins</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['total_admins'] ?? 0 }}</p>
                    </div>
                    <i class="ti ti-shield text-purple-500 text-3xl"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-5 shadow-md hover:shadow-lg transition border-l-4 border-yellow-500">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-gray-500 text-sm">Pending Join Requests</p>
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_requests'] ?? 0 }}</p>
        </div>
        <i class="ti ti-user-plus text-yellow-500 text-3xl"></i>
    </div>
    @if(($stats['pending_requests'] ?? 0) > 0)
        <a href="{{ route('institution.join-requests.index') }}" class="text-sm text-purple-600 mt-2 inline-block hover:underline">Review Requests →</a>
    @endif
</div>
            <div class="bg-white rounded-xl p-5 shadow-md hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Librarians</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['total_librarians'] ?? 0 }}</p>
                    </div>
                    <i class="ti ti-book text-blue-500 text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Wallet & Pending Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-5 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Wallet Balance</p>
                        <p class="text-2xl font-bold text-green-600">TSh {{ number_format($stats['wallet_balance'] ?? 0, 2) }}</p>
                    </div>
                    <i class="ti ti-wallet text-green-500 text-3xl"></i>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-5 border border-orange-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Pending Withdrawals</p>
                        <p class="text-2xl font-bold text-orange-600">TSh {{ number_format($stats['pending_withdrawal_requests'] ?? 0, 2) }}</p>
                    </div>
                    <i class="ti ti-clock text-orange-500 text-3xl"></i>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl p-5 border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Pending Join Requests</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_requests'] ?? 0 }}</p>
                    </div>
                    <i class="ti ti-clock text-yellow-500 text-3xl"></i>
                </div>
                @if(($stats['pending_requests'] ?? 0) > 0)
                    <a href="{{ route('institution.join-requests.index') }}" class="text-sm text-yellow-600 mt-2 inline-block hover:underline">Review Requests →</a>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-5 mb-8 border border-purple-200">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="font-semibold text-gray-800">Quick Actions</h3>
                    <p class="text-sm text-gray-500">Manage your institution resources</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    @can('create', App\Models\User::class)
                        <a href="{{ route('institution.members.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition">
                            <i class="ti ti-user-plus"></i> Add Member
                        </a>
                    @endcan
                    @can('create', App\Models\WithdrawalRequest::class)
                        <a href="{{ route('institution.withdrawals.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                            <i class="ti ti-wallet"></i> Request Withdrawal
                        </a>
                    @endcan
                    @can('create', App\Models\Book::class)
                        <a href="{{ route('institution.books.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700 transition">
                            <i class="ti ti-book"></i> Add Book
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Members -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-5 py-4 border-b bg-gradient-to-r from-indigo-50 to-purple-50">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ti ti-users text-indigo-600"></i>
                        Recently Joined Members
                    </h3>
                </div>
                <div class="divide-y">
                    @forelse($recentMembers ?? [] as $member)
                    <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="ti ti-user text-purple-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $member->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $member->email }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400">{{ $member->created_at->diffForHumans() }}</span>
                    </div>
                    @empty
                    <div class="px-5 py-8 text-center text-gray-500">
                        <i class="ti ti-users text-3xl mb-2 block"></i>
                        No members yet
                    </div>
                    @endforelse
                </div>
                <div class="px-5 py-3 text-center border-t">
                    <a href="{{ route('institution.members.index') }}" class="text-purple-600 text-sm hover:underline">View All Members →</a>
                </div>
            </div>
            
            <!-- Recent Books -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-5 py-4 border-b bg-gradient-to-r from-indigo-50 to-purple-50">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ti ti-books text-indigo-600"></i>
                        Recently Added Books
                    </h3>
                </div>
                <div class="divide-y">
                    @forelse($recentBooks ?? [] as $book)
                    <div class="px-5 py-3 hover:bg-gray-50">
                        <p class="font-medium text-gray-800">{{ $book->title }}</p>
                        <p class="text-xs text-gray-500">Added {{ $book->created_at->diffForHumans() }}</p>
                    </div>
                    @empty
                    <div class="px-5 py-8 text-center text-gray-500">
                        <i class="ti ti-books text-3xl mb-2 block"></i>
                        No books added yet
                    </div>
                    @endforelse
                </div>
                <div class="px-5 py-3 text-center border-t">
                    <a href="{{ route('institution.books.index') }}" class="text-purple-600 text-sm hover:underline">View All Books →</a>
                </div>
            </div>
        </div>

        <!-- Recent Join Requests -->
        @if(isset($recentRequests) && $recentRequests->count() > 0)
        <div class="mt-6 bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-5 py-4 border-b bg-gradient-to-r from-yellow-50 to-amber-50">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="ti ti-clock text-yellow-600"></i>
                    Pending Join Requests
                </h3>
            </div>
            <div class="divide-y">
                @foreach($recentRequests as $request)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="ti ti-user text-yellow-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $request->user->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $request->user->email }}</p>
                            @if($request->message)
                                <p class="text-xs text-gray-400 mt-1">"{{ Str::limit($request->message, 50) }}"</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                            Pending
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            @if(($stats['pending_requests'] ?? 0) > 5)
            <div class="px-5 py-3 text-center border-t">
                <a href="{{ route('institution.join-requests.index') }}" class="text-purple-600 text-sm hover:underline">View all {{ $stats['pending_requests'] ?? 0 }} requests →</a>
            </div>
            @endif
        </div>
        @endif

        <!-- Back to Top -->
        <div class="text-center py-6">
            <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
                    class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-full transition">
                <i class="ti ti-arrow-up"></i> Back to Top
            </button>
        </div>
    </div>
</div>
@endsection