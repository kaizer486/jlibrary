@extends('layouts.guest')

@section('content')
<style>
    /* ========================================== */
    /* HERO BACKGROUND - Deep Navy                */
    /* ========================================== */
    .hero-gradient {
        background: linear-gradient(135deg, #0a1628 0%, #0d1b3e 100%);
        position: relative;
        overflow: hidden;
        padding-top: 0;
        margin-top: 0;
    }

    /* Bubbles Animation - Soft Blue */
    .bubble {
        position: absolute;
        border-radius: 50%;
        animation: bubbleFloat linear infinite;
        box-shadow: inset 0 0 20px rgba(255, 255, 255, 0.05);
    }

    @keyframes bubbleFloat {
        0% {
            transform: translateY(0) translateX(0) scale(1);
            opacity: 0.4;
        }
        25% {
            transform: translateY(-30px) translateX(10px) scale(1.1);
            opacity: 0.7;
        }
        50% {
            transform: translateY(-60px) translateX(-5px) scale(0.9);
            opacity: 0.9;
        }
        75% {
            transform: translateY(-90px) translateX(15px) scale(1.2);
            opacity: 0.6;
        }
        100% {
            transform: translateY(-120px) translateX(0) scale(1);
            opacity: 0.3;
        }
    }
    

    @keyframes bubbleFloat2 {
        0% {
            transform: translateY(0) translateX(0) scale(1);
            opacity: 0.3;
        }
        25% {
            transform: translateY(-25px) translateX(-15px) scale(1.2);
            opacity: 0.6;
        }
        50% {
            transform: translateY(-50px) translateX(10px) scale(0.8);
            opacity: 0.8;
        }
        75% {
            transform: translateY(-75px) translateX(-10px) scale(1.1);
            opacity: 0.5;
        }
        100% {
            transform: translateY(-100px) translateX(0) scale(1);
            opacity: 0.2;
        }
    }

    .bubble:nth-child(odd) {
        animation-name: bubbleFloat;
    }
    .bubble:nth-child(even) {
        animation-name: bubbleFloat2;
    }

    /* Soft Blue Bubbles */
    .bubble-blue-1 {
        background: rgba(96, 165, 250, 0.25);
        border: 2px solid rgba(96, 165, 250, 0.2);
        animation-duration: 14s;
    }
    .bubble-blue-2 {
        background: rgba(147, 197, 253, 0.2);
        border: 2px solid rgba(147, 197, 253, 0.15);
        animation-duration: 12s;
    }
    .bubble-blue-3 {
        background: rgba(59, 130, 246, 0.2);
        border: 2px solid rgba(59, 130, 246, 0.15);
        animation-duration: 16s;
    }
    .bubble-blue-4 {
        background: rgba(191, 219, 254, 0.15);
        border: 2px solid rgba(191, 219, 254, 0.1);
        animation-duration: 10s;
    }
    .bubble-blue-5 {
        background: rgba(37, 99, 235, 0.2);
        border: 2px solid rgba(37, 99, 235, 0.15);
        animation-duration: 18s;
    }

    /* Dashboard Image */
    .dashboard-image {
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        width: 100%;
        height: 420px;
        background: rgba(0, 0, 0, 0.1);
    }

    .dashboard-image img {
        width: 100%;
        height: 100%;
        object-fit: fill;
        display: block;
    }

    /* Slider Transitions */
    .hero-slide {
        transition: opacity 1.2s ease-in-out;
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
    }

    .hero-slide.active {
        opacity: 1;
        z-index: 10;
    }

    .hero-slide.inactive {
        opacity: 0;
        z-index: 0;
    }

    /* ========================================== */
    /* BUTTONS - Orange                           */
    /* ========================================== */
    .btn-primary {
        background: linear-gradient(135deg, #F97316, #EA580C);
        color: white;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(249, 115, 22, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(249, 115, 22, 0.4);
        background: linear-gradient(135deg, #FB923C, #EA580C);
    }

    .btn-secondary {
        background: transparent;
        color: white;
        border: 1.5px solid rgba(255, 255, 255, 0.4);
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: #F97316;
    }

    /* Navigation Buttons - Orange */
    .nav-btn-primary {
        background: linear-gradient(135deg, #F97316, #EA580C);
        color: white;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(249, 115, 22, 0.3);
    }

    .nav-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(249, 115, 22, 0.4);
        background: linear-gradient(135deg, #FB923C, #EA580C);
    }

    /* ========================================== */
    /* ICONS - Blue (as used on hero)             */
    /* ========================================== */
    .icon-gradient {
        background: linear-gradient(135deg, #04065cc9, #362504);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
    }

    .icon-gradient:hover {
        background: linear-gradient(135deg, #60A5FA, #3B82F6);
        box-shadow: 0 4px 24px rgba(59, 130, 246, 0.4);
    }

    .icon-gradient-sm {
        width: 48px;
        height: 48px;
        font-size: 20px;
    }

    /* ========================================== */
    /* GRADIENT TEXT - Orange                    */
    /* ========================================== */
    .gradient-text {
        background: linear-gradient(135deg, #F97316, #EA580C);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* ========================================== */
    /* FOUNDER CARD - Orange Border              */
    /* ========================================== */
    .founder-card-gradient {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: 20px;
        padding: 3px;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #1125686b, #0805308e);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
    }

    .founder-card-gradient:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 48px rgba(249, 115, 22, 0.25);
    }

    .founder-card-inner {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 17px;
        overflow: hidden;
        height: 100%;
    }

    .founder-image-wrap {
        width: 100%;
        height: 280px;
        overflow: hidden;
        background: #f0f2f5;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .founder-image-wrap img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center top;
    }

    .founder-image-wrap .placeholder {
        font-size: 80px;
        font-weight: 700;
        color: #3B82F6;
    }

    /* ========================================== */
    /* LIGHT BACKGROUND SECTIONS                 */
    /* ========================================== */
    .light-body-bg {
        background: #f0f2f5;
    }

    /* Glass Cards - Blue Hover */
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    .glass-card:hover {
        background: rgba(255, 255, 255, 0.85);
        box-shadow: 0 12px 48px rgba(59, 130, 246, 0.12);
        transform: translateY(-2px);
    }

    .glass-card-light {
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        transition: all 0.3s ease;
    }

    .glass-card-light:hover {
        background: rgba(255, 255, 255, 0.7);
        border-color: rgba(59, 130, 246, 0.2);
    }

    /* CTA - Orange */
    .cta-gradient {
        background: linear-gradient(135deg, #F97316, #EA580C);
    }
</style>

<!-- ========================================== -->
<!-- NAVIGATION                                 -->
<!-- ========================================== -->
<nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">
    <div class="container mx-auto px-4 md:px-8">
        <div class="flex items-center justify-between h-16 md:h-20">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/logo.jpeg') }}" alt="JLIBRARY" class="h-8 w-auto md:h-10">
                <span class="text-lg md:text-xl font-bold text-gray-800">JLIBRARY</span>
            </div>

            <!-- Desktop Navigation - Blue -->
            <div class="hidden lg:flex items-center gap-6 text-sm font-medium text-gray-600">
                <a href="{{ route('welcome') }}" class="text-blue-600 font-semibold hover:text-blue-700 transition">Home</a>
                @auth
                    <a href="{{ route('library.index') }}" class="hover:text-blue-600 transition">Browse Library</a>
                    <a href="{{ route('community.index') }}" class="hover:text-blue-600 transition">Community</a>
                    <a href="{{ route('certificates.index') }}" class="hover:text-blue-600 transition">Certificates</a>
                    <a href="{{ route('marketplace.listings') }}" class="hover:text-blue-600 transition">Marketplace</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-blue-600 transition">Browse Library</a>
                    <a href="{{ route('login') }}" class="hover:text-blue-600 transition">Community</a>
                    <a href="{{ route('login') }}" class="hover:text-blue-600 transition">Certificates</a>
                    <a href="{{ route('login') }}" class="hover:text-blue-600 transition">Marketplace</a>
                @endauth
                <a href="#about" class="hover:text-blue-600 transition">About</a>
                <a href="#founders" class="hover:text-blue-600 transition">Founders</a>
            </div>

            <!-- Right Side Buttons - Orange -->
            <div class="flex items-center gap-2">
                @guest
                  <a href="{{ route('login') }}" 
   class="inline-flex items-center gap-1 px-3 py-1.5 md:px-4 md:py-2 text-xs md:text-sm font-bold text-white transition rounded-full"
   style=" background: linear-gradient(135deg, #04065cc9, #362504);">
    <i class="ti ti-login text-sm md:text-base"></i>
    <span class="hidden sm:inline">Login</span>
</a>
                    
                    <a href="{{ route('register') }}" 
                       class="inline-flex items-center gap-1 px-3 py-1.5 md:px-5 md:py-2.5 nav-btn-primary text-white text-xs md:text-sm font-semibold rounded-full">
                        <i class="ti ti-rocket text-sm md:text-base"></i>
                        <span class="hidden sm:inline">Get Started</span>
                        <span class="sm:hidden">Join</span>
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center gap-1 px-3 py-1.5 md:px-5 md:py-2.5 nav-btn-primary text-white text-xs md:text-sm font-semibold rounded-full">
                        <i class="ti ti-dashboard text-sm md:text-base"></i>
                        <span class="hidden sm:inline">Dashboard</span>
                        <span class="sm:hidden">Dash</span>
                    </a>
                @endguest

                <button id="mobile-menu-toggle" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition">
                    <i class="ti ti-menu-2 text-2xl text-gray-600"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobile-menu" class="lg:hidden hidden pb-4 border-t border-gray-100">
            <div class="flex flex-col gap-1 pt-4">
                <a href="{{ route('welcome') }}" class="px-3 py-2 text-blue-600 font-semibold hover:bg-blue-50 rounded-lg transition">Home</a>
                @auth
                    <a href="{{ route('library.index') }}" class="px-3 py-2 hover:bg-blue-50 rounded-lg transition">Browse Library</a>
                    <a href="{{ route('community.index') }}" class="px-3 py-2 hover:bg-blue-50 rounded-lg transition">Community</a>
                    <a href="{{ route('certificates.index') }}" class="px-3 py-2 hover:bg-blue-50 rounded-lg transition">Certificates</a>
                    <a href="{{ route('marketplace.listings') }}" class="px-3 py-2 hover:bg-blue-50 rounded-lg transition">Marketplace</a>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-2 hover:bg-blue-50 rounded-lg transition">Browse Library</a>
                    <a href="{{ route('login') }}" class="px-3 py-2 hover:bg-blue-50 rounded-lg transition">Community</a>
                    <a href="{{ route('login') }}" class="px-3 py-2 hover:bg-blue-50 rounded-lg transition">Certificates</a>
                    <a href="{{ route('login') }}" class="px-3 py-2 hover:bg-blue-50 rounded-lg transition">Marketplace</a>
                @endauth
                <a href="#about" class="px-3 py-2 hover:bg-blue-50 rounded-lg transition">About</a>
                <a href="#founders" class="px-3 py-2 hover:bg-blue-50 rounded-lg transition">Founders</a>
                
                <div class="mt-2 pt-2 border-t border-gray-100 flex flex-col gap-2">
                    @guest
                        <a href="{{ route('login') }}" class="px-3 py-2 text-center text-blue-600 font-semibold hover:bg-blue-50 rounded-lg transition">
                            <i class="ti ti-login"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="px-3 py-2 text-center nav-btn-primary text-white font-semibold rounded-lg">
                            <i class="ti ti-rocket"></i> Get Started
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 text-center nav-btn-primary text-white font-semibold rounded-lg">
                            <i class="ti ti-dashboard"></i> Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="w-full px-3 py-2 text-center text-red-600 font-semibold hover:bg-red-50 rounded-lg transition">
                                <i class="ti ti-logout"></i> Logout
                            </button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- ========================================== -->
<!-- HERO SLIDER                                -->
<!-- ========================================== -->
<section class="hero-gradient relative overflow-hidden" id="hero-slider-container" style="padding-top: 0 !important; margin-top: 0 !important;">
    <!-- Soft Blue Bubbles -->
    <div class="absolute inset-0 pointer-events-none z-0">
        
        <div class="bubble bubble-blue-4 w-8 h-8 top-[30%] left-[75%]" style="animation-duration: 11s;"></div>
        <div class="bubble bubble-blue-5 w-10 h-10 bottom-[40%] right-[40%]" style="animation-duration: 17s;"></div>
        <div class="bubble bubble-blue-1 w-5 h-5 top-[15%] left-[50%]" style="animation-duration: 9s;"></div>
        <div class="bubble bubble-blue-2 w-13 h-13 bottom-[5%] left-[35%]" style="animation-duration: 25s;"></div>
        <div class="bubble bubble-blue-3 w-4 h-4 top-[45%] right-[25%]" style="animation-duration: 8s;"></div>
        <div class="bubble bubble-blue-4 w-10 h-10 top-[80%] right-[10%]" style="animation-duration: 16s;"></div>
        <div class="bubble bubble-blue-5 w-6 h-6 top-[60%] left-[10%]" style="animation-duration: 19s;"></div>
        <div class="bubble bubble-blue-1 w-11 h-11 top-[25%] right-[45%]" style="animation-duration: 21s;"></div>
        <div class="bubble bubble-blue-2 w-8 h-8 top-[40%] left-[20%]" style="animation-duration: 14s;"></div>
        <div class="bubble bubble-blue-3 w-7 h-7 bottom-[30%] right-[55%]" style="animation-duration: 12s;"></div>
        <div class="bubble bubble-blue-4 w-9 h-9 top-[10%] right-[30%]" style="animation-duration: 24s;"></div>
        <div class="bubble bubble-blue-5 w-4 h-4 bottom-[20%] left-[25%]" style="animation-duration: 7s;"></div>
        <div class="bubble bubble-blue-1 w-12 h-12 top-[55%] left-[55%]" style="animation-duration: 23s;"></div>
    </div>

    @php
        $slides = \App\Models\HeroSlide::active()->ordered()->get();
    @endphp

    <div class="relative w-full h-[420px] md:h-[480px] lg:h-[520px] z-10" id="hero-slider">
        @if($slides->count() > 0)
            @foreach($slides as $index => $slide)
            <div class="hero-slide {{ $index === 0 ? 'active' : 'inactive' }}" data-index="{{ $index }}" data-duration="{{ $slide->slide_duration ?? 5 }}">
                <div class="container mx-auto px-4 md:px-8 h-full flex items-center">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 items-center w-full">
                        <!-- Text Content -->
                        <div class="text-white z-20 space-y-3">
                            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold leading-tight">
                                {{ $slide->title }}
                            </h1>

                            @if($slide->subtitle)
                                <p class="text-base md:text-lg text-white/80 max-w-lg leading-relaxed">
                                    {{ $slide->subtitle }}
                                </p>
                            @endif

                            @if($slide->cta_text && $slide->cta_url)
                                <div class="flex flex-wrap gap-3 pt-1">
                                    <a href="{{ $slide->cta_url }}" 
                                       class="inline-flex items-center gap-2 px-6 md:px-8 py-2.5 btn-primary text-white font-semibold rounded-full text-sm">
                                        <i class="ti ti-arrow-right"></i> {{ $slide->cta_text }}
                                    </a>
                                    <a href="#about" 
                                       class="inline-flex items-center gap-2 px-6 md:px-8 py-2.5 btn-secondary text-white font-semibold rounded-full text-sm">
                                        <i class="ti ti-info-circle"></i> Learn More
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Image -->
                        <div class="hidden lg:block relative z-20">
                            <div class="dashboard-image">
                                <img src="{{ asset('storage/' . $slide->image) }}" 
                                     alt="{{ $slide->title }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Slider Controls -->
            <button id="slider-prev" class="absolute left-3 top-1/2 -translate-y-1/2 z-30 p-2 rounded-full transition-all hover:scale-110 bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/10">
                <i class="ti ti-chevron-left text-white text-xl"></i>
            </button>
            <button id="slider-next" class="absolute right-3 top-1/2 -translate-y-1/2 z-30 p-2 rounded-full transition-all hover:scale-110 bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/10">
                <i class="ti ti-chevron-right text-white text-xl"></i>
            </button>

            <!-- Slider Dots -->
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-30 flex gap-2">
                @foreach($slides as $index => $slide)
                    <button class="slider-dot rounded-full transition-all duration-300 {{ $index === 0 ? 'bg-white w-8 h-1.5' : 'bg-white/40 hover:bg-white/60 w-1.5 h-1.5' }}" data-index="{{ $index }}"></button>
                @endforeach
            </div>
        @else
            <!-- Fallback -->
            <div class="container mx-auto px-4 md:px-8 h-full flex items-center">
                <div class="max-w-2xl">
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white leading-tight mb-3">
                        Empowering Digital<br>Learning Through <span class="gradient-text">Innovation</span>
                    </h1>
                    <p class="text-base md:text-lg text-white/80 mb-6 max-w-lg leading-relaxed">
                        Your all-in-one digital library platform. Read thousands of books, connect with a global community, earn recognized certificates, and sell your own work.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center gap-2 px-6 md:px-8 py-2.5 btn-primary text-white font-semibold rounded-full text-sm">
                            <i class="ti ti-rocket"></i> Get Started Free
                        </a>
                        <a href="#about" 
                           class="inline-flex items-center gap-2 px-6 md:px-8 py-2.5 btn-secondary text-white font-semibold rounded-full text-sm">
                            <i class="ti ti-info-circle"></i> Learn More
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>



<!-- ========================================== -->
<!-- MAIN CONTENT - Light Background            -->
<!-- ========================================== -->
<section id="about" class="py-16 light-body-bg" style="padding-top: 12px;">
    <div class="container mx-auto px-4 md:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                @php
                    $vision = \App\Models\SiteSetting::getValue('vision');
                    $mission = \App\Models\SiteSetting::getValue('mission');
                    $motto = \App\Models\SiteSetting::getValue('motto');
                    $platformMessage = \App\Models\SiteSetting::getValue('platform_message');
                    $announcement1 = \App\Models\SiteSetting::getValue('announcement_1');
                    $announcement2 = \App\Models\SiteSetting::getValue('announcement_2');
                    $announcement3 = \App\Models\SiteSetting::getValue('announcement_3');
                @endphp

                <!-- Vision, Mission, Motto - Blue Icons -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @if($vision)
                        <div class="glass-card p-6">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="icon-gradient icon-gradient-sm">
                                    <i class="ti ti-bulb text-xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800">Our Vision</h3>
                            </div>
                            <p class="text-gray-600 text-base leading-relaxed">{{ $vision }}</p>
                        </div>
                    @endif
                    @if($mission)
                        <div class="glass-card p-6">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="icon-gradient icon-gradient-sm">
                                    <i class="ti ti-flag text-xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800">Our Mission</h3>
                            </div>
                            <p class="text-gray-600 text-base leading-relaxed">{{ $mission }}</p>
                        </div>
                    @endif
                    @if($motto)
                        <div class="glass-card p-6">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="icon-gradient icon-gradient-sm">
                                    <i class="ti ti-heart text-xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800">Our Motto</h3>
                            </div>
                            <p class="text-gray-600 text-base leading-relaxed font-semibold text-black-600">{{ $motto }}</p>
                        </div>
                    @endif
                </div>

                @if($platformMessage)
                    <div class="glass-card p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="icon-gradient icon-gradient-sm">
                                <i class="ti ti-message text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Welcome to JLIBRARY</h3>
                        </div>
                        <div class="text-gray-600 text-base leading-relaxed whitespace-pre-line">
                            {{ $platformMessage }}
                        </div>
                    </div>
                @endif

                @if($announcement1 || $announcement2 || $announcement3)
                    <div class="glass-card p-6" style="border-color: rgba(59, 130, 246, 0.2);">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="icon-gradient icon-gradient-sm">
                          <i class="ti ti-speakerphone"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Announcements</h3>
                        </div>
                        <div class="space-y-3">
                            @if($announcement1)
                                <div class="glass-card-light p-3 flex items-start gap-3">
                                    <span class="text-blue-500 mt-0.5">•</span>
                                    <span class="text-gray-600 text-base">{{ $announcement1 }}</span>
                                </div>
                            @endif
                            @if($announcement2)
                                <div class="glass-card-light p-3 flex items-start gap-3">
                                    <span class="text-blue-500 mt-0.5">•</span>
                                    <span class="text-gray-600 text-base">{{ $announcement2 }}</span>
                                </div>
                            @endif
                            @if($announcement3)
                                <div class="glass-card-light p-3 flex items-start gap-3">
                                    <span class="text-blue-500 mt-0.5">•</span>
                                    <span class="text-gray-600 text-base">{{ $announcement3 }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Latest Updates - Blue Icons -->
            <div class="lg:col-span-1">
                <div class="glass-card p-6 sticky top-24">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="icon-gradient icon-gradient-sm">
                            <i class="ti ti-news text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Latest Updates</h3>
                    </div>

                    @php
                        $newsItems = \App\Models\NewsItem::active()->ordered()->latestPublished()->limit(6)->get();
                    @endphp

                    @if($newsItems->count() > 0)
                        <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                            @foreach($newsItems as $item)
                                <div class="glass-card-light p-3 transition-all hover:border-blue-300">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs px-2 py-0.5 rounded-full 
                                            @if($item->category == 'Books') bg-blue-100 text-blue-700
                                            @elseif($item->category == 'Events') bg-green-100 text-green-700
                                            @elseif($item->category == 'Certificates') bg-orange-100 text-orange-700
                                            @elseif($item->category == 'Announcements') bg-blue-100 text-blue-700
                                            @elseif($item->category == 'Authors') bg-pink-100 text-pink-700
                                            @else bg-gray-100 text-gray-700 @endif">
                                            {{ $item->category ?? 'General' }}
                                        </span>
                                        @if($item->is_featured)
                                            <span class="text-xs">⭐</span>
                                        @endif
                                    </div>
                                    @if($item->link)
                                        <a href="{{ $item->link }}" class="text-base font-semibold text-gray-800 hover:text-blue-600 transition block">
                                            {{ $item->title }}
                                        </a>
                                    @else
                                        <p class="text-base font-semibold text-gray-800">{{ $item->title }}</p>
                                    @endif
                                    <p class="text-sm text-gray-400 mt-1 flex items-center gap-1">
                                        <i class="ti ti-clock"></i> {{ $item->published_at->format('M d, Y') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-6">No updates yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================== -->
<!-- CTA SECTION - Glassmorphism Pale Dark Blue -->
<!-- ========================================== -->
<section class="py-16 relative overflow-hidden" style="background: linear-gradient(135deg, #0a1628 0%, #0d1b3e 100%); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-top: 2px solid rgba(255, 255, 255, 0.27);">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-16 -left-16 w-64 h-64 rounded-full opacity-10" style="background: radial-gradient(circle, rgba(59, 130, 246, 0.3), transparent); filter:blur(48px);"></div>
        <div class="absolute -bottom-16 -right-16 w-64 h-64 rounded-full opacity-10" style="background: radial-gradient(circle, rgba(59, 130, 246, 0.2), transparent); filter:blur(48px);"></div>
        <!-- Subtle grid overlay -->
        <div style="position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px); background-size: 50px 50px; pointer-events: none;"></div>
    </div>

    <div class="relative container mx-auto px-4 md:px-8 z-10">
        <div class="max-w-3xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full" style="background: rgba(255,255,255,0.08); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.1);">
                <i class="ti ti-sparkles text-yellow-200"></i>
                <span class="text-white/80 text-sm font-medium">Join 12,000+ Learners</span>
            </div>

            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4 leading-tight">
                Ready to Start Your Learning Journey?
            </h2>

            <p class="text-white/70 text-lg mb-8 max-w-xl mx-auto leading-relaxed">
                Join thousands of learners already growing with JLIBRARY. Access thousands of books, connect with a global community, earn recognized certificates, and start selling your work.
            </p>

            <div class="flex flex-wrap justify-center gap-4">
                @guest
                    <a href="{{ route('register') }}" 
                       class="inline-flex items-center gap-2 bg-white text-gray-900 px-8 py-3.5 rounded-full font-semibold text-base hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                        <i class="ti ti-user-plus"></i> Create Free Account
                    </a>
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center gap-2 border-2 border-white/30 text-white px-8 py-3.5 rounded-full font-semibold text-base hover:bg-white/10 transition-all duration-300" style="backdrop-filter: blur(8px);">
                        <i class="ti ti-login"></i> Sign In
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center gap-2 bg-white text-gray-900 px-8 py-3.5 rounded-full font-semibold text-base hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                        <i class="ti ti-dashboard"></i> Go to Dashboard
                    </a>
                    <a href="{{ route('library.index') }}" 
                       class="inline-flex items-center gap-2 border-2 border-white/30 text-white px-8 py-3.5 rounded-full font-semibold text-base hover:bg-white/10 transition-all duration-300" style="backdrop-filter: blur(8px);">
                        <i class="ti ti-books"></i> Browse Library
                    </a>
                @endguest
            </div>

            <div class="mt-6 flex flex-wrap justify-center items-center gap-6 text-white/60 text-sm">
                <span class="flex items-center gap-1">
                    <i class="ti ti-check text-green-300"></i> Free to join
                </span>
                <span class="hidden sm:inline text-white/20">|</span>
                <span class="flex items-center gap-1">
                    <i class="ti ti-check text-green-300"></i> 12K+ books
                </span>
                <span class="hidden sm:inline text-white/20">|</span>
                <span class="flex items-center gap-1">
                    <i class="ti ti-check text-green-300"></i> 8.4K readers
                </span>
                <span class="hidden sm:inline text-white/20">|</span>
                <span class="flex items-center gap-1">
                    <i class="ti ti-check text-green-300"></i> 320+ certificates
                </span>
            </div>
        </div>
    </div>
</section>
<!-- ========================================== -->
<!-- FOUNDER SECTION - Orange Border            -->
<!-- ========================================== -->
<section id="founders" class="py-16 light-body-bg">
    <div class="container mx-auto px-4 md:px-8">
        <div class="text-center mb-10">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-800 mb-3">
                Meet Our <span class="gradient-text">Founder</span>
            </h2>
            <p class="text-lg text-gray-500 max-w-xl mx-auto">
                The visionary behind JLIBRARY, dedicated to transforming digital education
            </p>
        </div>

        @php
            $founders = \App\Models\Founder::active()->ordered()->get();
        @endphp

        @if($founders->count() > 0)
            <div class="flex justify-center">
                <div class="w-full max-w-sm">
                    @foreach($founders as $founder)
                        @php
                            $socialLinks = is_string($founder->social_links) ? json_decode($founder->social_links, true) : ($founder->social_links ?? []);
                            $socialLinks = is_array($socialLinks) ? $socialLinks : [];
                            $hasLinks = count($socialLinks) > 0;
                        @endphp

                        <div class="founder-card-gradient">
                            <div class="founder-card-inner">
                                <div class="founder-image-wrap">
                                    @if($founder->photo)
                                        <img src="{{ asset('storage/' . $founder->photo) }}" 
                                             alt="{{ $founder->name }}">
                                    @else
                                        <div class="placeholder">
                                            {{ substr($founder->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="p-6 text-center">
                                    <h3 class="text-xl font-bold text-gray-800">{{ $founder->name }}</h3>
                                    @if($founder->title)
                                        <p class="text-sm text-orange-500 font-semibold mt-1">{{ $founder->title }}</p>
                                    @endif

                                    @if($founder->bio)
                                        <div class="mt-3 text-gray-600 text-sm leading-relaxed line-clamp-4">
                                            {{ Str::limit($founder->bio, 120) }}
                                        </div>
                                    @endif

                                    @if($hasLinks)
                                        <div class="mt-4 flex justify-center gap-2 flex-wrap">
                                            @if(isset($socialLinks['twitter']) && $socialLinks['twitter'])
                                                <a href="{{ $socialLinks['twitter'] }}" target="_blank" 
                                                   class="w-9 h-9 rounded-full bg-gray-100 hover:bg-blue-50 text-gray-600 hover:text-blue-500 flex items-center justify-center transition-all hover:scale-110">
                                                    <i class="ti ti-brand-twitter"></i>
                                                </a>
                                            @endif
                                            @if(isset($socialLinks['instagram']) && $socialLinks['instagram'])
                                                <a href="{{ $socialLinks['instagram'] }}" target="_blank" 
                                                   class="w-9 h-9 rounded-full bg-gray-100 hover:bg-pink-50 text-gray-600 hover:text-pink-500 flex items-center justify-center transition-all hover:scale-110">
                                                    <i class="ti ti-brand-instagram"></i>
                                                </a>
                                            @endif
                                            @if(isset($socialLinks['facebook']) && $socialLinks['facebook'])
                                                <a href="{{ $socialLinks['facebook'] }}" target="_blank" 
                                                   class="w-9 h-9 rounded-full bg-gray-100 hover:bg-blue-50 text-gray-600 hover:text-blue-600 flex items-center justify-center transition-all hover:scale-110">
                                                    <i class="ti ti-brand-facebook"></i>
                                                </a>
                                            @endif
                                            @if(isset($socialLinks['tiktok']) && $socialLinks['tiktok'])
                                                <a href="{{ $socialLinks['tiktok'] }}" target="_blank" 
                                                   class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-black flex items-center justify-center transition-all hover:scale-110">
                                                    <i class="ti ti-brand-tiktok"></i>
                                                </a>
                                            @endif
                                            @if(isset($socialLinks['youtube']) && $socialLinks['youtube'])
                                                <a href="{{ $socialLinks['youtube'] }}" target="_blank" 
                                                   class="w-9 h-9 rounded-full bg-gray-100 hover:bg-red-50 text-gray-600 hover:text-red-600 flex items-center justify-center transition-all hover:scale-110">
                                                    <i class="ti ti-brand-youtube"></i>
                                                </a>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="mt-3 pt-3 border-t border-gray-100 flex justify-center gap-4 text-xs text-gray-500">
                                        @if($founder->email)
                                            <a href="mailto:{{ $founder->email }}" class="hover:text-orange-500 transition flex items-center gap-1">
                                                <i class="ti ti-mail"></i> Email
                                            </a>
                                        @endif
                                        @if($founder->phone)
                                            <a href="tel:{{ $founder->phone }}" class="hover:text-orange-500 transition flex items-center gap-1">
                                                <i class="ti ti-phone"></i> Call
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-10 glass-card max-w-md mx-auto">
                <i class="ti ti-users text-4xl text-gray-300 mb-2 block"></i>
                <p class="text-gray-500">No founders added yet</p>
            </div>
        @endif
    </div>
</section>

<!-- ========================================== -->
<!-- FOOTER                                    -->
<!-- ========================================== -->
<footer class="bg-gray-900 text-gray-300">
    <div class="container mx-auto px-4 md:px-8 py-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="JLIBRARY" class="h-8 w-auto">
                    <span class="text-lg font-bold text-white">JLIBRARY</span>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed">
                    Empowering digital learning through innovation. Your all-in-one platform for books, community, certificates, and marketplace.
                </p>
                <div class="mt-3 flex gap-3">
                    <a href="https://x.com/JNashon20" target="_blank" class="text-gray-400 hover:text-white transition" aria-label="Twitter (X)">
                        <i class="ti ti-brand-x text-xl"></i>
                    </a>
                    <a href="https://www.instagram.com/jlib_rary?igsh=bjJ4ZHJ0NXptdG5j" target="_blank" class="text-gray-400 hover:text-white transition" aria-label="Instagram">
                        <i class="ti ti-brand-instagram text-xl"></i>
                    </a>
                    <a href="https://vm.tiktok.com/ZS967rA8uWvFr-iHBad/" target="_blank" class="text-gray-400 hover:text-white transition" aria-label="TikTok">
                        <i class="ti ti-brand-tiktok text-xl"></i>
                    </a>
                    <a href="https://www.facebook.com/share/1YDDzy1gnJ/" target="_blank" class="text-gray-400 hover:text-white transition" aria-label="Facebook">
                        <i class="ti ti-brand-facebook text-xl"></i>
                    </a>
                    <a href="https://whatsapp.com/channel/0029VaC8Tg460eBjk82Xlt0U" target="_blank" class="text-gray-400 hover:text-white transition" aria-label="WhatsApp Channel">
                        <i class="ti ti-brand-whatsapp text-xl"></i>
                    </a>
                    <a href="https://www.youtube.com/@Jlibraryonlinesystem" target="_blank" class="text-gray-400 hover:text-white transition" aria-label="YouTube">
                        <i class="ti ti-brand-youtube text-xl"></i>
                    </a>
                    <a href="https://www.linkedin.com/public-profile/settings/" target="_blank" class="text-gray-400 hover:text-white transition" aria-label="LinkedIn">
                        <i class="ti ti-brand-linkedin text-xl"></i>
                    </a>
                </div>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-3">Quick Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('library.index') }}" class="hover:text-white transition">Browse Books</a></li>
                    <li><a href="{{ route('community.index') }}" class="hover:text-white transition">Community</a></li>
                    <li><a href="{{ route('certificates.index') }}" class="hover:text-white transition">Certificates</a></li>
                    <li><a href="{{ route('marketplace.listings') }}" class="hover:text-white transition">Marketplace</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white transition">Become an Author</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-3">Resources</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('welcome') }}#about" class="hover:text-white transition">Blog</a></li>
                    <li><a href="{{ route('welcome') }}#about" class="hover:text-white transition">FAQs</a></li>
                    <li><a href="mailto:support@jlibrary.co.tz" class="hover:text-white transition">Help Center</a></li>
                    <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
                    <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-3">Contact Us</h4>
                <ul class="space-y-2 text-sm">
                    @php
                        $contactEmail = 'info@jlibrary.co.tz';
                        $supportEmail = 'support@jlibrary.co.tz';
                        $contactPhone = '0766408259';
                        $address = \App\Models\SiteSetting::getValue('address', 'Dar es Salaam, Tanzania');
                    @endphp
                    <li class="flex items-center gap-2">
                        <i class="ti ti-mail text-orange-400"></i>
                        <a href="mailto:info@jlibrary.co.tz" class="hover:text-white transition">info@jlibrary.co.tz</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="ti ti-mail text-orange-400"></i>
                        <a href="mailto:support@jlibrary.co.tz" class="hover:text-white transition">support@jlibrary.co.tz</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="ti ti-mail text-orange-400"></i>
                        <a href="mailto:contact@jlibrary.co.tz" class="hover:text-white transition">contact@jlibrary.co.tz</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="ti ti-phone text-orange-400"></i>
                        <a href="tel:0766408259" class="hover:text-white transition">0766 408 259</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="ti ti-map-pin text-orange-400"></i>
                        <span>{{ $address }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-6 pt-6 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} JLIBRARY. All rights reserved.
        </div>
    </div>
</footer>
<!-- ========================================== -->
<!-- JAVASCRIPT                                -->
<!-- ========================================== -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ==========================================
        // HERO SLIDER
        // ==========================================
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.slider-dot');
        const prevBtn = document.getElementById('slider-prev');
        const nextBtn = document.getElementById('slider-next');
        let currentSlide = 0;
        let slideInterval;
        let isTransitioning = false;

        if (slides.length === 0) return;

        function goToSlide(index) {
            if (isTransitioning || index === currentSlide) return;
            isTransitioning = true;

            slides.forEach((slide, i) => {
                if (i === index) {
                    slide.classList.remove('inactive');
                    slide.classList.add('active');
                } else {
                    slide.classList.remove('active');
                    slide.classList.add('inactive');
                }
            });

            dots.forEach((dot, i) => {
                if (i === index) {
                    dot.classList.remove('bg-white/40');
                    dot.classList.add('bg-white');
                    dot.style.width = '2rem';
                    dot.style.height = '0.375rem';
                } else {
                    dot.classList.remove('bg-white');
                    dot.classList.add('bg-white/40');
                    dot.style.width = '0.375rem';
                    dot.style.height = '0.375rem';
                }
            });

            currentSlide = index;
            startAutoSlide();

            setTimeout(() => {
                isTransitioning = false;
            }, 1200);
        }

        function nextSlide() {
            if (slides.length === 0) return;
            const next = (currentSlide + 1) % slides.length;
            goToSlide(next);
        }

        function prevSlide() {
            if (slides.length === 0) return;
            const prev = (currentSlide - 1 + slides.length) % slides.length;
            goToSlide(prev);
        }

        function startAutoSlide() {
            if (slides.length > 1) {
                if (slideInterval) clearInterval(slideInterval);

                const activeSlide = slides[currentSlide];
                let duration = 5000;
                if (activeSlide && activeSlide.dataset.duration) {
                    duration = parseInt(activeSlide.dataset.duration) * 1000;
                }

                slideInterval = setInterval(nextSlide, duration);
            }
        }

        function stopAutoSlide() {
            if (slideInterval) {
                clearInterval(slideInterval);
                slideInterval = null;
            }
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function(e) {
                e.preventDefault();
                prevSlide();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();
                nextSlide();
            });
        }

        dots.forEach((dot, index) => {
            dot.addEventListener('click', function(e) {
                e.preventDefault();
                goToSlide(index);
            });
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight') {
                e.preventDefault();
                nextSlide();
            } else if (e.key === 'ArrowLeft') {
                e.preventDefault();
                prevSlide();
            }
        });

        const sliderContainer = document.getElementById('hero-slider');
        if (sliderContainer) {
            sliderContainer.addEventListener('mouseenter', stopAutoSlide);
            sliderContainer.addEventListener('mouseleave', function() {
                if (slides.length > 1 && !slideInterval) {
                    startAutoSlide();
                }
            });
        }

        let touchStartX = 0;
        if (sliderContainer) {
            sliderContainer.addEventListener('touchstart', function(e) {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            sliderContainer.addEventListener('touchend', function(e) {
                const touchEndX = e.changedTouches[0].screenX;
                const diff = touchStartX - touchEndX;
                if (Math.abs(diff) > 50) {
                    if (diff > 0) {
                        nextSlide();
                    } else {
                        prevSlide();
                    }
                }
            }, { passive: true });
        }

        if (slides.length > 1) {
            startAutoSlide();
        }

        // Mobile Menu
        const menuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        if (menuToggle && mobileMenu) {
            menuToggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Smooth Scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection