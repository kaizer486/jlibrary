@extends('layouts.admin')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header with Date Range -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <i class="ti ti-chart-bar text-3xl text-indigo-600"></i>
                <h1 class="text-2xl font-bold text-gray-800">Analytics Dashboard</h1>
                <span class="ml-2 px-2 py-1 text-xs font-semibold bg-indigo-100 text-indigo-700 rounded-full">Live</span>
            </div>
            <p class="text-gray-500">Track your platform's performance and growth</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="flex items-center gap-2 bg-white rounded-lg px-3 py-2 border border-gray-200">
                <i class="ti ti-calendar text-gray-400 text-sm"></i>
                <span class="text-sm text-gray-600">{{ now()->format('M d, Y') }}</span>
            </div>
            <button onclick="window.location.reload()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition text-sm flex items-center gap-1">
                <i class="ti ti-refresh"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-indigo-500 hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide">Total Users</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalUsers) }}</p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="ti ti-trending-up"></i> +{{ number_format($userGrowth[11] ?? 0) }} this month
                    </p>
                </div>
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:bg-indigo-200 transition">
                    <i class="ti ti-users text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-purple-500 hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide">Total Books</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalBooks) }}</p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="ti ti-trending-up"></i> +{{ number_format($newBooksThisMonth ?? 0) }} new
                    </p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-200 transition">
                    <i class="ti ti-books text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500 hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide">Total Revenue</p>
                    <p class="text-2xl font-bold text-green-600">TSh {{ number_format($totalSales, 2) }}</p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="ti ti-trending-up"></i> +{{ number_format($salesGrowth ?? 0) }}% growth
                    </p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition">
                    <i class="ti ti-wallet text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-orange-500 hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide">Quizzes Taken</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalQuizzesTaken) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="ti ti-clock"></i> {{ number_format($avgQuizScore ?? 0) }}% avg score
                    </p>
                </div>
                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center group-hover:bg-orange-200 transition">
                    <i class="ti ti-brain text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-yellow-500 hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide">Certificates</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalCertificates) }}</p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="ti ti-trending-up"></i> +{{ number_format($certificatesThisMonth ?? 0) }} issued
                    </p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center group-hover:bg-yellow-200 transition">
                    <i class="ti ti-certificate text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-pink-500 hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide">Marketplace</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($marketplaceListings) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="ti ti-shopping-cart"></i> {{ number_format($marketplaceSales ?? 0) }} sold
                    </p>
                </div>
                <div class="w-10 h-10 bg-pink-100 rounded-xl flex items-center justify-center group-hover:bg-pink-200 transition">
                    <i class="ti ti-shopping-cart text-pink-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 hover:shadow-md transition">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="ti ti-chart-line text-indigo-600"></i>
                    Revenue Overview
                    <span class="text-xs text-gray-400 font-normal">({{ date('Y') }})</span>
                </h2>
                <div class="flex items-center gap-2">
                    <select id="period-select" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="monthly">Monthly</option>
                        <option value="weekly">Weekly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                    <button id="export-chart" class="text-gray-400 hover:text-gray-600 transition" title="Export data">
                        <i class="ti ti-download"></i>
                    </button>
                </div>
            </div>
            <div style="height: 250px; position: relative;">
                <canvas id="salesChart"></canvas>
            </div>
            @if(array_sum($salesData ?? []) == 0)
                <div class="text-center text-gray-400 text-sm mt-2">
                    <i class="ti ti-info-circle"></i> No sales data available yet
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 hover:shadow-md transition">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="ti ti-users text-indigo-600"></i>
                    User Growth
                    <span class="text-xs text-gray-400 font-normal">({{ date('Y') }})</span>
                </h2>
                <span class="text-xs text-gray-500">{{ number_format($totalUsers) }} total users</span>
            </div>
            <div style="height: 250px; position: relative;">
                <canvas id="userGrowthChart"></canvas>
            </div>
            @if(array_sum($userGrowthData ?? []) == 0)
                <div class="text-center text-gray-400 text-sm mt-2">
                    <i class="ti ti-info-circle"></i> No user data available yet
                </div>
            @endif
        </div>
    </div>

    <!-- Platform Insights Row -->
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 hover:shadow-md transition">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ti ti-device-analytics text-indigo-600"></i>
                Platform Insights
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Total Institutions</span>
                    <span class="font-semibold text-gray-800">{{ number_format($totalInstitutions ?? 0) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Active Users (30d)</span>
                    <span class="font-semibold text-green-600">{{ number_format($activeUsers ?? 0) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Total Downloads</span>
                    <span class="font-semibold text-gray-800">{{ number_format($totalDownloads ?? 0) }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-gray-600">Conversion Rate</span>
                    <span class="font-semibold text-indigo-600">{{ number_format($conversionRate ?? 0, 1) }}%</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 hover:shadow-md transition">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ti ti-category text-indigo-600"></i>
                Top Categories
            </h3>
            <div class="space-y-3">
                @forelse($topCategories ?? [] as $category)
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-8 rounded-full bg-{{ $category['color'] ?? 'indigo' }}-500"></div>
                        <div class="flex-1">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">{{ $category['name'] }}</span>
                                <span class="text-gray-500">{{ $category['count'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                <div class="bg-{{ $category['color'] ?? 'indigo' }}-500 h-1.5 rounded-full" 
                                     style="width: {{ $category['percentage'] ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No category data available</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 hover:shadow-md transition">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ti ti-subscription text-indigo-600"></i>
                Subscription Distribution
            </h3>
            <div class="space-y-3">
                @if(isset($subscriptionStats) && !empty($subscriptionStats))
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Free Users</span>
                        <span class="font-semibold text-gray-800">{{ number_format($subscriptionStats['free'] ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Basic</span>
                        <span class="font-semibold text-gray-800">{{ number_format($subscriptionStats['basic'] ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Premium</span>
                        <span class="font-semibold text-purple-600">{{ number_format($subscriptionStats['premium'] ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Enterprise</span>
                        <span class="font-semibold text-indigo-600">{{ number_format($subscriptionStats['enterprise'] ?? 0) }}</span>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No subscription data available</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Popular Books & Top Users -->
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 hover:shadow-md transition">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="ti ti-books text-indigo-600"></i>
                    Most Popular Books
                </h2>
                <a href="{{ route('admin.books.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700">View All →</a>
            </div>
            <div class="space-y-3">
                @forelse($popularBooks as $book)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="ti ti-book text-indigo-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-gray-800 truncate">{{ Str::limit($book->title, 35) }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($book->downloads) }} downloads</p>
                            </div>
                        </div>
                        <span class="text-xs bg-indigo-100 text-indigo-600 px-2 py-1 rounded-full flex-shrink-0">Popular</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No books yet</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 hover:shadow-md transition">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="ti ti-trophy text-indigo-600"></i>
                    Top Learners
                </h2>
                <a href="{{ route('admin.users.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700">View All →</a>
            </div>
            <div class="space-y-3">
                @forelse($topUsers as $index => $user)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-8 h-8 rounded-full 
                                {{ $index == 0 ? 'bg-gradient-to-r from-yellow-400 to-yellow-600' : 
                                   ($index == 1 ? 'bg-gradient-to-r from-gray-300 to-gray-400' : 
                                   ($index == 2 ? 'bg-gradient-to-r from-amber-600 to-amber-700' : 
                                   'bg-gradient-to-r from-indigo-400 to-purple-500')) }} 
                                flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                {{ $index + 1 }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-gray-800 truncate">{{ $user->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->quizzes_passed ?? 0 }} quizzes passed</p>
                            </div>
                        </div>
                        @if($index == 0)
                            <i class="ti ti-trophy text-yellow-500 text-lg flex-shrink-0"></i>
                        @elseif($index == 1)
                            <i class="ti ti-award text-gray-400 text-lg flex-shrink-0"></i>
                        @else
                            <i class="ti ti-star text-gray-300 text-lg flex-shrink-0"></i>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No users yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Activity Feed -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 hover:shadow-md transition">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="ti ti-activity text-indigo-600"></i>
                Recent Activity
            </h2>
            <span class="text-xs text-gray-400">{{ $recentActivities->count() ?? 0 }} activities</span>
        </div>
        <div class="space-y-3">
            @forelse($recentActivities as $activity)
                <div class="flex items-center gap-3 p-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 rounded-lg transition">
                    <div class="w-10 h-10 rounded-full bg-{{ $activity->color }}-100 flex items-center justify-center flex-shrink-0">
                        <i class="ti {{ $activity->icon }} text-{{ $activity->color }}-600 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-700">{{ $activity->message }}</p>
                        <p class="text-xs text-gray-400">{{ $activity->time->diffForHumans() }}</p>
                    </div>
                    <span class="text-xs text-gray-400 flex-shrink-0">{{ $activity->time->format('H:i') }}</span>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">No recent activity</p>
            @endforelse
        </div>
    </div>

    <!-- Pending Actions -->
    <div class="bg-yellow-50 rounded-xl p-5 border border-yellow-200 hover:shadow-md transition">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-clock text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Pending Approvals</h3>
                    <p class="text-sm text-gray-600">{{ $pendingApprovals }} books waiting for approval</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if(($pendingApplications ?? 0) > 0)
                    <a href="{{ route('admin.applications.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition text-sm flex items-center gap-1">
                        <i class="ti ti-files"></i> Applications ({{ $pendingApplications }})
                    </a>
                @endif
                <a href="{{ route('admin.books.index') }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition text-sm flex items-center gap-1">
                    <i class="ti ti-books"></i> Review Books
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let salesChart = null;
    let userGrowthChart = null;

    function initCharts(salesLabels, salesData, userData) {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const userCtx = document.getElementById('userGrowthChart').getContext('2d');

        if (salesChart) salesChart.destroy();
        if (userGrowthChart) userGrowthChart.destroy();

        const hasSalesData = salesData.some(v => v > 0);
        const hasUserData = userData.some(v => v > 0);

        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Revenue (TSh)',
                    data: salesData,
                    borderColor: '#6366f1',
                    backgroundColor: hasSalesData ? 'rgba(99, 102, 241, 0.1)' : 'rgba(200, 200, 200, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: hasSalesData ? '#6366f1' : '#d1d5db',
                    pointBorderColor: hasSalesData ? '#6366f1' : '#d1d5db',
                    borderWidth: 2,
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'TSh ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'TSh ' + (value / 1000).toFixed(1) + 'k';
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                }
            }
        });

        userGrowthChart = new Chart(userCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'New Users',
                    data: userData,
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
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' new users';
                            }
                        }
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

    // Initialize charts
    const initialSalesData = @json($salesData ?? array_fill(0, 12, 0));
    const initialUserData = @json($userGrowthData ?? array_fill(0, 12, 0));
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    initCharts(months, initialSalesData, initialUserData);

    // Period selector
    document.getElementById('period-select')?.addEventListener('change', function() {
        const period = this.value;
        fetch(`/admin/analytics/data?period=${period}`)
            .then(response => response.json())
            .then(data => {
                if (salesChart) {
                    salesChart.data.labels = data.labels;
                    salesChart.data.datasets[0].data = data.data;
                    salesChart.update();
                }
            })
            .catch(error => console.error('Error fetching chart data:', error));
    });

    // Export chart data
    document.getElementById('export-chart')?.addEventListener('click', function() {
        if (salesChart) {
            const data = {
                labels: salesChart.data.labels,
                values: salesChart.data.datasets[0].data,
                label: salesChart.data.datasets[0].label
            };
            const json = JSON.stringify(data, null, 2);
            const blob = new Blob([json], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'sales_data.json';
            a.click();
            URL.revokeObjectURL(url);
        }
    });
</script>
@endpush