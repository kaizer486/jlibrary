@extends('layouts.app')

@section('title', 'My Institution Creation Requests')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="text-purple-300 hover:text-purple-200 transition inline-flex items-center gap-1">
                <i class="ti ti-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <div class="flex justify-between items-center flex-wrap gap-4">
                    <div>
                        <h1 class="text-xl font-bold text-white">📋 My Institution Creation Requests</h1>
                        <p class="text-blue-100 text-sm">Track your requests to create an institution</p>
                    </div>
                    <a href="{{ route('institution.create-request') }}" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition text-sm flex items-center gap-2">
                        <i class="ti ti-plus"></i> New Request
                    </a>
                </div>
            </div>

            <div class="p-6">
                @if($requests->count() > 0)
                    <div class="space-y-4">
                        @foreach($requests as $request)
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 hover:shadow-md transition">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div>
                                        <h3 class="font-semibold text-gray-800">{{ $request->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $request->type_label }}</p>
                                        <p class="text-xs text-gray-400 mt-1">Submitted: {{ $request->created_at->format('M d, Y h:i A') }}</p>
                                        @if($request->motivation)
                                            <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ $request->motivation }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3">
                                        {!! $request->status_badge !!}
                                        @if($request->status === 'pending')
                                            <form method="POST" action="{{ route('institution.request.cancel', $request->id) }}" 
                                                  onsubmit="return confirm('Cancel your request?')" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm transition">
                                                    <i class="ti ti-x"></i> Cancel
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('institution.request.show', $request->id) }}" class="text-purple-600 hover:text-purple-800 text-sm transition">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                                @if($request->status === 'rejected' && $request->rejection_reason)
                                    <div class="mt-2 p-2 bg-red-50 rounded-lg text-sm text-red-700">
                                        <i class="ti ti-info-circle"></i> Reason: {{ $request->rejection_reason }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        {{ $requests->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="ti ti-file-plus text-5xl text-gray-400 mb-3 block"></i>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">No Requests</h3>
                        <p class="text-gray-500">You haven't submitted any institution creation requests yet.</p>
                        <a href="{{ route('institution.create-request') }}" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="ti ti-plus"></i> Create Your First Request
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection