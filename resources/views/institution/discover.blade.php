@extends('layouts.app')

@section('title', 'Discover Institutions')
@section('page-title', '🌍 Discover Institutions')

@section('content')
<div x-data="discoverModals()" x-init="init()" class="relative z-10 min-h-screen py-8">
    <div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>
    
    <div class="container mx-auto px-4 max-w-7xl">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white flex items-center gap-3">
                        <span class="bg-gradient-to-br from-cyan-500 to-blue-500 p-2 rounded-xl">
                            <i class="ti ti-building-community text-2xl"></i>
                        </span>
                        Discover Institutions
                    </h1>
                    <p class="text-gray-400 mt-2 flex items-center gap-2">
                        <i class="ti ti-search text-sm"></i>
                        Find and join institutions that match your interests
                    </p>
                </div>
                <a href="{{ route('institution.create-request') }}" 
                   class="bg-gradient-to-r from-purple-600 to-pink-600 hover:shadow-lg hover:shadow-purple-600/25 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-medium">
                    <i class="ti ti-plus"></i> Create Institution
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl p-4 mb-6 flex items-center gap-3">
                <i class="ti ti-check-circle text-xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl p-4 mb-6 flex items-center gap-3">
                <i class="ti ti-alert-circle text-xl"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Search & Filter -->
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-4 mb-8 border border-white/10">
            <form method="GET" class="flex flex-wrap gap-3">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" placeholder="Search by name, type, or location..." 
                           value="{{ request('search') }}"
                           class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                </div>
                <select name="type" class="bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                    <option value="">All Types</option>
                    <option value="school" {{ request('type') == 'school' ? 'selected' : '' }}>🏫 School</option>
                    <option value="college" {{ request('type') == 'college' ? 'selected' : '' }}>🎓 College</option>
                    <option value="university" {{ request('type') == 'university' ? 'selected' : '' }}>🏛️ University</option>
                    <option value="library" {{ request('type') == 'library' ? 'selected' : '' }}>📚 Library</option>
                    <option value="bookstore" {{ request('type') == 'bookstore' ? 'selected' : '' }}>📖 Bookstore</option>
                    <option value="publisher" {{ request('type') == 'publisher' ? 'selected' : '' }}>📰 Publisher</option>
                    <option value="research_center" {{ request('type') == 'research_center' ? 'selected' : '' }}>🔬 Research Center</option>
                    <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>🏢 Other</option>
                </select>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-xl transition flex items-center gap-2">
                    <i class="ti ti-search"></i> Search
                </button>
                @if(request()->has('search') || request()->has('type'))
                    <a href="{{ route('discover.institutions') }}" class="bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-xl transition flex items-center gap-2">
                        <i class="ti ti-x"></i> Clear
                    </a>
                @endif
            </form>
        </div>

        @if($institutions->count() > 0)
            <!-- Results Count -->
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-gray-400">
                    Showing <span class="text-white font-semibold">{{ $institutions->firstItem() ?? 0 }}</span> - 
                    <span class="text-white font-semibold">{{ $institutions->lastItem() ?? 0 }}</span> 
                    of <span class="text-white font-semibold">{{ $institutions->total() }}</span> institutions
                </p>
                <div class="flex items-center gap-2 text-sm text-gray-400">
                    <i class="ti ti-layout-grid"></i>
                    <span>{{ $institutions->count() }} results</span>
                </div>
            </div>

            <!-- Institutions Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($institutions as $institution)
                    @php
                        $isMember = auth()->user()->isMemberOf($institution->id);
                        $hasPendingRequest = isset($userRequests[$institution->id]) && $userRequests[$institution->id]->status === 'pending';
                        $pendingRequest = $hasPendingRequest ? $userRequests[$institution->id] : null;
                    @endphp
                    <div class="group bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-sm rounded-2xl border border-white/10 hover:border-purple-500/40 transition-all duration-300 hover:shadow-2xl hover:shadow-purple-500/10 overflow-hidden">
                        <!-- Top gradient bar -->
                        <div class="h-1.5 w-full bg-gradient-to-r from-cyan-500 via-blue-500 to-purple-500"></div>
                        
                        <div class="p-6">
                            <!-- Institution Header -->
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-cyan-500/20">
                                    <i class="ti ti-building text-2xl text-white"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-bold text-white truncate group-hover:text-cyan-300 transition">
                                        {{ $institution->name }}
                                    </h3>
                                    <p class="text-sm text-gray-400">
                                        {{ $institution->type_label ?? 'Institution' }}
                                    </p>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        @if($isMember)
                                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/20">
                                                <i class="ti ti-check text-[10px]"></i> Member
                                            </span>
                                        @elseif($hasPendingRequest)
                                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-yellow-500/20 text-yellow-300 border border-yellow-500/20">
                                                <i class="ti ti-clock text-[10px]"></i> Pending Request
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            @if($institution->description)
                                <p class="mt-3 text-sm text-gray-300 line-clamp-2">
                                    {{ $institution->description }}
                                </p>
                            @endif

                            <!-- Location -->
                            <div class="mt-3 flex items-center gap-2 text-sm text-gray-400">
                                <i class="ti ti-map-pin text-cyan-400 text-xs"></i>
                                <span>
                                    {{ $institution->city ?? '' }}{{ $institution->city && $institution->region ? ', ' : '' }}{{ $institution->region ?? '' }}
                                    @if(!$institution->city && !$institution->region)
                                        Location not set
                                    @endif
                                </span>
                            </div>

                            <!-- Stats -->
                            <div class="mt-4 grid grid-cols-3 gap-2">
                                <div class="bg-white/5 rounded-lg p-2.5 text-center">
                                    <p class="text-lg font-bold text-white">{{ $institution->members_count ?? 0 }}</p>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">Members</p>
                                </div>
                                <div class="bg-white/5 rounded-lg p-2.5 text-center">
                                    <p class="text-lg font-bold text-white">{{ $institution->books_count ?? 0 }}</p>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">Books</p>
                                </div>
                                <div class="bg-white/5 rounded-lg p-2.5 text-center">
                                    <p class="text-lg font-bold text-white">{{ $institution->shelves_count ?? 0 }}</p>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">Shelves</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-5 pt-4 border-t border-white/10">
                                @if($isMember)
                                    <a href="{{ route('institution.public.index', $institution->id) }}" 
                                       class="w-full bg-gradient-to-r from-emerald-600 to-emerald-500 hover:shadow-lg hover:shadow-emerald-600/25 text-white px-4 py-2.5 rounded-xl transition text-sm font-medium text-center flex items-center justify-center gap-2">
                                        <i class="ti ti-arrow-right"></i> Enter Institution
                                    </a>
                                @elseif($hasPendingRequest && $pendingRequest)
                                    <div class="flex gap-2 w-full">
                                        <div class="flex-1 bg-yellow-500/20 text-yellow-300 px-4 py-2.5 rounded-xl text-sm font-medium text-center border border-yellow-500/20 flex items-center justify-center gap-2">
                                            <i class="ti ti-clock"></i> Request Pending Approval
                                        </div>
                                        <button @click="openCancelModal({{ $pendingRequest->id }}, '{{ $institution->name }}')" 
                                                class="bg-red-500/20 hover:bg-red-500/30 text-red-400 hover:text-red-300 px-4 py-2.5 rounded-xl transition text-sm font-medium border border-red-500/20 hover:border-red-500/30 flex items-center gap-2">
                                            <i class="ti ti-x"></i> Cancel
                                        </button>
                                    </div>
                                @else
                                    <button @click="openJoinModal({{ $institution->id }}, '{{ $institution->name }}')" 
                                            class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:shadow-lg hover:shadow-purple-600/25 text-white px-4 py-2.5 rounded-xl transition text-sm font-medium flex items-center justify-center gap-2">
                                        <i class="ti ti-user-plus"></i> Request to Join
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $institutions->appends(request()->query())->links() }}
            </div>

        @else
            <!-- Empty State -->
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-16 text-center border border-white/10">
                <div class="w-24 h-24 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl shadow-cyan-500/20">
                    <i class="ti ti-building-community text-4xl text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3">No Institutions Found</h3>
                <p class="text-gray-400 max-w-md mx-auto mb-8">
                    @if(request()->has('search') || request()->has('type'))
                        No institutions match your search criteria. Try adjusting your filters.
                    @else
                        There are no institutions available to discover right now.
                    @endif
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    @if(request()->has('search') || request()->has('type'))
                        <a href="{{ route('discover.institutions') }}" 
                           class="bg-white/10 hover:bg-white/20 text-white px-8 py-3 rounded-xl transition font-semibold flex items-center gap-2 border border-white/10">
                            <i class="ti ti-x"></i> Clear Filters
                        </a>
                    @endif
                    <a href="{{ route('institution.create-request') }}" 
                       class="bg-gradient-to-r from-purple-600 to-pink-600 hover:shadow-lg hover:shadow-purple-600/25 text-white px-8 py-3 rounded-xl transition font-semibold flex items-center gap-2">
                        <i class="ti ti-plus"></i> Create Your Own
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- JOIN REQUEST MODAL                         -->
    <!-- ========================================== -->
    <div x-show="joinModal" 
         x-cloak
         class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50"
         @click.away="closeJoinModal()">
        
        <div class="bg-slate-900 border border-white/10 rounded-2xl max-w-md w-full mx-4 overflow-hidden shadow-2xl" @click.stop>
            <div class="bg-gradient-to-r from-purple-900/30 to-pink-900/30 px-6 py-4 border-b border-white/10">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="ti ti-user-plus text-purple-400"></i>
                        Request to Join <span x-text="joinInstitutionName" class="text-purple-300"></span>
                    </h3>
                    <button @click="closeJoinModal()" class="text-slate-400 hover:text-white transition">
                        <i class="ti ti-x text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <form id="joinRequestForm" method="POST" action="{{ route('join-requests.store') }}" class="p-6">
                @csrf
                <input type="hidden" name="institution_id" x-model="joinInstitutionId">
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Message (Optional)
                    </label>
                    <textarea name="message" rows="4" 
                              class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 transition resize-none"
                              placeholder="Why do you want to join this institution? (Optional)"></textarea>
                    <p class="text-xs text-gray-400 mt-1">This message will be sent to the institution admins.</p>
                </div>
                
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 hover:shadow-lg hover:shadow-purple-600/25 text-white px-6 py-2.5 rounded-xl transition font-medium flex items-center justify-center gap-2">
                        <i class="ti ti-send"></i> Send Request
                    </button>
                    <button type="button" @click="closeJoinModal()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-6 py-2.5 rounded-xl transition font-medium border border-white/5">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- CANCEL REQUEST MODAL                       -->
    <!-- ========================================== -->
    <div x-show="cancelModal" 
         x-cloak
         class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50"
         @click.away="closeCancelModal()">
        
        <div class="bg-slate-900 border border-white/10 rounded-2xl max-w-md w-full mx-4 overflow-hidden shadow-2xl" @click.stop>
            <div class="bg-gradient-to-r from-red-900/30 to-red-800/30 px-6 py-4 border-b border-white/10">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="ti ti-alert-triangle text-red-400"></i>
                        Cancel Request
                    </h3>
                    <button @click="closeCancelModal()" class="text-slate-400 hover:text-white transition">
                        <i class="ti ti-x text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ti ti-logout text-red-400 text-3xl"></i>
                    </div>
                    <p class="text-slate-300 text-lg font-semibold">
                        Cancel request to join <span x-text="cancelInstitutionName" class="text-white"></span>?
                    </p>
                    <p class="text-slate-400 text-sm mt-2">
                        This action cannot be undone. You can always send a new request later.
                    </p>
                </div>
                
                <form id="cancelRequestForm" method="POST" :action="'/join-requests/' + cancelRequestId">
                    @csrf
                    @method('DELETE')
                    <div class="flex gap-3">
                        <button type="button" @click="closeCancelModal()" class="flex-1 bg-slate-800 hover:bg-slate-700 text-slate-300 px-6 py-2.5 rounded-xl transition font-medium border border-white/5">
                            Keep Request
                        </button>
                        <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-xl transition font-medium flex items-center justify-center gap-2">
                            <i class="ti ti-x"></i> Yes, Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function discoverModals() {
    return {
        joinModal: false,
        cancelModal: false,
        joinInstitutionId: null,
        joinInstitutionName: '',
        cancelRequestId: null,
        cancelInstitutionName: '',
        isSubmitting: false,
        
        init() {
            // Close modals on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.joinModal = false;
                    this.cancelModal = false;
                    document.body.style.overflow = '';
                }
            });
        },
        
        openJoinModal(id, name) {
            this.joinInstitutionId = id;
            this.joinInstitutionName = name;
            this.joinModal = true;
            document.body.style.overflow = 'hidden';
        },
        
        closeJoinModal() {
            this.joinModal = false;
            document.body.style.overflow = '';
            document.getElementById('joinRequestForm')?.reset();
        },
        
        openCancelModal(id, name) {
            this.cancelRequestId = id;
            this.cancelInstitutionName = name;
            this.cancelModal = true;
            document.body.style.overflow = 'hidden';
        },
        
        closeCancelModal() {
            this.cancelModal = false;
            document.body.style.overflow = '';
        }
    }
}
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection