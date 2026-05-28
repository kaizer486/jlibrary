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

        <!-- Notifications List -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            @forelse($notifications as $notification)
                <div class="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition {{ $notification->is_read ? '' : 'bg-purple-50/30' }}">
                    <div class="p-5">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    @if(!$notification->is_read)
                                        <span class="w-2 h-2 bg-purple-600 rounded-full"></span>
                                    @endif
                                    <h3 class="font-semibold text-gray-800">{{ $notification->title }}</h3>
                                    <span class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-600 text-sm">{{ $notification->message }}</p>
                                
                                @if($notification->data)
                                    <div class="mt-3">
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
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
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
@endsection