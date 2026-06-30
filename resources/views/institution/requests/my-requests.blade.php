@extends('layouts.app')

@section('title', 'My Institution Requests')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white">📋 My Institution Requests</h1>
                <p class="text-gray-400 text-sm">Track your institution creation requests</p>
            </div>
            <a href="{{ route('institution.create-request') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                <i class="ti ti-plus"></i> New Request
            </a>
        </div>

        @if($requests->count() > 0)
            <div class="space-y-4">
                @foreach($requests as $request)
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10 hover:border-purple-500/30 transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-white">{{ $request->name }}</h3>
                                <p class="text-sm text-gray-400">{{ $request->type }}</p>
                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-400">
                                    <span><i class="ti ti-calendar"></i> {{ $request->created_at->format('M d, Y') }}</span>
                                    <span><i class="ti ti-building"></i> {{ $request->city ?? 'No city' }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                @if($request->status === 'pending')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-500/20 text-yellow-400 border border-yellow-500/20">
                                        ⏳ Pending
                                    </span>
                                    <div class="mt-2">
                                        <form method="POST" action="{{ route('institution.request.cancel', $request->id) }}" 
                                              onsubmit="return confirm('Cancel this request?')">
                                            @csrf
                                            <button type="submit" class="text-xs text-red-400 hover:text-red-300 transition">
                                                Cancel Request
                                            </button>
                                        </form>
                                    </div>
                                @elseif($request->status === 'approved')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/20">
                                        ✅ Approved
                                    </span>
                                @elseif($request->status === 'rejected')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/20">
                                        ❌ Rejected
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $requests->links() }}
            </div>
        @else
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-12 text-center border border-white/10">
                <i class="ti ti-file-off text-5xl text-gray-500 mb-4 block"></i>
                <h3 class="text-xl font-semibold text-white mb-2">No Requests Yet</h3>
                <p class="text-gray-400">You haven't submitted any institution creation requests.</p>
                <a href="{{ route('institution.create-request') }}" class="inline-block mt-4 bg-purple-600 hover:bg-purple-700 text-white px-6 py-2.5 rounded-lg transition">
                    <i class="ti ti-plus"></i> Create Request
                </a>
            </div>
        @endif
    </div>
</div>
@endsection