@extends('layouts.app')

@section('title', 'My Join Requests')
@section('page-title', '📋 My Join Requests')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white flex items-center gap-3">
                        <span class="bg-gradient-to-br from-yellow-500 to-amber-500 p-2 rounded-xl">
                            <i class="ti ti-clock text-2xl"></i>
                        </span>
                        My Join Requests
                    </h1>
                    <p class="text-gray-400 mt-2 flex items-center gap-2">
                        <i class="ti ti-file text-sm"></i>
                        Track your pending requests to join institutions
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('discover.institutions') }}" 
                       class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-medium shadow-lg shadow-purple-600/20">
                        <i class="ti ti-building-community"></i> Discover Institutions
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl p-4 mb-6 flex items-center gap-3">
                <i class="ti ti-check-circle text-xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl p-4 mb-6 flex items-center gap-3">
                <i class="ti ti-alert-circle text-xl"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($requests->count() > 0)
            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/5">
                    <p class="text-2xl font-bold text-white">{{ $requests->total() }}</p>
                    <p class="text-xs text-gray-400">Total Requests</p>
                </div>
                <div class="bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/5">
                    <p class="text-2xl font-bold text-yellow-400">
                        {{ $requests->where('status', 'pending')->count() }}
                    </p>
                    <p class="text-xs text-gray-400">Pending</p>
                </div>
                <div class="bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/5">
                    <p class="text-2xl font-bold text-emerald-400">
                        {{ $requests->where('status', 'approved')->count() }}
                    </p>
                    <p class="text-xs text-gray-400">Approved</p>
                </div>
                <div class="bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/5">
                    <p class="text-2xl font-bold text-red-400">
                        {{ $requests->where('status', 'rejected')->count() }}
                    </p>
                    <p class="text-xs text-gray-400">Rejected</p>
                </div>
            </div>

            <!-- Requests List -->
            <div class="space-y-4">
                @foreach($requests as $request)
                    <div class="group bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-sm rounded-2xl border border-white/10 hover:border-purple-500/40 transition-all duration-300 hover:shadow-2xl hover:shadow-purple-500/10 overflow-hidden">
                        <!-- Status color bar -->
                        <div class="h-1.5 w-full 
                            @if($request->status === 'pending') bg-gradient-to-r from-yellow-500 to-amber-500
                            @elseif($request->status === 'approved') bg-gradient-to-r from-emerald-500 to-green-500
                            @else bg-gradient-to-r from-red-500 to-rose-500 @endif">
                        </div>
                        
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <!-- Left: Institution Info -->
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-purple-500/20">
                                        <i class="ti ti-building text-xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-white group-hover:text-purple-300 transition">
                                            {{ $request->institution->name }}
                                        </h3>
                                        <p class="text-sm text-gray-400">
                                            {{ $request->institution->type_label ?? 'Institution' }}
                                        </p>
                                        @if($request->institution->city || $request->institution->region)
                                            <p class="text-xs text-gray-500 mt-1">
                                                <i class="ti ti-map-pin"></i>
                                                {{ $request->institution->city ?? '' }}{{ $request->institution->city && $request->institution->region ? ', ' : '' }}{{ $request->institution->region ?? '' }}
                                            </p>
                                        @endif
                                        @if($request->message)
                                            <p class="text-sm text-gray-300 mt-2 p-3 bg-white/5 rounded-lg border border-white/5">
                                                <i class="ti ti-quote text-purple-400 text-xs"></i>
                                                "{{ $request->message }}"
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Right: Status & Actions -->
                                <div class="flex flex-col items-end gap-3">
                                    <!-- Status Badge -->
                                    @if($request->status === 'pending')
                                        <span class="px-4 py-1.5 rounded-full text-sm font-semibold bg-yellow-500/20 text-yellow-400 border border-yellow-500/20 flex items-center gap-2">
                                            <i class="ti ti-clock"></i> Pending
                                        </span>
                                        <div class="flex gap-2">
                                            <form method="POST" action="{{ route('join-requests.cancel', $request) }}" 
                                                  onsubmit="return confirm('Cancel your request to join {{ $request->institution->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="bg-red-500/20 hover:bg-red-500/30 text-red-400 hover:text-red-300 px-4 py-2 rounded-xl transition text-sm font-medium border border-red-500/20 hover:border-red-500/30 flex items-center gap-2">
                                                    <i class="ti ti-x"></i> Cancel
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($request->status === 'approved')
                                        <span class="px-4 py-1.5 rounded-full text-sm font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 flex items-center gap-2">
                                            <i class="ti ti-check"></i> Approved
                                        </span>
                                        <a href="{{ route('institution.public.index', $request->institution_id) }}" 
                                           class="bg-gradient-to-r from-emerald-600 to-emerald-500 hover:shadow-lg hover:shadow-emerald-600/25 text-white px-5 py-2 rounded-xl transition text-sm font-medium flex items-center gap-2">
                                            <i class="ti ti-arrow-right"></i> Enter Institution
                                        </a>
                                    @else
                                        <span class="px-4 py-1.5 rounded-full text-sm font-semibold bg-red-500/20 text-red-400 border border-red-500/20 flex items-center gap-2">
                                            <i class="ti ti-x"></i> Rejected
                                        </span>
                                        @if($request->rejection_reason)
                                            <p class="text-xs text-red-400/70 mt-1 text-right">
                                                Reason: {{ $request->rejection_reason }}
                                            </p>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Footer: Date -->
                            <div class="mt-4 pt-4 border-t border-white/10 flex items-center justify-between text-xs text-gray-500">
                                <span>
                                    <i class="ti ti-calendar"></i>
                                    Requested: {{ $request->created_at->format('M d, Y H:i') }}
                                </span>
                                <span>
                                    <i class="ti ti-clock"></i>
                                    {{ $request->created_at->diffForHumans() }}
                                </span>
                                @if($request->status !== 'pending' && $request->reviewed_at)
                                    <span>
                                        <i class="ti ti-check"></i>
                                        Reviewed: {{ $request->reviewed_at->format('M d, Y H:i') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $requests->appends(request()->query())->links() }}
            </div>

        @else
            <!-- Empty State -->
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-16 text-center border border-white/10">
                <div class="w-24 h-24 bg-gradient-to-br from-yellow-500 to-amber-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl shadow-yellow-500/20">
                    <i class="ti ti-file-off text-4xl text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3">No Join Requests</h3>
                <p class="text-gray-400 max-w-md mx-auto mb-8">
                    You haven't sent any join requests yet. Discover institutions and request to join them!
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="{{ route('discover.institutions') }}" 
                       class="bg-gradient-to-r from-purple-600 to-pink-600 hover:shadow-lg hover:shadow-purple-600/25 text-white px-8 py-3 rounded-xl transition font-semibold flex items-center gap-2">
                        <i class="ti ti-building-community"></i> Discover Institutions
                    </a>
                    <a href="{{ route('institution.create-request') }}" 
                       class="bg-white/10 hover:bg-white/20 text-white px-8 py-3 rounded-xl transition font-semibold flex items-center gap-2 border border-white/10">
                        <i class="ti ti-plus"></i> Create Institution
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection