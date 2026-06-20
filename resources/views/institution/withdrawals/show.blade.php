@extends('layouts.institution')

@section('title', 'Withdrawal Details')

@section('content')

@php
    // ==========================================
    // SECURITY CHECKS
    // ==========================================
    
    // Check if user belongs to an institution
    if (!auth()->user()->institution_id) {
        abort(403, 'You do not belong to any institution.');
    }
    
    // Check if institution exists
    if (!isset($institution) || !$institution) {
        abort(404, 'Institution not found.');
    }
    
    // Check if user has access to this institution
    if (auth()->user()->institution_id != $institution->id) {
        abort(403, 'You do not have access to this institution.');
    }
    
    // Check if withdrawal exists and belongs to this institution
    if (!isset($withdrawal) || !$withdrawal) {
        abort(404, 'Withdrawal not found.');
    }
    
    if ($withdrawal->institution_id != $institution->id) {
        abort(403, 'This withdrawal does not belong to your institution.');
    }
    
    // Check if user has permission to view this withdrawal
    if (!auth()->user()->can('view', $withdrawal)) {
        abort(403, 'You do not have permission to view this withdrawal.');
    }
    
    // Status configuration
    $statusColors = [
        'pending' => 'bg-yellow-100 text-yellow-700',
        'processing' => 'bg-blue-100 text-blue-700',
        'completed' => 'bg-green-100 text-green-700',
        'rejected' => 'bg-red-100 text-red-700',
        'cancelled' => 'bg-gray-100 text-gray-700'
    ];
    
    $statusIcons = [
        'pending' => '⏳',
        'processing' => '🔄',
        'completed' => '✅',
        'rejected' => '❌',
        'cancelled' => '🚫'
    ];
    
    $statusLabels = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled'
    ];
    
    $color = $statusColors[$withdrawal->status] ?? 'bg-gray-100 text-gray-700';
    $icon = $statusIcons[$withdrawal->status] ?? '';
    $label = $statusLabels[$withdrawal->status] ?? ucfirst($withdrawal->status);
@endphp

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('institution.withdrawals.index') }}" class="text-purple-600 hover:text-purple-700 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Withdrawals
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <div class="flex justify-between items-center flex-wrap gap-2">
                <div>
                    <h1 class="text-xl font-bold text-white">Withdrawal Details</h1>
                    <p class="text-purple-200 text-sm">Reference: #WD-{{ $withdrawal->id }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $color }}">
                    {{ $icon }} {{ $label }}
                </span>
            </div>
        </div>
        
        <div class="p-6">
            <!-- ========================================== -->
            <!-- WITHDRAWAL INFORMATION                     -->
            <!-- ========================================== -->
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Amount</p>
                    <p class="text-2xl font-bold text-gray-800">TSh {{ number_format($withdrawal->amount, 2) }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Requested On</p>
                    <p class="text-gray-800 font-medium">{{ $withdrawal->created_at->format('F d, Y h:i A') }}</p>
                    <p class="text-xs text-gray-400">{{ $withdrawal->created_at->diffForHumans() }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Payment Method</p>
                    <p class="text-gray-800 font-medium">{{ strtoupper($withdrawal->payment_method) }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Account Details</p>
                    <p class="text-gray-800 font-medium">{{ $withdrawal->account_details }}</p>
                </div>
            </div>
            
            <!-- ========================================== -->
            <!-- NOTES (if any)                             -->
            <!-- ========================================== -->
            @if($withdrawal->notes)
            <div class="mt-4 bg-gray-50 rounded-lg p-4">
                <p class="text-xs text-gray-400 uppercase font-semibold">Notes</p>
                <p class="text-gray-800">{{ $withdrawal->notes }}</p>
            </div>
            @endif
            
            <!-- ========================================== -->
            <!-- REJECTION REASON (if rejected)             -->
            <!-- ========================================== -->
            @if($withdrawal->status === 'rejected' && $withdrawal->rejection_reason)
            <div class="mt-4 bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                <p class="text-xs text-red-600 uppercase font-semibold">Rejection Reason</p>
                <p class="text-red-700">{{ $withdrawal->rejection_reason }}</p>
            </div>
            @endif
            
            <!-- ========================================== -->
            <!-- STATUS TIMELINE / AUDIT TRAIL              -->
            <!-- ========================================== -->
            <div class="mt-6 bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-clock text-blue-600"></i> Status Timeline
                </h3>
                
                <div class="relative">
                    <!-- Timeline Line -->
                    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                    
                    <!-- Status: Created -->
                    <div class="relative pl-12 pb-6">
                        <div class="absolute left-0 top-1 w-8 h-8 rounded-full bg-green-500 flex items-center justify-center">
                            <i class="ti ti-check text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Request Submitted</p>
                            <p class="text-sm text-gray-500">{{ $withdrawal->created_at->format('F d, Y h:i A') }}</p>
                            <p class="text-xs text-gray-400">Withdrawal request created</p>
                        </div>
                    </div>
                    
                    <!-- Status: Pending -->
                    @if(in_array($withdrawal->status, ['pending', 'processing', 'completed', 'rejected']))
                    <div class="relative pl-12 pb-6">
                        <div class="absolute left-0 top-1 w-8 h-8 rounded-full bg-yellow-500 flex items-center justify-center">
                            <i class="ti ti-clock text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Pending Review</p>
                            <p class="text-sm text-gray-500">{{ $withdrawal->created_at->format('F d, Y h:i A') }}</p>
                            <p class="text-xs text-gray-400">Awaiting admin approval</p>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Status: Processing -->
                    @if(in_array($withdrawal->status, ['processing', 'completed']))
                    <div class="relative pl-12 pb-6">
                        <div class="absolute left-0 top-1 w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center">
                            <i class="ti ti-refresh text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Processing</p>
                            <p class="text-sm text-gray-500">{{ $withdrawal->updated_at->format('F d, Y h:i A') }}</p>
                            <p class="text-xs text-gray-400">Admin is processing your request</p>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Status: Completed -->
                    @if($withdrawal->status === 'completed')
                    <div class="relative pl-12 pb-6">
                        <div class="absolute left-0 top-1 w-8 h-8 rounded-full bg-green-600 flex items-center justify-center">
                            <i class="ti ti-check-circle text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-green-700">Completed</p>
                            <p class="text-sm text-gray-500">{{ $withdrawal->updated_at->format('F d, Y h:i A') }}</p>
                            <p class="text-xs text-gray-400">Withdrawal successfully completed</p>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Status: Rejected -->
                    @if($withdrawal->status === 'rejected')
                    <div class="relative pl-12 pb-6">
                        <div class="absolute left-0 top-1 w-8 h-8 rounded-full bg-red-500 flex items-center justify-center">
                            <i class="ti ti-x text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-red-700">Rejected</p>
                            <p class="text-sm text-gray-500">{{ $withdrawal->updated_at->format('F d, Y h:i A') }}</p>
                            <p class="text-xs text-gray-400">Withdrawal request was rejected</p>
                            @if($withdrawal->rejection_reason)
                                <p class="text-xs text-red-600 mt-1">Reason: {{ $withdrawal->rejection_reason }}</p>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    <!-- Status: Cancelled -->
                    @if($withdrawal->status === 'cancelled')
                    <div class="relative pl-12 pb-6">
                        <div class="absolute left-0 top-1 w-8 h-8 rounded-full bg-gray-500 flex items-center justify-center">
                            <i class="ti ti-ban text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-700">Cancelled</p>
                            <p class="text-sm text-gray-500">{{ $withdrawal->updated_at->format('F d, Y h:i A') }}</p>
                            <p class="text-xs text-gray-400">Withdrawal request was cancelled</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- ========================================== -->
            <!-- ADMIN ACTIONS (if pending)                 -->
            <!-- ========================================== -->
            @if($withdrawal->status === 'pending' && auth()->user()->can('cancel', $withdrawal))
            <div class="mt-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                            <i class="ti ti-settings text-gray-600"></i> Actions
                        </h4>
                        <p class="text-xs text-gray-500">Manage this withdrawal request</p>
                    </div>
                    <div class="flex gap-3">
                        <form method="POST" action="{{ route('institution.withdrawals.cancel', $withdrawal) }}" 
                              onsubmit="return confirm('Are you sure you want to cancel this withdrawal request?')" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                                <i class="ti ti-x"></i> Cancel Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- ========================================== -->
            <!-- BACK BUTTON                                -->
            <!-- ========================================== -->
            <div class="mt-6 text-center">
                <a href="{{ route('institution.withdrawals.index') }}" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg transition">
                    <i class="ti ti-arrow-left"></i> Back to Withdrawals
                </a>
            </div>
        </div>
    </div>
</div>

@endsection