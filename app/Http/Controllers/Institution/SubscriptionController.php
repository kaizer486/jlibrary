<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display subscription management page.
     */
    public function index()
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        // Get active subscription
        $activeSubscription = $institution->activeSubscription;
        
        // Get subscription history
        $history = $institution->subscriptionHistory()
            ->limit(10)
            ->get();
        
        // Stats
        $stats = [
            'is_active' => $institution->isSubscriptionActive(),
            'days_left' => $institution->getDaysLeft(),
            'progress' => $institution->getSubscriptionProgress(),
            'status_color' => $institution->getSubscriptionStatusColor(),
            'status_label' => $institution->getSubscriptionStatusLabel(),
            'plan_label' => $institution->getPlanLabel(),
            'plan' => $activeSubscription?->plan ?? $institution->subscription_tier ?? 'basic',
            'expires_at' => $activeSubscription?->ends_at ?? $institution->subscription_expires_at,
        ];
        
        // Plans
        $plans = [
            (object) [
                'id' => 'basic',
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Perfect for small institutions',
                'monthly_price' => 50000,
                'quarterly_price' => 135000,
                'semi_annual_price' => 270000,
                'annual_price' => 540000,
                'max_users' => 50,
                'max_books' => 100,
                'features' => [
                    'Up to 50 members',
                    'Up to 100 books',
                    'Institution Quotes',
                    'Join Requests',
                    'Member Management',
                ],
            ],
            (object) [
                'id' => 'premium',
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Ideal for growing institutions',
                'monthly_price' => 100000,
                'quarterly_price' => 270000,
                'semi_annual_price' => 540000,
                'annual_price' => 1080000,
                'max_users' => 200,
                'max_books' => 500,
                'features' => [
                    'Up to 200 members',
                    'Up to 500 books',
                    'All Basic features',
                    'Analytics Dashboard',
                    'Advanced Reporting',
                ],
            ],
            (object) [
                'id' => 'enterprise',
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Full featured for large institutions',
                'monthly_price' => 200000,
                'quarterly_price' => 540000,
                'semi_annual_price' => 1080000,
                'annual_price' => 2160000,
                'max_users' => null,
                'max_books' => null,
                'features' => [
                    'Unlimited members',
                    'Unlimited books',
                    'All Premium features',
                    'Priority Support',
                    'Custom Branding',
                    'API Access',
                ],
            ],
        ];
        
        return view('institution.subscription.index', compact(
            'institution',
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
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        $history = $institution->subscriptionHistory()
            ->paginate(20);
        
        return view('institution.subscription.history', compact('institution', 'history'));
    }
    
    /**
     * Extend or upgrade subscription.
     */
    public function extend(Request $request)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        $request->validate([
            'plan' => 'required|in:basic,premium,enterprise',
            'period' => 'required|in:monthly,quarterly,semi_annual,annual',
            'payment_method' => 'required|in:mpesa,tigopesa,halopesa,bank,pesapal',
        ]);
        
        // Calculate amount
        $amount = $this->calculateAmount($request->plan, $request->period);
        
        // Calculate dates
        $startDate = $institution->subscription_expires_at ?? now();
        $endDate = $this->calculateExpiry($startDate, $request->period);
        
        // Create subscription
        $subscription = $institution->subscriptions()->create([
            'plan' => $request->plan,
            'amount' => $amount,
            'status' => 'active',
            'starts_at' => $startDate,
            'ends_at' => $endDate,
            'auto_renew' => $request->auto_renew ?? true,
        ]);
        
        // Update institution
        $institution->update([
            'subscription_tier' => $request->plan,
            'subscription_expires_at' => $endDate,
            'subscription_status' => 'active',
            'subscription_price_paid' => $amount,
            'subscription_payment_method' => $request->payment_method,
        ]);
        
        // Update max users and books based on plan
        $limits = $this->getPlanLimits($request->plan);
        if ($limits['max_users']) {
            $institution->max_users = $limits['max_users'];
        }
        if ($limits['max_books']) {
            $institution->max_books = $limits['max_books'];
        }
        $institution->save();
        
        return redirect()->route('institution.subscription.index')
            ->with('success', "Subscription extended successfully! Your {$request->plan} plan is active until " . $endDate->format('M d, Y'));
    }
    
    /**
     * Cancel subscription.
     */
    public function cancel()
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        $active = $institution->activeSubscription;
        
        if (!$active) {
            return redirect()->route('institution.subscription.index')
                ->with('error', 'No active subscription to cancel.');
        }
        
        $active->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'auto_renew' => false,
        ]);
        
        $institution->update([
            'subscription_status' => 'cancelled',
        ]);
        
        return redirect()->route('institution.subscription.index')
            ->with('warning', 'Subscription has been cancelled. Your access will remain until ' . 
                ($active->ends_at ? $active->ends_at->format('M d, Y') : 'the end of the current period'));
    }
    
    /**
     * Calculate amount based on plan and period.
     */
    private function calculateAmount($plan, $period)
    {
        $prices = [
            'basic' => [
                'monthly' => 50000,
                'quarterly' => 135000,
                'semi_annual' => 270000,
                'annual' => 540000
            ],
            'premium' => [
                'monthly' => 100000,
                'quarterly' => 270000,
                'semi_annual' => 540000,
                'annual' => 1080000
            ],
            'enterprise' => [
                'monthly' => 200000,
                'quarterly' => 540000,
                'semi_annual' => 1080000,
                'annual' => 2160000
            ],
        ];
        
        return $prices[$plan][$period] ?? 0;
    }
    
    /**
     * Calculate expiry date.
     */
    private function calculateExpiry($startDate, $period)
    {
        $map = [
            'monthly' => 'addMonth',
            'quarterly' => 'addMonths',
            'semi_annual' => 'addMonths',
            'annual' => 'addYear',
        ];
        
        $counts = [
            'monthly' => 1,
            'quarterly' => 3,
            'semi_annual' => 6,
            'annual' => 1,
        ];
        
        $method = $map[$period];
        $count = $counts[$period];
        
        if ($method === 'addMonths') {
            return $startDate->copy()->addMonths($count);
        }
        
        return $startDate->copy()->$method();
    }
    
    /**
     * Get plan limits.
     */
    private function getPlanLimits($plan)
    {
        return match($plan) {
            'basic' => ['max_users' => 50, 'max_books' => 100],
            'premium' => ['max_users' => 200, 'max_books' => 500],
            'enterprise' => ['max_users' => null, 'max_books' => null],
            default => ['max_users' => 50, 'max_books' => 100],
        };
    }
}