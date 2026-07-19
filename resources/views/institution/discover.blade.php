@extends('layouts.app')

@section('title', 'Discover Institutions')
@section('page-title', 'Discover Institutions')

@section('content')

<!-- ========================================== -->
<!-- LIGHT BISQUE BACKGROUND                    -->
<!-- ========================================== -->
<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div x-data="discoverModals()" x-init="init()" class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-slate-800 flex items-center gap-3">
                        <span class="bg-gradient-to-br from-orange-500 to-amber-500 p-2 rounded-xl shadow-lg shadow-orange-500/20">
                            <i class="ti ti-building-community text-2xl text-white"></i>
                        </span>
                        Discover Institutions
                    </h1>
                    <p class="text-slate-600 mt-2 flex items-center gap-2">
                        <i class="ti ti-search text-sm"></i>
                        Find and join institutions that match your interests
                    </p>
                </div>
                <a href="{{ route('institution.create-request') }}" 
                   class="bg-gradient-to-r from-orange-600 to-amber-600 hover:shadow-lg hover:shadow-orange-600/25 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-medium">
                    <i class="ti ti-plus"></i> Create Institution
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 rounded-xl p-4 mb-6 flex items-center gap-3">
                <i class="ti ti-check-circle text-xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/20 text-red-700 rounded-xl p-4 mb-6 flex items-center gap-3">
                <i class="ti ti-alert-circle text-xl"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Search & Filter -->
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl p-4 mb-8 border border-white/60 shadow-sm">
            <form method="GET" class="flex flex-wrap gap-3">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" placeholder="Search by name, type, or location..." 
                           value="{{ request('search') }}"
                           class="w-full bg-white/80 border border-slate-200/60 rounded-xl px-4 py-3 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                </div>
                <select name="type" class="bg-white/80 border border-slate-200/60 rounded-xl px-4 py-3 text-slate-700 focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                    <option value="">All Types</option>
                    <option value="school" {{ request('type') == 'school' ? 'selected' : '' }}>School</option>
                    <option value="college" {{ request('type') == 'college' ? 'selected' : '' }}>College</option>
                    <option value="university" {{ request('type') == 'university' ? 'selected' : '' }}>University</option>
                    <option value="library" {{ request('type') == 'library' ? 'selected' : '' }}>Library</option>
                    <option value="bookstore" {{ request('type') == 'bookstore' ? 'selected' : '' }}>Bookstore</option>
                    <option value="publisher" {{ request('type') == 'publisher' ? 'selected' : '' }}>Publisher</option>
                    <option value="research_center" {{ request('type') == 'research_center' ? 'selected' : '' }}>Research Center</option>
                    <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-xl transition flex items-center gap-2">
                    <i class="ti ti-search"></i> Search
                </button>
                @if(request()->has('search') || request()->has('type'))
                    <a href="{{ route('discover.institutions') }}" class="bg-white/60 hover:bg-white/80 text-slate-700 px-6 py-3 rounded-xl transition flex items-center gap-2 border border-slate-200/60">
                        <i class="ti ti-x"></i> Clear
                    </a>
                @endif
            </form>
        </div>

        @if($institutions->count() > 0)
            <!-- Results Count -->
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-slate-600">
                    Showing <span class="text-slate-800 font-semibold">{{ $institutions->firstItem() ?? 0 }}</span> - 
                    <span class="text-slate-800 font-semibold">{{ $institutions->lastItem() ?? 0 }}</span> 
                    of <span class="text-slate-800 font-semibold">{{ $institutions->total() }}</span> institutions
                </p>
                <div class="flex items-center gap-2 text-sm text-slate-600">
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
                    <div class="group bg-white/80 backdrop-blur-sm rounded-2xl border border-white/80 hover:border-orange-300/60 transition-all duration-300 hover:shadow-xl hover:shadow-orange-500/10 overflow-hidden">
                        <!-- Top gradient bar removed -->
                        
                        <div class="p-6">
                            <!-- Institution Header -->
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-amber-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-orange-500/20">
                                    <i class="ti ti-building text-2xl text-white"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-bold text-slate-800 truncate group-hover:text-orange-600 transition">
                                        {{ $institution->name }}
                                    </h3>
                                    <p class="text-sm text-slate-600">
                                        {{ $institution->type_label ?? 'Institution' }}
                                    </p>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        @if($isMember)
                                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-700 border border-blue-500/20">
                                                <i class="ti ti-check text-[10px]"></i> Member
                                            </span>
                                        @elseif($hasPendingRequest)
                                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-yellow-500/20 text-yellow-700 border border-yellow-500/20">
                                                <i class="ti ti-clock text-[10px]"></i> Pending Request
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            @if($institution->description)
                                <p class="mt-3 text-sm text-slate-600 line-clamp-2">
                                    {{ $institution->description }}
                                </p>
                            @endif

                            <!-- Location -->
                            <div class="mt-3 flex items-center gap-2 text-sm text-slate-600">
                                <i class="ti ti-map-pin text-orange-500 text-xs"></i>
                                <span>
                                    {{ $institution->city ?? '' }}{{ $institution->city && $institution->region ? ', ' : '' }}{{ $institution->region ?? '' }}
                                    @if(!$institution->city && !$institution->region)
                                        Location not set
                                    @endif
                                </span>
                            </div>

                            <!-- Stats -->
                            <div class="mt-4 grid grid-cols-3 gap-2">
                                <div class="bg-white/60 rounded-lg p-2.5 text-center border border-slate-100/60">
                                    <p class="text-lg font-bold text-slate-700">{{ $institution->members_count ?? 0 }}</p>
                                    <p class="text-[10px] text-slate-500 uppercase tracking-wider">Members</p>
                                </div>
                                <div class="bg-white/60 rounded-lg p-2.5 text-center border border-slate-100/60">
                                    <p class="text-lg font-bold text-slate-700">{{ $institution->books_count ?? 0 }}</p>
                                    <p class="text-[10px] text-slate-500 uppercase tracking-wider">Books</p>
                                </div>
                                <div class="bg-white/60 rounded-lg p-2.5 text-center border border-slate-100/60">
                                    <p class="text-lg font-bold text-slate-700">{{ $institution->shelves_count ?? 0 }}</p>
                                    <p class="text-[10px] text-slate-500 uppercase tracking-wider">Shelves</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-5 pt-4 border-t border-slate-200/60">
                                @php
                                    $requiresApproval = in_array($institution->type, ['school', 'college', 'university']);
                                @endphp
                                
                                @if($isMember)
                                    <a href="{{ route('institution.public.index', $institution->id) }}" 
                                       class="w-full bg-gradient-to-r from-orange-600 to-amber-600 hover:shadow-lg hover:shadow-orange-600/25 text-white px-4 py-2.5 rounded-xl transition text-sm font-medium text-center flex items-center justify-center gap-2">
                                        <i class="ti ti-arrow-right"></i> Enter Institution
                                    </a>
                                @elseif($requiresApproval)
                                    @if($hasPendingRequest && $pendingRequest)
                                        <div class="flex gap-2 w-full">
                                            <div class="flex-1 bg-yellow-500/20 text-yellow-700 px-4 py-2.5 rounded-xl text-sm font-medium text-center border border-yellow-500/20 flex items-center justify-center gap-2">
                                                <i class="ti ti-clock"></i> Request Pending Approval
                                            </div>
                                            <button @click="openCancelModal({{ $pendingRequest->id }}, '{{ $institution->name }}')" 
                                                    class="bg-red-500/10 hover:bg-red-500/20 text-red-600 hover:text-red-700 px-4 py-2.5 rounded-xl transition text-sm font-medium border border-red-200/60 hover:border-red-300/70 flex items-center gap-2">
                                                <i class="ti ti-x"></i> Cancel
                                            </button>
                                        </div>
                                    @else
                                        <button @click="openJoinModal({{ $institution->id }}, '{{ $institution->name }}')" 
                                                class="w-full bg-gradient-to-r from-orange-600 to-amber-600 hover:shadow-lg hover:shadow-orange-600/25 text-white px-4 py-2.5 rounded-xl transition text-sm font-medium flex items-center justify-center gap-2">
                                            <i class="ti ti-user-plus"></i> Request to Join
                                        </button>
                                    @endif
                                @else
                                    <!-- Free Join (No approval needed) -->
                                    <form method="POST" action="{{ route('institution.join.free', $institution->id) }}" class="w-full"  x-ignore>
                                        @csrf
                                        <button type="submit" 
                                                class="w-full bg-gradient-to-r from-orange-600 to-amber-600 hover:shadow-lg hover:shadow-orange-600/25 text-white px-4 py-2.5 rounded-xl transition text-sm font-medium flex items-center justify-center gap-2">
                                            <i class="ti ti-check"></i> Join Now 
                                        </button>
                                    </form>
                                    @if($institution->description)
                                        <p class="text-xs text-slate-500 mt-2 text-center">Join now</p>
                                    @endif
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
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-16 text-center border border-white/70 shadow-sm">
                <div class="w-24 h-24 bg-gradient-to-br from-orange-500 to-amber-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl shadow-orange-500/20">
                    <i class="ti ti-building-community text-4xl text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 mb-3">No Institutions Found</h3>
                <p class="text-slate-600 max-w-md mx-auto mb-8">
                    @if(request()->has('search') || request()->has('type'))
                        No institutions match your search criteria. Try adjusting your filters.
                    @else
                        There are no institutions available to discover right now.
                    @endif
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    @if(request()->has('search') || request()->has('type'))
                        <a href="{{ route('discover.institutions') }}" 
                           class="bg-white/60 hover:bg-white/80 text-slate-700 px-8 py-3 rounded-xl transition font-semibold flex items-center gap-2 border border-slate-200/60">
                            <i class="ti ti-x"></i> Clear Filters
                        </a>
                    @endif
                    <a href="{{ route('institution.create-request') }}" 
                       class="bg-gradient-to-r from-orange-600 to-amber-600 hover:shadow-lg hover:shadow-orange-600/25 text-white px-8 py-3 rounded-xl transition font-semibold flex items-center gap-2">
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
         class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50"
         @click.away="closeJoinModal()">
        
        <div class="bg-white/95 backdrop-blur-md border border-white/60 rounded-2xl max-w-md w-full mx-4 overflow-hidden shadow-2xl" @click.stop>
            <div class="px-6 py-4 border-b border-slate-200/50 bg-orange-50/80">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        <i class="ti ti-user-plus text-orange-600"></i>
                        Request to Join <span x-text="joinInstitutionName" class="text-orange-600"></span>
                    </h3>
                    <button @click="closeJoinModal()" class="text-slate-400 hover:text-slate-600 transition">
                        <i class="ti ti-x text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <form id="joinRequestForm" method="POST" action="{{ route('join-requests.store') }}" class="p-6">
                @csrf
                <input type="hidden" name="institution_id" x-model="joinInstitutionId">
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Message (Optional)
                    </label>
                    <textarea name="message" rows="4" 
                              class="w-full bg-white/80 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 transition resize-none"
                              placeholder="Why do you want to join this institution? (Optional)"></textarea>
                    <p class="text-xs text-slate-400 mt-1">This message will be sent to the institution admins.</p>
                </div>
                
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-orange-600 to-amber-600 hover:shadow-lg hover:shadow-orange-600/25 text-white px-6 py-2.5 rounded-xl transition font-medium flex items-center justify-center gap-2">
                        <i class="ti ti-send"></i> Send Request
                    </button>
                    <button type="button" @click="closeJoinModal()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-2.5 rounded-xl transition font-medium border border-slate-200">
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
         class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50"
         @click.away="closeCancelModal()">
        
        <div class="bg-white/95 backdrop-blur-md border border-white/60 rounded-2xl max-w-md w-full mx-4 overflow-hidden shadow-2xl" @click.stop>
            <div class="px-6 py-4 border-b border-slate-200/50 bg-red-50/80">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        <i class="ti ti-alert-triangle text-red-600"></i>
                        Cancel Request
                    </h3>
                    <button @click="closeCancelModal()" class="text-slate-400 hover:text-slate-600 transition">
                        <i class="ti ti-x text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ti ti-logout text-red-500 text-3xl"></i>
                    </div>
                    <p class="text-slate-700 text-lg font-semibold">
                        Cancel request to join <span x-text="cancelInstitutionName" class="text-slate-900"></span>?
                    </p>
                    <p class="text-slate-600 text-sm mt-2">
                        This action cannot be undone. You can always send a new request later.
                    </p>
                </div>
                
                <form id="cancelRequestForm" method="POST" :action="'/join-requests/' + cancelRequestId">
                    @csrf
                    @method('DELETE')
                    <div class="flex gap-3">
                        <button type="button" @click="closeCancelModal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-2.5 rounded-xl transition font-medium border border-slate-200">
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
    
    /* Modal transitions */
    .modal-enter-active,
    .modal-leave-active {
        transition: opacity 0.3s ease;
    }
    .modal-enter-from,
    .modal-leave-to {
        opacity: 0;
    }
    
    /* Search input placeholder color */
    input::placeholder {
        color: #9ca3af;
    }
    select option {
        background: #f5f0eb;
        color: #1e293b;
    }
</style>
@endsection