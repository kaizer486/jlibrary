@extends('layouts.super-admin')

@section('title', 'Institution Creation Requests')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">🏛️ Institution Creation Requests</h1>
            <p class="text-gray-500 text-sm mt-1">Manage requests from users who want to create institutions</p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-gray-500">
        <p class="text-gray-500 text-sm">Total Requests</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-yellow-500">
        <p class="text-gray-500 text-sm">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500">
        <p class="text-gray-500 text-sm">Approved</p>
        <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-red-500">
        <p class="text-gray-500 text-sm">Rejected</p>
        <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] ?? 0 }}</p>
    </div>
</div>

<!-- Search & Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" placeholder="Search by institution name or user email..." 
                   value="{{ request('search') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
        </div>
        <div>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-purple-500">
                <option value="all">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
            </select>
        </div>
        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition flex items-center gap-2">
            <i class="ti ti-search"></i> Filter
        </button>
        <a href="{{ route('super-admin.institution-requests.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
            <i class="ti ti-x"></i> Clear
        </a>
    </form>
</div>

<!-- Requests Table -->
@if($requests->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Institution</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($requests as $request)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-medium text-gray-900">{{ $request->name }}</p>
                            @if($request->city)
                                <p class="text-xs text-gray-500">{{ $request->city }}{{ $request->region ? ', ' . $request->region : '' }}</p>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="text-sm text-gray-800">{{ $request->user->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $request->user->email }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $request->type_label }}</td>
                    <td class="px-6 py-4">{!! $request->status_badge !!}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $request->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('super-admin.institution-requests.show', $request->id) }}" class="text-purple-600 hover:text-purple-800 transition">
                            <i class="ti ti-eye"></i> View
                        </a>
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
    <i class="ti ti-file-plus text-6xl text-gray-400 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Requests Found</h3>
    <p class="text-gray-500">No institution creation requests have been submitted yet.</p>
</div>
@endif
@endsection