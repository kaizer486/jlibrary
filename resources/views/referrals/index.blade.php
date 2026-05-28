@extends('layouts.app')

@section('title', 'Refer & Earn')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        
        <!-- Header with Gradient -->
        <div class="relative overflow-hidden bg-gradient-to-r from-purple-600 via-pink-500 to-orange-500 rounded-2xl p-8 mb-8 text-white">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <i class="ti ti-users text-4xl"></i>
                        <h1 class="text-3xl font-bold">Refer & Earn</h1>
                    </div>
                    <p class="text-purple-100">Invite friends and earn TSh 5,000 for each friend who joins!</p>
                    <p class="text-purple-200 text-sm mt-2">Your friends also get TSh 2,000 welcome bonus</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2 text-center">
                        <i class="ti ti-gift text-2xl"></i>
                        <p class="text-sm font-semibold">Unlimited Earnings!</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards with Attractive Gradients -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <!-- Total Referrals Card -->
            <div class="group bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl p-5 text-white shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Total Referrals</p>
                        <p class="text-4xl font-bold mt-1">{{ $totalReferrals }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                        <i class="ti ti-users text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Completed Referrals Card -->
            <div class="group bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl p-5 text-white shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Completed Referrals</p>
                        <p class="text-4xl font-bold mt-1">{{ $completedReferrals }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                        <i class="ti ti-checkbox text-2xl"></i>
                    </div>
                </div>
                <p class="text-green-100 text-xs mt-2">= TSh {{ number_format($completedReferrals * 5000, 0) }} earned</p>
            </div>
            
            <!-- Total Earnings Card -->
            <div class="group bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl p-5 text-white shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-sm">Total Earnings</p>
                        <p class="text-4xl font-bold mt-1">TSh {{ number_format($totalEarnings, 0) }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                        <i class="ti ti-wallet text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Referral Link Card with Copy Button -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border-l-4 border-purple-500">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="ti ti-link text-purple-600 text-xl"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">Your Personal Referral Link</h2>
            </div>
            <p class="text-gray-500 text-sm mb-4">Share this link with friends. When they sign up and make their first purchase, you both get rewards!</p>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ti ti-link text-gray-400"></i>
                    </div>
                    <input type="text" id="referral-link" value="{{ $referralLink }}" 
                           class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-mono text-gray-700"
                           readonly>
                </div>
                <button onclick="copyReferralLink()" 
                        class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-xl hover:shadow-lg transition flex items-center gap-2 justify-center font-medium">
                    <i class="ti ti-copy"></i> Copy Link
                </button>
            </div>
        </div>

        <!-- How It Works - Attractive Steps -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 text-center shadow-sm hover:shadow-md transition border-t-4 border-purple-500">
                <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="ti ti-share text-white text-3xl"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-lg mb-2">1. Share Your Link</h3>
                <p class="text-gray-500 text-sm">Share your unique referral link with friends via WhatsApp, Instagram, or any platform</p>
            </div>
            <div class="bg-white rounded-xl p-6 text-center shadow-sm hover:shadow-md transition border-t-4 border-blue-500">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="ti ti-user-plus text-white text-3xl"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-lg mb-2">2. Friend Signs Up</h3>
                <p class="text-gray-500 text-sm">Your friend registers using your link and gets TSh 2,000 welcome bonus instantly!</p>
            </div>
            <div class="bg-white rounded-xl p-6 text-center shadow-sm hover:shadow-md transition border-t-4 border-green-500">
                <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="ti ti-wallet text-white text-3xl"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-lg mb-2">3. You Both Earn</h3>
                <p class="text-gray-500 text-sm">When friend makes first purchase or passes a quiz, you earn TSh 5,000!</p>
            </div>
        </div>

        <!-- Rewards Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="ti ti-list-details text-purple-600"></i>
                    Your Referrals
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Friend</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Joined</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">You Earned</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($referrals as $referral)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-pink-100 rounded-full flex items-center justify-center">
                                        <i class="ti ti-user text-purple-600"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-800">{{ $referral->referred->full_name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $referral->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                @if($referral->status == 'completed')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                        <i class="ti ti-check text-xs"></i> Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">
                                        <i class="ti ti-clock text-xs"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-green-600">TSh {{ number_format($referral->referrer_earned, 0) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($referral->status == 'pending')
                                    <span class="text-xs text-gray-400">Waiting for purchase</span>
                                @else
                                    <span class="text-xs text-green-500">✓ Paid</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="ti ti-users text-gray-400 text-3xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No referrals yet</p>
                                <p class="text-sm text-gray-400 mt-1">Share your link to start earning!</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($referrals->hasPages())
                <div class="px-6 py-4 border-t">
                    {{ $referrals->links() }}
                </div>
            @endif
        </div>

        <!-- Info Banner -->
        <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-200">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-info-circle text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-blue-800">How to earn your TSh 5,000 reward?</p>
                    <p class="text-xs text-blue-600 mt-1">
                        Your referred friend needs to complete ONE of these actions:
                    </p>
                    <ul class="text-xs text-blue-600 mt-2 space-y-1">
                        <li>• <i class="ti ti-shopping-cart text-xs"></i> Make their first purchase from the Marketplace</li>
                        <li>• <i class="ti ti-brain text-xs"></i> Pass their first quiz with 70% or higher</li>
                        <li>• <i class="ti ti-book text-xs"></i> Purchase a paid book from the Library</li>
                    </ul>
                    <p class="text-xs text-blue-600 mt-2 font-medium">No limits - refer as many friends as you want!</p>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function copyReferralLink() {
        const linkInput = document.getElementById('referral-link');
        linkInput.select();
        linkInput.setSelectionRange(0, 99999);
        document.execCommand('copy');
        
        // Show temporary success message
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="ti ti-check"></i> Copied!';
        btn.classList.add('bg-green-600');
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('bg-green-600');
        }, 2000);
    }
</script>
@endsection