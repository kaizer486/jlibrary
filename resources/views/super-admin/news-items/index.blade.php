@extends('layouts.super-admin')

@section('title', 'Manage News & Updates')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">News & Updates</h1>
            <p class="text-gray-500 text-sm mt-1">Manage the news items displayed on the welcome page</p>
        </div>
        <a href="{{ route('super-admin.news-items.create') }}" 
           class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
            <i class="ti ti-plus"></i> Add News
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div>
                <label class="text-sm text-gray-600">Filter by category:</label>
                <select id="category-filter" class="ml-2 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none">
                    <option value="all">All Categories</option>
                    <option value="Books">Books</option>
                    <option value="Events">Events</option>
                    <option value="Certificates">Certificates</option>
                    <option value="Announcements">Announcements</option>
                    <option value="Authors">Authors</option>
                </select>
            </div>
            <div>
                <label class="text-sm text-gray-600">Show:</label>
                <select id="status-filter" class="ml-2 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none">
                    <option value="all">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="featured">Featured Only</option>
                </select>
            </div>
            <div class="flex-1"></div>
            <span class="text-sm text-gray-500">{{ $newsItems->count() }} items</span>
        </div>
    </div>

    <!-- News List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        @if($newsItems->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Featured</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="sortable">
                    @foreach($newsItems as $item)
                    <tr class="hover:bg-gray-50 transition" data-id="{{ $item->id }}" data-category="{{ $item->category }}" data-active="{{ $item->is_active ? 'active' : 'inactive' }}" data-featured="{{ $item->is_featured ? 'featured' : 'not-featured' }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <i class="ti ti-grip-vertical text-gray-400 cursor-move"></i>
                                <span class="text-sm text-gray-600">{{ $item->order }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-800">{{ $item->title }}</p>
                                @if($item->content)
                                    <p class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($item->content, 80) }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-1">{{ $item->published_at->format('M d, Y') }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                @if($item->category == 'Books') bg-blue-100 text-blue-700
                                @elseif($item->category == 'Events') bg-green-100 text-green-700
                                @elseif($item->category == 'Certificates') bg-purple-100 text-purple-700
                                @elseif($item->category == 'Announcements') bg-yellow-100 text-yellow-700
                                @elseif($item->category == 'Authors') bg-pink-100 text-pink-700
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ $item->category ?? 'General' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('super-admin.news-items.toggle-featured', $item) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded-full text-xs font-semibold transition
                                    {{ $item->is_featured ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    {{ $item->is_featured ? '⭐ Featured' : 'Not Featured' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('super-admin.news-items.toggle-status', $item) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded-full text-xs font-semibold transition
                                    {{ $item->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    {{ $item->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('super-admin.news-items.edit', $item) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition">
                                    <i class="ti ti-edit text-lg"></i>
                                </a>
                                <form action="{{ route('super-admin.news-items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this news item?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition">
                                        <i class="ti ti-trash text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-12 text-center">
                <i class="ti ti-news text-5xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500">No news items created yet</p>
                <a href="{{ route('super-admin.news-items.create') }}" class="text-purple-600 hover:underline mt-2 inline-block">
                    Create your first news item
                </a>
            </div>
        @endif
    </div>

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <div class="flex items-start gap-3">
            <i class="ti ti-info-circle text-blue-500 text-xl mt-0.5"></i>
            <div>
                <p class="text-sm text-blue-700 font-medium">How it works:</p>
                <ul class="text-sm text-blue-600 list-disc list-inside mt-1 space-y-1">
                    <li>Drag and drop news items to reorder them</li>
                    <li>Featured items will appear with a star icon</li>
                    <li>Only active news items will appear on the welcome page</li>
                    <li>Categories help organize content for visitors</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sortable
        const sortable = document.getElementById('sortable');
        if (sortable) {
            new Sortable(sortable, {
                handle: '.ti-grip-vertical',
                animation: 150,
                onEnd: function() {
                    const items = [];
                    document.querySelectorAll('#sortable tr[data-id]').forEach((row, index) => {
                        items.push({
                            id: row.dataset.id,
                            order: index + 1
                        });
                    });

                    fetch('{{ route("super-admin.news-items.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ items: items.map(item => item.id) })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelectorAll('#sortable tr[data-id]').forEach((row, index) => {
                                const orderSpan = row.querySelector('td:first-child span');
                                if (orderSpan) {
                                    orderSpan.textContent = index + 1;
                                }
                            });
                        }
                    });
                }
            });
        }

        // Filters
        const categoryFilter = document.getElementById('category-filter');
        const statusFilter = document.getElementById('status-filter');

        function filterTable() {
            const category = categoryFilter.value;
            const status = statusFilter.value;
            const rows = document.querySelectorAll('#sortable tr[data-id]');

            rows.forEach(row => {
                let show = true;

                // Category filter
                if (category !== 'all' && row.dataset.category !== category) {
                    show = false;
                }

                // Status filter
                if (status !== 'all') {
                    if (status === 'active' && row.dataset.active !== 'active') show = false;
                    if (status === 'inactive' && row.dataset.active !== 'inactive') show = false;
                    if (status === 'featured' && row.dataset.featured !== 'featured') show = false;
                }

                row.style.display = show ? '' : 'none';
            });
        }

        categoryFilter.addEventListener('change', filterTable);
        statusFilter.addEventListener('change', filterTable);
    });
</script>
@endpush
@endsection