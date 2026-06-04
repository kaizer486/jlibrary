@extends('layouts.super-admin')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="ti ti-chart-bar text-purple-600"></i>
                Analytics Dashboard
            </h1>
            <p class="text-gray-500 text-sm mt-1">Track platform performance and growth metrics</p>
        </div>
        <div class="flex gap-2">
            <button onclick="exportReport('revenue')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition text-sm flex items-center gap-2">
                <i class="ti ti-file-spreadsheet"></i> Export Revenue
            </button>
            <button onclick="exportReport('users')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition text-sm flex items-center gap-2">
                <i class="ti ti-users"></i> Export Users
            </button>
        </div>
    </div>
</div>

<!-- Stats Cards Row 1 -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-purple-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Total Users</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($totalUsers) }}</p>
                <p class="text-xs text-green-600 mt-1">+{{ number_format($newUsersThisMonth) }} this month</p>
            </div>
            <i class="ti ti-users text-3xl text-purple-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-blue-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Total Books</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($totalBooks) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ number_format($approvedBooks) }} approved</p>
            </div>
            <i class="ti ti-books text-3xl text-blue-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-green-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Total Revenue</p>
                <p class="text-2xl font-bold text-green-600">TSh {{ number_format($totalRevenue, 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Platform earns 20%</p>
            </div>
            <i class="ti ti-wallet text-3xl text-green-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-yellow-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Active Users</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($activeUsers) }}</p>
                <p class="text-xs text-gray-500 mt-1">Last 30 days</p>
            </div>
            <i class="ti ti-user-check text-3xl text-yellow-500"></i>
        </div>
    </div>
</div>

<!-- Stats Cards Row 2 -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-indigo-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Institutions</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($totalInstitutions) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ number_format($approvedInstitutions) }} approved</p>
            </div>
            <i class="ti ti-building text-3xl text-indigo-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-pink-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Quizzes Taken</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($totalQuizzes) }}</p>
            </div>
            <i class="ti ti-brain text-3xl text-pink-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-orange-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Certificates Issued</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($totalCertificates) }}</p>
            </div>
            <i class="ti ti-certificate text-3xl text-orange-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-teal-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Marketplace</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($marketplaceListings) }}</p>
                <p class="text-xs text-gray-500 mt-1">Total listings</p>
            </div>
            <i class="ti ti-shopping-cart text-3xl text-teal-500"></i>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="ti ti-chart-line text-purple-600"></i>
                Revenue Overview ({{ date('Y') }})
            </h2>
            <select id="period-select" class="text-sm border rounded-lg px-3 py-1">
                <option value="monthly">Monthly</option>
                <option value="weekly">Weekly</option>
            </select>
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

<!-- Top Lists Row -->
<div class="grid lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="ti ti-books text-purple-600"></i>
            Most Popular Books
        </h2>
        <div class="space-y-3">
            @forelse($topBooks as $book)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-800">{{ Str::limit($book->title, 40) }}</p>
                        <p class="text-xs text-gray-500">by {{ $book->author }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-blue-600">{{ number_format($book->downloads) }}</p>
                        <p class="text-xs text-gray-400">downloads</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">No books data yet</p>
            @endforelse
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="ti ti-building text-purple-600"></i>
            Top Institutions
        </h2>
        <div class="space-y-3">
            @forelse($topInstitutions as $inst)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-800">{{ Str::limit($inst->name, 40) }}</p>
                        <p class="text-xs text-gray-500">{{ $inst->type_label }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-green-600">{{ number_format($inst->users_count) }}</p>
                        <p class="text-xs text-gray-400">members</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">No institutions data yet</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="ti ti-activity text-purple-600"></i>
        Recent Activity
    </h2>
    <div class="grid md:grid-cols-3 gap-4">
        <div>
            <p class="text-sm font-medium text-gray-600 mb-2">📚 Recent Books</p>
            <div class="space-y-2">
                @foreach($recentBooks as $book)
                    <p class="text-sm text-gray-800">{{ Str::limit($book->title, 35) }}</p>
                @endforeach
            </div>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-600 mb-2">👥 New Users</p>
            <div class="space-y-2">
                @foreach($recentUsers as $user)
                    <p class="text-sm text-gray-800">{{ $user->full_name }}</p>
                @endforeach
            </div>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-600 mb-2">💰 Recent Payments</p>
            <div class="space-y-2">
                @foreach($recentPayments as $payment)
                    <p class="text-sm text-gray-800">TSh {{ number_format($payment->amount, 2) }} - {{ $payment->user->full_name ?? 'N/A' }}</p>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let revenueChart, userChart;
    
    function initCharts(revenueData, userData, labels) {
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const userCtx = document.getElementById('userChart').getContext('2d');
        
        if (revenueChart) revenueChart.destroy();
        if (userChart) userChart.destroy();
        
        revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue (TSh)',
                    data: revenueData,
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { responsive: true, maintainAspectRatio: true }
        });
        
        userChart = new Chart(userCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'New Users',
                    data: userData,
                    backgroundColor: '#a78bfa',
                    borderRadius: 8
                }]
            },
            options: { responsive: true, maintainAspectRatio: true }
        });
    }
    
    // Initial data
    const monthlyRevenue = @json($monthlyRevenue);
    const monthlyUsers = @json($monthlyUsers);
    const months = @json($months);
    
    initCharts(monthlyRevenue, monthlyUsers, months);
    
    // Period selector
    document.getElementById('period-select')?.addEventListener('change', function() {
        const period = this.value;
        fetch(`/super-admin/analytics/data?period=${period}`)
            .then(response => response.json())
            .then(data => {
                initCharts(data.revenue, data.users, data.labels);
            });
    });
    
    // Export functions
    function exportReport(type) {
        window.location.href = `/super-admin/analytics/export?type=${type}`;
    }
</script>
@endpush
@endsection