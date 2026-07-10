@extends('layouts.app')

@section('title', 'My Institutions')
@section('page-title', 'My Institutions')

@section('content')

<!-- ========================================== -->
<!-- LIGHT BISQUE BACKGROUND                    -->
<!-- ========================================== -->
<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div x-data="{ 
    institutions: {{ $institutions->toJson() }},
    showLeaveModal: false,
    leaveInstitutionId: null,
    leaveInstitutionName: '',
    isLeaving: false,
    
    async leaveInstitution() {
        this.isLeaving = true;
        
        try {
            const response = await fetch(`/institution/leave/${this.leaveInstitutionId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.institutions = this.institutions.filter(inst => inst.id !== this.leaveInstitutionId);
                this.showLeaveModal = false;
                this.showToast('success', data.message);
            } else {
                this.showToast('error', data.message || 'Failed to leave institution.');
            }
        } catch (error) {
            this.showToast('error', 'An error occurred. Please try again.');
        } finally {
            this.isLeaving = false;
            this.leaveInstitutionId = null;
            this.leaveInstitutionName = '';
        }
    },
    
    showToast(type, message) {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        const toastIcon = document.getElementById('toast-icon');
        
        toastMessage.textContent = message;
        
        if (type === 'success') {
            toastIcon.className = 'ti ti-check-circle text-emerald-500 text-xl';
            toast.className = 'fixed bottom-6 right-6 bg-white/90 backdrop-blur-sm border border-emerald-200/50 rounded-xl px-6 py-4 shadow-2xl flex items-center gap-3 z-50 transition-all duration-500 opacity-0 translate-y-4';
        } else {
            toastIcon.className = 'ti ti-alert-circle text-red-500 text-xl';
            toast.className = 'fixed bottom-6 right-6 bg-white/90 backdrop-blur-sm border border-red-200/50 rounded-xl px-6 py-4 shadow-2xl flex items-center gap-3 z-50 transition-all duration-500 opacity-0 translate-y-4';
        }
        
        setTimeout(() => {
            toast.classList.remove('opacity-0', 'translate-y-4');
            toast.classList.add('opacity-100', 'translate-y-0');
        }, 10);
        
        setTimeout(() => {
            toast.classList.remove('opacity-100', 'translate-y-0');
            toast.classList.add('opacity-0', 'translate-y-4');
        }, 4000);
    }
}" 
x-init="console.log('Institutions loaded:', institutions)">

<div style="position: relative; z-index: 10; min-height: 100vh; padding: 2rem 0;">
    <div class="container mx-auto px-4 max-w-6xl">
        
        <!-- Toast Notification -->
        <div id="toast" class="fixed bottom-6 right-6 bg-white/90 backdrop-blur-sm border border-white/30 rounded-xl px-6 py-4 shadow-2xl flex items-center gap-3 z-50 transition-all duration-500 opacity-0 translate-y-4 pointer-events-none">
            <i id="toast-icon" class="ti ti-check-circle text-emerald-500 text-xl"></i>
            <span id="toast-message" class="text-slate-800 text-sm font-medium"></span>
        </div>

        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-slate-800 flex items-center gap-3">
                        <span class="bg-gradient-to-br from-orange-500 to-amber-500 p-2 rounded-xl shadow-lg shadow-orange-500/20">
                            <i class="ti ti-building-community text-2xl text-white"></i>
                        </span>
                        My Institutions
                    </h1>
                    <p class="text-slate-600 mt-2 flex items-center gap-2">
                        <i class="ti ti-users text-sm"></i>
                        You are a member of <span class="text-slate-800 font-semibold" x-text="institutions.length"></span> institution(s)
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('discover.institutions') }}" 
                       class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-medium shadow-lg shadow-orange-600/20">
                        <i class="ti ti-building-community"></i> Discover More
                    </a>
                    <a href="{{ route('institution.create-request') }}" 
                       class="bg-white/70 hover:bg-white/90 text-slate-700 px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-medium border border-slate-200/50 backdrop-blur-sm">
                        <i class="ti ti-plus"></i> Create Institution
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 border border-white/60 shadow-sm">
                <p class="text-2xl font-bold text-orange-600" x-text="institutions.length"></p>
                <p class="text-xs text-slate-600">Total Institutions</p>
            </div>
            <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 border border-white/60 shadow-sm">
                <p class="text-2xl font-bold text-amber-600" x-text="institutions.reduce((sum, inst) => sum + (inst.members_count || 0), 0)"></p>
                <p class="text-xs text-slate-600">Total Members</p>
            </div>
            <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 border border-white/60 shadow-sm">
                <p class="text-2xl font-bold text-orange-600" x-text="institutions.reduce((sum, inst) => sum + (inst.books_count || 0), 0)"></p>
                <p class="text-xs text-slate-600">Total Books</p>
            </div>
        </div>

        <!-- Institutions Grid -->
        <template x-if="institutions.length > 0">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="institution in institutions" :key="institution.id">
                    <div class="group bg-white/80 backdrop-blur-sm rounded-2xl border border-white/80 hover:border-orange-300/60 transition-all duration-300 hover:shadow-xl hover:shadow-orange-500/10 overflow-hidden">
                        <!-- Top gradient bar removed -->
                        
                        <div class="p-6">
                            <!-- Institution Header -->
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-amber-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-orange-500/20">
                                    <i class="ti ti-building text-2xl text-white"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-bold text-slate-800 truncate group-hover:text-orange-600 transition" x-text="institution.name"></h3>
                                    <p class="text-sm text-slate-600" x-text="institution.type_label || 'Institution'"></p>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <template x-if="institution.pivot">
                                            <span class="text-xs px-2.5 py-0.5 rounded-full" 
                                                  :class="institution.pivot.role === 'admin' || institution.pivot.role === 'institution_admin' 
                                                      ? 'bg-orange-500/20 text-orange-700 border border-orange-500/20' 
                                                      : 'bg-blue-500/20 text-blue-700 border border-blue-500/20'">
                                                <i class="ti ti-shield text-[10px]"></i>
                                                <span x-text="institution.pivot.role.charAt(0).toUpperCase() + institution.pivot.role.slice(1)"></span>
                                            </span>
                                        </template>
                                        <template x-if="institution.pivot && institution.pivot.status === 'active'">
                                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-700 border border-emerald-500/20">
                                                <i class="ti ti-check text-[10px]"></i> Active
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Location -->
                            <template x-if="institution.city || institution.region">
                                <div class="mt-3 flex items-center gap-2 text-sm text-slate-600">
                                    <i class="ti ti-map-pin text-orange-500 text-xs"></i>
                                    <span x-text="institution.city + (institution.city && institution.region ? ', ' : '') + (institution.region || '')"></span>
                                </div>
                            </template>

                            <!-- Stats -->
                            <div class="mt-4 grid grid-cols-3 gap-2">
                                <div class="bg-white/60 rounded-lg p-2.5 text-center border border-slate-100/60">
                                    <p class="text-lg font-bold text-slate-700" x-text="institution.members_count || 0"></p>
                                    <p class="text-[10px] text-slate-500 uppercase tracking-wider">Members</p>
                                </div>
                                <div class="bg-white/60 rounded-lg p-2.5 text-center border border-slate-100/60">
                                    <p class="text-lg font-bold text-slate-700" x-text="institution.books_count || 0"></p>
                                    <p class="text-[10px] text-slate-500 uppercase tracking-wider">Books</p>
                                </div>
                                <div class="bg-white/60 rounded-lg p-2.5 text-center border border-slate-100/60">
                                    <p class="text-lg font-bold text-slate-700" x-text="institution.shelves_count || 0"></p>
                                    <p class="text-[10px] text-slate-500 uppercase tracking-wider">Shelves</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-5 pt-4 border-t border-slate-200/60 flex flex-wrap gap-2">
                                <a :href="`/institution/${institution.id}/library`" 
                                   class="flex-1 bg-gradient-to-r from-orange-600 to-amber-600 hover:shadow-lg hover:shadow-orange-600/25 text-white px-4 py-2.5 rounded-xl transition text-sm font-medium text-center">
                                    <i class="ti ti-arrow-right"></i> Enter
                                </a>
                                
                                <!-- Leave Button -->
                                <button @click="leaveInstitutionId = institution.id; leaveInstitutionName = institution.name; showLeaveModal = true" 
                                        class="bg-red-500/10 hover:bg-red-500/20 text-red-600 hover:text-red-700 px-4 py-2.5 rounded-xl transition text-sm font-medium text-center border border-red-200/60 hover:border-red-300/70 flex items-center gap-2">
                                    <i class="ti ti-logout"></i> Leave
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <!-- Empty State -->
        <template x-if="institutions.length === 0">
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-16 text-center border border-white/70 shadow-sm">
                <div class="w-24 h-24 bg-gradient-to-br from-orange-500 to-amber-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl shadow-orange-500/20">
                    <i class="ti ti-building-community text-4xl text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 mb-3">No Institutions Yet</h3>
                <p class="text-slate-600 max-w-md mx-auto mb-8">
                    You haven't joined any institution. Discover and join one to access exclusive resources and connect with your community.
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="{{ route('discover.institutions') }}" 
                       class="bg-gradient-to-r from-orange-600 to-amber-600 hover:shadow-lg hover:shadow-orange-600/25 text-white px-8 py-3 rounded-xl transition font-semibold flex items-center gap-2">
                        <i class="ti ti-building-community"></i> Discover Institutions
                    </a>
                    <a href="{{ route('institution.create-request') }}" 
                       class="bg-white/80 hover:bg-white/95 text-slate-700 px-8 py-3 rounded-xl transition font-semibold flex items-center gap-2 border border-slate-200/60 backdrop-blur-sm">
                        <i class="ti ti-plus"></i> Create Institution
                    </a>
                </div>
            </div>
        </template>
    </div>
</div>

<!-- ========================================== -->
<!-- LEAVE CONFIRMATION MODAL                   -->
<!-- ========================================== -->
<div x-show="showLeaveModal" 
     x-cloak
     class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50"
     @click.away="showLeaveModal = false">
    
    <div class="bg-white/95 backdrop-blur-md border border-white/60 rounded-2xl max-w-md w-full mx-4 overflow-hidden shadow-2xl" 
         @click.stop>
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-200/50 bg-red-50/80">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <i class="ti ti-logout text-red-600"></i>
                    Leave Institution
                </h3>
                <button @click="showLeaveModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ti ti-alert-triangle text-red-500 text-3xl"></i>
                </div>
                <p class="text-slate-700 text-lg font-semibold">
                    Leave <span class="text-slate-900" x-text="leaveInstitutionName"></span>?
                </p>
                <p class="text-slate-600 text-sm mt-2">
                    You will lose access to all resources and content of this institution. This action cannot be undone.
                </p>
                
                <div class="mt-4 p-3 bg-amber-50/80 border border-amber-200/60 rounded-lg">
                    <p class="text-amber-700 text-xs flex items-center gap-2">
                        <i class="ti ti-info-circle"></i>
                        You can always rejoin later if the institution allows.
                    </p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button @click="showLeaveModal = false" 
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-2.5 rounded-xl transition font-medium border border-slate-200">
                    Cancel
                </button>
                <button @click="leaveInstitution()" 
                        :disabled="isLeaving"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-xl transition font-medium flex items-center justify-center gap-2">
                    <i class="ti ti-logout" :class="{'animate-spin': isLeaving}"></i>
                    <span x-text="isLeaving ? 'Leaving...' : 'Yes, Leave'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    /* Optional: smooth transitions for modal */
    .modal-enter-active,
    .modal-leave-active {
        transition: opacity 0.3s ease;
    }
    .modal-enter-from,
    .modal-leave-to {
        opacity: 0;
    }
</style>

@endsection