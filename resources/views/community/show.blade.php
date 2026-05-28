@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button -->
    <a href="{{ route('community.index') }}" class="inline-flex items-center text-jlibrary-600 hover:text-jlibrary-700 mb-4">
        <i class="ti ti-arrow-left mr-1"></i> Back to Community
    </a>
    
    <div class="grid lg:grid-cols-4 gap-6">
        <!-- Sidebar - Group Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden sticky top-24">
                <!-- Group Cover -->
                <div class="h-32 bg-gradient-to-r from-jlibrary-500 to-jlibrary-700 relative">
                    @if($group->cover_image)
                        <img src="{{ Storage::url($group->cover_image) }}" alt="{{ $group->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="ti ti-users text-5xl text-white/50"></i>
                        </div>
                    @endif
                </div>
                
                <!-- Group Details -->
                <div class="p-4">
                    <h1 class="text-xl font-bold text-gray-900 mb-2">{{ $group->name }}</h1>
                    <p class="text-gray-600 text-sm mb-4">{{ $group->description }}</p>
                    
                    <div class="border-t pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Members</span>
                            <span class="font-semibold">{{ $memberCount }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Created</span>
                            <span class="font-semibold">{{ $group->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Created by</span>
                            <span class="font-semibold">{{ $group->creator->full_name ?? 'Admin' }}</span>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mt-4 space-y-2">
                        @auth
                            @if($isMember)
                                <form method="POST" action="{{ route('community.leave', $group) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                                        <i class="ti ti-logout"></i> Leave Group
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('community.join', $group) }}">
                                    @csrf
                                    <button type="submit" class="w-full bg-jlibrary-600 text-white px-4 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                                        <i class="ti ti-user-plus"></i> Join Group
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full text-center bg-jlibrary-600 text-white px-4 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                                Login to Join
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Chat Area -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-col" style="height: 70vh;">
                <!-- Chat Header -->
                <div class="bg-jlibrary-50 px-4 py-3 border-b">
                    <h2 class="font-semibold text-gray-900">Group Discussion</h2>
                    <p class="text-xs text-gray-500">Chat with other members</p>
                </div>
                
                <!-- Messages Area -->
                <div id="messages-container" class="flex-1 overflow-y-auto p-4 space-y-3">
                    @if($messages->count() > 0)
                        @foreach($messages as $message)
                            <div class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[70%]">
                                    @if($message->user_id !== auth()->id())
                                        <p class="text-xs text-gray-500 mb-1">{{ $message->user->full_name }}</p>
                                    @endif
                                    <div class="rounded-lg px-4 py-2 {{ $message->user_id === auth()->id() ? 'bg-jlibrary-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                                        <p class="text-sm">{{ $message->message }}</p>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">{{ $message->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-gray-500 py-8">
                            <i class="ti ti-message-circle text-4xl mb-2 block"></i>
                            <p>No messages yet. Start the conversation!</p>
                        </div>
                    @endif
                </div>
                
                <!-- Message Input -->
                @auth
                    @if($isMember)
                        <div class="border-t p-4">
                            <form method="POST" action="{{ route('community.send-message', $group) }}" class="flex gap-2">
                                @csrf
                                <input type="text" name="message" placeholder="Type your message..." 
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500 focus:border-transparent"
                                       required>
                                <button type="submit" class="bg-jlibrary-600 text-white px-6 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                                    <i class="ti ti-send"></i> Send
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="border-t p-4 text-center bg-gray-50">
                            <p class="text-gray-500">Join this group to participate in discussions</p>
                        </div>
                    @endif
                @else
                    <div class="border-t p-4 text-center bg-gray-50">
                        <a href="{{ route('login') }}" class="text-jlibrary-600 hover:text-jlibrary-700">Login</a> to join the conversation
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-scroll to bottom of messages
    const container = document.getElementById('messages-container');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
    
    // Auto-refresh messages every 5 seconds (simple polling for now)
    let lastMessageId = {{ $messages->last()->id ?? 0 }};
    
    setInterval(function() {
        fetch('{{ route("community.messages", $group) }}/' + lastMessageId)
            .then(response => response.json())
            .then(messages => {
                if (messages.length > 0) {
                    lastMessageId = messages[messages.length - 1].id;
                    location.reload(); // Simple reload for now
                }
            })
            .catch(error => console.log('Error:', error));
    }, 5000);
</script>
@endpush
@endsection