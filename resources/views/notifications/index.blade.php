@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="ti ti-bell text-purple-600 text-3xl"></i>
                    Notifications
                </h1>
                <p class="text-gray-500 text-sm mt-1">Stay updated with your latest activities</p>
            </div>
            @if($unreadCount > 0)
                <button onclick="markAllRead()" class="text-purple-600 text-sm hover:underline">
                    Mark all as read
                </button>
            @endif
        </div>

        <!-- Filter Tabs -->
        <div class="flex gap-2 mb-4">
            <button onclick="filterNotifications('all')" class="filter-btn active px-4 py-2 rounded-lg text-sm font-medium bg-purple-600 text-white">
                All
            </button>
            <button onclick="filterNotifications('library')" class="filter-btn px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-600 hover:bg-gray-300 transition">
                📚 Library
            </button>
            <button onclick="filterNotifications('platform')" class="filter-btn px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-600 hover:bg-gray-300 transition">
                🌐 Platform
            </button>
        </div>

        <!-- Notifications List -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden" id="notifications-list">
            @forelse($notifications as $notification)
                <div class="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition {{ $notification->is_read ? '' : 'bg-purple-50/30' }} 
                    @if($notification->isLibraryNotification()) library-notification @else platform-notification @endif"
                    data-type="{{ $notification->isLibraryNotification() ? 'library' : 'platform' }}">
                    <div class="p-5">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    @if(!$notification->is_read)
                                        <span class="w-2 h-2 bg-purple-600 rounded-full"></span>
                                    @endif
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $notification->badge_class }}">
                                        @if($notification->isLibraryNotification())
                                            📚
                                        @else
                                            🌐
                                        @endif
                                        {{ ucfirst(str_replace('library_', '', $notification->type)) }}
                                    </span>
                                    <h3 class="font-semibold text-gray-800">{{ $notification->title }}</h3>
                                    <span class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-600 text-sm">{{ $notification->message }}</p>
                                
                                @if($notification->data)
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @if(isset($notification->data['book_id']))
                                            <a href="{{ route('library.show', $notification->data['book_id']) }}" 
                                               class="text-purple-600 text-sm hover:underline inline-flex items-center gap-1">
                                                View Book <i class="ti ti-arrow-right text-xs"></i>
                                            </a>
                                        @endif
                                        @if(isset($notification->data['certificate_id']))
                                            <a href="{{ route('certificates.show', $notification->data['certificate_id']) }}" 
                                               class="text-purple-600 text-sm hover:underline inline-flex items-center gap-1">
                                                View Certificate <i class="ti ti-arrow-right text-xs"></i>
                                            </a>
                                        @endif
                                        @if(isset($notification->data['quiz_id']))
                                            <a href="{{ route('quizzes.results', $notification->data['quiz_id']) }}" 
                                               class="text-purple-600 text-sm hover:underline inline-flex items-center gap-1">
                                                View Results <i class="ti ti-arrow-right text-xs"></i>
                                            </a>
                                        @endif
                                        @if(isset($notification->data['institution_id']) && isset($notification->data['institution_name']))
                                            <a href="{{ route('institutions.show', $notification->data['institution_id']) }}" 
                                               class="text-purple-600 text-sm hover:underline inline-flex items-center gap-1">
                                                View Institution <i class="ti ti-arrow-right text-xs"></i>
                                            </a>
                                        @endif
                                      @if(isset($notification->data['join_request_id']))
    <a href="{{ route('institution.join-requests.index') }}" 
       class="text-purple-600 text-sm hover:underline inline-flex items-center gap-1">
        View Requests <i class="ti ti-arrow-right text-xs"></i>
    </a>
@endif
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                @if(!$notification->is_read)
                                    <button onclick="markAsRead({{ $notification->id }})" 
                                            class="text-xs text-purple-600 hover:underline">
                                        Mark read
                                    </button>
                                @endif
                                <button onclick="deleteNotification({{ $notification->id }})" 
                                        class="text-gray-400 hover:text-red-500 transition">
                                    <i class="ti ti-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <i class="ti ti-bell-off text-5xl text-gray-300 mb-3 block"></i>
                    <p class="text-gray-500">No notifications yet</p>
                    <p class="text-gray-400 text-sm mt-1">When you have activities, they'll appear here</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function filterNotifications(type) {
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active', 'bg-purple-600', 'text-white'));
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.add('bg-gray-200', 'text-gray-600');
        btn.classList.remove('bg-purple-600', 'text-white');
    });
    
    if (type === 'all') {
        document.querySelectorAll('.filter-btn')[0].classList.add('bg-purple-600', 'text-white');
        document.querySelectorAll('.filter-btn')[0].classList.remove('bg-gray-200', 'text-gray-600');
        document.querySelectorAll('.notification-item').forEach(el => el.style.display = 'block');
    } else if (type === 'library') {
        document.querySelectorAll('.filter-btn')[1].classList.add('bg-purple-600', 'text-white');
        document.querySelectorAll('.filter-btn')[1].classList.remove('bg-gray-200', 'text-gray-600');
        document.querySelectorAll('.platform-notification').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.library-notification').forEach(el => el.style.display = 'block');
    } else {
        document.querySelectorAll('.filter-btn')[2].classList.add('bg-purple-600', 'text-white');
        document.querySelectorAll('.filter-btn')[2].classList.remove('bg-gray-200', 'text-gray-600');
        document.querySelectorAll('.library-notification').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.platform-notification').forEach(el => el.style.display = 'block');
    }
}

function markAsRead(id) {
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function markAllRead() {
    fetch('{{ route("notifications.mark-all-read") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function deleteNotification(id) {
    if (confirm('Delete this notification?')) {
        fetch(`/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>

<style>
.filter-btn.active {
    background-color: #7c3aed !important;
    color: white !important;
}
</style>
@endsection