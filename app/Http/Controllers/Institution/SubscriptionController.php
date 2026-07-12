<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    protected $paymentService;
    
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    
    /**
     * Display subscription management page.
     * PATH: GET /institution/subscription
     */
    public function index()
    {
        $user = Auth::user();
        $institution = $user->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        // Get current subscription
        $activeSubscription = $institution->activeSubscription;
        
        $stats = $this->getSubscriptionStats($activeSubscription);
        
        // Get subscription history
        $history = $institution->subscriptionHistory()
            ->latest()
            ->limit(10)
            ->get();
        
        // Get available plans
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        if ($plans->isEmpty()) {
            // Fallback to hardcoded plans if none in DB
            $plans = $this->getDefaultPlans();
        }
        
        // Check if user can choose a plan
        $canChoosePlan = !$stats['is_active'];
        
        return view('institution.subscription.index', compact(
            'institution',
            'activeSubscription',
            'history',
            'plans',
            'stats',
            'canChoosePlan'
        ));
    }
    
    /**
     * Show subscription history.
     * PATH: GET /institution/subscription/history
     */
    public function history()
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        $history = $institution->subscriptionHistory()
            ->latest()
            ->paginate(20);
        
        return view('institution.subscription.history', compact('institution', 'history'));
    }
    
    /**
     * Initiate payment for subscription.
     * PATH: POST /institution/subscription/initiate-payment
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'plan' => 'required|string|exists:subscription_plans,slug',
            'period' => 'required|in:monthly,quarterly,semi_annual,annual',
            'payment_method' => 'required|in:mpesa,tigopesa,halopesa,pesapal,bank',
            'phone_number' => 'required_if:payment_method,mpesa,tigopesa,halopesa',
            'terms' => 'accepted',
        ]);
        
        $user = auth()->user();
        $institution = $user->institution;
        
        if (!$institution) {
            return back()->with('error', 'You do not belong to any institution.');
        }
        
        // Check if already active
        if ($institution->activeSubscription) {
            return back()->with('error', 'You already have an active subscription.');
        }
        
        // Get plan details
        $plan = SubscriptionPlan::where('slug', $request->plan)->first();
        if (!$plan) {
            return back()->with('error', 'Invalid plan selected.');
        }
        
        $amount = $plan->getPriceForPeriod($request->period);
        
        DB::beginTransaction();
        
        try {
            // Create subscription record (pending)
            $subscription = Subscription::create([
                'subscribable_type' => Institution::class,
                'subscribable_id' => $institution->id,
                'institution_id' => $institution->id,
                'plan' => $request->plan,
                'amount' => $amount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'billing_period' => $request->period,
                'auto_renew' => $request->auto_renew ?? false,
            ]);
            
            DB::commit();
            
            // Process payment
            $result = $this->paymentService->processPayment(
                $subscription,
                $request->payment_method,
                $request->all()
            );
            
            if (isset($result['is_redirect']) && $result['is_redirect']) {
                return redirect()->away($result['redirect_url']);
            }
            
            if (isset($result['redirect_url'])) {
                return redirect($result['redirect_url']);
            }
            
            return redirect()->route('institution.subscription.payment-status', $subscription->id)
                ->with('success', $result['message'] ?? 'Payment initiated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment initiation failed: ' . $e->getMessage());
            return back()->with('error', 'Payment initiation failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Check payment status.
     * PATH: GET /institution/subscription/payment-status/{id}
     */
    public function paymentStatus($subscriptionId)
    {
        $subscription = Subscription::with('institution')->findOrFail($subscriptionId);
        
        // Verify ownership
        if ($subscription->institution_id !== auth()->user()->institution->id) {
            abort(403);
        }
        
        return view('institution.subscription.payment-status', compact('subscription'));
    }
    
    /**
     * Show payment instructions for bank transfer.
     * PATH: GET /institution/subscription/payment-instructions/{id}
     */
    public function paymentInstructions($subscriptionId)
    {
        $subscription = Subscription::with('institution')->findOrFail($subscriptionId);
        
        if ($subscription->institution_id !== auth()->user()->institution->id) {
            abort(403);
        }
        
        return view('institution.subscription.payment-instructions', compact('subscription'));
    }
    
    /**
     * Upload payment proof for bank transfer.
     * PATH: POST /institution/subscription/upload-payment-proof/{id}
     */
    public function uploadPaymentProof(Request $request, $subscriptionId)
    {
        $request->validate([
            'payment_proof' => 'required|file|image|max:5120|mimes:jpeg,png,pdf',
        ]);
        
        $subscription = Subscription::findOrFail($subscriptionId);
        
        if ($subscription->institution_id !== auth()->user()->institution->id) {
            abort(403);
        }
        
        if ($subscription->payment_status === 'paid') {
            return back()->with('error', 'This subscription is already paid.');
        }
        
        // Store file
        $path = $request->file('payment_proof')->store(
            'subscription-proofs/' . $subscriptionId,
            'public'
        );
        
        $subscription->uploadPaymentProof($path);
        
        // Notify admins (optional)
        // event(new PaymentProofUploaded($subscription));
        
        return redirect()->route('institution.subscription.payment-status', $subscriptionId)
            ->with('success', 'Payment proof uploaded successfully. Awaiting verification.');
    }
    
    /**
     * Extend or upgrade subscription (Manual/Super Admin only).
     * PATH: POST /institution/subscription/extend
     */
    public function extend(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic,premium,enterprise',
            'period' => 'required|in:monthly,quarterly,semi_annual,annual',
        ]);
        
        $user = auth()->user();
        $institution = $user->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        // Only allow Super Admin to extend manually
        if (!auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Only Super Admin can manually extend subscriptions.');
        }
        
        $amount = $this->calculateAmount($request->plan, $request->period);
        $endDate = $this->calculateExpiry(now(), $request->period);
        
        // Create subscription
        $subscription = Subscription::create([
            'subscribable_type' => Institution::class,
            'subscribable_id' => $institution->id,
            'institution_id' => $institution->id,
            'plan' => $request->plan,
            'amount' => $amount,
            'status' => 'active',
            'payment_status' => 'paid',
            'payment_method' => 'admin',
            'starts_at' => now(),
            'ends_at' => $endDate,
            'billing_period' => $request->period,
            'auto_renew' => $request->auto_renew ?? true,
        ]);
        
        // Update institution
        $institution->update([
            'subscription_tier' => $request->plan,
            'subscription_expires_at' => $endDate,
            'subscription_status' => 'active',
        ]);
        
        return redirect()->route('institution.subscription.index')
            ->with('success', "Subscription extended successfully! Expires: " . $endDate->format('M d, Y'));
    }
    
    /**
     * Cancel subscription.
     * PATH: POST /institution/subscription/cancel
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
        
        $active->cancel();
        
        return redirect()->route('institution.subscription.index')
            ->with('warning', 'Subscription has been cancelled.');
    }
    
    // ==========================================
    // PRIVATE HELPERS
    // ==========================================
    
    private function getSubscriptionStats($subscription): array
    {
        if (!$subscription) {
            return [
                'is_active' => false,
                'is_expired' => true,
                'days_left' => 0,
                'progress' => 0,
                'status_color' => 'text-slate-400',
                'status_label' => 'No Subscription',
                'plan_label' => '📘 No Plan',
                'plan' => null,
                'expires_at' => null,
                'can_choose_plan' => true,
            ];
        }
        
        $isActive = $subscription->isActive();
        $isExpired = $subscription->isExpired();
        $daysLeft = $subscription->daysRemaining();
        
        return [
            'is_active' => $isActive,
            'is_expired' => $isExpired,
            'days_left' => $daysLeft,
            'progress' => $this->getProgress($subscription),
            'status_color' => $isActive ? 'text-emerald-600' : ($isExpired ? 'text-red-600' : 'text-slate-400'),
            'status_label' => $isActive ? '✅ Active' : ($isExpired ? '❌ Expired' : 'No Subscription'),
            'plan_label' => $subscription->getPlanLabel(),
            'plan' => $subscription->plan,
            'expires_at' => $subscription->ends_at,
            'can_choose_plan' => !$isActive,
        ];
    }
    
    private function getProgress($subscription): int
    {
        if (!$subscription->starts_at || !$subscription->ends_at) {
            return 0;
        }
        
        $total = $subscription->starts_at->diffInDays($subscription->ends_at);
        $elapsed = $subscription->starts_at->diffInDays(now());
        
        if ($total <= 0) return 0;
        return round(($elapsed / $total) * 100);
    }
    
    private function calculateAmount($plan, $period): float
    {
        $prices = [
            'basic' => ['monthly' => 50000, 'quarterly' => 135000, 'semi_annual' => 270000, 'annual' => 540000],
            'premium' => ['monthly' => 100000, 'quarterly' => 270000, 'semi_annual' => 540000, 'annual' => 1080000],
            'enterprise' => ['monthly' => 200000, 'quarterly' => 540000, 'semi_annual' => 1080000, 'annual' => 2160000],
        ];
        
        return $prices[$plan][$period] ?? 0;
    }
    
    private function calculateExpiry($startDate, $period): Carbon
    {
        $map = [
            'monthly' => ['method' => 'addMonths', 'count' => 1],
            'quarterly' => ['method' => 'addMonths', 'count' => 3],
            'semi_annual' => ['method' => 'addMonths', 'count' => 6],
            'annual' => ['method' => 'addYear', 'count' => 1],
        ];
        
        $config = $map[$period] ?? $map['monthly'];
        
        if ($config['method'] === 'addMonths') {
            return $startDate->copy()->addMonths($config['count']);
        }
        
        return $startDate->copy()->addYear();
    }
    
    private function getDefaultPlans(): \Illuminate\Support\Collection
    {
        return collect([
            (object) [
                'id' => 1,
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
                'is_active' => true,
            ],
            (object) [
                'id' => 2,
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
                'is_active' => true,
            ],
            (object) [
                'id' => 3,
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
                'is_active' => true,
            ],
        ]);
    }
}