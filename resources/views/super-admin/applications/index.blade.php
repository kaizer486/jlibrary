@extends('layouts.super-admin')

@section('title', 'Applications')

@section('content')
<div class="mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <i class="ti ti-files text-purple-600"></i>
            Applications
        </h1>
        <p class="text-gray-500 text-sm mt-1">Review author, bookseller, and publisher applications</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border-l-4 border-purple-500 shadow-sm">
        <p class="text-gray-500 text-sm">Total Applications</p>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-yellow-500 shadow-sm">
        <p class="text-gray-500 text-sm">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['pending']) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-green-500 shadow-sm">
        <p class="text-gray-500 text-sm">Approved</p>
        <p class="text-2xl font-bold text-green-600">{{ number_format($stats['approved']) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-red-500 shadow-sm">
        <p class="text-gray-500 text-sm">Rejected</p>
        <p class="text-2xl font-bold text-red-600">{{ number_format($stats['rejected']) }}</p>
    </div>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('super-admin.applications.index') }}" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
            <i class="ti ti-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" placeholder="Search by user name or email..." value="{{ request('search') }}" 
                   class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
        <div>
            <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg bg-white">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
            </select>
        </div>
        <div>
            <select name="type" class="px-4 py-2 border border-gray-200 rounded-lg bg-white">
                <option value="">All Types</option>
                <option value="author" {{ request('type') == 'author' ? 'selected' : '' }}>📚 Author</option>
                <option value="bookseller" {{ request('type') == 'bookseller' ? 'selected' : '' }}>📖 Bookseller</option>
                <option value="publisher" {{ request('type') == 'publisher' ? 'selected' : '' }}>📰 Publisher</option>
                <option value="researcher" {{ request('type') == 'researcher' ? 'selected' : '' }}>🔬 Researcher</option>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">🔍 Filter</button>
        </div>
        <div>
            <a href="{{ route('super-admin.applications.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition inline-block">Clear</a>
        </div>
    </form>
</div>

<!-- Applications Table -->
@if($applications->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applicant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($applications as $app)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $app->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-800">{{ $app->user->full_name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $app->user->email ?? 'N/A' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($app->type === 'author')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">📚 Author</span>
                        @elseif($app->type === 'bookseller')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">📖 Bookseller</span>
                        @elseif($app->type === 'publisher')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">📰 Publisher</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">🔬 Researcher</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($app->status === 'pending')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⏳ Pending</span>
                        @elseif($app->status === 'approved')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✅ Approved</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">❌ Rejected</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('super-admin.applications.show', $app) }}" class="text-purple-600 hover:text-purple-800 flex items-center gap-1">
                            <i class="ti ti-eye"></i> Review
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $applications->appends(request()->query())->links() }}
</div>

@else
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="ti ti-files text-6xl text-gray-400 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Applications Found</h3>
    <p class="text-gray-500">Applications will appear here when users submit them.</p>
</div>
@endif
@endsection