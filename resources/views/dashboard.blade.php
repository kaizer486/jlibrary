@extends('layouts.app')

@section('content')
<!-- Perfect Balance Dark Blue Background - Full Page -->
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        
        <!-- Welcome Banner -->
        <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl p-6 mb-8 text-white shadow-xl border border-white/20">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-3xl"></span>
                        <h1 class="text-2xl md:text-3xl font-bold">Welcome back, {{ Auth::user()->full_name }}!</h1>
                        <span class="text-2xl"></span>
                    </div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="flex items-center gap-1 bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full">
                            <span class="text-yellow-300">🔥</span>
                            
                            <span class="text-sm">{{ Auth::user()->streak_days ?? 0 }}-day streak</span>
                        </div>
                        <div class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-sm">
                            Level {{ Auth::user()->level ?? 1 }} Learner
                        </div>
                        @if(Auth::user()->institution_id && Auth::user()->institution)
                        <div class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-sm">
                            <i class="ti ti-building"></i> {{ Auth::user()->institution->name }}
                        </div>
                        @endif
                    </div>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('ai.chat') }}" class="inline-flex items-center gap-2 bg-white text-purple-600 px-5 py-2.5 rounded-xl hover:shadow-lg transition-all hover:scale-105 font-semibold">
                        <i class="ti ti-robot text-lg"></i>
                        Ask AI Assistant
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Books Card -->
            <div class="bg-white rounded-2xl p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(59, 130, 246, 0.12);">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-gray-500 text-sm font-medium">Total Books</span>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md">
                        <i class="ti ti-books text-white text-xl"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ Auth::user()->books()->count() }}</p>
                <p class="text-gray-400 text-sm mt-2">In your library</p>
            </div>

            <!-- Reading Card -->
            <div class="bg-white rounded-2xl p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(16, 185, 129, 0.12);">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-gray-500 text-sm font-medium">Currently Reading</span>
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-md">
                        <i class="ti ti-book-open text-white text-xl"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ Auth::user()->books()->wherePivot('status', 'reading')->count() }}</p>
                <p class="text-gray-400 text-sm mt-2">Books in progress</p>
            </div>

            <!-- Wallet Card -->
            <div class="bg-white rounded-2xl p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(245, 158, 11, 0.12);">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-gray-500 text-sm font-medium">Wallet Balance</span>
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-md">
                        <i class="ti ti-wallet text-white text-xl"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">TSh {{ number_format(Auth::user()->wallet_balance ?? 0, 2) }}</p>
                <p class="text-gray-400 text-sm mt-2">From referrals & quizzes</p>
                <div class="mt-2">
                    <a href="{{ route('withdrawals.index') }}" class="text-sm text-purple-600 hover:text-purple-700 underline">Request Withdrawal →</a>
                </div>
            </div>

            <!-- Certificates Card -->
            <div class="bg-white rounded-2xl p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(236, 72, 153, 0.12);">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-gray-500 text-sm font-medium">Certificates</span>
                    <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-rose-500 rounded-xl flex items-center justify-center shadow-md">
                        <i class="ti ti-certificate text-white text-xl"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ Auth::user()->certificates()->count() }}</p>
                <p class="text-gray-400 text-sm mt-2">Earned so far</p>
            </div>
        </div>


        <!-- INSTITUTIONS SECTION - SINGLE LONG BAR -->
        <!-- ============================================ -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl p-6 text-white shadow-xl border border-white/20">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <i class="ti ti-building-community text-3xl"></i>
                            <h3 class="text-2xl font-bold">Institutions</h3>
                        </div>
                        <p class="text-indigo-100 text-sm">Connect with learning communities and access exclusive resources</p>
                    </div>
                    <div class="flex gap-3 flex-wrap">
                        <!-- My Institution Button -->
                        <a href="{{ route('my.institution') }}" 
                           class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl transition font-semibold text-sm flex items-center gap-2">
                            <i class="ti ti-building"></i>
                            My Institution
                        </a>
                        <!-- Discover Institutions Button -->
                        <a href="{{ route('discover.institutions') }}" 
                           class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl transition font-semibold text-sm flex items-center gap-2">
                            <i class="ti ti-building-community"></i>
                            Discover Institutions
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- BECOME A CREATOR SECTION - ONLY FOR REGULAR USERS -->
        <!-- ============================================ -->
        @if(auth()->user()->role === 'user')
            <!-- Pending Application Alert -->
            @if(auth()->user()->hasPendingApplication())
            <div class="mb-8">
                <div class="bg-yellow-100 border-l-4 border-yellow-500 rounded-xl p-4 shadow-md">
                    <div class="flex items-center">
                        <i class="ti ti-alert-circle text-yellow-500 text-2xl mr-3"></i>
                        <div>
                            <p class="font-semibold text-yellow-800">Application Pending Review</p>
                            <p class="text-sm text-yellow-700">Your application is being reviewed by our team. You'll be notified once approved.</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Become a Creator Banner -->
            @if(!auth()->user()->hasPendingApplication() && !auth()->user()->isApprovedAuthor() && !auth()->user()->isApprovedBookseller())
            <div class="mb-8">
                <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl p-6 text-white shadow-xl">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <i class="ti ti-rocket text-3xl"></i>
                                <h3 class="text-2xl font-bold">Become a Creator</h3>
                            </div>
                            <p class="text-amber-100 text-sm">Share your knowledge and earn money by becoming an author or bookseller</p>
                        </div>
                        <div class="flex gap-3 mt-4 md:mt-0">
                            <a href="{{ route('applications.create', 'author') }}" class="bg-white text-amber-600 px-5 py-2.5 rounded-lg hover:shadow-lg transition font-semibold text-sm flex items-center gap-2">
                                <i class="ti ti-edit"></i> Become an Author
                            </a>
                            <a href="{{ route('applications.create', 'bookseller') }}" class="bg-white text-amber-600 px-5 py-2.5 rounded-lg hover:shadow-lg transition font-semibold text-sm flex items-center gap-2">
                                <i class="ti ti-shopping-cart"></i> Become a Bookseller
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endif

        <!-- Continue Learning -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-white">📖 Continue Learning</h2>
                <a href="#" class="text-indigo-300 text-sm hover:underline">View All →</a>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl transition-all duration-300 overflow-hidden group hover:shadow-xl">
                    <div class="h-1.5 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-800 text-lg">Intermediate AI Systems</h3>
                                <p class="text-sm text-gray-500 mt-1">Lesson 3 of 10 • 45 min remaining</p>
                                <div class="flex items-center gap-2 mt-3">
                                    <div class="w-40 bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full" style="width: 25%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-600">25%</span>
                                </div>
                            </div>
                            <a href="#" class="w-10 h-10 bg-indigo-50 rounded-full flex items-center justify-center group-hover:bg-indigo-500 transition-all">
                                <i class="ti ti-arrow-right text-indigo-600 group-hover:text-white"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl transition-all duration-300 overflow-hidden group hover:shadow-xl">
                    <div class="h-1.5 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-800 text-lg">Network Security Basics</h3>
                                <p class="text-sm text-gray-500 mt-1">Lesson 2 of 8 • 30 min remaining</p>
                                <div class="flex items-center gap-2 mt-3">
                                    <div class="w-40 bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-2 rounded-full" style="width: 15%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-600">15%</span>
                                </div>
                            </div>
                            <a href="#" class="w-10 h-10 bg-emerald-50 rounded-full flex items-center justify-center group-hover:bg-emerald-500 transition-all">
                                <i class="ti ti-arrow-right text-emerald-600 group-hover:text-white"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Browse Library -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-white">🔍 Browse Library</h2>
                <a href="{{ route('library.index') }}" class="text-indigo-300 text-sm hover:underline">View Library →</a>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="px-4 py-2 bg-gradient-to-r from-indigo-100 to-purple-100 text-purple-700 rounded-full text-sm font-medium shadow-sm">Popular This Week</span>
                <span class="px-4 py-2 bg-gradient-to-r from-pink-100 to-rose-100 text-rose-700 rounded-full text-sm font-medium shadow-sm">Trending Now</span>
                <span class="px-4 py-2 bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-700 rounded-full text-sm font-medium shadow-sm">Data Science</span>
                <span class="px-4 py-2 bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 rounded-full text-sm font-medium shadow-sm">Deep Learning</span>
                <span class="px-4 py-2 bg-gradient-to-r from-orange-100 to-amber-100 text-orange-700 rounded-full text-sm font-medium shadow-sm">SQL for Beginners</span>
                <span class="px-4 py-2 bg-gradient-to-r from-teal-100 to-green-100 text-teal-700 rounded-full text-sm font-medium shadow-sm">Mobile App Development</span>
                <span class="px-4 py-2 bg-gradient-to-r from-slate-100 to-gray-200 text-gray-700 rounded-full text-sm font-medium shadow-sm">Computer Science</span>

            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid lg:grid-cols-3 gap-6 mb-8">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-5 border border-indigo-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <i class="ti ti-users text-indigo-600 text-xl"></i>
                                <h3 class="font-semibold text-gray-800">Community Groups</h3>
                            </div>
                            <p class="text-gray-600 text-sm">Join study groups and track the most active communities.</p>
                        </div>
                        <a href="{{ route('community.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition shadow-md">
                            Find Groups →
                        </a>
                    </div>
                </div>
                
                <!-- Quiz Section -->
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-5 border border-purple-200">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex items-center gap-2">
                            <i class="ti ti-brain text-purple-600 text-xl"></i>
                            <h3 class="font-semibold text-gray-800">Interactive Quizzes</h3>
                        </div>
                        <a href="{{ route('quizzes.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700 transition shadow-md">
                            View All →
                        </a>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">Challenge yourself, earn certificates, and track your progress.</p>
                    
                    @php
                        $recentQuizAttempt = App\Models\QuizAttempt::where('user_id', auth()->id())
                                            ->with('quiz')
                                            ->latest()
                                            ->first();
                        $totalQuizzes = App\Models\Quiz::count();
                        $passedQuizzes = App\Models\QuizAttempt::where('user_id', auth()->id())
                                            ->where('passed', true)
                                            ->count();
                    @endphp
                    
                    <div class="grid grid-cols-3 gap-3 mt-3 pt-2 border-t border-purple-200">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-700">{{ $totalQuizzes }}</p>
                            <p class="text-xs text-gray-500">Total Quizzes</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600">{{ $passedQuizzes }}</p>
                            <p class="text-xs text-gray-500">Completed</p>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('quizzes.history') }}" class="text-purple-600 text-xs hover:underline flex items-center justify-center gap-1">
                                History
                                <i class="ti ti-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                    
                    @if($recentQuizAttempt)
                    <div class="mt-3 p-2 bg-white/60 rounded-lg">
                        <p class="text-xs text-gray-500">Last attempt</p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">{{ $recentQuizAttempt->quiz->title }}</span>
                            <span class="text-xs {{ $recentQuizAttempt->passed ? 'text-green-600' : 'text-red-600' }}">
                                {{ $recentQuizAttempt->percentage }}%
                            </span>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('quizzes.index') }}" class="mt-3 block text-center bg-purple-100 text-purple-700 py-2 rounded-lg text-sm font-medium hover:bg-purple-200 transition">
                        <i class="ti ti-plus"></i> Take Your First Quiz
                    </a>
                    @endif
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Achievements -->
                <div class="bg-white rounded-xl shadow-md p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="ti ti-trophy text-yellow-500 text-xl"></i>
                        <h3 class="font-semibold text-gray-800">Achievements</h3>
                    </div>
                    <div class="flex justify-around">
                        <div class="text-center">
                            <div class="w-14 h-14 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-full flex items-center justify-center mx-auto">
                                <i class="ti ti-certificate text-yellow-600 text-xl"></i>
                            </div>
                            <p class="text-xs text-gray-600 mt-1 font-semibold">{{ Auth::user()->certificates()->count() }}</p>
                            <p class="text-xs text-gray-400">Certificates</p>
                        </div>
                        <div class="text-center">
                            <div class="w-14 h-14 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center mx-auto">
                                <i class="ti ti-star text-purple-600 text-xl"></i>
                            </div>
                            <p class="text-xs text-gray-600 mt-1 font-semibold">{{ Auth::user()->average_quiz_score }}%</p>
                            <p class="text-xs text-gray-400">Quiz Avg</p>
                        </div>
                        <div class="text-center">
                            <div class="w-14 h-14 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full flex items-center justify-center mx-auto">
                                <i class="ti ti-flame text-orange-600 text-xl"></i>
                            </div>
                            <p class="text-xs text-gray-600 mt-1 font-semibold">{{ Auth::user()->streak_days ?? 0 }}</p>
                            <p class="text-xs text-gray-400">Day Streak</p>
                        </div>
                    </div>
                </div>

                <!-- Top Learner Card -->
                <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-xl p-5 text-white shadow-xl border border-white/20">
                    <div class="absolute inset-0 bg-black/10"></div>
                    <div class="relative">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                <i class="ti ti-user text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">{{ Auth::user()->full_name }}</h4>
                                <p class="text-xs text-purple-200">Level {{ Auth::user()->level ?? 1 }} Learner ⭐</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p class="text-sm text-purple-100">{{ Auth::user()->xp_points ?? 0 }} XP Total</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-purple-200">Progress to Level {{ (Auth::user()->level ?? 1) + 1 }}</span>
                                <span class="text-sm font-semibold">{{ Auth::user()->level_progress ?? 0 }}%</span>
                            </div>
                            <div class="mt-1 w-full bg-white/20 rounded-full h-1.5">
                                <div class="bg-gradient-to-r from-yellow-400 to-orange-500 h-1.5 rounded-full" style="width: {{ Auth::user()->level_progress ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
           
        
         <!-- QUOTE OF THE DAY SECTION -->
        <!-- ============================================ -->
         @include('components.quote-of-the-day')


        <!-- Knowledge Tips -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-5 mb-8 text-white shadow-xl border border-white/20">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="ti ti-lightbulb text-white text-xl"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-lg">💡 Knowledge Tips</h4>
                    <ul class="text-sm text-blue-100 mt-1 space-y-1">
                        <li>• Review your quizzes to see correct answers and explanations</li>
                        <li>• Ask the AI Assistant for help in explaining difficult topics</li>
                        <li>• Join study groups to discuss materials with other learners</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Marketplace CTA -->
        <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 rounded-xl p-6 text-white shadow-xl border border-white/20">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <i class="ti ti-shopping-cart text-2xl"></i>
                        <h3 class="text-2xl font-bold">Marketplace</h3>
                    </div>
                    <p class="text-amber-100">Student Notes & Lecture Guides</p>
                    <p class="text-amber-100 text-sm">Find quality notes and study quizzes or sell your own.</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-2 bg-white text-orange-600 px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all hover:scale-105">
                        Go to Marketplace
                        <i class="ti ti-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Join Request Modal -->
<div id="joinModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl max-w-md w-full mx-4 overflow-hidden transform transition-all">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">📝 Request to Join</h3>
                <button onclick="closeJoinModal()" class="text-white/80 hover:text-white">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('join-requests.store') }}" class="p-6">
            @csrf
            <input type="hidden" name="institution_id" id="join_institution_id">
            
            <div class="mb-4 p-4 bg-purple-50 rounded-xl">
                <p class="text-sm text-gray-600 mb-1">You are requesting to join:</p>
                <p class="font-bold text-gray-800 text-lg" id="join_institution_name"></p>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Optional Message</label>
                <textarea name="message" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                          placeholder="Why do you want to join this institution? (Optional)"></textarea>
                <p class="text-xs text-gray-400 mt-1">This message will be sent to the institution admin</p>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-2.5 rounded-lg hover:shadow-lg transition font-semibold">
                    Send Request
                </button>
                <button type="button" onclick="closeJoinModal()" class="px-6 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openJoinModal(institutionId, institutionName) {
    document.getElementById('join_institution_id').value = institutionId;
    document.getElementById('join_institution_name').textContent = institutionName;
    document.getElementById('joinModal').classList.remove('hidden');
    document.getElementById('joinModal').classList.add('flex');
}

function closeJoinModal() {
    document.getElementById('joinModal').classList.add('hidden');
    document.getElementById('joinModal').classList.remove('flex');
}

// Close modal on click outside
document.getElementById('joinModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeJoinModal();
    }
});
</script>
@endsection