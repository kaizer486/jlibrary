@extends('layouts.master')

@section('title', 'Institutions')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">🏛️ Institutions</h1>
            <p class="text-gray-500 text-sm mt-1">View all registered institutions </p>
            <p class="text-xs text-blue-600 mt-1">
        
            </p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-gray-500">
        <p class="text-gray-500 text-sm">Total Institutions</p>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total'] ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500">
        <p class="text-gray-500 text-sm">Approved</p>
        <p class="text-2xl font-bold text-green-600">{{ number_format($stats['approved'] ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-yellow-500">
        <p class="text-gray-500 text-sm">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['pending'] ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-red-500">
        <p class="text-gray-500 text-sm">Suspended</p>
        <p class="text-2xl font-bold text-red-600">{{ number_format($stats['suspended'] ?? 0) }}</p>
    </div>
</div>

<!-- Search & Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" placeholder="Search institutions..." 
                   value="{{ request('search') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
        <div>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg bg-white">
                <option value="all">All Status</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>⚠️ Suspended</option>
            </select>
        </div>
        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition flex items-center gap-2">
            <i class="ti ti-search"></i> Filter
        </button>
        <a href="{{ route('admin.institutions.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
            <i class="ti ti-x"></i> Clear
        </a>
    </form>
</div>

<!-- Institutions Table -->
@if($institutions->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Institution</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Members</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Books</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($institutions as $institution)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900">{{ $institution->name }}</p>
                        <p class="text-xs text-gray-500">{{ $institution->city ?? 'N/A' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                            {{ $institution->type_label ?? 'Other' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <div class="flex items-center gap-1">
                            <i class="ti ti-users text-gray-400"></i>
                            {{ number_format($institution->users_count ?? 0) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <div class="flex items-center gap-1">
                            <i class="ti ti-books text-gray-400"></i>
                            {{ number_format($institution->books_count ?? 0) }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $colors = ['approved' => 'bg-green-100 text-green-700', 'pending' => 'bg-yellow-100 text-yellow-700', 'suspended' => 'bg-red-100 text-red-700'];
                            $color = $colors[$institution->status] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $color }}">
                            {{ ucfirst($institution->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.institutions.show', $institution->id) }}" class="text-purple-600 hover:text-purple-800 transition inline-flex items-center gap-1">
                            <i class="ti ti-eye"></i> View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">{{ $institutions->links() }}</div>
@else
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="ti ti-building text-6xl text-gray-400 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Institutions Found</h3>
    <p class="text-gray-500">No institutions match your search criteria.</p>
</div>
@endif
@endsection