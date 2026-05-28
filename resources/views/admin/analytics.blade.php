@extends('layouts.admin')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Analytics Dashboard</h1>
        <p class="text-gray-500">Track your platform's performance and growth</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 mb-2">
                <i class="ti ti-users text-blue-500 text-xl"></i>
                <p class="text-gray-500 text-xs">Total Users</p>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalUsers) }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 mb-2">
                <i class="ti ti-books text-purple-500 text-xl"></i>
                <p class="text-gray-500 text-xs">Total Books</p>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalBooks) }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 mb-2">
                <i class="ti ti-wallet text-green-500 text-xl"></i>
                <p class="text-gray-500 text-xs">Total Sales</p>
            </div>
            <p class="text-2xl font-bold text-green-600">TSh {{ number_format($totalSales, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 mb-2">
                <i class="ti ti-brain text-orange-500 text-xl"></i>
                <p class="text-gray-500 text-xs">Quizzes Taken</p>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalQuizzesTaken) }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 mb-2">
                <i class="ti ti-certificate text-yellow-500 text-xl"></i>
                <p class="text-gray-500 text-xs">Certificates</p>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalCertificates) }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 mb-2">
                <i class="ti ti-shopping-cart text-pink-500 text-xl"></i>
                <p class="text-gray-500 text-xs">Marketplace</p>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($marketplaceListings) }}</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="ti ti-chart-line text-purple-600"></i>
                    Sales Overview
                </h2>
                <select id="period-select" class="text-sm border rounded-lg px-3 py-1">
                    <option value="monthly">Monthly</option>
                    <option value="weekly">Weekly</option>
                </select>
            </div>
            <canvas id="salesChart" height="250"></canvas>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ti ti-users text-purple-600"></i>
                User Growth ({{ date('Y') }})
            </h2>
            <canvas id="userGrowthChart" height="250"></canvas>
        </div>
    </div>

    <!-- Popular Books & Top Users -->
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ti ti-books text-purple-600"></i>
                Most Popular Books
            </h2>
            <div class="space-y-3">
                @forelse($popularBooks as $book)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="ti ti-book text-purple-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ Str::limit($book->title, 40) }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($book->downloads) }} downloads</p>
                            </div>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded-full">Popular</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No books yet</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ti ti-trophy text-purple-600"></i>
                Top Learners
            </h2>
            <div class="space-y-3">
                @forelse($topUsers as $index => $user)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 flex items-center justify-center text-white font-bold text-sm">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $user->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->quizzes_passed ?? 0 }} quizzes passed</p>
                            </div>
                        </div>
                        <i class="ti ti-star text-yellow-500"></i>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No users yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Activity Feed -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="ti ti-activity text-purple-600"></i>
            Recent Activity
        </h2>
        <div class="space-y-3">
            @forelse($recentActivities as $activity)
                <div class="flex items-center gap-3 p-3 border-b border-gray-100 last:border-0">
                    <div class="w-10 h-10 rounded-full bg-{{ $activity->color }}-100 flex items-center justify-center">
                        <i class="ti {{ $activity->icon }} text-{{ $activity->color }}-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-700">{{ $activity->message }}</p>
                        <p class="text-xs text-gray-400">{{ $activity->time->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">No recent activity</p>
            @endforelse
        </div>
    </div>

    <!-- Pending Actions -->
    <div class="bg-yellow-50 rounded-xl p-5 border border-yellow-200">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="ti ti-clock text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Pending Approvals</h3>
                    <p class="text-sm text-gray-600">{{ $pendingApprovals }} books waiting for approval</p>
                </div>
            </div>
            <a href="{{ route('admin.books.index') }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition">
                Review Now
            </a>
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

        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Sales (TSh)',
                    data: salesData,
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { responsive: true, maintainAspectRatio: true }
        });

        userGrowthChart = new Chart(userCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
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

    const initialSalesData = @json($salesData);
    const initialUserData = @json($userGrowthData);
    initCharts(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], initialSalesData, initialUserData);

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
            });
    });
</script>
@endpush