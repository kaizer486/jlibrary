<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display user subscription management page.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get active subscription
        $activeSubscription = $user->activeSubscription;
        
        // Get subscription history
        $history = $user->subscriptionHistory()
            ->limit(10)
            ->get();
        
        // Subscription stats
        $stats = [
            'is_active' => $user->hasActiveSubscription(),
            'days_left' => $user->getSubscriptionDaysLeft(),
            'status_color' => $user->getSubscriptionStatusColor(),
            'status_label' => $user->getSubscriptionStatusLabel(),
            'plan_label' => $user->getPlanLabel(),
            'plan' => $activeSubscription?->plan ?? 'free',
            'expires_at' => $activeSubscription?->ends_at,
        ];
        
        // Plans
        $plans = [
            (object) [
                'id' => 'free',
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Basic access to the platform',
                'monthly_price' => 0,
                'annual_price' => 0,
                'features' => [
                    'Read free books',
                    'Join 1 institution',
                    'Limited AI Assistant (5/day)',
                ],
            ],
            (object) [
                'id' => 'premium',
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Full access to all features',
                'monthly_price' => 15000,
                'annual_price' => 162000,
                'features' => [
                    'Read paid books',
                    'Unlimited AI Assistant (50/day)',
                    'Certificates',
                    'Join unlimited institutions',
                ],
            ],
            (object) [
                'id' => 'pro',
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'For creators and sellers',
                'monthly_price' => 30000,
                'annual_price' => 324000,
                'features' => [
                    'All Premium features',
                    'Create courses',
                    'Sell books',
                    'Advanced analytics',
                    'Priority support',
                ],
            ],
        ];
        
        return view('user.subscription.index', compact(
            'user',
            'activeSubscription',
            'history',
            'plans',
            'stats'
        ));
    }
    
    /**
     * Show subscription history.
     */
    public function history()
    {
        $user = auth()->user();
        
        $history = $user->subscriptionHistory()
            ->paginate(20);
        
        return view('user.subscription.history', compact('user', 'history'));
    }
    
    /**
     * Extend or upgrade subscription.
     */
    public function extend(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'plan' => 'required|in:free,premium,pro',
            'period' => 'required|in:monthly,annual',
            'payment_method' => 'required|in:mpesa,tigopesa,halopesa,bank,pesapal',
        ]);
        
        // If free plan, cancel existing subscription
        if ($request->plan === 'free') {
            $active = $user->activeSubscription;
            if ($active) {
                $active->update(['status' => 'cancelled']);
            }
            return redirect()->route('user.subscription.index')
                ->with('info', 'You have switched to Free plan.');
        }
        
        // Calculate amount
        $amount = $request->period === 'monthly' 
            ? $this->getMonthlyPrice($request->plan)
            : $this->getAnnualPrice($request->plan);
        
        // Calculate dates
        $startDate = now();
        $endDate = $request->period === 'monthly' 
            ? now()->addMonth() 
            : now()->addYear();
        
        // Create subscription
        $subscription = $user->subscriptions()->create([
            'plan' => $request->plan,
            'amount' => $amount,
            'status' => 'active',
            'starts_at' => $startDate,
            'ends_at' => $endDate,
            'auto_renew' => $request->auto_renew ?? true,
        ]);
        
        // Update user
        $user->update([
            'subscription_tier' => $request->plan,
            'subscription_expires_at' => $endDate,
        ]);
        
        return redirect()->route('user.subscription.index')
            ->with('success', 'Subscription upgraded successfully! You now have ' . ucfirst($request->plan) . ' plan.');
    }
    
    /**
     * Cancel subscription.
     */
    public function cancel()
    {
        $user = auth()->user();
        
        $active = $user->activeSubscription;
        
        if (!$active) {
            return redirect()->route('user.subscription.index')
                ->with('error', 'No active subscription to cancel.');
        }
        
        $active->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'auto_renew' => false,
        ]);
        
        return redirect()->route('user.subscription.index')
            ->with('warning', 'Subscription cancelled. You will lose access after ' . 
                ($active->ends_at ? $active->ends_at->format('M d, Y') : 'the current period'));
    }
    
    private function getMonthlyPrice($plan)
    {
        return match($plan) {
            'premium' => 15000,
            'pro' => 30000,
            default => 0,
        };
    }
    
    private function getAnnualPrice($plan)
    {
        return match($plan) {
            'premium' => 162000, // 10% discount
            'pro' => 324000,    // 10% discount
            default => 0,
        };
    }
}