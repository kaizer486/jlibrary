<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $referrals = Referral::where('referrer_id', $user->id)
            ->with('referred')
            ->latest()
            ->paginate(10);
        
        $totalReferrals = $user->total_referrals;
        $completedReferrals = $user->completed_referrals;
        $totalEarnings = $user->referral_earnings ?? 0;
        $referralLink = $user->referral_link;
        
        return view('referrals.index', compact(
            'referrals', 'totalReferrals', 'completedReferrals', 
            'totalEarnings', 'referralLink'
        ));
    }

    public function processReferral($code)
    {
        // Store referral code in session for registration
        session(['referral_code' => $code]);
        return redirect()->route('register');
    }

    public function completeReferral($referredId)
    {
        $referrerCode = session('referral_code');
        
        if (!$referrerCode) {
            return;
        }
        
        $referrer = User::where('referral_code', $referrerCode)->first();
        
        if (!$referrer || $referrer->id == $referredId) {
            return;
        }
        
        // Check if already referred
        $existing = Referral::where('referred_id', $referredId)->first();
        if ($existing) {
            return;
        }
        
        // Create referral record with COINS instead of money
        Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $referredId,
            'referral_code' => $referrerCode,
            'referrer_earned' => 100, // 100 coins for referrer
            'referred_earned' => 50,  // 50 coins for referred
            'status' => 'pending'
        ]);
        
        // Update referred user
        $referred = User::find($referredId);
        if ($referred) {
            $referred->referred_by = $referrer->id;
            $referred->save();
        }
        
        // Clear session
        session()->forget('referral_code');
    }

    public function markComplete($id)
    {
        $referral = Referral::findOrFail($id);
        
        // Only referrer can mark as complete
        if ($referral->referrer_id != Auth::id()) {
            abort(403);
        }
        
        $referral->status = 'completed';
        $referral->save();
        
        // Add COINS earnings to referrer
        $referrerCoins = floatval($referral->referrer_earned ?? 100);
        $referredCoins = floatval($referral->referred_earned ?? 50);
        
        // Add coins to referrer
        $referrer = User::find($referral->referrer_id);
        if ($referrer) {
            $currentEarnings = floatval($referrer->referral_earnings ?? 0);
            $currentCoins = floatval($referrer->coins ?? 0);
            
            $referrer->referral_earnings = $currentEarnings + $referrerCoins;
            $referrer->coins = $currentCoins + $referrerCoins;
            $referrer->save();
        }
        
        // Add coins to referred user
        $referred = User::find($referral->referred_id);
        if ($referred) {
            $currentReferredEarnings = floatval($referred->referral_earnings ?? 0);
            $currentReferredCoins = floatval($referred->coins ?? 0);
            
            $referred->referral_earnings = $currentReferredEarnings + $referredCoins;
            $referred->coins = $currentReferredCoins + $referredCoins;
            $referred->save();
        }
        
        $amount = number_format($referrerCoins, 0);
        
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Referral completed! 🪙 {$amount} coins added to your wallet."
            ]);
        }
        
        return redirect()->back()->with('success', "Referral completed! 🪙 {$amount} coins added to your wallet.");
    }
}