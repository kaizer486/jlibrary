@extends('layouts.institution')

@section('title', 'Institution Quotes')

@section('content')

@php
    // ==========================================
    // SECURITY CHECKS
    // ==========================================
    
    // Check if user belongs to an institution
    if (!auth()->user()->institution_id) {
        abort(403, 'You do not belong to any institution.');
    }
    
    // Check if institution exists
    if (!isset($institution) || !$institution) {
        abort(404, 'Institution not found.');
    }
    
    // Check if user has access to this institution
    if (auth()->user()->institution_id != $institution->id) {
        abort(403, 'You do not have access to this institution.');
    }
@endphp

<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">🏛️ Institution Quotes</h1>
            <p class="text-gray-500 text-sm mt-1">Manage quotes for {{ $institution->name }}</p>
            <p class="text-xs text-blue-600 mt-1"> These quotes will ONLY be visible to members of your institution</p>
        </div>
        @can('create', App\Models\Quote::class)
            <a href="{{ route('institution.quotes.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition flex items-center gap-2 shadow-md">
                <i class="ti ti-plus"></i> Add New Quote
            </a>
        @endcan
    </div>
</div>

<!-- ========================================== -->
<!-- INFO BANNER                                -->
<!-- ========================================== -->
<div class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4 mb-6">
    <div class="flex items-center gap-3">
        <i class="ti ti-info-circle text-blue-500 text-xl"></i>
        <div>
            <p class="text-sm text-blue-800">
                <strong>Note:</strong> Quotes you create here will ONLY be visible to members of <strong>{{ $institution->name }}</strong>.
                Users outside your institution will not see these quotes.
            </p>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- STATS CARDS                                -->
<!-- ========================================== -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-purple-500">
        <p class="text-gray-500 text-sm">Total Quotes</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500">
        <p class="text-gray-500 text-sm">Active Quotes</p>
        <p class="text-2xl font-bold text-green-600">{{ $stats['active'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-blue-500">
        <p class="text-gray-500 text-sm">Total Saves</p>
        <p class="text-2xl font-bold text-blue-600">{{ $stats['saves'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-yellow-500">
        <p class="text-gray-500 text-sm">Total Shares</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['shares'] ?? 0 }}</p>
    </div>
</div>

<!-- ========================================== -->
<!-- SEARCH AND FILTER                          -->
<!-- ========================================== -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <input type="text" name="search" placeholder="Search quotes or authors..." 
                   value="{{ request('search') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
        </div>
        <div>
            <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-purple-500">
                <option value="all">All Categories</option>
                @foreach($categories ?? [] as $key => $label)
                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-purple-500">
                <option value="all">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}> Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}> Draft</option>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition flex items-center gap-2">
                <i class="ti ti-search"></i> Filter
            </button>
        </div>
        <div>
            <a href="{{ route('institution.quotes.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition inline-block">
                <i class="ti ti-x"></i> Clear
            </a>
        </div>
    </form>
</div>

<!-- ========================================== -->
<!-- QUOTES TABLE                               -->
<!-- ========================================== -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quote</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stats</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($quotes as $quote)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <p class="text-gray-800 line-clamp-2">{{ Str::limit($quote->quote_text, 80) }}</p>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $quote->author ?? 'Anonymous' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                            {{ ucfirst($quote->category ?? 'General') }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-3 text-xs">
                            <span title="Views"><i class="ti ti-eye"></i> {{ number_format($quote->views_count ?? 0) }}</span>
                            <span title="Saves"><i class="ti ti-heart"></i> {{ number_format($quote->saves_count ?? 0) }}</span>
                            <span title="Shares"><i class="ti ti-share"></i> {{ number_format($quote->shares_count ?? 0) }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'active' => 'bg-green-100 text-green-700',
                                'inactive' => 'bg-red-100 text-red-700',
                                'draft' => 'bg-yellow-100 text-yellow-700'
                            ];
                            $color = $statusColors[$quote->status] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $color }}">
                            {{ ucfirst($quote->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            @can('update', $quote)
                                <a href="{{ route('institution.quotes.edit', $quote) }}" class="text-blue-600 hover:text-blue-800 transition" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </a>
                            @endcan
                            @can('view', $quote)
                                <a href="{{ route('institution.quotes.analytics', $quote) }}" class="text-green-600 hover:text-green-800 transition" title="Analytics">
                                    <i class="ti ti-chart-bar"></i>
                                </a>
                            @endcan
                            @can('delete', $quote)
                                <form method="POST" action="{{ route('institution.quotes.destroy', $quote) }}" 
                                      onsubmit="return confirm('Are you sure you want to delete this quote?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition" title="Delete">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="ti ti-quote text-4xl mb-2 block"></i>
                        <p class="text-gray-600">No quotes found.</p>
                        @can('create', App\Models\Quote::class)
                            <a href="{{ route('institution.quotes.create') }}" class="mt-3 inline-block text-purple-600 hover:text-purple-800 text-sm font-medium">
                                <i class="ti ti-plus"></i> Add Quote
                            </a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $quotes->withQueryString()->links() }}
</div>

@endsection