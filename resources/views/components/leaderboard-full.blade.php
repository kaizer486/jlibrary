<div class="w-full bg-white rounded-2xl shadow-xl overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 px-6 py-4">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <i class="ti ti-trophy text-white text-2xl"></i>
                <h2 class="text-white font-bold text-xl">Complete Leaderboard</h2>
            </div>
            <div class="text-white/80 text-sm">
                Showing top {{ count($topLearners) }} learners
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full min-w-[800px]">
            <thead class="bg-gray-50 border-b border-gray-200 sticky top-0">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-16">RANK</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">LEARNER</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-20">
                        <div class="tooltip-container">
                            🎓
                            <span class="tooltip-text">Certificates Earned</span>
                        </div>
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-20">
                        <div class="tooltip-container">
                            📚
                            <span class="tooltip-text">Books Completed</span>
                        </div>
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-24">
                        <div class="tooltip-container">
                            🧠
                            <span class="tooltip-text">Quiz Average Score</span>
                        </div>
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-24">
                        <div class="tooltip-container">
                            🔥
                            <span class="tooltip-text">Day Streak</span>
                        </div>
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-24">
                        <div class="tooltip-container">
                            ⭐
                            <span class="tooltip-text">Total Points</span>
                        </div>
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

    <!-- Back to Dashboard -->
    <div class="px-6 py-4 bg-gray-50 text-center border-t">
        <a href="{{ route('dashboard') }}" class="text-purple-600 hover:text-purple-700">
            ← Back to Dashboard
        </a>
    </div>
</div>

<style>
.tooltip-container {
    position: relative;
    display: inline-block;
    cursor: help;
}

.tooltip-text {
    visibility: hidden;
    position: absolute;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    background-color: #1f2937;
    color: white;
    text-align: center;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    white-space: nowrap;
    z-index: 100;
    opacity: 0;
    transition: opacity 0.2s;
    pointer-events: none;
}

.tooltip-container:hover .tooltip-text {
    visibility: visible;
    opacity: 1;
}
</style>