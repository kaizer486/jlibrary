@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<!-- Welcome Section - Compact -->
<div class="mb-6">
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-xl p-5 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <i class="ti ti-shield text-2xl"></i>
                    <h1 class="text-xl font-bold">Admin Dashboard</h1>
                </div>
                <p class="text-indigo-100 text-sm mt-1">Welcome back, <span class="font-semibold">{{ Auth::user()->full_name }}</span></p>
                <div class="flex items-center gap-3 text-xs text-indigo-200 mt-1">
                    <span class="flex items-center gap-1"><i class="ti ti-calendar text-xs"></i> {{ now()->format('M j, Y') }}</span>
                    <span class="flex items-center gap-1"><i class="ti ti-clock text-xs"></i> {{ now()->format('g:i A') }}</span>
                </div>
            </div>
            <div class="hidden md:block">
                <i class="ti ti-shield-check text-6xl opacity-20"></i>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards - Compact -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-300 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-600 text-xs font-semibold uppercase tracking-wider">Total Users</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalUsers ?? \App\Models\User::count()) }}</p>
            </div>
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="ti ti-users text-purple-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-2 flex items-center gap-2">
            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">
                <i class="ti ti-chart-line text-xs"></i> +{{ number_format($userGrowth[11] ?? 0) }}
            </span>
            <span class="text-xs text-gray-400">this month</span>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-300 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-600 text-xs font-semibold uppercase tracking-wider">Institutions</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalInstitutions ?? \App\Models\Institution::count()) }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="ti ti-building text-blue-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-2 flex items-center gap-2">
            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">
                <i class="ti ti-check text-xs"></i> {{ number_format($activeInstitutions ?? 0) }} active
            </span>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-300 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-600 text-xs font-semibold uppercase tracking-wider">Total Books</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalBooks ?? \App\Models\Book::count()) }}</p>
            </div>
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="ti ti-books text-green-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-2 flex items-center gap-2">
            <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-medium">
                <i class="ti ti-clock text-xs"></i> {{ number_format($pendingBooks ?? 0) }} pending
            </span>
        </div>
    </div>
</div>

<!-- Second Row Stats - Compact Activity Metrics -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-300 border-l-4 border-pink-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Quizzes Taken</p>
                <p class="text-xl font-bold text-gray-800 mt-1">{{ number_format($totalQuizzes ?? \App\Models\QuizAttempt::count()) }}</p>
            </div>
            <div class="w-9 h-9 bg-pink-100 rounded-xl flex items-center justify-center">
                <i class="ti ti-brain text-pink-600 text-lg"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-300 border-l-4 border-emerald-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Certificates</p>
                <p class="text-xl font-bold text-gray-800 mt-1">{{ number_format($totalCertificates ?? \App\Models\Certificate::count()) }}</p>
            </div>
            <div class="w-9 h-9 bg-emerald-100 rounded-xl flex items-center justify-center">
                <i class="ti ti-certificate text-emerald-600 text-lg"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-300 border-l-4 border-cyan-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Active Subs</p>
                <p class="text-xl font-bold text-gray-800 mt-1">{{ number_format($activeSubscriptions ?? 0) }}</p>
            </div>
            <div class="w-9 h-9 bg-cyan-100 rounded-xl flex items-center justify-center">
                <i class="ti ti-subscription text-cyan-600 text-lg"></i>
            </div>
        </div>
    </div>
</div>

<!-- Subscription Stats - Compact -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
            <i class="ti ti-subscription text-purple-600"></i>
            Subscription Overview
        </h3>
        <span class="text-xs text-gray-400">Last 30 days</span>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-purple-50 rounded-lg p-3 text-center">
            <p class="text-purple-600 text-xs font-semibold">Total</p>
            <p class="text-xl font-bold text-purple-800">{{ number_format($subscriptionStats['total'] ?? 0) }}</p>
        </div>
        <div class="bg-emerald-50 rounded-lg p-3 text-center">
            <p class="text-emerald-600 text-xs font-semibold">Active</p>
            <p class="text-xl font-bold text-emerald-800">{{ number_format($subscriptionStats['status_counts']['active'] ?? 0) }}</p>
        </div>
        <div class="bg-yellow-50 rounded-lg p-3 text-center">
            <p class="text-yellow-600 text-xs font-semibold">Pending</p>
            <p class="text-xl font-bold text-yellow-800">{{ number_format($subscriptionStats['status_counts']['pending'] ?? 0) }}</p>
        </div>
        <div class="bg-red-50 rounded-lg p-3 text-center">
            <p class="text-red-600 text-xs font-semibold">Expired</p>
            <p class="text-xl font-bold text-red-800">{{ number_format($subscriptionStats['status_counts']['expired'] ?? 0) }}</p>
        </div>
    </div>
</div>

<!-- Enhanced User Growth Section with Chart & Circle Views -->
<div class="bg-white rounded-xl shadow-sm p-5 mb-6">
    <div class="flex flex-wrap items-center justify-between mb-4">
        <div>
            <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <i class="ti ti-users text-purple-600"></i>
                User Growth Analytics
            </h2>
            <p class="text-xs text-gray-400 mt-0.5">Track registration trends</p>
        </div>
        <div class="flex items-center gap-2 mt-1 sm:mt-0">
            <!-- View Toggle -->
            <div class="flex bg-gray-100 rounded-lg p-0.5">
                <button onclick="switchChart('bar')" id="barViewBtn" class="px-3 py-1 rounded-lg text-xs font-medium transition-all duration-200 bg-purple-600 text-white shadow-sm">
                    <i class="ti ti-chart-bar text-xs"></i> Bar
                </button>
                <button onclick="switchChart('line')" id="lineViewBtn" class="px-3 py-1 rounded-lg text-xs font-medium transition-all duration-200 hover:bg-gray-200 text-gray-600">
                    <i class="ti ti-chart-line text-xs"></i> Line
                </button>
                <button onclick="switchChart('area')" id="areaViewBtn" class="px-3 py-1 rounded-lg text-xs font-medium transition-all duration-200 hover:bg-gray-200 text-gray-600">
                    <i class="ti ti-chart-area text-xs"></i> Area
                </button>
                <button onclick="switchChart('doughnut')" id="doughnutViewBtn" class="px-3 py-1 rounded-lg text-xs font-medium transition-all duration-200 hover:bg-gray-200 text-gray-600">
                    <i class="ti ti-chart-pie text-xs"></i> Circle
                </button>
            </div>
            <!-- Time Period -->
            <select id="timePeriod" onchange="updateChartPeriod()" class="bg-gray-100 border-0 rounded-lg px-3 py-1 text-xs font-medium text-gray-700 focus:ring-2 focus:ring-purple-500">
                <option value="12">12 Months</option>
                <option value="6">6 Months</option>
                <option value="3">3 Months</option>
                <option value="1">1 Month</option>
            </select>
        </div>
    </div>
    
    <!-- Chart + Stats Grid -->
    <div class="grid lg:grid-cols-4 gap-4">
        <!-- Chart Container - 3 columns -->
        <div class="lg:col-span-3" style="height: 280px; position: relative;">
            <canvas id="userChart"></canvas>
        </div>
        
        <!-- Circle/Doughnut View Container - 1 column (hidden when other charts active) -->
        <div id="doughnutContainer" class="lg:col-span-3 hidden" style="height: 280px; position: relative;">
            <canvas id="doughnutChart"></canvas>
        </div>
        
        <!-- Summary Stats - 1 column -->
        <div class="lg:col-span-1">
            <div class="grid grid-cols-2 lg:grid-cols-1 gap-2 h-full">
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-3 text-center">
                    <p class="text-purple-600 text-xs font-semibold">Total Users</p>
                    <p class="text-xl font-bold text-purple-800">{{ number_format(array_sum($userGrowth ?? [])) }}</p>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-3 text-center">
                    <p class="text-green-600 text-xs font-semibold">This Month</p>
                    <p class="text-xl font-bold text-green-700">+{{ number_format($userGrowth[11] ?? 0) }}</p>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-3 text-center">
                    <p class="text-blue-600 text-xs font-semibold">Monthly Avg</p>
                    <p class="text-xl font-bold text-blue-700">{{ number_format(round(array_sum($userGrowth ?? []) / max(1, count($userGrowth ?? [])))) }}</p>
                </div>
                <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-xl p-3 text-center">
                    <p class="text-pink-600 text-xs font-semibold">Growth Rate</p>
                    <p class="text-xl font-bold text-pink-700">
                        @php
                            $total = array_sum($userGrowth ?? []);
                            $growthRate = $total > 0 ? round(($userGrowth[11] ?? 0) / $total * 100, 1) : 0;
                        @endphp
                        {{ $growthRate }}%
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity - Compact -->
<div class="grid lg:grid-cols-3 gap-4">
    <div class="bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition-all duration-300">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <i class="ti ti-users text-purple-600"></i>
                Recent Users
            </h2>
            <a href="{{ route('admin.users.index') }}" class="text-xs text-purple-600 hover:text-purple-700 font-medium hover:underline">
                View All →
            </a>
        </div>
        <div class="space-y-2">
            @forelse($recentUsers ?? \App\Models\User::latest()->limit(5)->get() as $user)
            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg hover:bg-purple-50 transition-all duration-200">
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $user->full_name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-400">{{ $user->email ?? 'No email' }}</p>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full font-medium 
                    {{ $user->hasRole('admin') ? 'bg-purple-100 text-purple-700' : 
                       ($user->hasRole('instructor') ? 'bg-blue-100 text-blue-700' : 
                       'bg-gray-100 text-gray-700') }}">
                    {{ $user->getRoleLabel() ?? 'User' }}
                </span>
            </div>
            @empty
            <p class="text-gray-400 text-center py-4 text-sm">No users yet</p>
            @endforelse
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition-all duration-300">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <i class="ti ti-building text-blue-600"></i>
                Recent Institutions
            </h2>
            <a href="{{ route('admin.institutions.index') }}" class="text-xs text-purple-600 hover:text-purple-700 font-medium hover:underline">
                View All →
            </a>
        </div>
        <div class="space-y-2">
            @forelse($recentInstitutions ?? \App\Models\Institution::latest()->limit(5)->get() as $inst)
            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg hover:bg-blue-50 transition-all duration-200">
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ Str::limit($inst->name ?? 'Unknown', 25) }}</p>
                    <p class="text-xs text-gray-400">{{ $inst->type_label ?? $inst->type ?? 'N/A' }}</p>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full font-medium 
                    {{ ($inst->status ?? '') === 'approved' ? 'bg-green-100 text-green-700' : 
                       (($inst->status ?? '') === 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                       'bg-gray-100 text-gray-700') }}">
                    {{ ucfirst($inst->status ?? 'N/A') }}
                </span>
            </div>
            @empty
            <p class="text-gray-400 text-center py-4 text-sm">No institutions yet</p>
            @endforelse
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition-all duration-300">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <i class="ti ti-books text-green-600"></i>
                Recent Books
            </h2>
            <a href="{{ route('admin.books.index') }}" class="text-xs text-purple-600 hover:text-purple-700 font-medium hover:underline">
                View All →
            </a>
        </div>
        <div class="space-y-2">
            @forelse($recentBooks ?? \App\Models\Book::latest()->limit(5)->get() as $book)
            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg hover:bg-green-50 transition-all duration-200">
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ Str::limit($book->title ?? 'Unknown', 25) }}</p>
                    <p class="text-xs text-gray-400">by {{ $book->author ?? 'Unknown' }}</p>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full font-medium 
                    {{ ($book->status ?? '') === 'approved' ? 'bg-green-100 text-green-700' : 
                       (($book->status ?? '') === 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                       'bg-gray-100 text-gray-700') }}">
                    {{ ucfirst($book->status ?? 'N/A') }}
                </span>
            </div>
            @empty
            <p class="text-gray-400 text-center py-4 text-sm">No books yet</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Quick Actions - Compact -->
<div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
    <a href="{{ route('admin.books.create') }}" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl p-3 text-center hover:shadow-lg transition-all duration-300 hover:scale-105">
        <div class="flex items-center justify-center gap-2">
            <i class="ti ti-plus text-sm"></i>
            <span class="text-sm font-medium">Add Book</span>
        </div>
    </a>
    
    <a href="{{ route('admin.institutions.create') }}" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl p-3 text-center hover:shadow-lg transition-all duration-300 hover:scale-105">
        <div class="flex items-center justify-center gap-2">
            <i class="ti ti-building-plus text-sm"></i>
            <span class="text-sm font-medium">Add Institution</span>
        </div>
    </a>
    
    <a href="{{ route('admin.users.create') }}" class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl p-3 text-center hover:shadow-lg transition-all duration-300 hover:scale-105">
        <div class="flex items-center justify-center gap-2">
            <i class="ti ti-user-plus text-sm"></i>
            <span class="text-sm font-medium">Add User</span>
        </div>
    </a>
    
    <a href="{{ route('admin.applications.index') }}" class="bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl p-3 text-center hover:shadow-lg transition-all duration-300 hover:scale-105">
        <div class="flex items-center justify-center gap-2">
            <i class="ti ti-files text-sm"></i>
            <span class="text-sm font-medium">Applications</span>
        </div>
    </a>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let userChartInstance = null;
    let doughnutChartInstance = null;
    let currentChartType = 'bar';
    let currentMonths = [];
    let currentData = [];
    
    // Color palette for doughnut chart
    const colors = [
        '#8b5cf6', '#ec4899', '#3b82f6', '#10b981', '#f59e0b', 
        '#ef4444', '#14b8a6', '#8b5cf6', '#f97316', '#6366f1',
        '#06b6d4', '#a855f7'
    ];
    
    document.addEventListener('DOMContentLoaded', function() {
        currentMonths = {!! json_encode($months ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']) !!};
        currentData = {!! json_encode($userGrowth ?? array_fill(0, 12, 0)) !!};
        initChart('bar');
        initDoughnutChart();
    });
    
    function initChart(type) {
        const ctx = document.getElementById('userChart');
        if (!ctx) return;
        
        // Show/hide containers
        const chartContainer = document.getElementById('userChart').parentElement;
        const doughnutContainer = document.getElementById('doughnutContainer');
        
        if (type === 'doughnut') {
            chartContainer.classList.add('hidden');
            doughnutContainer.classList.remove('hidden');
            return;
        } else {
            chartContainer.classList.remove('hidden');
            doughnutContainer.classList.add('hidden');
        }
        
        // Destroy existing chart
        if (userChartInstance) {
            userChartInstance.destroy();
        }
        
        const hasData = currentData.some(value => value > 0);
        const isArea = type === 'area';
        const isLine = type === 'line';
        const isBar = type === 'bar';
        
        let chartConfig = {
            type: isBar ? 'bar' : 'line',
            data: {
                labels: currentMonths,
                datasets: [{
                    label: 'New Users',
                    data: currentData,
                    borderColor: '#8b5cf6',
                    backgroundColor: isArea ? 'rgba(139, 92, 246, 0.2)' : 
                                    isBar ? 'rgba(139, 92, 246, 0.7)' : 
                                    'rgba(139, 92, 246, 0.1)',
                    borderWidth: isBar ? 1 : 3,
                    fill: isArea || isLine,
                    tension: 0.4,
                    pointBackgroundColor: '#8b5cf6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: isBar ? 3 : 4,
                    pointHoverRadius: 7,
                    borderRadius: isBar ? 6 : 0,
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255,255,255,0.95)',
                        titleColor: '#1f2937',
                        bodyColor: '#4b5563',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(context) {
                                return `${context.parsed.y} new users`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: Math.max(1, Math.ceil(Math.max(...currentData) / 6)),
                            callback: function(value) {
                                return Math.round(value);
                            },
                            font: { size: 10 }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)',
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                },
                animation: {
                    duration: 500,
                    easing: 'easeInOutQuart'
                }
            }
        };
        
        if (!hasData) {
            chartConfig.data.datasets[0].data = currentData.map(() => 0);
            chartConfig.data.datasets[0].backgroundColor = '#d1d5db';
            chartConfig.data.datasets[0].borderColor = '#9ca3af';
        }
        
        userChartInstance = new Chart(ctx, chartConfig);
    }
    
    function initDoughnutChart() {
        const ctx = document.getElementById('doughnutChart');
        if (!ctx) return;
        
        if (doughnutChartInstance) {
            doughnutChartInstance.destroy();
        }
        
        // Get monthly data for doughnut
        const labels = currentMonths;
        const data = currentData;
        const hasData = data.some(v => v > 0);
        
        // Use colors for each month
        const colorSet = colors.slice(0, data.length);
        
        let chartConfig = {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colorSet,
                    borderColor: '#fff',
                    borderWidth: 2,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '55%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            padding: 8,
                            font: { size: 10 },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255,255,255,0.95)',
                        titleColor: '#1f2937',
                        bodyColor: '#4b5563',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return `${context.label}: ${context.parsed} users (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    duration: 800
                }
            }
        };
        
        if (!hasData) {
            chartConfig.data.datasets[0].data = data.map(() => 1);
            chartConfig.data.datasets[0].backgroundColor = '#d1d5db';
        }
        
        doughnutChartInstance = new Chart(ctx, chartConfig);
    }
    
    function switchChart(type) {
        currentChartType = type;
        
        // Update button styles
        document.querySelectorAll('#barViewBtn, #lineViewBtn, #areaViewBtn, #doughnutViewBtn').forEach(btn => {
            btn.classList.remove('bg-purple-600', 'text-white', 'shadow-sm');
            btn.classList.add('hover:bg-gray-200', 'text-gray-600');
        });
        
        const btnMap = {
            'bar': 'barViewBtn',
            'line': 'lineViewBtn',
            'area': 'areaViewBtn',
            'doughnut': 'doughnutViewBtn'
        };
        
        const activeBtn = document.getElementById(btnMap[type]);
        if (activeBtn) {
            activeBtn.classList.remove('hover:bg-gray-200', 'text-gray-600');
            activeBtn.classList.add('bg-purple-600', 'text-white', 'shadow-sm');
        }
        
        if (type === 'doughnut') {
            initDoughnutChart();
            // Show doughnut, hide chart
            document.getElementById('userChart').parentElement.classList.add('hidden');
            document.getElementById('doughnutContainer').classList.remove('hidden');
        } else {
            // Show chart, hide doughnut
            document.getElementById('userChart').parentElement.classList.remove('hidden');
            document.getElementById('doughnutContainer').classList.add('hidden');
            initChart(type);
        }
    }
    
    function updateChartPeriod() {
        const period = parseInt(document.getElementById('timePeriod').value);
        const fullMonths = {!! json_encode($months ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']) !!};
        const fullData = {!! json_encode($userGrowth ?? array_fill(0, 12, 0)) !!};
        
        currentMonths = fullMonths.slice(-period);
        currentData = fullData.slice(-period);
        
        if (currentChartType === 'doughnut') {
            initDoughnutChart();
        } else {
            initChart(currentChartType);
        }
    }
</script>
@endpush
@endsection