@extends('layouts.app')

@section('title', $user->full_name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 py-8">
    <div class="container mx-auto px-4 max-w-5xl">
        
        <!-- Profile Header -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
            <!-- Cover Photo -->
            <div class="relative h-48 bg-gradient-to-r from-purple-500 to-pink-500">
                @if($user->cover_photo)
                   <img src="{{ url('media/' . $user->cover_photo) }}" class="w-full h-full object-cover">
                @endif
            </div>
            
            <!-- Avatar and Info -->
            <div class="relative px-6 pb-6">
                <div class="flex flex-col md:flex-row md:items-end justify-between -mt-16">
                    <div class="flex items-end gap-4">
                        <div class="w-32 h-32 rounded-full border-4 border-white bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center overflow-hidden">
                            @if($user->avatar)
                                <img src="{{ url('media/' . $user->avatar) }}" class="w-full h-full object-cover">
                            @else
                                <i class="ti ti-user text-white text-5xl"></i>
                            @endif
                        </div>
                        <div class="mb-2">
                            <h1 class="text-2xl font-bold text-gray-800">{{ $user->full_name }}</h1>
                            <div class="flex items-center gap-2 text-sm text-gray-500 mt-1">
                                <i class="ti ti-calendar"></i>
                                <span>Joined {{ $user->joined_date }}</span>
                                @if($user->location)
                                    <span>•</span>
                                    <i class="ti ti-map-pin"></i>
                                    <span>{{ $user->location }}</span>
                                @endif
                                @if($user->occupation)
                                    <span>•</span>
                                    <i class="ti ti-briefcase"></i>
                                    <span>{{ $user->occupation }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @auth
                        @if(Auth::id() === $user->id)
                            <a href="{{ route('profile.edit') }}" class="mt-4 md:mt-0 px-4 py-2 border border-purple-600 text-purple-600 rounded-lg hover:bg-purple-50 transition">
                                <i class="ti ti-edit"></i> Edit Profile
                            </a>
                        @endif
                    @endauth
                </div>
                
                <!-- Bio -->
                @if($user->bio)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-700">{{ $user->bio }}</p>
                    </div>
                @endif
                
                <!-- Social Links -->
                @php $socialLinks = $user->social_links; @endphp
                @if(count(array_filter($socialLinks)) > 0)
                    <div class="flex flex-wrap gap-2 mt-4">
                        @if($socialLinks['facebook'])
                            <a href="{{ $socialLinks['facebook'] }}" target="_blank" class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center hover:bg-blue-200 transition">
                                <i class="ti ti-brand-facebook text-blue-600"></i>
                            </a>
                        @endif
                        @if($socialLinks['twitter'])
                            <a href="{{ $socialLinks['twitter'] }}" target="_blank" class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center hover:bg-blue-200 transition">
                                <i class="ti ti-brand-twitter text-blue-400"></i>
                            </a>
                        @endif
                        @if($socialLinks['linkedin'])
                            <a href="{{ $socialLinks['linkedin'] }}" target="_blank" class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center hover:bg-blue-200 transition">
                                <i class="ti ti-brand-linkedin text-blue-700"></i>
                            </a>
                        @endif
                        @if($socialLinks['github'])
                            <a href="{{ $socialLinks['github'] }}" target="_blank" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition">
                                <i class="ti ti-brand-github text-gray-800"></i>
                            </a>
                        @endif
                        @if($socialLinks['instagram'])
                            <a href="{{ $socialLinks['instagram'] }}" target="_blank" class="w-10 h-10 bg-pink-100 rounded-full flex items-center justify-center hover:bg-pink-200 transition">
                                <i class="ti ti-brand-instagram text-pink-600"></i>
                            </a>
                        @endif
                        @if($socialLinks['website'])
                            <a href="{{ $socialLinks['website'] }}" target="_blank" class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center hover:bg-green-200 transition">
                                <i class="ti ti-world text-green-600"></i>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 text-center">
                <i class="ti ti-books text-2xl text-purple-600 mb-1 block"></i>
                <p class="text-2xl font-bold text-gray-800">{{ $user->books()->count() }}</p>
                <p class="text-xs text-gray-500">Books Read</p>
            </div>
            <div class="bg-white rounded-xl p-4 text-center">
                <i class="ti ti-certificate text-2xl text-green-600 mb-1 block"></i>
                <p class="text-2xl font-bold text-gray-800">{{ $user->certificates()->count() }}</p>
                <p class="text-xs text-gray-500">Certificates</p>
            </div>
            <div class="bg-white rounded-xl p-4 text-center">
                <i class="ti ti-brain text-2xl text-orange-600 mb-1 block"></i>
                <p class="text-2xl font-bold text-gray-800">{{ $user->quizAttempts()->where('passed', true)->count() }}</p>
                <p class="text-xs text-gray-500">Quizzes Passed</p>
            </div>
            <div class="bg-white rounded-xl p-4 text-center">
                <i class="ti ti-wallet text-2xl text-amber-600 mb-1 block"></i>
                <p class="text-2xl font-bold text-gray-800">TSh {{ number_format($user->wallet_balance ?? 0, 2) }}</p>
                <p class="text-xs text-gray-500">Wallet Balance</p>
            </div>
        </div>
        
        <!-- Recently Read Books -->
        @if($books->count() > 0)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-book"></i>
                    Recently Read Books
                </h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($books as $book)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="ti ti-book text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-800 text-sm">{{ $book->title }}</p>
                                <p class="text-xs text-gray-500">{{ $book->author }}</p>
                            </div>
                            <a href="{{ route('library.show', $book) }}" class="text-purple-600 text-xs hover:underline">View</a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Certificates -->
        @if($certificates->count() > 0)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-certificate"></i>
                    Recent Certificates
                </h2>
                <div class="space-y-3">
                    @foreach($certificates as $certificate)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <i class="ti ti-award text-2xl text-yellow-600"></i>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $certificate->quiz->title ?? 'Quiz Certificate' }}</p>
                                    <p class="text-xs text-gray-500">Earned {{ $certificate->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <a href="{{ route('certificates.show', $certificate) }}" class="text-purple-600 text-sm hover:underline">View</a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection