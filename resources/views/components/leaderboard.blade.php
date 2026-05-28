<div class="w-full bg-white rounded-2xl shadow-xl overflow-hidden">
    <!-- Header with Gradient -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 px-4 md:px-6 py-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="ti ti-trophy text-white text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-white font-bold text-lg md:text-xl">🏆 Top Learners Leaderboard</h3>
                    <p class="text-purple-200 text-xs">Ranked by points | Certificates | Quizzes | Streaks</p>
                </div>
            </div>
            
            <!-- Timeframe Filter -->
            <div class="flex gap-2 bg-black/20 rounded-xl p-1">
                <button data-timeframe="all_time" class="timeframe-btn px-3 md:px-4 py-1.5 rounded-lg text-xs md:text-sm font-medium transition-all {{ $timeframe == 'all_time' ? 'bg-white/20 text-white' : 'text-purple-200 hover:text-white' }}">
                    <i class="ti ti-infinity text-sm mr-1"></i> All Time
                </button>
                <button data-timeframe="this_month" class="timeframe-btn px-3 md:px-4 py-1.5 rounded-lg text-xs md:text-sm font-medium transition-all {{ $timeframe == 'this_month' ? 'bg-white/20 text-white' : 'text-purple-200 hover:text-white' }}">
                    <i class="ti ti-calendar text-sm mr-1"></i> Month
                </button>
                <button data-timeframe="this_week" class="timeframe-btn px-3 md:px-4 py-1.5 rounded-lg text-xs md:text-sm font-medium transition-all {{ $timeframe == 'this_week' ? 'bg-white/20 text-white' : 'text-purple-200 hover:text-white' }}">
                    <i class="ti ti-clock text-sm mr-1"></i> Week
                </button>
            </div>
        </div>
    </div>
    
    <!-- Loading Spinner -->
    <div id="leaderboard-loader" class="hidden py-12 text-center">
        <i class="ti ti-loader-2 animate-spin text-3xl text-purple-500 mb-3 block"></i>
        <p class="text-gray-500">Loading leaderboard...</p>
    </div>
    
    <!-- Table Container -->
    <div id="leaderboard-table-container" class="w-full">
        <div class="w-full overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-3 md:px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">RANK</th>
                        <th class="px-3 md:px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">LEARNER</th>
                        <th class="px-3 md:px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-20">
                            <div class="tooltip">🎓<span class="tooltip-text">Certificates Earned</span></div>
                        </th>
                        <th class="px-3 md:px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-20">
                            <div class="tooltip">📚<span class="tooltip-text">Books Completed</span></div>
                        </th>
                        <th class="px-3 md:px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                            <div class="tooltip">🧠<span class="tooltip-text">Quiz Average Score</span></div>
                        </th>
                        <th class="px-3 md:px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                            <div class="tooltip">🔥<span class="tooltip-text">Day Streak</span></div>
                        </th>
                        <th class="px-3 md:px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                            <div class="tooltip">⭐<span class="tooltip-text">Total Points</span></div>
                        </th>
                    </tr>
                </thead>
                
                <tbody class="divide-y divide-gray-100" id="leaderboard-tbody">
                    @forelse($topLearners as $index => $learner)
                        @php
                            $rank = $index + 1;
                            $rankDisplay = match($rank) {
                                1 => '<span class="text-2xl">🥇</span>',
                                2 => '<span class="text-2xl">🥈</span>',
                                3 => '<span class="text-2xl">🥉</span>',
                                default => '<span class="text-sm font-bold text-gray-500">#' . $rank . '</span>'
                            };
                            $rowClass = ($learner->id == auth()->id()) ? 'bg-gradient-to-r from-purple-50 to-pink-50' : 'hover:bg-gray-50';
                            $userInitial = substr($learner->full_name ?? $learner->name, 0, 1);
                        @endphp
                        <tr class="{{ $rowClass }} transition-all duration-200">
                            <td class="px-3 md:px-4 py-3 text-center">
                                {!! $rankDisplay !!}
                            </td>
                            
                            <td class="px-3 md:px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-md flex-shrink-0">
                                        <span class="text-white text-xs md:text-sm font-bold">{{ $userInitial }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="font-semibold text-gray-800 text-sm md:text-base">{{ $learner->full_name ?? $learner->name }}</span>
                                            @if($learner->id == auth()->id())
                                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">You</span>
                                            @endif
                                            <span class="text-xs bg-gradient-to-r from-amber-400 to-orange-500 text-white px-2 py-0.5 rounded-full">Lv {{ $learner->level ?? 1 }}</span>
                                        </div>
                                        <div class="hidden sm:flex items-center gap-3 mt-1">
                                            <div class="w-24 bg-gray-100 rounded-full h-1.5">
                                                <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-1.5 rounded-full" style="width: {{ $learner->level_progress ?? 0 }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-400">{{ $learner->xp_points ?? 0 }} XP</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-3 md:px-4 py-3 text-center">
                                <span class="text-base md:text-lg font-bold text-emerald-600">{{ $learner->certificates_count ?? 0 }}</span>
                            </td>
                            
                            <td class="px-3 md:px-4 py-3 text-center">
                                <span class="text-base md:text-lg font-bold text-blue-600">{{ $learner->books_completed_count ?? 0 }}</span>
                            </td>
                            
                            <td class="px-3 md:px-4 py-3 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-base md:text-lg font-bold {{ ($learner->quiz_avg ?? 0) >= 70 ? 'text-green-600' : 'text-orange-500' }}">
                                        {{ $learner->quiz_avg ?? 0 }}%
                                    </span>
                                    <div class="hidden sm:block w-12 bg-gray-100 rounded-full h-1 mt-1">
                                        <div class="bg-gradient-to-r from-green-400 to-green-500 h-1 rounded-full" style="width: {{ $learner->quiz_avg ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-3 md:px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <i class="ti ti-flame text-orange-500 text-sm md:text-base"></i>
                                    <span class="font-semibold text-gray-700 text-sm md:text-base">{{ $learner->streak_days ?? 0 }}</span>
                                    <span class="hidden sm:inline text-xs text-gray-400">days</span>
                                </div>
                            </td>
                            
                            <td class="px-3 md:px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <i class="ti ti-star text-yellow-500 text-sm md:text-base"></i>
                                    <span class="font-bold text-gray-800 text-sm md:text-base">{{ number_format($learner->combined_score ?? 0) }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <i class="ti ti-users text-5xl text-gray-300 mb-3 block"></i>
                                <p class="text-gray-500">No learners found yet</p>
                                <p class="text-gray-400 text-sm mt-1">Start completing quizzes and reading books to appear here!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- View All Link -->
        <div class="px-4 md:px-6 py-3 bg-gray-50 text-center border-t border-gray-100">
            <a href="{{ route('leaderboard.index') }}" class="text-purple-600 text-sm font-medium hover:text-purple-700 inline-flex items-center gap-1 group">
                View Full Leaderboard
                <i class="ti ti-arrow-right group-hover:translate-x-1 transition-transform text-sm"></i>
            </a>
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

/* Small arrow on top of tooltip */
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const timeframeBtns = document.querySelectorAll('.timeframe-btn');
    const tableContainer = document.getElementById('leaderboard-table-container');
    const loader = document.getElementById('leaderboard-loader');
    
    timeframeBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const timeframe = this.dataset.timeframe;
            
            // Update active state
            timeframeBtns.forEach(b => {
                b.classList.remove('bg-white/20', 'text-white');
                b.classList.add('text-purple-200');
            });
            this.classList.add('bg-white/20', 'text-white');
            this.classList.remove('text-purple-200');
            
            // Show loader, hide table
            if (loader) loader.classList.remove('hidden');
            if (tableContainer) tableContainer.classList.add('hidden');
            
            // Fetch new data via AJAX
            fetch(`{{ url('/leaderboard/data') }}?timeframe=${timeframe}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Reload the page with the timeframe parameter
                window.location.href = `{{ url('/dashboard') }}?timeframe=${timeframe}`;
            })
            .catch(error => {
                console.error('Error:', error);
                if (loader) loader.classList.add('hidden');
                if (tableContainer) tableContainer.classList.remove('hidden');
            });
        });
    });
});
</script>