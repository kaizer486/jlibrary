@php
    $layout = 'layouts.app'; 
    
    if (auth()->check()) {
        if (auth()->user()->hasRole('super_admin')) {
            $layout = 'layouts.super-admin';
        } elseif (auth()->user()->hasRole('institution_admin')) {
            $layout = 'layouts.institution';
        } elseif (auth()->user()->hasRole('admin')) {
            $layout = 'layouts.admin';
        } elseif (auth()->user()->hasRole('instructor')) {
            $layout = 'layouts.instructor';
        } elseif (auth()->user()->hasRole('librarian')) {
            $layout = 'layouts.librarian';
        } else {
            $layout = 'layouts.app';
        }
    }
@endphp

@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-2xl p-6 text-white shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <i class="ti ti-shield text-3xl"></i>
                    <h1 class="text-2xl font-bold">Admin Dashboard</h1>
                </div>
                <p class="text-indigo-100">Welcome back, {{ Auth::user()->full_name }}! You have platform management privileges.</p>
            </div>
            <div class="hidden md:block">
                <i class="ti ti-shield-check text-6xl opacity-30"></i>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-purple-500 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Users</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalUsers ?? \App\Models\User::count()) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="ti ti-users text-purple-600 text-2xl"></i>
            </div>
        </div>
        <div class="mt-2 text-xs text-green-600">
            <i class="ti ti-chart-line"></i> +{{ number_format($userGrowth[11] ?? 0) }} this month
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-blue-500 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Institutions</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalInstitutions ?? \App\Models\Institution::count()) }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="ti ti-building text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-green-500 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Books</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalBooks ?? \App\Models\Book::count()) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="ti ti-books text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-yellow-500 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Revenue</p>
                <p class="text-2xl font-bold text-yellow-600">TSh {{ number_format($totalRevenue ?? 0, 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <i class="ti ti-wallet text-yellow-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- PENDING MARKETPLACE LISTINGS CARD -->
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-orange-500 hover:shadow-md transition cursor-pointer" onclick="window.location='{{ route('admin.marketplace.pending') }}'">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Pending Listings</p>
                <p class="text-3xl font-bold text-orange-600">{{ $pendingListings ?? \App\Models\MarketplaceListing::where('status', 'pending')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <i class="ti ti-shopping-cart text-orange-600 text-2xl"></i>
            </div>
        </div>
        <div class="mt-2">
            <a href="{{ route('admin.marketplace.pending') }}" class="text-xs text-purple-600 hover:underline">
                <i class="ti ti-arrow-right"></i> Review Listings
            </a>
        </div>
    </div>
</div>

<!-- Second Row Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-indigo-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Platform Earnings (20%)</p>
                <p class="text-2xl font-bold text-indigo-600">TSh {{ number_format($platformEarnings ?? 0, 2) }}</p>
            </div>
            <i class="ti ti-chart-pie text-indigo-500 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-pink-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Quizzes Taken</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalQuizzes ?? \App\Models\QuizAttempt::count()) }}</p>
            </div>
            <i class="ti ti-brain text-pink-500 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-emerald-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Certificates Issued</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalCertificates ?? \App\Models\Certificate::count()) }}</p>
            </div>
            <i class="ti ti-certificate text-emerald-500 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-cyan-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Active Subscriptions</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($activeSubscriptions ?? 0) }}</p>
            </div>
            <i class="ti ti-subscription text-cyan-500 text-3xl"></i>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- SUBSCRIPTION STATS ROW                     -->
<!-- ========================================== -->
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-purple-500 hover:shadow-md transition">
        <p class="text-gray-500 text-xs">Total Subscriptions</p>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($subscriptionStats['total'] ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-emerald-500 hover:shadow-md transition">
        <p class="text-gray-500 text-xs">Active</p>
        <p class="text-2xl font-bold text-emerald-600">{{ number_format($subscriptionStats['status_counts']['active'] ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-yellow-500 hover:shadow-md transition">
        <p class="text-gray-500 text-xs">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">{{ number_format($subscriptionStats['status_counts']['pending'] ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-red-500 hover:shadow-md transition">
        <p class="text-gray-500 text-xs">Expired</p>
        <p class="text-2xl font-bold text-red-600">{{ number_format($subscriptionStats['status_counts']['expired'] ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-orange-500 hover:shadow-md transition">
        <p class="text-gray-500 text-xs">Expiring Soon</p>
        <p class="text-2xl font-bold text-orange-600">{{ number_format($expiringSoon ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-indigo-500 hover:shadow-md transition">
        <p class="text-gray-500 text-xs">Sub Revenue</p>
        <p class="text-2xl font-bold text-indigo-600">TSh {{ number_format($totalSubscriptionRevenue ?? 0) }}</p>
    </div>
</div>

<!-- ========================================== -->
<!-- PLAN & PAYMENT DISTRIBUTION                -->
<!-- ========================================== -->
<div class="grid lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 flex items-center gap-2 mb-4">
            <i class="ti ti-chart-pie text-purple-600"></i> Plan Distribution
        </h3>
        @if(!empty($planDistribution) && array_sum($planDistribution) > 0)
            <div class="space-y-3">
                @foreach(['basic', 'premium', 'enterprise'] as $plan)
                    @php
                        $count = $planDistribution[$plan] ?? 0;
                        $total = array_sum($planDistribution);
                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700 capitalize">{{ $plan }}</span>
                            <span class="text-gray-500">{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-500 
                                @if($plan === 'enterprise') bg-purple-500
                                @elseif($plan === 'premium') bg-blue-500
                                @else bg-gray-500 @endif" 
                                style="width: {{ $percentage }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="ti ti-chart-pie text-3xl block mb-2"></i>
                <p>No subscription data available</p>
            </div>
        @endif
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 flex items-center gap-2 mb-4">
            <i class="ti ti-credit-card text-purple-600"></i> Payment Methods
        </h3>
        @if(!empty($paymentMethodDistribution) && array_sum($paymentMethodDistribution) > 0)
            <div class="space-y-3">
                @foreach(['mpesa', 'tigopesa', 'halopesa', 'pesapal', 'bank'] as $method)
                    @php
                        $count = $paymentMethodDistribution[$method] ?? 0;
                        $total = array_sum($paymentMethodDistribution);
                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700 capitalize">{{ $method }}</span>
                            <span class="text-gray-500">{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-500 bg-emerald-500" 
                                style="width: {{ $percentage }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="ti ti-credit-card text-3xl block mb-2"></i>
                <p>No payment data available</p>
            </div>
        @endif
    </div>
</div>

<!-- Charts Row -->
<div class="grid lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="ti ti-chart-line text-purple-600"></i>
                Revenue Overview ({{ date('Y') }})
            </h2>
        </div>
        <div style="height: 250px; position: relative;">
            <canvas id="revenueChart"></canvas>
        </div>
        @if(array_sum($monthlyRevenue ?? []) == 0)
            <div class="text-center text-gray-400 text-sm mt-2">
                <i class="ti ti-info-circle"></i> No revenue data available yet
            </div>
        @endif
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="ti ti-users text-purple-600"></i>
                User Growth ({{ date('Y') }})
            </h2>
        </div>
        <div style="height: 250px; position: relative;">
            <canvas id="userChart"></canvas>
        </div>
        @if(array_sum($userGrowth ?? []) == 0)
            <div class="text-center text-gray-400 text-sm mt-2">
                <i class="ti ti-info-circle"></i> No user data available yet
            </div>
        @endif
    </div>
</div>

<!-- ========================================== -->
<!-- RECENT SUBSCRIPTIONS                       -->
<!-- ========================================== -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
            <i class="ti ti-subscription text-purple-600"></i> Recent Subscriptions
        </h3>
        <a href="{{ route('admin.institutions.index') }}" class="text-sm text-purple-600 hover:text-purple-700">
            View All →
        </a>
    </div>
    <div class="divide-y divide-gray-200">
        @forelse($recentSubscriptions ?? [] as $sub)
        <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition">
            <div>
                <p class="text-gray-800 font-medium">{{ $sub->institution->name ?? 'Unknown' }}</p>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <span class="capitalize">{{ $sub->plan ?? 'N/A' }}</span>
                    <span>•</span>
                    <span>TSh {{ number_format($sub->amount ?? 0, 0) }}</span>
                    <span>•</span>
                    <span class="capitalize">{{ $sub->payment_method ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="text-right">
                <span class="px-2 py-1 rounded-full text-xs font-semibold
                    @if($sub->status === 'active') bg-green-100 text-green-700
                    @elseif($sub->status === 'pending') bg-yellow-100 text-yellow-700
                    @elseif($sub->status === 'expired') bg-red-100 text-red-700
                    @else bg-gray-100 text-gray-700 @endif">
                    {{ ucfirst($sub->status ?? 'N/A') }}
                </span>
                <p class="text-xs text-gray-400 mt-1">{{ $sub->created_at ? $sub->created_at->diffForHumans() : 'N/A' }}</p>
            </div>
        </div>
        @empty
        <div class="px-6 py-8 text-center text-gray-500">
            <i class="ti ti-subscription text-3xl block mb-2"></i>
            No recent subscriptions
        </div>
        @endforelse
    </div>
</div>

<!-- Recent Activity -->
<div class="grid lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">👥 Recent Users</h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-purple-600 hover:underline">View All →</a>
        </div>
        <div class="space-y-3">
            @forelse($recentUsers ?? \App\Models\User::latest()->limit(5)->get() as $user)
            <div class="flex items-center justify-between py-2 border-b last:border-0">
                <div>
                    <p class="font-medium text-gray-800">{{ $user->full_name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-500">{{ $user->email ?? 'No email' }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $user->hasRole('admin') ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                    {{ $user->getRoleLabel() ?? 'User' }}
                </span>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">No users yet</p>
            @endforelse
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">🏢 Recent Institutions</h2>
            <a href="{{ route('admin.institutions.index') }}" class="text-sm text-purple-600 hover:underline">View All →</a>
        </div>
        <div class="space-y-3">
            @forelse($recentInstitutions ?? \App\Models\Institution::latest()->limit(5)->get() as $inst)
            <div class="flex items-center justify-between py-2 border-b last:border-0">
                <div>
                    <p class="font-medium text-gray-800">{{ Str::limit($inst->name ?? 'Unknown', 30) }}</p>
                    <p class="text-xs text-gray-500">{{ $inst->type_label ?? $inst->type ?? 'N/A' }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ ($inst->status ?? '') === 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ ucfirst($inst->status ?? 'N/A') }}
                </span>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">No institutions yet</p>
            @endforelse
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">📚 Recent Books</h2>
            <a href="{{ route('admin.books.index') }}" class="text-sm text-purple-600 hover:underline">View All →</a>
        </div>
        <div class="space-y-3">
            @forelse($recentBooks ?? \App\Models\Book::latest()->limit(5)->get() as $book)
            <div class="flex items-center justify-between py-2 border-b last:border-0">
                <div>
                    <p class="font-medium text-gray-800">{{ Str::limit($book->title ?? 'Unknown', 30) }}</p>
                    <p class="text-xs text-gray-500">by {{ $book->author ?? 'Unknown' }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ ($book->status ?? '') === 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ ucfirst($book->status ?? 'N/A') }}
                </span>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">No books yet</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-8 grid md:grid-cols-5 gap-4">
    <a href="{{ route('admin.books.create') }}" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl p-3 text-center hover:shadow-lg transition text-sm">
        <i class="ti ti-plus"></i> Add Book
    </a>
    <a href="{{ route('admin.institutions.create') }}" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl p-3 text-center hover:shadow-lg transition text-sm">
        <i class="ti ti-building-plus"></i> Add Institution
    </a>
    <a href="{{ route('admin.users.create') }}" class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl p-3 text-center hover:shadow-lg transition text-sm">
        <i class="ti ti-user-plus"></i> Add User
    </a>
    <a href="{{ route('admin.applications.index') }}" class="bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl p-3 text-center hover:shadow-lg transition text-sm">
        <i class="ti ti-files"></i> Applications
    </a>
    <a href="{{ route('admin.marketplace.pending') }}" class="bg-gradient-to-r from-amber-600 to-yellow-600 text-white rounded-xl p-3 text-center hover:shadow-lg transition text-sm">
        <i class="ti ti-shopping-cart"></i> Review Listings
    </a>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the data from the controller
        const months = {!! json_encode($months ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']) !!};
        const monthlyRevenue = {!! json_encode($monthlyRevenue ?? array_fill(0, 12, 0)) !!};
        const userGrowth = {!! json_encode($userGrowth ?? array_fill(0, 12, 0)) !!};
        
        // Check if there is any data
        const hasRevenueData = monthlyRevenue.some(value => value > 0);
        const hasUserData = userGrowth.some(value => value > 0);
        
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Revenue (TSh)',
                        data: monthlyRevenue,
                        borderColor: '#8b5cf6',
                        backgroundColor: hasRevenueData ? 'rgba(139, 92, 246, 0.1)' : 'rgba(200, 200, 200, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: hasRevenueData ? '#8b5cf6' : '#d1d5db',
                        pointBorderColor: hasRevenueData ? '#8b5cf6' : '#d1d5db',
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'TSh ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // User Growth Chart
        const userCtx = document.getElementById('userChart');
        if (userCtx) {
            new Chart(userCtx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'New Users',
                        data: userGrowth,
                        backgroundColor: hasUserData ? '#a78bfa' : '#d1d5db',
                        borderRadius: 8,
                        borderColor: hasUserData ? '#8b5cf6' : '#9ca3af',
                        borderWidth: 1,
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                callback: function(value) {
                                    return Math.round(value);
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection