@extends('layouts.app')

@section('content')

<!-- HERO SECTION -->
<section class="relative overflow-hidden min-h-screen flex flex-col"
         style="background: linear-gradient(135deg, #1e0a3c 0%, #6b21a8 45%, #a855f7 75%, #ec4899 100%);">

    {{-- Decorative orbs (kept from original, made larger & more layered) --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-24 -left-16 w-96 h-96 rounded-full opacity-25 animate-pulse"
             style="background: radial-gradient(circle, #a855f7, transparent); filter: blur(72px);"></div>
        <div class="absolute top-1/2 left-1/2 w-72 h-72 rounded-full opacity-15 animate-pulse"
             style="background: radial-gradient(circle, #7c3aed, transparent); filter: blur(60px); animation-delay:.8s; transform:translate(-50%,-50%)"></div>
        <div class="absolute -bottom-16 -right-12 w-96 h-96 rounded-full opacity-20 animate-pulse"
             style="background: radial-gradient(circle, #ec4899, transparent); filter: blur(72px); animation-delay:1.2s"></div>
    </div>

    {{-- ── NEW: Top Navbar ──────────────────────────────────── --}}
    <nav class="relative z-10 flex items-center justify-between px-6 md:px-12 py-5">
       <div class="flex items-center gap-2">
    <img src="{{ asset('images/jlibrary.jpeg') }}" alt="Logo" class="h-10 w-auto">
    <span class="text-white text-lg tracking-tight">JLIBRARY</span>
</div>
        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-white/70">
            <a href="#features" class="hover:text-white transition-colors">Browse</a>
            <a href="#" class="hover:text-white transition-colors">Community</a>
            <a href="#" class="hover:text-white transition-colors">Certificates</a>
            <a href="#" class="hover:text-white transition-colors">Marketplace</a>
        </div>
        @guest
            <a href="{{ route('login') }}"
               class="hidden md:inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-semibold text-white
                      border border-white/30 hover:bg-white/10 transition-all backdrop-blur-sm">
                <i class="ti ti-login"></i> Login
            </a>
        @else
            <a href="{{ route('dashboard') }}"
               class="hidden md:inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-semibold text-white
                      border border-white/30 hover:bg-white/10 transition-all backdrop-blur-sm">
                <i class="ti ti-dashboard"></i> Dashboard
            </a>
        @endguest
    </nav>
    {{-- ── End Navbar ───────────────────────────────────────── --}}

    {{-- Hero body: two-column on desktop, stacked on mobile --}}
    <div class="relative z-10 flex-1 flex items-center">
        <div class="container mx-auto px-6 md:px-12 py-12 md:py-20">
            <div class="flex flex-col lg:flex-row items-center gap-12">

                {{-- Left: headline + CTAs + stats --}}
                <div class="flex-1 text-center lg:text-left">

                    {{-- Badge (kept from original, refined) --}}
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-semibold text-white/90 mb-6"
                         style="background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); backdrop-filter:blur(8px)">
                        <i class="ti ti-sparkles text-yellow-400"></i>
                        Welcome to the Future of Digital Learning
                    </div>

                    <h1 class="text-4xl md:text-5xl lg:text-7xl font-black leading-none tracking-tighter text-white mb-5">
                        Learn. Share.
                        <span class="bg-gradient-to-r from-yellow-400 to-pink-400 bg-clip-text text-transparent">
                            Grow Together.
                        </span>
                    </h1>

                    <p class="text-lg text-white/70 mb-8 max-w-x2 mx-auto lg:mx-0 leading-relaxed">
                        Your all-in-one digital library platform. Read thousands of books, join communities,<br>
                         sell your work, and earn certificates.
                    </p>

                    {{-- CTAs --}}
                    <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                        @guest
                            <a href="{{ route('register') }}"
                               class="inline-flex items-center justify-center gap-2 bg-white text-purple-700 px-7 py-3 mx-7 rounded-full
                                      font-bold text-sm hover:shadow-2xl hover:-translate-y-0.5 transition-all">
                                <i class="ti ti-rocket"></i> Get Started Free
                            </a>
                            {{-- NEW: softer secondary CTA --}}
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center justify-center gap-2 border-2 border-white/30 text-white px-7 py-3
                                      rounded-full font-semibold text-sm hover:bg-white/10 transition-all backdrop-blur-sm">
                                <i class="ti ti-login"></i> Login
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center justify-center gap-2 bg-white text-purple-700 px-7 py-3 rounded-full
                                      font-bold text-sm hover:shadow-2xl hover:-translate-y-0.5 transition-all">
                                <i class="ti ti-dashboard"></i> Go to Dashboard
                            </a>
                            <a href="{{ route('library.index') }}"
                               class="inline-flex items-center justify-center gap-2 border-2 border-white/30 text-white px-7 py-3
                                      rounded-full font-semibold text-sm hover:bg-white/10 transition-all backdrop-blur-sm">
                                <i class="ti ti-books"></i> Browse Library
                            </a>
                        @endguest
                    </div>

                    {{-- NEW: Stats bar --}}
                    <div class="mt-10 flex items-center gap-6 justify-center lg:justify-start">
                        <div>
                            <p class="text-2xl font-black text-white tracking-tight">12K+</p>
                            <p class="text-xs text-white/50 font-medium mt-0.5">Books available</p>
                        </div>
                        <div class="h-8 w-px bg-white/20"></div>
                        <div>
                            <p class="text-2xl font-black text-white tracking-tight">8.4K</p>
                            <p class="text-xs text-white/50 font-medium mt-0.5">Active readers</p>
                        </div>
                        <div class="h-8 w-px bg-white/20"></div>
                        <div>
                            <p class="text-2xl font-black text-white tracking-tight">320+</p>
                            <p class="text-xs text-white/50 font-medium mt-0.5">Certificates issued</p>
                        </div>
                    </div>
                </div>

                {{-- NEW: Right column — mini feature cards --}}
                <div class="flex-shrink-0 w-full lg:w-64 flex flex-col gap-3">
                    <div class="flex items-center gap-3 p-4 rounded-2xl backdrop-blur-sm hover:bg-white/15 transition-all"
                         style="background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15)">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background:rgba(251,191,36,.2)">
                            <i class="ti ti-book-open text-yellow-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Digital Library</p>
                            <p class="text-xs text-white/55 leading-snug">Thousands of titles, always available</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-4 rounded-2xl backdrop-blur-sm hover:bg-white/15 transition-all"
                         style="background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15)">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background:rgba(236,72,153,.2)">
                            <i class="ti ti-users text-pink-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Community</p>
                            <p class="text-xs text-white/55 leading-snug">Connect with learners worldwide</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-4 rounded-2xl backdrop-blur-sm hover:bg-white/15 transition-all"
                         style="background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15)">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background:rgba(45,212,191,.2)">
                            <i class="ti ti-certificate text-teal-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Earn Certificates</p>
                            <p class="text-xs text-white/55 leading-snug">Prove your skills and knowledge</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-4 rounded-2xl backdrop-blur-sm hover:bg-white/15 transition-all"
                         style="background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15)">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background:rgba(251,191,36,.2)">
                            <i class="ti ti-shopping-bag text-yellow-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Marketplace</p>
                            <p class="text-xs text-white/55 leading-snug">Sell your books and courses</p>
                        </div>
                    </div>
                </div>
                {{-- End right column --}}

            </div>
        </div>
    </div>
</section>
{{-- End Hero --}}


<!-- ================================================
     FEATURES SECTION  (kept from original, refined)
     Improvements: Rounded pill icon bg, richer hover
     state, subtle top border accent per card color.
================================================ -->
<section id="features" class="py-24 bg-gray-50">
    <div class="container mx-auto px-6 md:px-12">
        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">
                Everything You Need in One Platform
            </h2>
            <p class="text-gray-500 max-w-xl mx-auto">
                Powerful features to help you learn, share, and grow your knowledge
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Card: Digital Library --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm
                        hover:shadow-xl hover:-translate-y-1 transition-all duration-300
                        border-t-4 border-t-purple-500">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="ti ti-books text-2xl text-purple-600"></i>
                </div>
                <h3 class="text-base font-semibold mb-1 text-gray-900">Digital Library</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Access thousands of free and premium books, any time.</p>
            </div>

            {{-- Card: Learning Community --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm
                        hover:shadow-xl hover:-translate-y-1 transition-all duration-300
                        border-t-4 border-t-green-500">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="ti ti-users text-2xl text-green-600"></i>
                </div>
                <h3 class="text-base font-semibold mb-1 text-gray-900">Learning Community</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Join groups, discuss books, and grow with readers worldwide.</p>
            </div>

            {{-- Card: AI Assistant --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm
                        hover:shadow-xl hover:-translate-y-1 transition-all duration-300
                        border-t-4 border-t-blue-500">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="ti ti-robot text-2xl text-blue-600"></i>
                </div>
                <h3 class="text-base font-semibold mb-1 text-gray-900">AI Assistant</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Get personalised AI-powered learning guidance and book recommendations.</p>
            </div>

            {{-- Card: Book Marketplace --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm
                        hover:shadow-xl hover:-translate-y-1 transition-all duration-300
                        border-t-4 border-t-orange-500">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="ti ti-shopping-cart text-2xl text-orange-600"></i>
                </div>
                <h3 class="text-base font-semibold mb-1 text-gray-900">Book Marketplace</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Publish and sell your own books directly to the community.</p>
            </div>
        </div>
    </div>
</section>
{{-- End Features --}}


<!-- ================================================
     CTA SECTION  (kept from original)
     Improvements: Tighter copy, pill button shape,
     subtle pattern overlay for depth.
================================================ -->
<section class="py-20 relative overflow-hidden"
         style="background: linear-gradient(135deg, #7c3aed 0%, #ec4899 100%);">
    {{-- Decorative circles --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-16 -left-16 w-64 h-64 rounded-full opacity-10"
             style="background:#fff; filter:blur(48px)"></div>
        <div class="absolute -bottom-16 -right-16 w-64 h-64 rounded-full opacity-10"
             style="background:#fff; filter:blur(48px)"></div>
    </div>

    <div class="relative container mx-auto px-6 md:px-12 text-center">
        <p class="text-white/60 text-sm font-semibold uppercase tracking-widest mb-3">Start today — it's free</p>
        <h2 class="text-3xl md:text-5xl font-black text-white mb-4 tracking-tight">
            Ready to Start Your<br class="hidden md:block"> Learning Journey?
        </h2>
        <p class="text-white/70 text-lg mb-10 max-w-xl mx-auto">
            Join thousands of learners already growing with JLIBRARY
        </p>
        @guest
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 bg-white text-purple-700 px-8 py-3.5 rounded-full
                      font-bold text-sm hover:shadow-2xl hover:-translate-y-0.5 transition-all">
                <i class="ti ti-user-plus"></i>
                Create Free Account
            </a>
        @else
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 bg-white text-purple-700 px-8 py-3.5 rounded-full
                      font-bold text-sm hover:shadow-2xl hover:-translate-y-0.5 transition-all">
                <i class="ti ti-dashboard"></i>
                Go to Dashboard
            </a>
        @endguest
    </div>
</section>
{{-- End CTA --}}

@endsection