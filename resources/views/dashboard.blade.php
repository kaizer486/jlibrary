@extends('layouts.app')

@section('content')
<!-- ========================================== -->
<!-- BACKGROUND: Light Bisque                   -->
<!-- ========================================== -->
<div class="min-h-screen" style="background: #e9e8e6; padding-top: 0; margin-top: 0;">
    <div class="container mx-auto px-4 py-0 max-w-7xl" style="padding-top: 0; margin-top: 0;">
        
        <!-- ========================================== -->
        <!-- WELCOME BANNER - Warm Orange/Amber Style   -->
        <!-- ========================================== -->
        <div class="rounded-2xl p-6 mb-4 border-2 border-orange-200/80 shadow-md" style="
            background: white;
            border-radius: 20px;
            margin-top: 0;
        ">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-extrabold" style="color: #1E293B;">Welcome back, {{ Auth::user()->full_name }}.</h1>
                    <p class="text-sm font-medium mt-1" style="
                        background: linear-gradient(135deg, #db570a, #e87a2a, #f59e4c);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        background-clip: text;
                    ">
                        Great to see you again! Ready to continue learning?
                    </p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('ai.chat') }}" class="inline-flex items-center gap-2 text-white font-medium px-5 py-2.5 transition-all duration-300 hover:-translate-y-1 border-2 border-orange-400/30" style="
                        background: linear-gradient(135deg, #db570a, #e87a2a);
                        box-shadow: 0 4px 16px rgba(219,87,10,0.2);
                        border-radius: 14px;
                    ">
                        <i class="ti ti-robot"></i>
                        Ask AI Assistant
                    </a>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- QUOTE - Glassmorphic with Dark Blue Text  -->
        <!-- ========================================== -->
        @php
            $quote = \App\Models\Quote::where('status', 'active')
                ->inRandomOrder()
                ->first();
        @endphp

        @if($quote)
            <div class="mb-2" style="
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border-radius: 20px; 
                padding: 20px 24px;
                border: 2px solid rgba(255, 255, 255, 0.8);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.6);
                position: relative;
                overflow: hidden;
                text-align: center;
                font-style: italic;
            ">
                <div class="relative z-10">
                    <p style="
                        font-size: 14px; 
                        text-transform: uppercase; 
                        letter-spacing: 3px; 
                        font-weight: 600; 
                        color: #db570a;
                        margin-bottom: 6px;
                        font-family: 'Poppins', sans-serif;
                        font-style: italic;
                    ">
                        Daily Quote
                    </p>
                    
                    <p class="text-base md:text-lg font-medium leading-relaxed" style="
                        font-family: 'Poppins', sans-serif;
                        color: #0f172a;
                        font-weight: 500;
                        letter-spacing: 0.3px;
                        font-size: 20px;
                    ">
                        "{{ $quote->quote_text }}"
                    </p>
                    
                    <p class="text-sm font-medium mt-3" style="
                        color: #1e293b !important; 
                        font-family: 'Inter', sans-serif; 
                        font-weight: 500;
                    ">
                        — {{ $quote->author ?? 'Unknown' }}
                    </p>
                </div>
            </div>
        @else
            <div class="mb-4" style="
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border-radius: 20px; 
                padding: 24px 28px;
                border: 2px solid rgba(255, 255, 255, 0.8);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.6);
                position: relative;
                overflow: hidden;
                text-align: center;
            ">
                <div class="relative z-10">
                    <p style="
                        font-size: 14px; 
                        text-transform: uppercase; 
                        letter-spacing: 3px; 
                        font-weight: 600; 
                        color: #db570a;
                        margin-bottom: 6px;
                        font-family: 'Inter', sans-serif;
                    ">
                        Daily Quote
                    </p>
                    
                    <div style="margin-bottom: 4px;">
                        <i class="ti ti-quote" style="color: #db570a; font-size: 28px; opacity: 0.2;"></i>
                    </div>
                    
                    <p class="text-base md:text-lg font-medium leading-relaxed" style="
                        font-family: 'Georgia', 'Times New Roman', serif;
                        color: #0f172a;
                        font-weight: 500;
                        letter-spacing: 0.3px;
                        font-size: 20px;
                    ">
                        "The only way to do great work is to love what you do."
                    </p>
                    
                    <p class="text-sm font-medium mt-3" style="
                        color: #1e293b !important; 
                        font-family: 'Inter', sans-serif; 
                        font-weight: 500;
                    ">
                        — Steve Jobs
                    </p>
                </div>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- STATS CARDS - Glass Card Style            -->
        <!-- ========================================== -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-6">
            <!-- Total Books -->
            <div class="rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 border-2 border-slate-200/80 shadow-md" style="
                background: white;
                border-radius: 20px;
            ">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: #64748B;">Total Books</p>
                        <p class="text-2xl font-bold mt-1" style="color: #1E293B;">{{ Auth::user()->books()->count() }}</p>
                    </div>
                    <div class="w-14 h-14 rounded-full flex items-center justify-center" style="
                        background: linear-gradient(135deg, #db570a, #e87a2a);
                        box-shadow: 0 4px 12px rgba(219,87,10,0.15);
                    ">
                        <i class="ti ti-books text-white text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Currently Reading -->
            <div class="rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 border-2 border-slate-200/80 shadow-md" style="
                background: white;
                border-radius: 20px;
            ">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: #64748B;">Currently Reading</p>
                        <p class="text-2xl font-bold mt-1" style="color: #1E293B;">{{ Auth::user()->books()->wherePivot('status', 'reading')->count() }}</p>
                    </div>
                    <div class="w-14 h-14 rounded-full flex items-center justify-center" style="
                        background: linear-gradient(135deg, #db570a, #e87a2a);
                        box-shadow: 0 4px 12px rgba(219,87,10,0.15);
                    ">
                       <i class="ti ti-book text-white text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Wallet Balance -->
            <div class="rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 border-2 border-slate-200/80 shadow-md" style="
                background: white;
                border-radius: 20px;
            ">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: #64748B;">Wallet Balance</p>
                        <p class="text-2xl font-bold mt-1" style="color: #1E293B;">TSh {{ number_format(Auth::user()->wallet_balance ?? 0, 2) }}</p>
                    </div>
                    <div class="w-14 h-14 rounded-full flex items-center justify-center" style="
                        background: linear-gradient(135deg, #db570a, #e87a2a);
                        box-shadow: 0 4px 12px rgba(219,87,10,0.15);
                    ">
                        <i class="ti ti-wallet text-white text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Certificates -->
            <div class="rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 border-2 border-slate-200/80 shadow-md" style="
                background: white;
                border-radius: 20px;
            ">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: #64748B;">Certificates</p>
                        <p class="text-2xl font-bold mt-1" style="color: #1E293B;">{{ Auth::user()->certificates()->count() }}</p>
                    </div>
                    <div class="w-14 h-14 rounded-full flex items-center justify-center" style="
                        background: linear-gradient(135deg, #db570a, #e87a2a);
                        box-shadow: 0 4px 12px rgba(219,87,10,0.15);
                    ">
                        <i class="ti ti-certificate text-white text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MAIN TWO COLUMN LAYOUT                     -->
        <!-- ========================================== -->
        <div class="grid lg:grid-cols-3 gap-6 mb-6">
            
            <!-- LEFT COLUMN (2/3) -->
            <div class="lg:col-span-2 space-y-6">
                
               <!-- Become a Creator -->
@if(!auth()->user()->isApprovedAuthor() && !auth()->user()->isApprovedBookseller())
    @if(auth()->user()->hasPendingApplication())
        <div class="rounded-2xl p-5 border-2 border-amber-200/80 shadow-md" style="background: #FEFCE8; border-radius: 20px;">
            <div class="flex items-center gap-3">
                <i class="ti ti-alert-circle text-2xl" style="color: #F59E0B;"></i>
                <div>
                    <p class="font-semibold" style="color: #92400E;">Application Pending Review</p>
                    <p class="text-sm" style="color: #B45309;">Your Creator application is being reviewed. You'll be notified once approved.</p>
                </div>
            </div>
        </div>
    @else
        <div class="rounded-2xl p-5 border-2 border-orange-200/80 shadow-md" style="background: #FFF7ED; border-radius: 20px;">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <i class="ti ti-rocket text-xl" style="color: #EA580C;"></i>
                        <h3 class="text-xl font-bold" style="color: #1E293B;">Become a Creator</h3>
                    </div>
                    <p class="text-sm font-medium" style="color: #64748B;">Publish books, sell products, and earn from your content</p>
                </div>
                <div class="flex gap-3 mt-3 md:mt-0">
                    <a href="{{ route('applications.create', 'creator') }}" class="text-white font-medium px-5 py-2.5 transition-all duration-300 hover:-translate-y-1 flex items-center gap-2 border-2 border-orange-400/30" style="background: linear-gradient(135deg, #db570a, #e87a2a); box-shadow: 0 4px 12px rgba(219,87,10,0.15); border-radius: 14px;">
                        <i class="ti ti-crown"></i> Apply as Creator
                    </a>
                </div>
            </div>
        </div>
    @endif
@endif
                <!-- Continue Learning -->
                <div class="rounded-2xl overflow-hidden border-2 border-slate-200/80 shadow-md" style="
                    background: white;
                    border-radius: 20px;
                ">
                    <div class="px-6 py-4 flex items-center justify-between" style="border-bottom: 2px solid #e9e8e6;">
                        <h2 class="text-xl font-bold flex items-center gap-2" style="color: #1E293B;">
                            <i class="ti ti-book-2" style="color: #db570a;"></i>
                            Continue Learning
                        </h2>
                        <a href="{{ route('library.my-library') }}" class="text-sm font-medium hover:underline" style="color: #db570a;">View All →</a>
                    </div>
                    <div class="p-6">
                        @php
                            $readingBooks = Auth::user()->books()->wherePivot('status', 'reading')->take(3)->get();
                        @endphp
                        @if($readingBooks->count() > 0)
                            <div class="space-y-4">
                                @foreach($readingBooks as $book)
                                <div class="rounded-xl p-4 transition-all hover:bg-orange-50/50 border border-slate-200/60" style="background: rgba(255, 255, 255, 0.4); border-radius: 16px;">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="font-semibold text-sm" style="color: #1E293B;">{{ $book->title }}</p>
                                            <div class="flex items-center gap-2 mt-2">
                                                <div class="flex-1 max-w-xs rounded-full" style="background: #E2E8F0; height: 8px; border-radius: 999px;">
                                                    <div class="rounded-full" style="
                                                        background: linear-gradient(90deg, #db570a, #e87a2a);
                                                        height: 8px;
                                                        border-radius: 999px;
                                                        width: {{ $book->pivot->progress_percent ?? 0 }}%;
                                                    "></div>
                                                </div>
                                                <span class="text-xs font-medium" style="color: #64748B;">{{ $book->pivot->progress_percent ?? 0 }}%</span>
                                            </div>
                                        </div>
                                        <a href="{{ route('library.read', $book) }}" class="text-white text-xs font-medium px-4 py-2 transition-all duration-300 hover:-translate-y-1 border-2 border-orange-400/30" style="
                                            background: linear-gradient(135deg, #db570a, #e87a2a);
                                            box-shadow: 0 4px 12px rgba(219,87,10,0.15);
                                            border-radius: 14px;
                                        ">Continue</a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm font-medium text-center py-4" style="color: #64748B;">No books in progress. <a href="{{ route('library.index') }}" class="font-semibold hover:underline" style="color: #db570a;">Browse library →</a></p>
                        @endif
                    </div>
                </div>

                <!-- Interactive Quizzes -->
                <div class="rounded-2xl overflow-hidden border-2 border-slate-200/80 shadow-md" style="
                    background: white;
                    border-radius: 20px;
                ">
                    <div class="px-6 py-4 flex items-center justify-between" style="border-bottom: 2px solid #e9e8e6;">
                        <h2 class="text-xl font-bold flex items-center gap-2" style="color: #1E293B;">
                            <i class="ti ti-brain" style="color: #db570a;"></i>
                            Interactive Quizzes
                        </h2>
                        <a href="{{ route('quizzes.index') }}" class="text-sm font-medium hover:underline" style="color: #db570a;">View All →</a>
                    </div>
                    <div class="p-6">
                        @php
                            $totalQuizzes = App\Models\Quiz::count();
                            $passedQuizzes = App\Models\QuizAttempt::where('user_id', auth()->id())->where('passed', true)->count();
                            $recentQuiz = App\Models\QuizAttempt::where('user_id', auth()->id())->with('quiz')->latest()->first();
                        @endphp
                        <div class="grid grid-cols-3 gap-3">
                            <div class="text-center p-3 rounded-xl border-2 border-orange-200/60" style="background: #FFF7ED;">
                                <p class="text-2xl font-bold" style="color: #db570a;">{{ $totalQuizzes }}</p>
                                <p class="text-xs font-medium" style="color: #64748B;">Total Quizzes</p>
                            </div>
                            <div class="text-center p-3 rounded-xl border-2 border-emerald-200/60" style="background: #F0FDF4;">
                                <p class="text-2xl font-bold" style="color: #16A34A;">{{ $passedQuizzes }}</p>
                                <p class="text-xs font-medium" style="color: #64748B;">Completed</p>
                            </div>
                            <div class="text-center p-3 rounded-xl border-2 border-red-200/60" style="background: #FEF2F2;">
                                <p class="text-2xl font-bold" style="color: #DC2626;">{{ $totalQuizzes - $passedQuizzes }}</p>
                                <p class="text-xs font-medium" style="color: #64748B;">Remaining</p>
                            </div>
                        </div>
                        @if($recentQuiz)
                        <div class="mt-3 p-3 rounded-xl flex items-center justify-between border border-slate-200/60" style="background: rgba(255, 255, 255, 0.4); border-radius: 16px;">
                            <div>
                                <p class="text-xs font-medium" style="color: #64748B;">Last attempt</p>
                                <p class="text-sm font-semibold" style="color: #1E293B;">{{ $recentQuiz->quiz->title }}</p>
                            </div>
                            <span class="text-sm font-bold {{ $recentQuiz->passed ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $recentQuiz->percentage }}%
                            </span>
                        </div>
                        @else
                        <a href="{{ route('quizzes.index') }}" class="mt-3 block text-center font-medium py-2 transition-all hover:bg-opacity-80 border-2 border-orange-200/60" style="
                            background: #FFF7ED;
                            color: #db570a;
                            border-radius: 14px;
                        ">
                            <i class="ti ti-plus"></i> Take Your First Quiz
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Achievements -->
                <div class="rounded-2xl overflow-hidden border-2 border-slate-200/80 shadow-md" style="
                    background: white;
                    border-radius: 20px;
                ">
                    <div class="px-6 py-4" style="border-bottom: 2px solid #e9e8e6;">
                        <h2 class="text-xl font-bold flex items-center gap-2" style="color: #1E293B;">
                            <i class="ti ti-trophy" style="color: #F59E0B;"></i>
                            Achievements
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-3 gap-3">
                            <div class="text-center">
                                <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto border-2 border-orange-200/60" style="
                                    background: linear-gradient(135deg, #db570a, #e87a2a);
                                    box-shadow: 0 4px 12px rgba(219,87,10,0.15);
                                ">
                                    <i class="ti ti-certificate text-white text-xl"></i>
                                </div>
                                <p class="text-lg font-bold mt-2" style="color: #1E293B;">{{ Auth::user()->certificates()->count() }}</p>
                                <p class="text-xs font-medium" style="color: #64748B;">Certificates</p>
                            </div>
                            <div class="text-center">
                                <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto border-2 border-orange-200/60" style="
                                    background: linear-gradient(135deg, #db570a, #e87a2a);
                                    box-shadow: 0 4px 12px rgba(219,87,10,0.15);
                                ">
                                    <i class="ti ti-star text-white text-xl"></i>
                                </div>
                                <p class="text-lg font-bold mt-2" style="color: #1E293B;">{{ Auth::user()->average_quiz_score ?? 0 }}%</p>
                                <p class="text-xs font-medium" style="color: #64748B;">Quiz Avg</p>
                            </div>
                            <div class="text-center">
                                <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto border-2 border-orange-200/60" style="
                                    background: linear-gradient(135deg, #db570a, #e87a2a);
                                    box-shadow: 0 4px 12px rgba(219,87,10,0.15);
                                ">
                                    <i class="ti ti-flame text-white text-xl"></i>
                                </div>
                                <p class="text-lg font-bold mt-2" style="color: #1E293B;">{{ Auth::user()->streak_days ?? 0 }}</p>
                                <p class="text-xs font-medium" style="color: #64748B;">Day Streak</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN (1/3) -->
            <div class="space-y-6">
                
                <!-- Latest Updates -->
                <div class="rounded-2xl p-6 border-2 border-slate-200/80 shadow-md" style="
                    background: white;
                    border-radius: 20px;
                ">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center border-2 border-orange-200/60" style="
                            background: linear-gradient(135deg, #db570a, #e87a2a);
                            box-shadow: 0 4px 12px rgba(219,87,10,0.15);
                        ">
                            <i class="ti ti-news text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold" style="color: #1E293B;">Latest Updates</h3>
                    </div>

                    @php
                        $newsItems = \App\Models\NewsItem::active()->ordered()->latestPublished()->limit(6)->get();
                    @endphp

                    @if($newsItems->count() > 0)
                        <div class="space-y-3 max-h-[600px] overflow-y-auto pr-1 custom-scrollbar">
                            @foreach($newsItems as $item)
                                <div class="rounded-xl p-3 transition-all hover:bg-orange-50/50 border border-slate-200/60" style="background: rgba(255, 255, 255, 0.4); border-radius: 16px;">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full border" style="
                                            @if($item->category == 'Books') background: #F3E8FF; color: #7E22CE; border-color: #E9D5FF;
                                            @elseif($item->category == 'Events') background: #F0FDF4; color: #16A34A; border-color: #BBF7D0;
                                            @elseif($item->category == 'Certificates') background: #FFF7ED; color: #EA580C; border-color: #FED7AA;
                                            @elseif($item->category == 'Announcements') background: #F3E8FF; color: #7E22CE; border-color: #E9D5FF;
                                            @elseif($item->category == 'Authors') background: #FCE7F3; color: #DB2777; border-color: #F9A8D4;
                                            @else background: #F1F5F9; color: #64748B; border-color: #E2E8F0; @endif
                                            border-radius: 999px;
                                        ">
                                            {{ $item->category ?? 'General' }}
                                        </span>
                                        @if($item->is_featured)
                                            <span class="text-xs">⭐</span>
                                        @endif
                                    </div>
                                    @if($item->link)
                                        <a href="{{ $item->link }}" class="text-sm font-semibold hover:underline block" style="color: #1E293B;">{{ $item->title }}</a>
                                    @else
                                        <p class="text-sm font-semibold" style="color: #1E293B;">{{ $item->title }}</p>
                                    @endif
                                    <p class="text-xs font-medium mt-1 flex items-center gap-1" style="color: #64748B;">
                                        <i class="ti ti-clock"></i> {{ $item->published_at->format('M d, Y') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm font-medium text-center py-6" style="color: #64748B;">No updates yet</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- KNOWLEDGE TIPS                            -->
        <!-- ========================================== -->
        <div class="rounded-2xl p-6 mb-6 border-2 border-slate-200/80 shadow-md" style="
            background: white;
            border-radius: 20px;
        ">
            <div class="flex items-start gap-3">
                <div class="w-14 h-14 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-orange-200/60" style="
                    background: linear-gradient(135deg, #db570a, #e87a2a);
                    box-shadow: 0 4px 12px rgba(219,87,10,0.15);
                ">
                    <i class="ti ti-bulb text-white text-xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-lg" style="color: #1E293B;">💡 Knowledge Tips</h4>
                    <ul class="text-sm font-medium mt-1 space-y-1" style="color: #64748B;">
                        <li>• Review your quizzes to see correct answers and explanations</li>
                        <li>• Ask the AI Assistant for help in explaining difficult topics</li>
                        <li>• Join study groups to discuss materials with other learners</li>
                    </ul>
                </div>
            </div>
        </div>

       <!-- ========================================== -->
<!-- BROWSE LIBRARY TAGS                        -->
<!-- ========================================== -->
<div class="rounded-2xl p-6 border-2 border-slate-200/80 shadow-md" style="
    background: white;
    border-radius: 20px;
">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold flex items-center gap-2" style="color: #1E293B;">
            <i class="ti ti-search" style="color: #db570a;"></i>
            Browse Library
        </h2>
        <a href="{{ route('library.index') }}" class="text-sm font-medium hover:underline" style="color: #db570a;">View All Books →</a>
    </div>
    
    @php
        // Get counts for each section
        $trendingCount = App\Models\Book::where('is_trending', true)
            ->where('status', 'approved')
            ->count();
        
        $recentCount = App\Models\Book::where('status', 'approved')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        
        $featuredCount = App\Models\Book::where('is_featured', true)
            ->where('status', 'approved')
            ->count();
        
        $freeCount = App\Models\Book::where('status', 'approved')
            ->where('is_paid', false)
            ->count();
        
        // Get popular categories with book counts
        $popularCategories = App\Models\Book::where('status', 'approved')
            ->select('category', \DB::raw('count(*) as total'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->limit(6)
            ->get();
    @endphp
    
    <div class="flex flex-wrap gap-2">
        
        <!-- ========================================== -->
        <!-- TRENDING - Clickable Badge Only            -->
        <!-- ========================================== -->
        @if($trendingCount > 0)
            <a href="{{ route('library.index', ['trending' => 'true']) }}" 
               class="px-4 py-1.5 text-sm font-medium rounded-full border-2 border-red-200/60 hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 flex items-center gap-1" 
               style="background: #FEF2F2; color: #DC2626; border-radius: 999px; text-decoration: none;">
                <i class="ti ti-flame"></i> Trending
                <span class="text-xs text-red-400 bg-red-50 px-1.5 py-0.5 rounded-full">({{ $trendingCount }})</span>
            </a>
        @endif
        
        <!-- ========================================== -->
        <!-- NEW ARRIVALS - Clickable Badge Only        -->
        <!-- ========================================== -->
        @if($recentCount > 0)
            <a href="{{ route('library.index', ['recent' => 'true']) }}" 
               class="px-4 py-1.5 text-sm font-medium rounded-full border-2 border-emerald-200/60 hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 flex items-center gap-1" 
               style="background: #F0FDF4; color: #16A34A; border-radius: 999px; text-decoration: none;">
                <i class="ti ti-clock"></i> New Arrivals
                <span class="text-xs text-emerald-400 bg-emerald-50 px-1.5 py-0.5 rounded-full">({{ $recentCount }})</span>
            </a>
        @endif
        
        <!-- ========================================== -->
        <!-- FEATURED - Clickable Badge Only            -->
        <!-- ========================================== -->
        @if($featuredCount > 0)
            <a href="{{ route('library.index', ['featured' => 'true']) }}" 
               class="px-4 py-1.5 text-sm font-medium rounded-full border-2 border-purple-200/60 hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 flex items-center gap-1" 
               style="background: #F3E8FF; color: #7E22CE; border-radius: 999px; text-decoration: none;">
                <i class="ti ti-star text-yellow-500 text-xs"></i> Featured
                <span class="text-xs text-purple-400 bg-purple-50 px-1.5 py-0.5 rounded-full">({{ $featuredCount }})</span>
            </a>
        @endif
        
        <!-- ========================================== -->
        <!-- FREE BOOKS - Clickable Badge Only          -->
        <!-- ========================================== -->
        @if($freeCount > 0)
            <a href="{{ route('library.index', ['price_type' => 'free']) }}" 
               class="px-4 py-1.5 text-sm font-medium rounded-full border-2 border-blue-200/60 hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 flex items-center gap-1" 
               style="background: #EFF6FF; color: #2563EB; border-radius: 999px; text-decoration: none;">
                <i class="ti ti-gift"></i> Free Books
                <span class="text-xs text-blue-400 bg-blue-50 px-1.5 py-0.5 rounded-full">({{ $freeCount }})</span>
            </a>
        @endif
        
      
        
        <!-- ========================================== -->
        <!-- VIEW ALL CATEGORIES                        -->
        <!-- ========================================== -->
        <a href="{{ route('library.index') }}" 
           class="px-4 py-1.5 text-sm font-medium rounded-full border-2 border-dashed border-orange-200/60 hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 flex items-center gap-1" 
           style="background: #FFF7ED; color: #EA580C; border-radius: 999px; text-decoration: none;">
            <i class="ti ti-plus"></i> More Categories
        </a>
    </div>
</div>

    </div>
</div>

<style>
    /* Custom scrollbar for Latest Updates */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #F1F5F9;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #FED7AA;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #FCD34D;
    }
</style>
@endsection