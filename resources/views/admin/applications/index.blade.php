@extends('layouts.admin')

@section('title', 'Applications')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📋 Applications</h1>
                <p class="text-gray-500 text-sm mt-1">Review author, bookseller, and publisher applications</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border-l-4 border-purple-500 shadow-sm">
            <p class="text-gray-500 text-sm">Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border-l-4 border-yellow-500 shadow-sm">
            <p class="text-gray-500 text-sm">Pending</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border-l-4 border-green-500 shadow-sm">
            <p class="text-gray-500 text-sm">Approved</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border-l-4 border-red-500 shadow-sm">
            <p class="text-gray-500 text-sm">Rejected</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Search by name or email..." 
                       value="{{ request('search') }}"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <select name="type" class="px-4 py-2 border rounded-lg bg-white">
                    <option value="all">All Types</option>
                    <option value="author" {{ request('type') == 'author' ? 'selected' : '' }}>📚 Author</option>
                    <option value="bookseller" {{ request('type') == 'bookseller' ? 'selected' : '' }}>📖 Bookseller</option>
                    <option value="publisher" {{ request('type') == 'publisher' ? 'selected' : '' }}>📰 Publisher</option>
                    <option value="researcher" {{ request('type') == 'researcher' ? 'selected' : '' }}>🔬 Researcher</option>
                </select>
            </div>
            <div>
                <select name="status" class="px-4 py-2 border rounded-lg bg-white">
                    <option value="all">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                    <i class="ti ti-search"></i> Filter
                </button>
            </div>
            <div>
                <a href="{{ route('admin.applications.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Applications Table -->
    @if($applications->count() > 0)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applicant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($applications as $app)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-medium text-gray-900">{{ $app->user->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $app->user->email }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($app->type === 'author')
                            <span class="text-purple-600">📚 Author</span>
                        @elseif($app->type === 'bookseller')
                            <span class="text-orange-600">📖 Bookseller</span>
                        @elseif($app->type === 'publisher')
                            <span class="text-blue-600">📰 Publisher</span>
                        @elseif($app->type === 'researcher')
                            <span class="text-teal-600">🔬 Researcher</span>
                        @else
                            <span>{{ ucfirst($app->type) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($app->status === 'pending')
                            <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">⏳ Pending</span>
                        @elseif($app->status === 'approved')
                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">✅ Approved</span>
                        @elseif($app->status === 'rejected')
                            <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">❌ Rejected</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $app->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.applications.show', $app) }}" class="text-purple-600 hover:text-purple-800 text-sm">
                            <i class="ti ti-eye"></i> Review
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $applications->links() }}</div>
    @else
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <i class="ti ti-files text-6xl text-gray-400 mb-4 block"></i>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Applications Found</h3>
        <p class="text-gray-500">Applications will appear here when users apply.</p>
    </div>
    @endif
</div>
@endsection