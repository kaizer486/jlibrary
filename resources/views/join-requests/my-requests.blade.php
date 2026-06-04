@extends('layouts.app')

@section('title', 'My Join Requests')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="text-purple-300 hover:text-purple-200 inline-flex items-center gap-2 mb-4">
                <i class="ti ti-arrow-left"></i> Back to Dashboard
            </a>
            <h1 class="text-2xl font-bold text-white">📋 My Join Requests</h1>
            <p class="text-gray-300 text-sm mt-1">Track your institution join requests</p>
        </div>
        
        @if($requests->count() > 0)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Institution</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($requests as $request)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $request->institution->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $request->institution->type_label }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $request->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">{!! $request->status_badge !!}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($request->status === 'pending')
                                    <form method="POST" action="{{ route('join-requests.cancel', $request) }}" class="inline" onsubmit="return confirm('Cancel this request?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Cancel</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $requests->links() }}</div>
        @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="ti ti-send text-6xl text-gray-400 mb-4 block"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Join Requests</h3>
            <p class="text-gray-500">You haven't requested to join any institution yet.</p>
            <a href="{{ route('dashboard') }}" class="inline-block mt-4 text-purple-600 hover:text-purple-700">Browse Institutions →</a>
        </div>
        @endif
        
    </div>
</div>
@endsection