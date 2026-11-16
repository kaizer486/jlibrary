@extends('layouts.author')

@section('title', 'My Listings')
@section('page-title', 'My Marketplace Listings')

@section('content')
<div class="space-y-6">

    <!-- ========================================== -->
    <!-- HEADER                                     -->
    <!-- ========================================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">My Listings</h2>
            <p class="text-gray-500 text-sm">Manage your marketplace products</p>
        </div>
        <a href="{{ route('marketplace.create') }}" class="btn-author flex items-center gap-2">
            <i class="ti ti-plus"></i>
            Add New Listing
        </a>
    </div>

    <!-- ========================================== -->
    <!-- STATS                                      -->
    <!-- ========================================== -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
        <!-- Total Listings -->
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 truncate">Total Listings</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-shopping-bag"></i>
                </div>
            </div>
        </div>

        <!-- Approved -->
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs text-emerald-600 truncate">Approved</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-800">{{ $stats['approved'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-check"></i>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs text-yellow-600 truncate">Pending</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-800">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-yellow-100 text-yellow-600 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-clock"></i>
                </div>
            </div>
        </div>

        <!-- Total Sales -->
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 truncate">Total Sales</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-800">{{ $stats['sales'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-coin"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SEARCH + FILTER                            -->
    <!-- ========================================== -->
    <form method="GET" action="{{ route('seller.listings') }}"
          class="bg-white rounded-xl shadow-sm p-3 md:p-4 flex flex-col md:flex-row gap-3 items-start md:items-center">
        <div class="w-full md:flex-1">
            <div class="relative">
                <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by title or description..."
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>
        </div>
        <div class="w-full md:w-auto">
            <select name="status"
                    class="w-full md:w-auto px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-orange-500">
                <option value="">All Statuses</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>✅ Approved</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>⏳ Pending</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
            </select>
        </div>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('seller.listings') }}"
               class="w-full md:w-auto text-center px-3 py-2 text-sm text-gray-600 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-lg transition whitespace-nowrap">
                <i class="ti ti-x"></i> Clear
            </a>
        @endif
        <button type="submit"
                class="w-full md:w-auto bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 text-sm rounded-lg font-medium transition whitespace-nowrap">
            <i class="ti ti-filter"></i> Filter
        </button>
    </form>

    <!-- ========================================== -->
    <!-- LISTINGS TABLE                             -->
    <!-- ========================================== -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Product</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Price</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Stats</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Sales</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($listings as $listing)
                        <tr class="hover:bg-gray-50 align-top">

                            <!-- Product -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-14 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                                        @if($listing->cover_image)
                                            <img src="{{ url('media/' . $listing->cover_image) }}"
                                                 alt="{{ $listing->title }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <i class="ti ti-book text-orange-500 text-xl"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-800 line-clamp-1">{{ $listing->title }}</p>
                                        @if($listing->description)
                                            <p class="text-xs text-gray-500 line-clamp-1 mt-0.5">{{ $listing->description }}</p>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            <i class="ti ti-hash text-[10px]"></i> {{ $listing->id }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <!-- Price + Format -->
                            <td class="px-4 py-3">
                                {!! $listing->format_badge !!}
                                <div class="mt-1 space-y-0.5 text-xs">
                                    @if($listing->offersSoftcopy())
                                        <p>
                                            <span class="text-blue-600 font-medium"></span>
                                            <span class="text-gray-700">TSh {{ number_format($listing->effectiveSoftcopyPrice(), 0) }}</span>
                                        </p>
                                    @endif
                                    @if($listing->offersHardcopy())
                                        <p>
                                            <span class="text-orange-600 font-medium"></span>
                                            <span class="text-gray-700">TSh {{ number_format($listing->effectiveHardcopyPrice(), 0) }}</span>
                                        </p>
                                        <p class="text-[10px] text-gray-500">
                                            Stock: {{ (int) ($listing->stock ?? 0) }}
                                        </p>
                                    @endif
                                </div>
                                @if($listing->commission_paid)
                                    <p class="text-[10px] text-emerald-600 mt-1">
                                        <i class="ti ti-circle-check text-[10px]"></i> Commission Paid
                                    </p>
                                @endif
                            </td>
                            <!-- Status -->
                            <td class="px-4 py-3">
                                @php
                                    $statusColor = match($listing->status) {
                                        'approved' => 'bg-emerald-100 text-emerald-700',
                                        'pending'  => 'bg-yellow-100 text-yellow-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        default    => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium {{ $statusColor }}">
                                    {{ ucfirst($listing->status) }}
                                </span>
                                @if($listing->status === 'rejected' && $listing->admin_notes)
                                    <p class="text-[10px] text-red-600 mt-1 line-clamp-2 max-w-[180px]"
                                       title="{{ $listing->admin_notes }}">
                                        <i class="ti ti-alert-circle text-[10px]"></i>
                                        {{ Str::limit($listing->admin_notes, 50) }}
                                    </p>
                                @endif
                            </td>

                            <!-- Stats -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3 text-xs text-gray-600">
                                    <span title="Views">
                                        <i class="ti ti-eye text-blue-500"></i> {{ number_format($listing->views ?? 0) }}
                                    </span>
                                    <span title="Downloads">
                                        <i class="ti ti-download text-green-500"></i> {{ number_format($listing->downloads ?? 0) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Sales -->
                            <td class="px-4 py-3">
                                @php
                                    $salesCount = $listing->orders_count ?? 0;
                                    $revenue = $listing->seller_revenue ?? 0;
                                @endphp
                                @if($salesCount > 0)
                                    <p class="font-semibold text-gray-800">{{ $salesCount }} order{{ $salesCount === 1 ? '' : 's' }}</p>
                                    <p class="text-xs text-emerald-600 mt-0.5">TSh {{ number_format($revenue, 0) }}</p>
                                @else
                                    <span class="text-xs text-gray-400">No sales yet</span>
                                @endif
                            </td>

                            <!-- Date -->
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $listing->created_at->format('M d, Y') }}
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1 flex-wrap justify-end">
                                    @if($listing->status === 'approved')
                                        <a href="{{ route('marketplace.show', $listing->id) }}"
                                           class="inline-flex items-center gap-1 text-xs px-2.5 py-1.5 rounded-lg text-gray-700 hover:bg-gray-100 font-medium transition"
                                           title="View public listing">
                                            <i class="ti ti-eye"></i> View
                                        </a>
                                    @endif
                                    <a href="{{ route('marketplace.edit', $listing) }}"
                                       class="inline-flex items-center gap-1 text-xs px-2.5 py-1.5 rounded-lg text-orange-600 hover:bg-orange-50 font-medium transition">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('marketplace.destroy', $listing) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Delete "{{ addslashes($listing->title) }}"? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 text-xs px-2.5 py-1.5 rounded-lg text-red-600 hover:bg-red-50 font-medium transition">
                                            <i class="ti ti-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <!-- Empty state -->
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                @if(request()->hasAny(['search', 'status']))
                                    <i class="ti ti-search-off text-5xl text-gray-300 mb-3 block"></i>
                                    <p class="text-gray-500 mb-1">No listings match your filters</p>
                                    <a href="{{ route('seller.listings') }}"
                                       class="text-sm text-orange-600 hover:text-orange-800 underline mt-2 inline-block">
                                        Clear filters
                                    </a>
                                @else
                                    <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="ti ti-shopping-bag-off text-3xl text-orange-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-1">No listings yet</h3>
                                    <p class="text-sm text-gray-500 mb-4">Add your first product to start selling in the marketplace.</p>
                                    <a href="{{ route('marketplace.create') }}"
                                       class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg font-medium transition">
                                        <i class="ti ti-plus"></i> Create Your First Listing
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($listings->hasPages())
            <div class="px-4 py-3 border-t">
                {{ $listings->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

</div>

<style>
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection