@extends('layouts.app')

@section('title', 'Leaderboard')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                <i class="ti ti-trophy text-4xl text-yellow-500"></i>
                <h1 class="text-3xl font-bold text-white">🏆 Full Leaderboard</h1>
            </div>
            <p class="text-gray-300">All learners ranked by total points from certificates, quizzes, books, and streaks</p>
        </div>
        
        <!-- Full Leaderboard Component -->
        <div class="w-full bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="ti ti-trophy text-white text-2xl"></i>
                        <h2 class="text-white font-bold text-xl">Complete Ranking</h2>
                    </div>
                    <div class="text-white/80 text-sm">
                        Showing {{ count($topLearners) }} learners
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px]">
                    <thead class="bg-gray-50 border-b border-gray-200 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-16">RANK</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">LEARNER</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-20">
                                <div class="tooltip">🎓<span class="tooltip-text">Certificates</span></div>
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-20">
                                <div class="tooltip">📚<span class="tooltip-text">Books</span></div>
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-24">
                                <div class="tooltip">🧠<span class="tooltip-text">Quiz Avg</span></div>
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-24">
                                <div class="tooltip">🔥<span class="tooltip-text">Streak</span></div>
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-24">
                                <div class="tooltip">⭐<span class="tooltip-text">Points</span></div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topLearners as $index => $learner)
                            @php
                                $rank = $index + 1;
                                $rankDisplay = match($rank) {
                                    1 => '🥇',
                                    2 => '🥈',
                                    3 => '🥉',
                                    default => '#'.$rank
                                };
                                $rowClass = ($learner->id == auth()->id()) ? 'bg-gradient-to-r from-purple-50 to-pink-50' : 'hover:bg-gray-50';
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td class="px-4 py-3 text-center font-bold {{ $rank <= 3 ? 'text-2xl' : 'text-gray-500' }}">
                                    {{ $rankDisplay }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                                            <span class="text-white font-bold">{{ substr($learner->full_name ?? $learner->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-800">{{ $learner->full_name ?? $learner->name }}</span>
                                            @if($learner->id == auth()->id())
                                                <span class="ml-2 text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">You</span>
                                            @endif
                                            <div class="text-xs text-gray-400">Level {{ $learner->level ?? 1 }} · {{ $learner->xp_points ?? 0 }} XP</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center text-lg font-bold text-emerald-600">{{ $learner->certificates_count ?? 0 }}</td>
                                <td class="px-4 py-3 text-center text-lg font-bold text-blue-600">{{ $learner->books_completed_count ?? 0 }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-bold {{ ($learner->quiz_avg ?? 0) >= 70 ? 'text-green-600' : 'text-orange-500' }}">
                                        {{ $learner->quiz_avg ?? 0 }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <i class="ti ti-flame text-orange-500"></i>
                                        <span>{{ $learner->streak_days ?? 0 }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <i class="ti ti-star text-yellow-500"></i>
                                        <span class="font-bold">{{ number_format($learner->combined_score ?? 0) }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                    No learners found yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 text-center border-t">
                <a href="{{ route('dashboard') }}" class="text-purple-600 hover:text-purple-700 inline-flex items-center gap-2">
                    <i class="ti ti-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
        
    </div>
</div>
<style>
.tooltip {
    position: relative;
    display: inline-block;
    cursor: help;
}

.tooltip .tooltip-text {
    visibility: hidden;
    position: absolute;
    top: 125%;
    left: 50%;
    transform: translateX(-50%);
    background-color: #1f2937;
    color: white;
    text-align: center;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: normal;
    white-space: nowrap;
    z-index: 1000;
    opacity: 0;
    transition: opacity 0.2s;
    pointer-events: none;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Small arrow on top of tooltip pointing up to the icon */
.tooltip .tooltip-text::before {
    content: '';
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    border-width: 5px;
    border-style: solid;
    border-color: transparent transparent #1f2937 transparent;
}

.tooltip:hover .tooltip-text {
    visibility: visible;
    opacity: 1;
}
</style>
@endsection