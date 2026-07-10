@extends('layouts.super-admin')

@section('title', 'Subscription Details - ' . $institution->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('super-admin.institutions.subscriptions.index') }}" class="text-purple-600 hover:text-purple-700 transition inline-flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Back to Institutions
    </a>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <!-- ========================================== -->
    <!-- INSTITUTION INFO                          -->
    <!-- ========================================== -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="ti ti-building"></i> {{ $institution->name }}
                    </h2>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold 
                        @if($institution->status === 'approved') bg-emerald-500/20 text-emerald-200
                        @elseif($institution->status === 'pending') bg-yellow-500/20 text-yellow-200
                        @elseif($institution->status === 'suspended') bg-red-500/20 text-red-200
                        @else bg-gray-500/20 text-gray-200 @endif">
                        {{ ucfirst($institution->status) }}
                    </span>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Email</p>
                        <p class="text-gray-800">{{ $institution->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Type</p>
                        <p class="text-gray-800">{{ $institution->type_label ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total Users</p>
                        <p class="text-gray-800 font-semibold">{{ $institution->users()->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total Books</p>
                        <p class="text-gray-800 font-semibold">{{ $institution->books()->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Wallet Balance</p>
                        <p class="text-gray-800 font-semibold">TSh {{ number_format($institution->wallet->balance ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">City</p>
                        <p class="text-gray-800">{{ $institution->city ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ========================================== -->
        <!-- SUBSCRIPTION HISTORY                      -->
        <!-- ========================================== -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="ti ti-history text-purple-600"></i> Subscription History
                </h3>
            </div>
            <div class="p-4">
                @if($allSubscriptions->count() > 0)
                    <div class="space-y-2">
                        @foreach($allSubscriptions as $sub)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-gray-800 font-medium capitalize">{{ $sub->plan }}</p>
                                <p class="text-xs text-gray-500">TSh {{ number_format($sub->amount, 2) }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                    @if($sub->status === 'active') bg-green-100 text-green-700
                                    @elseif($sub->status === 'pending') bg-yellow-100 text-yellow-700
                                    @elseif($sub->status === 'expired') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ ucfirst($sub->status) }}
                                </span>
                                <p class="text-xs text-gray-400 mt-1">{{ $sub->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $allSubscriptions->links() }}
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No subscription history</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- ========================================== -->
    <!-- CURRENT SUBSCRIPTION & ACTIONS            -->
    <!-- ========================================== -->
    <div class="lg:col-span-1">
        @php $sub = $institution->activeSubscription; @endphp
        
        <!-- Current Subscription -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="ti ti-subscription text-purple-600"></i> Current Subscription
                </h3>
            </div>
            <div class="p-6">
                @if($sub && $sub->isActive())
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-sm">Plan</span>
                            <span class="text-gray-800 font-semibold capitalize">{{ $sub->plan }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-sm">Amount</span>
                            <span class="text-gray-800 font-semibold">TSh {{ number_format($sub->amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-sm">Status</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Active</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-sm">Starts</span>
                            <span class="text-gray-800">{{ $sub->starts_at ? $sub->starts_at->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-sm">Ends</span>
                            <span class="text-gray-800">{{ $sub->ends_at ? $sub->ends_at->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-sm">Days Left</span>
                            <span class="font-semibold {{ $sub->ends_at && $sub->ends_at->diffInDays(now()) < 7 ? 'text-orange-600' : 'text-emerald-600' }}">
                                {{ $sub->ends_at ? $sub->ends_at->diffInDays(now()) : 'N/A' }} days
                            </span>
                        </div>
                        @if($sub->payment_method)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-sm">Payment Method</span>
                            <span class="text-gray-800 capitalize">{{ $sub->payment_method }}</span>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ti ti-subscription-off text-4xl text-gray-300 block mb-2"></i>
                        <p class="text-gray-500">No active subscription</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Actions -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="ti ti-tools text-purple-600"></i> Actions
                </h3>
            </div>
            <div class="p-4 space-y-3">
                <button onclick="openAssignModal()" 
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2.5 rounded-xl transition font-semibold flex items-center justify-center gap-2">
                    <i class="ti ti-settings"></i> Assign/Change Subscription
                </button>
                
                @if($sub && $sub->status === 'pending')
                    <form method="POST" action="{{ route('super-admin.institutions.subscriptions.activate', $sub->id) }}">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-xl transition font-semibold flex items-center justify-center gap-2">
                            <i class="ti ti-check"></i> Activate Pending
                        </button>
                    </form>
                @endif
                
                @if($sub && $sub->isActive())
                    <form method="POST" action="{{ route('super-admin.institutions.subscriptions.cancel', $sub->id) }}">
                        @csrf
                        <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2.5 rounded-xl transition font-semibold flex items-center justify-center gap-2" onclick="return confirm('Cancel this subscription?')">
                            <i class="ti ti-ban"></i> Cancel Subscription
                        </button>
                    </form>
                @endif
                
                <a href="{{ route('super-admin.institutions.edit', $institution) }}" 
                   class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2.5 rounded-xl transition font-semibold flex items-center justify-center gap-2 block text-center">
                    <i class="ti ti-edit"></i> Edit Institution
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- ASSIGN SUBSCRIPTION MODAL                  -->
<!-- ========================================== -->
<div id="assignModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="ti ti-settings text-purple-600"></i>
                Assign Subscription
            </h3>
            <button onclick="closeAssignModal()" class="text-gray-400 hover:text-gray-600">
                <i class="ti ti-x text-2xl"></i>
            </button>
        </div>
        
        <p class="text-gray-500 text-sm mb-4">Assigning to: <span class="text-gray-800 font-semibold">{{ $institution->name }}</span></p>
        
        <form method="POST" action="{{ route('super-admin.institutions.subscriptions.assign', $institution->id) }}">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Plan <span class="text-red-500">*</span></label>
                    <select name="plan" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                        <option value="basic">📘 Basic</option>
                        <option value="premium">📚 Premium</option>
                        <option value="enterprise">🏢 Enterprise</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Period <span class="text-red-500">*</span></label>
                    <select name="period" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="semi_annual">Semi-Annual</option>
                        <option value="annual">Annual</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (TSh) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" step="0.01" required placeholder="Enter amount"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                        <option value="active">✅ Active</option>
                        <option value="pending">⏳ Pending</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
                    <select name="payment_method" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                        <option value="">Select Method</option>
                        <option value="mpesa">📱 M-Pesa</option>
                        <option value="tigopesa">📱 TigoPesa</option>
                        <option value="halopesa">📱 HaloPesa</option>
                        <option value="pesapal">💳 PesaPal</option>
                        <option value="bank">🏦 Bank Transfer</option>
                        <option value="manual">📝 Manual</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500" placeholder="Optional notes..."></textarea>
                </div>
                
                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-xl hover:shadow-lg transition font-semibold">
                    <i class="ti ti-check"></i> Assign Subscription
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAssignModal() {
    document.getElementById('assignModal').classList.remove('hidden');
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
}

document.getElementById('assignModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeAssignModal();
});
</script>
@endsection