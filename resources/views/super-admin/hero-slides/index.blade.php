@extends('layouts.super-admin')

@section('title', 'Manage Hero Slides')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Hero Slides</h1>
            <p class="text-gray-500 text-sm mt-1">Manage the hero slider content with glassmorphism design</p>
        </div>
        <a href="{{ route('super-admin.hero-slides.create') }}" 
           class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
            <i class="ti ti-plus"></i> Add New Slide
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        @if($slides->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Preview</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stats</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="sortable">
                    @foreach($slides as $slide)
                    <tr class="hover:bg-gray-50 transition" data-id="{{ $slide->id }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <i class="ti ti-grip-vertical text-gray-400 cursor-move"></i>
                                <span class="text-sm text-gray-600">{{ $slide->order }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($slide->image)
                                <img src="{{ asset('storage/' . $slide->image) }}" alt="{{ $slide->title }}" class="w-20 h-14 object-cover rounded-lg">
                            @else
                                <div class="w-20 h-14 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-xs">No image</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-800">{{ $slide->title }}</p>
                                @if($slide->subtitle)
                                    <p class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($slide->subtitle, 60) }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                {{ $slide->slide_type_label }}
                            </span>
                        </td>
                       
                        <td class="px-6 py-4">
                            <form action="{{ route('super-admin.hero-slides.toggle-status', $slide) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded-full text-xs font-semibold transition
                                    {{ $slide->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    {{ $slide->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('super-admin.hero-slides.edit', $slide) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition">
                                    <i class="ti ti-edit text-lg"></i>
                                </a>
                                <form action="{{ route('super-admin.hero-slides.destroy', $slide) }}" method="POST" class="inline" onsubmit="return confirm('Delete this slide?')">
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
                <i class="ti ti-photo text-5xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500">No hero slides created yet</p>
                <a href="{{ route('super-admin.hero-slides.create') }}" class="text-purple-600 hover:underline mt-2 inline-block">
                    Create your first slide
                </a>
            </div>
        @endif
    </div>

    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <div class="flex items-start gap-3">
            <i class="ti ti-info-circle text-blue-500 text-xl mt-0.5"></i>
            <div>
                <p class="text-sm text-blue-700 font-medium">How it works:</p>
                <ul class="text-sm text-blue-600 list-disc list-inside mt-1 space-y-1">
                    <li>Drag and drop slides to reorder them</li>
                  
                    <li>Slide types: Dashboard, Books, AI, Community, Custom</li>
                    <li>Only active slides will appear on the welcome page</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sortable = document.getElementById('sortable');
        if (sortable) {
            new Sortable(sortable, {
                handle: '.ti-grip-vertical',
                animation: 150,
                onEnd: function() {
                    const items = [];
                    document.querySelectorAll('#sortable tr[data-id]').forEach((row, index) => {
                        items.push({ id: row.dataset.id, order: index + 1 });
                    });

                    fetch('{{ route("super-admin.hero-slides.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ slides: items.map(item => item.id) })
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
    });
</script>
@endpush
@endsection