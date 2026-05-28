<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    // Remove the constructor - middleware will be in routes instead
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

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
        
        // Create referral record
        Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $referredId,
            'referral_code' => $referrerCode,
            'referrer_earned' => 5000,
            'referred_earned' => 2000,
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
        
        // Only referrer can mark as complete (when referred user makes first purchase)
        if ($referral->referrer_id != Auth::id()) {
            abort(403);
        }
        
        $referral->status = 'completed';
        $referral->save();
        
        // Add earnings to referrer using floatval to avoid type issues
        $referrerEarned = floatval($referral->referrer_earned ?? 0);
        $referredEarned = floatval($referral->referred_earned ?? 0);
        
        // Add earnings to referrer
        $referrer = User::find($referral->referrer_id);
        if ($referrer) {
            $currentEarnings = floatval($referrer->referral_earnings ?? 0);
            $currentBalance = floatval($referrer->wallet_balance ?? 0);
            
            $referrer->referral_earnings = $currentEarnings + $referrerEarned;
            $referrer->wallet_balance = $currentBalance + $referrerEarned;
            $referrer->save();
        }
        
        // Add earnings to referred user
        $referred = User::find($referral->referred_id);
        if ($referred) {
            $currentReferredEarnings = floatval($referred->referral_earnings ?? 0);
            $currentReferredBalance = floatval($referred->wallet_balance ?? 0);
            
            $referred->referral_earnings = $currentReferredEarnings + $referredEarned;
            $referred->wallet_balance = $currentReferredBalance + $referredEarned;
            $referred->save();
        }
        
        $amount = number_format($referrerEarned, 2);
        
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Referral completed! TSh {$amount} added to your wallet."
            ]);
        }
        
        return redirect()->back()->with('success', "Referral completed! TSh {$amount} added to your wallet.");
    }
}