<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Display subscription management page.
     */
    public function index()
    {
        $user = Auth::user();
        $institution = $user->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        // ==========================================
        // CHECK BOTH SOURCES FOR SUBSCRIPTION
        // ==========================================
        
        // 1. Check if user has subscription in subscriptions table (polymorphic)
        $subscriptionFromTable = $user->activeSubscription;
        
        // 2. Check if user has subscription fields in users table
        $hasUserSubscription = !empty($user->subscription_tier) && 
                               $user->subscription_expires_at && 
                               $user->subscription_expires_at > now();
        
        // Determine which subscription to use
        $activeSubscription = null;
        $isActive = false;
        $isExpired = false;
        $daysLeft = 0;
        $planLabel = '📘 No Plan';
        $plan = 'basic';
        $expiresAt = null;
        
        if ($subscriptionFromTable) {
            // Use subscription from subscriptions table
            $activeSubscription = $subscriptionFromTable;
            $isActive = $subscriptionFromTable->isActive();
            $isExpired = $subscriptionFromTable->isExpired();
            $daysLeft = $subscriptionFromTable->daysRemaining();
            $planLabel = $subscriptionFromTable->getPlanLabel();
            $plan = $subscriptionFromTable->plan;
            $expiresAt = $subscriptionFromTable->ends_at;
        } elseif ($hasUserSubscription) {
            // Use subscription from users table fields
            $isActive = true;
            $isExpired = false;
            $daysLeft = max(0, Carbon::now()->diffInDays($user->subscription_expires_at, false));
            $planLabel = match($user->subscription_tier) {
                'basic' => '📘 Basic',
                'premium' => '📚 Premium',
                'enterprise' => '🏢 Enterprise',
                'free' => '🆓 Free',
                default => '📘 Basic'
            };
            $plan = $user->subscription_tier ?? 'basic';
            $expiresAt = $user->subscription_expires_at;
            
            // Create a virtual subscription object
            $activeSubscription = (object) [
                'plan' => $plan,
                'amount' => 0,
                'status' => 'active',
                'starts_at' => null,
                'ends_at' => $expiresAt,
                'isActive' => function() use ($expiresAt) {
                    return $expiresAt && $expiresAt > now();
                },
                'isExpired' => function() use ($expiresAt) {
                    return $expiresAt && $expiresAt <= now();
                },
                'daysRemaining' => function() use ($expiresAt) {
                    if (!$expiresAt) return 0;
                    if ($expiresAt <= now()) return 0;
                    return max(0, Carbon::now()->diffInDays($expiresAt, false));
                },
                'getPlanLabel' => function() use ($plan) {
                    return match($plan) {
                        'basic' => '📘 Basic',
                        'premium' => '📚 Premium',
                        'enterprise' => '🏢 Enterprise',
                        'free' => '🆓 Free',
                        default => '📘 Basic'
                    };
                }
            ];
        }
        
        // Check if subscription is expired
        if ($expiresAt && $expiresAt <= now()) {
            $isActive = false;
            $isExpired = true;
            $daysLeft = 0;
        }
        
        $canChoosePlan = !$isActive;
        
        // Get subscription history from institution
        $history = $institution->subscriptionHistory()
            ->limit(10)
            ->get();
        
        // Stats
        $stats = [
            'is_active' => $isActive,
            'is_expired' => $isExpired,
            'days_left' => $daysLeft,
            'progress' => $this->getProgress($activeSubscription, $expiresAt),
            'status_color' => $isActive ? 'text-emerald-400' : ($isExpired ? 'text-red-400' : 'text-slate-400'),
            'status_label' => $isActive ? '✅ Active' : ($isExpired ? '❌ Expired' : 'No Subscription'),
            'plan_label' => $planLabel,
            'plan' => $plan,
            'expires_at' => $expiresAt,
            'can_choose_plan' => $canChoosePlan,
        ];
        
        // Plans for institution
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
        $user = auth()->user();
        $institution = $user->institution;
        
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
        $startDate = now();
        $endDate = $this->calculateExpiry($startDate, $request->period);
        
        // Update user subscription fields
        $user->update([
            'subscription_tier' => $request->plan,
            'subscription_expires_at' => $endDate,
        ]);
        
        // Create subscription record in subscriptions table
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
        $user = auth()->user();
        $institution = $user->institution;
        
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
        
        // Clear user subscription fields
        $user->update([
            'subscription_tier' => null,
            'subscription_expires_at' => null,
        ]);
        
        $institution->update([
            'subscription_status' => 'cancelled',
        ]);
        
        return redirect()->route('institution.subscription.index')
            ->with('warning', 'Subscription has been cancelled.');
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

    /**
     * Get subscription progress.
     */
    private function getProgress($subscription, $expiresAt = null)
    {
        if ($subscription instanceof \App\Models\Subscription && $subscription->ends_at) {
            $total = Carbon::now()->diffInDays($subscription->ends_at);
            $elapsed = Carbon::now()->diffInDays($subscription->starts_at ?? Carbon::now());
            
            if ($total <= 0) return 0;
            return round(($elapsed / $total) * 100);
        }
        
        if ($expiresAt && $expiresAt > now()) {
            // Calculate progress for user-based subscription
            $total = max(1, Carbon::now()->diffInDays($expiresAt));
            $startDate = Carbon::now()->subDays(30);
            $elapsed = Carbon::now()->diffInDays($startDate);
            
            if ($total <= 0) return 0;
            return round(($elapsed / $total) * 100);
        }
        
        return 0;
    }
}