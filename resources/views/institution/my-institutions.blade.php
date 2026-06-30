@extends('layouts.app')

@section('title', 'My Institutions')
@section('page-title', '🏛️ My Institutions')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

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
                // ✅ Remove institution from the list instantly
                this.institutions = this.institutions.filter(inst => inst.id !== this.leaveInstitutionId);
                this.showLeaveModal = false;
                
                // Show success toast
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
            toastIcon.className = 'ti ti-check-circle text-emerald-400 text-xl';
            toast.className = 'fixed bottom-6 right-6 bg-slate-900 border border-emerald-500/30 rounded-xl px-6 py-4 shadow-2xl flex items-center gap-3 z-50 transition-all duration-500 opacity-0 translate-y-4';
        } else {
            toastIcon.className = 'ti ti-alert-circle text-red-400 text-xl';
            toast.className = 'fixed bottom-6 right-6 bg-slate-900 border border-red-500/30 rounded-xl px-6 py-4 shadow-2xl flex items-center gap-3 z-50 transition-all duration-500 opacity-0 translate-y-4';
        }
        
        // Show toast
        setTimeout(() => {
            toast.classList.remove('opacity-0', 'translate-y-4');
            toast.classList.add('opacity-100', 'translate-y-0');
        }, 10);
        
        // Hide after 4 seconds
        setTimeout(() => {
            toast.classList.remove('opacity-100', 'translate-y-0');
            toast.classList.add('opacity-0', 'translate-y-4');
        }, 4000);
    }
}" 
x-init="console.log('Institutions loaded:', institutions)">

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        
        <!-- Toast Notification -->
        <div id="toast" class="fixed bottom-6 right-6 bg-slate-900 border border-white/10 rounded-xl px-6 py-4 shadow-2xl flex items-center gap-3 z-50 transition-all duration-500 opacity-0 translate-y-4 pointer-events-none">
            <i id="toast-icon" class="ti ti-check-circle text-emerald-400 text-xl"></i>
            <span id="toast-message" class="text-white text-sm font-medium"></span>
        </div>

        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white flex items-center gap-3">
                        <span class="bg-gradient-to-br from-purple-500 to-pink-500 p-2 rounded-xl">
                            <i class="ti ti-building-community text-2xl"></i>
                        </span>
                        My Institutions
                    </h1>
                    <p class="text-gray-400 mt-2 flex items-center gap-2">
                        <i class="ti ti-users text-sm"></i>
                        You are a member of <span class="text-white font-semibold" x-text="institutions.length"></span> institution(s)
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('discover.institutions') }}" 
                       class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-medium shadow-lg shadow-purple-600/20">
                        <i class="ti ti-building-community"></i> Discover More
                    </a>
                    <a href="{{ route('institution.create-request') }}" 
                       class="bg-white/10 hover:bg-white/20 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-medium border border-white/10">
                        <i class="ti ti-plus"></i> Create Institution
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/5">
                <p class="text-2xl font-bold text-purple-400" x-text="institutions.length"></p>
                <p class="text-xs text-gray-400">Total Institutions</p>
            </div>
            <div class="bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/5">
                <p class="text-2xl font-bold text-blue-400" x-text="institutions.reduce((sum, inst) => sum + (inst.members_count || 0), 0)"></p>
                <p class="text-xs text-gray-400">Total Members</p>
            </div>
            <div class="bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/5">
                <p class="text-2xl font-bold text-amber-400" x-text="institutions.reduce((sum, inst) => sum + (inst.books_count || 0), 0)"></p>
                <p class="text-xs text-gray-400">Total Books</p>
            </div>
        </div>

        <!-- Institutions Grid -->
        <template x-if="institutions.length > 0">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="institution in institutions" :key="institution.id">
                    <div class="group bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-sm rounded-2xl border border-white/10 hover:border-purple-500/40 transition-all duration-300 hover:shadow-2xl hover:shadow-purple-500/10 overflow-hidden">
                        <!-- Top gradient bar -->
                        <div class="h-1.5 w-full bg-gradient-to-r from-purple-500 via-pink-500 to-purple-500"></div>
                        
                        <div class="p-6">
                            <!-- Institution Header -->
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-purple-500/20">
                                    <i class="ti ti-building text-2xl text-white"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-bold text-white truncate group-hover:text-purple-300 transition" x-text="institution.name"></h3>
                                    <p class="text-sm text-gray-400" x-text="institution.type_label || 'Institution'"></p>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <template x-if="institution.pivot">
                                            <span class="text-xs px-2.5 py-0.5 rounded-full" 
                                                  :class="institution.pivot.role === 'admin' || institution.pivot.role === 'institution_admin' 
                                                      ? 'bg-purple-500/20 text-purple-300 border border-purple-500/20' 
                                                      : 'bg-blue-500/20 text-blue-300 border border-blue-500/20'">
                                                <i class="ti ti-shield text-[10px]"></i>
                                                <span x-text="institution.pivot.role.charAt(0).toUpperCase() + institution.pivot.role.slice(1)"></span>
                                            </span>
                                        </template>
                                        <template x-if="institution.pivot && institution.pivot.status === 'active'">
                                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/20">
                                                <i class="ti ti-check text-[10px]"></i> Active
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Location -->
                            <template x-if="institution.city || institution.region">
                                <div class="mt-3 flex items-center gap-2 text-sm text-gray-400">
                                    <i class="ti ti-map-pin text-purple-400 text-xs"></i>
                                    <span x-text="institution.city + (institution.city && institution.region ? ', ' : '') + (institution.region || '')"></span>
                                </div>
                            </template>

                            <!-- Stats -->
                            <div class="mt-4 grid grid-cols-3 gap-2">
                                <div class="bg-white/5 rounded-lg p-2.5 text-center">
                                    <p class="text-lg font-bold text-white" x-text="institution.members_count || 0"></p>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">Members</p>
                                </div>
                                <div class="bg-white/5 rounded-lg p-2.5 text-center">
                                    <p class="text-lg font-bold text-white" x-text="institution.books_count || 0"></p>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">Books</p>
                                </div>
                                <div class="bg-white/5 rounded-lg p-2.5 text-center">
                                    <p class="text-lg font-bold text-white" x-text="institution.shelves_count || 0"></p>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">Shelves</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-5 pt-4 border-t border-white/10 flex flex-wrap gap-2">
                                <a :href="`/institution/${institution.id}/library`" 
                                   class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 hover:shadow-lg hover:shadow-purple-600/25 text-white px-4 py-2.5 rounded-xl transition text-sm font-medium text-center">
                                    <i class="ti ti-arrow-right"></i> Enter
                                </a>
                                
                                <!-- Leave Button -->
                                <button @click="leaveInstitutionId = institution.id; leaveInstitutionName = institution.name; showLeaveModal = true" 
                                        class="bg-red-500/20 hover:bg-red-500/30 text-red-400 hover:text-red-300 px-4 py-2.5 rounded-xl transition text-sm font-medium text-center border border-red-500/20 hover:border-red-500/30 flex items-center gap-2">
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
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-16 text-center border border-white/10">
                <div class="w-24 h-24 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl shadow-purple-500/20">
                    <i class="ti ti-building-community text-4xl text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3">No Institutions Yet</h3>
                <p class="text-gray-400 max-w-md mx-auto mb-8">
                    You haven't joined any institution. Discover and join one to access exclusive resources and connect with your community.
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="{{ route('discover.institutions') }}" 
                       class="bg-gradient-to-r from-purple-600 to-pink-600 hover:shadow-lg hover:shadow-purple-600/25 text-white px-8 py-3 rounded-xl transition font-semibold flex items-center gap-2">
                        <i class="ti ti-building-community"></i> Discover Institutions
                    </a>
                    <a href="{{ route('institution.create-request') }}" 
                       class="bg-white/10 hover:bg-white/20 text-white px-8 py-3 rounded-xl transition font-semibold flex items-center gap-2 border border-white/10">
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
     class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50"
     @click.away="showLeaveModal = false">
    
    <div class="bg-slate-900 border border-white/10 rounded-2xl max-w-md w-full mx-4 overflow-hidden shadow-2xl" 
         @click.stop>
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-red-900/30 to-red-800/30 px-6 py-4 border-b border-white/10">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="ti ti-logout text-red-400"></i>
                    Leave Institution
                </h3>
                <button @click="showLeaveModal = false" class="text-slate-400 hover:text-white transition">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ti ti-alert-triangle text-red-400 text-3xl"></i>
                </div>
                <p class="text-slate-300 text-lg font-semibold">
                    Leave <span class="text-white" x-text="leaveInstitutionName"></span>?
                </p>
                <p class="text-slate-400 text-sm mt-2">
                    You will lose access to all resources and content of this institution. This action cannot be undone.
                </p>
                
                <div class="mt-4 p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
                    <p class="text-yellow-400 text-xs flex items-center gap-2">
                        <i class="ti ti-info-circle"></i>
                        You can always rejoin later if the institution allows.
                    </p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button @click="showLeaveModal = false" 
                        class="flex-1 bg-slate-800 hover:bg-slate-700 text-slate-300 px-6 py-2.5 rounded-xl transition font-medium border border-white/5">
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
</style>
@endsection