@extends('layouts.super-admin')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-yellow-500 via-orange-500 to-red-500 rounded-2xl p-6 text-white shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <i class="ti ti-crown text-3xl"></i>
                    <h1 class="text-2xl font-bold">Super Admin Dashboard</h1>
                </div>
                <p class="text-yellow-100">Welcome back, {{ Auth::user()->full_name }}! You have full platform control.</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-yellow-100">Platform Version</p>
                <p class="text-xl font-bold">v2.0.0</p>
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
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalUsers) }}</p>
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
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalInstitutions) }}</p>
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
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalBooks) }}</p>
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
                <p class="text-2xl font-bold text-yellow-600">TSh {{ number_format($totalRevenue, 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <i class="ti ti-wallet text-yellow-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- PENDING INSTITUTION REQUESTS CARD         -->
    <!-- ========================================== -->
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-orange-500 hover:shadow-md transition cursor-pointer" onclick="window.location='{{ route('super-admin.institution-requests.index') }}'">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Pending Institution Requests</p>
                <p class="text-3xl font-bold text-orange-600">{{ $pendingRequests ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <i class="ti ti-file-plus text-orange-600 text-2xl"></i>
            </div>
        </div>
        <div class="mt-2">
            <a href="{{ route('super-admin.institution-requests.index') }}" class="text-xs text-purple-600 hover:underline">
                <i class="ti ti-arrow-right"></i> Manage Requests
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
                <p class="text-2xl font-bold text-indigo-600">TSh {{ number_format($platformEarnings, 2) }}</p>
            </div>
            <i class="ti ti-chart-pie text-indigo-500 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-pink-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Quizzes Taken</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalQuizzes) }}</p>
            </div>
            <i class="ti ti-brain text-pink-500 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-emerald-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Certificates Issued</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalCertificates) }}</p>
            </div>
            <i class="ti ti-certificate text-emerald-500 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Pending Withdrawals</p>
                <p class="text-2xl font-bold text-red-600">TSh {{ number_format($pendingWithdrawals, 2) }}</p>
            </div>
            <i class="ti ti-clock text-red-500 text-3xl"></i>
        </div>
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
        <canvas id="revenueChart" height="250"></canvas>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="ti ti-users text-purple-600"></i>
                User Growth ({{ date('Y') }})
            </h2>
        </div>
        <canvas id="userChart" height="250"></canvas>
    </div>
</div>

<!-- Recent Activity -->
<div class="grid lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">👥 Recent Users</h2>
            <a href="{{ route('super-admin.users.index') }}" class="text-sm text-purple-600 hover:underline">View All →</a>
        </div>
        <div class="space-y-3">
            @forelse($recentUsers as $user)
            <div class="flex items-center justify-between py-2 border-b last:border-0">
                <div>
                    <p class="font-medium text-gray-800">{{ $user->full_name }}</p>
                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $user->isSuperAdmin() ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700' }}">
                    {{ $user->getRoleLabel() }}
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
            <a href="{{ route('super-admin.institutions.index') }}" class="text-sm text-purple-600 hover:underline">View All →</a>
        </div>
        <div class="space-y-3">
            @forelse($recentInstitutions as $inst)
            <div class="flex items-center justify-between py-2 border-b last:border-0">
                <div>
                    <p class="font-medium text-gray-800">{{ Str::limit($inst->name, 30) }}</p>
                    <p class="text-xs text-gray-500">{{ $inst->type_label }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $inst->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ ucfirst($inst->status) }}
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
            <a href="{{ route('super-admin.books.index') }}" class="text-sm text-purple-600 hover:underline">View All →</a>
        </div>
        <div class="space-y-3">
            @forelse($recentBooks as $book)
            <div class="flex items-center justify-between py-2 border-b last:border-0">
                <div>
                    <p class="font-medium text-gray-800">{{ Str::limit($book->title, 30) }}</p>
                    <p class="text-xs text-gray-500">by {{ $book->author }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $book->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ ucfirst($book->status) }}
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
    <a href="{{ route('super-admin.books.create') }}" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl p-3 text-center hover:shadow-lg transition text-sm">
        <i class="ti ti-plus"></i> Add Book
    </a>
    <a href="{{ route('super-admin.institutions.create') }}" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl p-3 text-center hover:shadow-lg transition text-sm">
        <i class="ti ti-building-plus"></i> Add Institution
    </a>
    <a href="{{ route('super-admin.users.create') }}" class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl p-3 text-center hover:shadow-lg transition text-sm">
        <i class="ti ti-user-plus"></i> Add User
    </a>
    <a href="{{ route('super-admin.applications.index') }}" class="bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl p-3 text-center hover:shadow-lg transition text-sm">
        <i class="ti ti-files"></i> Applications
    </a>
    <a href="{{ route('super-admin.payments.index') }}" class="bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-xl p-3 text-center hover:shadow-lg transition text-sm">
        <i class="ti ti-wallet"></i> Payments
    </a>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {{ json_encode($months) }},
            datasets: [{
                label: 'Revenue (TSh)',
                data: {{ json_encode($monthlyRevenue) }},
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { responsive: true, maintainAspectRatio: true }
    });
    
    // User Growth Chart
    const userCtx = document.getElementById('userChart').getContext('2d');
    new Chart(userCtx, {
        type: 'bar',
        data: {
            labels: {{ json_encode($months) }},
            datasets: [{
                label: 'New Users',
                data: {{ json_encode($userGrowth) }},
                backgroundColor: '#a78bfa',
                borderRadius: 8
            }]
        },
        options: { responsive: true, maintainAspectRatio: true }
    });
</script>
@endpush
@endsection