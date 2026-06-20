@extends('layouts.institution')

@section('title', 'Quote Analytics')

@section('content')

@php
    // ==========================================
    // SECURITY CHECKS
    // ==========================================
    
    // Check if user belongs to an institution
    if (!auth()->user()->institution_id) {
        abort(403, 'You do not belong to any institution.');
    }
    
    // Check if institution exists
    if (!isset($institution) || !$institution) {
        abort(404, 'Institution not found.');
    }
    
    // Check if user has access to this institution
    if (auth()->user()->institution_id != $institution->id) {
        abort(403, 'You do not have access to this institution.');
    }
    
    // Check if quote exists and belongs to this institution
    if (!isset($quote) || !$quote) {
        abort(404, 'Quote not found.');
    }
    
    if ($quote->institution_id != $institution->id) {
        abort(403, 'This quote does not belong to your institution.');
    }
    
    // Check if user has permission to view analytics
    if (!auth()->user()->can('view', $quote)) {
        abort(403, 'You do not have permission to view analytics for this quote.');
    }
    
    // Calculate engagement rate
    $totalInteractions = ($quote->saves_count ?? 0) + ($quote->shares_count ?? 0);
    $engagementRate = ($quote->views_count ?? 0) > 0 ? round(($totalInteractions / ($quote->views_count ?? 1)) * 100, 1) : 0;
@endphp

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('institution.quotes.index') }}" class="text-purple-600 hover:text-purple-700 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Quotes
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <h1 class="text-xl font-bold text-white">📊 Quote Analytics</h1>
            <p class="text-blue-100 text-sm">Performance metrics for this quote</p>
        </div>
        
        <div class="p-6">
            <!-- ========================================== -->
            <!-- QUOTE DISPLAY                               -->
            <!-- ========================================== -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 mb-6 text-center border border-blue-200">
                <i class="ti ti-quote text-3xl text-blue-500 mb-2 block"></i>
                <p class="text-gray-700 text-lg italic">"{{ $quote->quote_text }}"</p>
                <p class="text-gray-500 mt-3">— {{ $quote->author ?? 'Anonymous' }}</p>
                <div class="mt-3 flex justify-center gap-4 text-sm text-gray-500">
                    <span class="flex items-center gap-1"><i class="ti ti-tag"></i> {{ ucfirst($quote->category ?? 'General') }}</span>
                    <span class="flex items-center gap-1"><i class="ti ti-calendar"></i> {{ $quote->created_at->format('M d, Y') }}</span>
                </div>
            </div>
            
            <!-- ========================================== -->
            <!-- STATS CARDS                                -->
            <!-- ========================================== -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 rounded-xl p-4 text-center border border-blue-200">
                    <i class="ti ti-eye text-blue-500 text-2xl mb-2 block"></i>
                    <p class="text-2xl font-bold text-blue-700">{{ number_format($quote->views_count ?? 0) }}</p>
                    <p class="text-xs text-gray-500">Total Views</p>
                </div>
                <div class="bg-red-50 rounded-xl p-4 text-center border border-red-200">
                    <i class="ti ti-heart text-red-500 text-2xl mb-2 block"></i>
                    <p class="text-2xl font-bold text-red-700">{{ number_format($quote->saves_count ?? 0) }}</p>
                    <p class="text-xs text-gray-500">Saves</p>
                </div>
                <div class="bg-green-50 rounded-xl p-4 text-center border border-green-200">
                    <i class="ti ti-share text-green-500 text-2xl mb-2 block"></i>
                    <p class="text-2xl font-bold text-green-700">{{ number_format($quote->shares_count ?? 0) }}</p>
                    <p class="text-xs text-gray-500">Shares</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-4 text-center border border-purple-200">
                    <i class="ti ti-chart-bar text-purple-500 text-2xl mb-2 block"></i>
                    <p class="text-2xl font-bold text-purple-700">{{ $engagementRate }}%</p>
                    <p class="text-xs text-gray-500">Engagement Rate</p>
                </div>
            </div>
            
            <!-- ========================================== -->
            <!-- ENGAGEMENT RATE DETAILS                    -->
            <!-- ========================================== -->
            <div class="bg-gray-50 rounded-xl p-4 mb-6 border border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                    <i class="ti ti-chart-pie text-blue-600"></i> Engagement Breakdown
                </h3>
                <div class="flex items-center gap-3">
                    <div class="flex-1 bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-500" style="width: {{ min($engagementRate, 100) }}%"></div>
                    </div>
                    <span class="text-sm font-semibold text-blue-600">{{ $engagementRate }}%</span>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ $totalInteractions }} total interactions out of {{ number_format($quote->views_count ?? 0) }} views</p>
            </div>
            
            <!-- ========================================== -->
            <!-- PERFORMANCE CHART                          -->
            <!-- ========================================== -->
            @if(isset($performanceData) && !empty($performanceData['dates']))
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-chart-line text-blue-600"></i> Performance Over Time
                </h3>
                <canvas id="performanceChart" height="200"></canvas>
            </div>
            @else
            <div class="bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                <i class="ti ti-chart-bar text-gray-400 text-4xl mb-2 block"></i>
                <p class="text-gray-500">No performance data available yet.</p>
                <p class="text-xs text-gray-400 mt-1">Data will appear as users interact with this quote.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- CHART.JS FOR PERFORMANCE CHART             -->
<!-- ========================================== -->
@if(isset($performanceData) && !empty($performanceData['dates']))
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    // Default colors
    const colors = {
        views: {
            border: 'rgb(59, 130, 246)',
            background: 'rgba(59, 130, 246, 0.1)'
        },
        saves: {
            border: 'rgb(239, 68, 68)',
            background: 'rgba(239, 68, 68, 0.1)'
        },
        shares: {
            border: 'rgb(16, 185, 129)',
            background: 'rgba(16, 185, 129, 0.1)'
        }
    };
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($performanceData['dates'] ?? []),
            datasets: [
                {
                    label: 'Views',
                    data: @json($performanceData['views'] ?? []),
                    borderColor: colors.views.border,
                    backgroundColor: colors.views.background,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Saves',
                    data: @json($performanceData['saves'] ?? []),
                    borderColor: colors.saves.border,
                    backgroundColor: colors.saves.background,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Shares',
                    data: @json($performanceData['shares'] ?? []),
                    borderColor: colors.shares.border,
                    backgroundColor: colors.shares.background,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    cornerRadius: 8,
                    padding: 12
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
});
</script>
@endpush
@endif

@endsection