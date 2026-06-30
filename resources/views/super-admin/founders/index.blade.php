@extends('layouts.super-admin')

@section('title', 'Manage Founders')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Founders & Leadership</h1>
            <p class="text-gray-500 text-sm mt-1">Manage the founders and leadership team displayed on the welcome page</p>
        </div>
        <a href="{{ route('super-admin.founders.create') }}" 
           class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
            <i class="ti ti-plus"></i> Add Founder
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

    <!-- Founders Grid -->
    @if($founders->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="sortable-grid">
            @foreach($founders as $founder)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition" data-id="{{ $founder->id }}">
                <div class="p-6">
                    <!-- Drag Handle -->
                    <div class="flex justify-end mb-2">
                        <i class="ti ti-grip-vertical text-gray-400 cursor-move"></i>
                    </div>
                    
                    <!-- Photo -->
                    <div class="flex justify-center mb-4">
                        @if($founder->photo)
                            <img src="{{ asset('storage/' . $founder->photo) }}" 
                                 alt="{{ $founder->name }}" 
                                 class="w-24 h-24 rounded-full object-cover border-4 border-purple-100">
                        @else
                            <div class="w-24 h-24 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-white text-2xl font-bold">
                                {{ substr($founder->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <!-- Name & Title -->
                    <div class="text-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">{{ $founder->name }}</h3>
                        @if($founder->title)
                            <p class="text-sm text-purple-600 font-medium">{{ $founder->title }}</p>
                        @endif
                    </div>

                    <!-- Bio Preview -->
                    @if($founder->bio)
                        <div class="text-center mb-4">
                            <p class="text-sm text-gray-600 line-clamp-3">{{ Str::limit($founder->bio, 120) }}</p>
                        </div>
                    @endif

                    <!-- Email & Phone -->
                    <div class="space-y-1 mb-4 text-sm text-gray-600">
                        @if($founder->email)
                            <div class="flex items-center justify-center gap-2">
                                <i class="ti ti-mail text-gray-400"></i>
                                <span>{{ $founder->email }}</span>
                            </div>
                        @endif
                        @if($founder->phone)
                            <div class="flex items-center justify-center gap-2">
                                <i class="ti ti-phone text-gray-400"></i>
                                <span>{{ $founder->phone }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Social Links -->
                    @if($founder->social_links && count($founder->social_links) > 0)
                        <div class="flex justify-center gap-2 mb-4 flex-wrap">
                            @if(isset($founder->social_links['twitter']))
                                <a href="{{ $founder->social_links['twitter'] }}" target="_blank" class="text-gray-500 hover:text-blue-400 transition">
                                    <i class="ti ti-brand-twitter text-lg"></i>
                                </a>
                            @endif
                            @if(isset($founder->social_links['instagram']))
                                <a href="{{ $founder->social_links['instagram'] }}" target="_blank" class="text-gray-500 hover:text-pink-500 transition">
                                    <i class="ti ti-brand-instagram text-lg"></i>
                                </a>
                            @endif
                            @if(isset($founder->social_links['facebook']))
                                <a href="{{ $founder->social_links['facebook'] }}" target="_blank" class="text-gray-500 hover:text-blue-600 transition">
                                    <i class="ti ti-brand-facebook text-lg"></i>
                                </a>
                            @endif
                            @if(isset($founder->social_links['tiktok']))
                                <a href="{{ $founder->social_links['tiktok'] }}" target="_blank" class="text-gray-500 hover:text-black transition">
                                    <i class="ti ti-brand-tiktok text-lg"></i>
                                </a>
                            @endif
                            @if(isset($founder->social_links['whatsapp']))
                                <a href="{{ $founder->social_links['whatsapp'] }}" target="_blank" class="text-gray-500 hover:text-green-500 transition">
                                    <i class="ti ti-brand-whatsapp text-lg"></i>
                                </a>
                            @endif
                            @if(isset($founder->social_links['youtube']))
                                <a href="{{ $founder->social_links['youtube'] }}" target="_blank" class="text-gray-500 hover:text-red-600 transition">
                                    <i class="ti ti-brand-youtube text-lg"></i>
                                </a>
                            @endif
                        </div>
                    @endif

                    <!-- Status & Actions -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">#{{ $founder->order }}</span>
                            <form action="{{ route('super-admin.founders.toggle-status', $founder) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded-full text-xs font-semibold transition
                                    {{ $founder->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    {{ $founder->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('super-admin.founders.edit', $founder) }}" 
                               class="text-blue-600 hover:text-blue-800 transition">
                                <i class="ti ti-edit text-lg"></i>
                            </a>
                            <form action="{{ route('super-admin.founders.destroy', $founder) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this founder?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 transition">
                                    <i class="ti ti-trash text-lg"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="ti ti-users text-5xl text-gray-300 mb-3 block"></i>
            <p class="text-gray-500">No founders added yet</p>
            <a href="{{ route('super-admin.founders.create') }}" class="text-purple-600 hover:underline mt-2 inline-block">
                Add your first founder
            </a>
        </div>
    @endif

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <div class="flex items-start gap-3">
            <i class="ti ti-info-circle text-blue-500 text-xl mt-0.5"></i>
            <div>
                <p class="text-sm text-blue-700 font-medium">How it works:</p>
                <ul class="text-sm text-blue-600 list-disc list-inside mt-1 space-y-1">
                    <li>Drag and drop founders to reorder them</li>
                    <li>Each founder can have a profile photo and social links</li>
                    <li>Only active founders will appear on the welcome page</li>
                    <li>Add multiple founders to build your leadership team</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const grid = document.getElementById('sortable-grid');
        if (grid) {
            new Sortable(grid, {
                handle: '.ti-grip-vertical',
                animation: 150,
                onEnd: function() {
                    const items = [];
                    document.querySelectorAll('#sortable-grid div[data-id]').forEach((div, index) => {
                        items.push({
                            id: div.dataset.id,
                            order: index + 1
                        });
                    });

                    fetch('{{ route("super-admin.founders.reorder") }}', {
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
                            document.querySelectorAll('#sortable-grid div[data-id]').forEach((div, index) => {
                                const orderSpan = div.querySelector('.text-xs.text-gray-500');
                                if (orderSpan) {
                                    orderSpan.textContent = '#' + (index + 1);
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