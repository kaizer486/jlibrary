@extends('layouts.app')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen">
    <div class="w-full px-4 py-6">
        
        <!-- Institution Header - Full Width -->
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl p-6 mb-6 text-white shadow-xl">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                    <i class="ti ti-building text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold">{{ $institution->name }}</h1>
                    <div class="flex items-center gap-3 mt-2 flex-wrap">
                        <span class="text-sm bg-white/20 px-3 py-1 rounded-full">{{ $institution->type_label ?? 'University' }}</span>
                        @if($institution->city)
                        <span class="text-sm flex items-center gap-1">
                            <i class="ti ti-map-pin"></i> {{ $institution->city }}, {{ $institution->region ?? '' }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Members</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $institution->users_count ?? 0 }}</p>
                    </div>
                    <i class="ti ti-users text-indigo-500 text-3xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Books & Resources</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $institution->books_count ?? 0 }}</p>
                    </div>
                    <i class="ti ti-books text-indigo-500 text-3xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Established</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $institution->created_at?->format('Y') ?? '2020' }}</p>
                    </div>
                    <i class="ti ti-calendar text-indigo-500 text-3xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Rating</p>
                        <p class="text-lg font-semibold text-yellow-500">★★★★☆ 4.8</p>
                    </div>
                    <i class="ti ti-star text-yellow-500 text-3xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Contact & About -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            @if($institution->email || $institution->phone)
            <div class="bg-white rounded-xl p-5 shadow-md">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="ti ti-mail"></i> Contact Information
                </h3>
                <div class="space-y-2 text-sm">
                    @if($institution->email)
                    <div class="flex items-center gap-2">
                        <i class="ti ti-mail text-gray-400"></i>
                        <span class="text-gray-600">{{ $institution->email }}</span>
                    </div>
                    @endif
                    @if($institution->phone)
                    <div class="flex items-center gap-2">
                        <i class="ti ti-phone text-gray-400"></i>
                        <span class="text-gray-600">{{ $institution->phone }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            <div class="bg-white rounded-xl p-5 shadow-md">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="ti ti-info-circle"></i> About This Institution
                </h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    {{ $institution->description ?? 'This institution is dedicated to providing quality education and resources to its members. Join to access exclusive learning materials, connect with peers, and participate in community events.' }}
                </p>
            </div>
        </div>
        
        <!-- Yellow Separator -->
        <div class="w-24 h-1 bg-yellow-400 rounded-full my-6"></div>
        
        <!-- Books Section -->
        <div class="mb-6">
            <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                <i class="ti ti-books"></i> Books & Resources
            </h2>
            @if($institutionBooks->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($institutionBooks as $book)
                <div class="bg-white rounded-xl p-4 shadow-md hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-3">
                        <i class="ti ti-book text-indigo-600 text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800">{{ Str::limit($book->title, 40) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $book->author ?? 'Unknown' }}</p>
                </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $institutionBooks->links() }}
            </div>
            @else
            <div class="bg-white/10 rounded-xl p-8 text-center">
                <i class="ti ti-books text-4xl text-gray-400 mb-2 block"></i>
                <p class="text-gray-300">No books available yet</p>
            </div>
            @endif
        </div>
        
        <!-- Members Section -->
        <div class="mb-6">
            <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                <i class="ti ti-users"></i> Members ({{ $institution->users_count ?? 0 }})
            </h2>
            @if($members->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($members as $member)
                <div class="bg-white rounded-xl p-4 shadow-md flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center">
                        <i class="ti ti-user text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">{{ $member->full_name ?? $member->name }}</p>
                        <p class="text-xs text-gray-500">Member</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $members->links() }}
            </div>
            @else
            <div class="bg-white/10 rounded-xl p-8 text-center">
                <i class="ti ti-users text-4xl text-gray-400 mb-2 block"></i>
                <p class="text-gray-300">No members yet</p>
            </div>
            @endif
        </div>
        
        <!-- REQUEST TO JOIN SECTION -->
        @if(!$isMember)
        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl p-6 mb-6">
            <div class="text-center">
                <i class="ti ti-building-community text-purple-500 text-4xl mb-3 block"></i>
                
                @if(isset($existingRequest) && $existingRequest && $existingRequest->status === 'pending')
                    <div class="inline-flex items-center gap-2 bg-yellow-100 text-yellow-700 px-4 py-2 rounded-full mb-3">
                        <i class="ti ti-clock"></i>
                        <span>Request Pending Approval</span>
                    </div>
                    <p class="text-gray-600 mb-3">Your request to join has been submitted.</p>
                    <form method="POST" action="{{ route('join-requests.cancel', $existingRequest->id) }}" class="inline" onsubmit="return confirm('Cancel your request?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 text-sm hover:underline">Cancel Request</button>
                    </form>
                @elseif(isset($existingRequest) && $existingRequest && $existingRequest->status === 'rejected')
                    <div class="inline-flex items-center gap-2 bg-red-100 text-red-700 px-4 py-2 rounded-full mb-3">
                        <i class="ti ti-x-circle"></i>
                        <span>Request Declined</span>
                    </div>
                    <button onclick="openJoinModal()" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-2 rounded-lg font-semibold">
                        Submit New Request
                    </button>
                @else
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Want to join this institution?</h3>
                    <p class="text-gray-600 mb-4">Submit a request to become a member</p>
                    <button onclick="openJoinModal()" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-8 py-3 rounded-xl font-semibold transition transform hover:scale-105">
                        <i class="ti ti-send"></i> Request to Join
                    </button>
                @endif
            </div>
        </div>
        @endif
        
        <!-- BACK TO BUTTON AT BOTTOM -->
        <div class="text-center py-6 flex justify-center gap-4">
            <a href="{{ route('discover.institutions') }}" 
               class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-full transition">
                <i class="ti ti-arrow-left"></i> Back to Discover
            </a>
            <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
                    class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-full transition">
                <i class="ti ti-arrow-up"></i> Back to Top
            </button>
        </div>
        
    </div>
</div>

<!-- Join Request Modal -->
<div id="joinModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl max-w-md w-full mx-4 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Request to Join</h3>
                <button onclick="closeJoinModal()" class="text-white/80 hover:text-white">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('join-requests.store') }}" class="p-6">
            @csrf
            <input type="hidden" name="institution_id" value="{{ $institution->id }}">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Why do you want to join? (Optional)</label>
                <textarea name="message" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Tell the admin why you'd like to join..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-4 py-2 rounded-lg font-semibold">Send Request</button>
                <button type="button" onclick="closeJoinModal()" class="px-4 py-2 border rounded-lg">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openJoinModal() {
    document.getElementById('joinModal').classList.remove('hidden');
    document.getElementById('joinModal').classList.add('flex');
}
function closeJoinModal() {
    document.getElementById('joinModal').classList.add('hidden');
    document.getElementById('joinModal').classList.remove('flex');
}
</script>
@endsection