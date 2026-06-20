@extends('layouts.app')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen">
    <div class="w-full px-4 py-6">
        
        <!-- ========================================== -->
        <!-- HEADER                                      -->
        <!-- ========================================== -->
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-2">
                <i class="ti ti-building-community text-purple-400 text-3xl"></i>
                <h1 class="text-2xl md:text-3xl font-bold text-white">Discover Institutions</h1>
            </div>
            <div class="w-16 h-1 bg-yellow-400 rounded-full mb-2"></div>
            <p class="text-gray-300">Browse institutions you can join</p>
        </div>

        <!-- ========================================== -->
        <!-- MY INSTITUTION STATUS                      -->
        <!-- ========================================== -->
        @if(auth()->user()->institution_id)
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl p-4 mb-6 text-white shadow-lg">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="ti ti-building text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-green-100">Your Institution</p>
                            <p class="font-bold text-lg">{{ auth()->user()->institution->name }}</p>
                        </div>
                    </div>
                    <a href="{{ route('my.institution') }}" class="bg-white text-green-700 px-4 py-2 rounded-lg hover:bg-green-50 transition text-sm font-semibold">
                        <i class="ti ti-arrow-right"></i> Go to My Institution
                    </a>
                </div>
            </div>
        @else
            <div class="bg-yellow-500/20 border border-yellow-500/30 rounded-xl p-4 mb-6">
                <div class="flex items-center gap-3">
                    <i class="ti ti-alert-circle text-yellow-400 text-2xl"></i>
                    <div>
                        <p class="text-white font-semibold">You haven't joined an institution yet</p>
                        <p class="text-sm text-yellow-300">Browse institutions below and request to join</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- PENDING REQUESTS STATUS                    -->
        <!-- ========================================== -->
        @if(isset($userRequests) && $userRequests->where('status', 'pending')->count() > 0)
            <div class="bg-blue-500/20 border border-blue-500/30 rounded-xl p-4 mb-6">
                <div class="flex items-center gap-3">
                    <i class="ti ti-clock text-blue-400 text-2xl"></i>
                    <div>
                        <p class="text-white font-semibold">You have pending join requests</p>
                        <p class="text-sm text-blue-300">
                            You have requested to join: 
                            @foreach($userRequests->where('status', 'pending') as $request)
                                <strong>{{ $request->institution->name }}</strong>@if(!$loop->last), @endif
                            @endforeach
                        </p>
                        <a href="{{ route('join-requests.my-requests') }}" class="text-sm text-blue-300 hover:text-blue-200 underline">
                            View My Requests →
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- SEARCH & FILTERS                           -->
        <!-- ========================================== -->
        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 mb-6 border border-white/20">
            <form method="GET" class="flex flex-wrap gap-3">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" placeholder="Search institutions by name or location..." 
                           value="{{ request('search') }}"
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <select name="type" class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">All Types</option>
                        <option value="university" {{ request('type') == 'university' ? 'selected' : '' }}>🏛️ University</option>
                        <option value="college" {{ request('type') == 'college' ? 'selected' : '' }}>🎓 College</option>
                        <option value="school" {{ request('type') == 'school' ? 'selected' : '' }}>🏫 School</option>
                        <option value="library" {{ request('type') == 'library' ? 'selected' : '' }}>📚 Library</option>
                        <option value="academy" {{ request('type') == 'academy' ? 'selected' : '' }}>📖 Academy</option>
                        <option value="institute" {{ request('type') == 'institute' ? 'selected' : '' }}>🏢 Institute</option>
                    </select>
                </div>
                <div>
                    <select name="sort" class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="most_members" {{ request('sort') == 'most_members' ? 'selected' : '' }}>Most Members</option>
                        <option value="most_books" {{ request('sort') == 'most_books' ? 'selected' : '' }}>Most Books</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                    </select>
                </div>
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition flex items-center gap-2">
                    <i class="ti ti-search"></i> Filter
                </button>
                <a href="{{ route('discover.institutions') }}" class="text-gray-400 hover:text-white px-3 py-2 transition flex items-center gap-1">
                    <i class="ti ti-x"></i> Clear
                </a>
            </form>
        </div>

        <!-- ========================================== -->
        <!-- INSTITUTIONS GRID                          -->
        <!-- ========================================== -->
        @if($institutions->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($institutions as $institution)
            <div class="group bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <!-- Colored top accent -->
                <div class="h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                
                <div class="p-4">
                    <!-- Badges -->
                    <div class="flex flex-wrap gap-1 mb-2">
                        @if(isset($institution->is_featured) && $institution->is_featured)
                            <span class="px-2 py-0.5 bg-gradient-to-r from-yellow-400 to-orange-400 text-white text-xs rounded-full">
                                ⭐ Featured
                            </span>
                        @endif
                        @if(isset($institution->is_verified) && $institution->is_verified)
                            <span class="px-2 py-0.5 bg-blue-500 text-white text-xs rounded-full">
                                ✅ Verified
                            </span>
                        @endif
                        @if(isset($institution->created_at) && $institution->created_at->diffInDays(now()) < 30)
                            <span class="px-2 py-0.5 bg-green-500 text-white text-xs rounded-full">
                                🆕 New
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-xl flex items-center justify-center">
                            @if(isset($institution->logo) && $institution->logo)
                                <img src="{{ asset('storage/' . $institution->logo) }}" alt="{{ $institution->name }}" class="w-8 h-8 rounded-lg object-cover">
                            @else
                                <i class="ti ti-building text-indigo-600 text-lg"></i>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">{{ Str::limit($institution->name, 25) }}</h3>
                            <span class="text-xs text-gray-500">{{ $institution->type_label ?? 'Institution' }}</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 text-xs text-gray-500 mb-2">
                        <span class="flex items-center gap-1"><i class="ti ti-users"></i> {{ $institution->users_count ?? 0 }} members</span>
                        <span class="flex items-center gap-1"><i class="ti ti-books"></i> {{ $institution->books_count ?? 0 }} books</span>
                    </div>
                    
                    <p class="text-gray-500 text-xs line-clamp-2 mb-3">
                        {{ $institution->city ?? 'Location not specified' }}
                        @if(isset($institution->region) && $institution->region)
                            , {{ $institution->region }}
                        @endif
                    </p>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <a href="{{ route('institutions.show', $institution->id) }}" 
                           class="flex-1 text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium px-3 py-2 rounded-lg transition text-sm">
                            <i class="ti ti-eye"></i> View Details
                        </a>

                        @if(isset($userRequests[$institution->id]))
                            @if($userRequests[$institution->id]->status === 'pending')
                                <span class="flex-1 text-center bg-yellow-100 text-yellow-700 font-medium px-3 py-2 rounded-lg text-sm">
                                    <i class="ti ti-clock"></i> Pending
                                </span>
                            @elseif($userRequests[$institution->id]->status === 'approved')
                                <span class="flex-1 text-center bg-green-100 text-green-700 font-medium px-3 py-2 rounded-lg text-sm">
                                    <i class="ti ti-check"></i> Member
                                </span>
                            @endif
                        @elseif(auth()->user()->institution_id == $institution->id)
                            <span class="flex-1 text-center bg-green-100 text-green-700 font-medium px-3 py-2 rounded-lg text-sm">
                                <i class="ti ti-check"></i> Your Institution
                            </span>
                        @else
                            <button onclick="openJoinModal({{ $institution->id }}, '{{ addslashes($institution->name) }}')" 
                                    class="flex-1 text-center bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-medium px-3 py-2 rounded-lg transition text-sm shadow-md hover:shadow-lg">
                                <i class="ti ti-user-plus"></i> Join
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $institutions->withQueryString()->links() }}
        </div>
        
        @else
        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-12 text-center border border-white/20">
            <i class="ti ti-building-community text-5xl text-gray-400 mb-3 block"></i>
            <h3 class="text-xl font-semibold text-white mb-2">No Institutions Available</h3>
            <p class="text-gray-300">Check back later for new institutions to join</p>
        </div>
        @endif
        
        <!-- ========================================== -->
        <!-- BOTTOM BUTTONS                             -->
        <!-- ========================================== -->
        <div class="flex justify-start gap-4 py-6">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-full transition">
                <i class="ti ti-dashboard"></i> Back to Dashboard
            </a>
            
            <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
                    class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-full transition">
                <i class="ti ti-arrow-up"></i> Back to Top
            </button>
        </div>
        
    </div>
</div>

<!-- ========================================== -->
<!-- JOIN MODAL                                 -->
<!-- ========================================== -->
<div id="joinModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl max-w-md w-full mx-4 overflow-hidden transform transition-all">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">📝 Request to Join</h3>
                <button onclick="closeJoinModal()" class="text-white/80 hover:text-white transition">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('join-requests.store') }}" class="p-6">
            @csrf
            <input type="hidden" name="institution_id" id="join_institution_id">
            
            <div class="mb-4 p-4 bg-purple-50 rounded-xl">
                <p class="text-sm text-gray-600 mb-1">You are requesting to join:</p>
                <p class="font-bold text-gray-800 text-lg" id="join_institution_name"></p>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Optional Message</label>
                <textarea name="message" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                          placeholder="Why do you want to join this institution? (Optional)"></textarea>
                <p class="text-xs text-gray-400 mt-1">This message will be sent to the institution admin</p>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-2.5 rounded-lg hover:shadow-lg transition font-semibold">
                    <i class="ti ti-send"></i> Send Request
                </button>
                <button type="button" onclick="closeJoinModal()" class="px-6 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- SCRIPTS                                    -->
<!-- ========================================== -->
<script>
function openJoinModal(institutionId, institutionName) {
    document.getElementById('join_institution_id').value = institutionId;
    document.getElementById('join_institution_name').textContent = institutionName;
    document.getElementById('joinModal').classList.remove('hidden');
    document.getElementById('joinModal').classList.add('flex');
}

function closeJoinModal() {
    document.getElementById('joinModal').classList.add('hidden');
    document.getElementById('joinModal').classList.remove('flex');
}

// Close modal on click outside
document.getElementById('joinModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeJoinModal();
    }
});
</script>

@endsection