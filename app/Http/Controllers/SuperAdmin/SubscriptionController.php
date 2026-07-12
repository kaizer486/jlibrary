<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * Display all subscriptions with filters
     */
    public function index(Request $request)
    {
        $query = Subscription::with(['institution', 'subscribable']);
        
        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by plan
        if ($request->plan && $request->plan !== 'all') {
            $query->where('plan', $request->plan);
        }
        
        // Filter by payment method
        if ($request->payment_method && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }
        
        // Search by institution name
        if ($request->search) {
            $query->whereHas('institution', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }
        
        // Date range filter
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        $subscriptions = $query->latest()->paginate(20);
        
        // Stats for filters
        $statusCounts = [
            'all' => Subscription::count(),
            'active' => Subscription::where('status', 'active')->count(),
            'pending' => Subscription::where('status', 'pending')->count(),
            'expired' => Subscription::where('status', 'expired')->count(),
            'cancelled' => Subscription::where('status', 'cancelled')->count(),
        ];
        
        $planCounts = [
            'all' => Subscription::count(),
            'basic' => Subscription::where('plan', 'basic')->count(),
            'premium' => Subscription::where('plan', 'premium')->count(),
            'enterprise' => Subscription::where('plan', 'enterprise')->count(),
        ];
        
        $paymentMethods = [
            'all' => Subscription::count(),
            'mpesa' => Subscription::where('payment_method', 'mpesa')->count(),
            'tigopesa' => Subscription::where('payment_method', 'tigopesa')->count(),
            'halopesa' => Subscription::where('payment_method', 'halopesa')->count(),
            'pesapal' => Subscription::where('payment_method', 'pesapal')->count(),
            'bank' => Subscription::where('payment_method', 'bank')->count(),
        ];
        
        // Calculate revenue
        $subscriptionStats = [
            'revenue' => Subscription::whereIn('status', ['active', 'paid'])
                ->sum('amount'),
        ];
        
        return view('super-admin.subscriptions.index', compact(
            'subscriptions',
            'statusCounts',
            'planCounts',
            'paymentMethods',
            'subscriptionStats'
        ));
    }
    
    /**
     * Show subscription details
     */
    public function show($id)
    {
        $subscription = Subscription::with(['institution', 'subscribable'])->findOrFail($id);
        
        // Get payment history
        $payments = $subscription->payments ?? collect();
        
        // Get institution details
        $institution = $subscription->institution;
        
        // Get all subscriptions for this institution
        $institutionSubscriptions = Institution::find($institution->id)
            ->subscriptions()
            ->latest()
            ->get();
        
        return view('super-admin.subscriptions.show', compact(
            'subscription',
            'payments',
            'institution',
            'institutionSubscriptions'
        ));
    }
    
    /**
     * Show override form
     */
    public function overrideForm($id)
    {
        $subscription = Subscription::with('institution')->findOrFail($id);
        
        return view('super-admin.subscriptions.override', compact('subscription'));
    }
    
    /**
     * Override subscription plan (Super Admin only)
     * ✅ FIXED: Properly updates institution and creates new subscription
     */
    public function override(Request $request, $id)
    {
        $subscription = Subscription::with('institution')->findOrFail($id);
        
        $request->validate([
            'plan' => 'required|in:basic,premium,enterprise',
            'period' => 'required|in:monthly,quarterly,semi_annual,annual',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|in:mpesa,tigopesa,halopesa,pesapal,bank',
            'note' => 'nullable|string|max:500',
        ]);
        
        $institution = $subscription->institution;
        
        if (!$institution) {
            return back()->with('error', 'Institution not found for this subscription.');
        }
        
        $endDate = $this->calculateExpiry(now(), $request->period);
        $planLimits = $this->getPlanLimits($request->plan);
        
        DB::beginTransaction();
        
        try {
            // 1. Cancel the old subscription
            $subscription->status = 'cancelled';
            $subscription->cancelled_at = now();
            $subscription->auto_renew = false;
            $subscription->save();
            
            // 2. Create new subscription
            $newSubscription = Subscription::create([
                'subscribable_type' => Institution::class,
                'subscribable_id' => $institution->id,
                'institution_id' => $institution->id,
                'plan' => $request->plan,
                'amount' => $request->amount,
                'status' => 'active',
                'payment_status' => 'paid',
                'payment_method' => $request->payment_method ?? $subscription->payment_method,
                'starts_at' => now(),
                'ends_at' => $endDate,
                'auto_renew' => false,
                'billing_period' => $request->period,
                'transaction_reference' => 'OVERRIDE-' . Str::random(12) . '-' . time(),
                'mpesa_response_description' => $request->note ?? 'Overridden by Super Admin',
            ]);
            
            // 3. ✅ UPDATE INSTITUTION - This is what makes it show up!
            $institution->update([
                'subscription_tier' => $request->plan,
                'subscription_expires_at' => $endDate,
                'subscription_status' => 'active',
                'subscription_price_paid' => $request->amount,
                'subscription_payment_method' => $request->payment_method ?? $subscription->payment_method,
            ]);
            
            // 4. ✅ UPDATE PLAN LIMITS
            if ($planLimits['max_users'] !== null) {
                $institution->max_users = $planLimits['max_users'];
            } else {
                $institution->max_users = null; // Unlimited
            }
            
            if ($planLimits['max_books'] !== null) {
                $institution->max_books = $planLimits['max_books'];
            } else {
                $institution->max_books = null; // Unlimited
            }
            
            $institution->save();
            
            // 5. ✅ Update the user's subscription fields (if user exists)
            // This ensures the sidebar shows correctly
            $admin = $institution->users()->where('is_institution_admin', true)->first();
            if ($admin) {
                $admin->update([
                    'subscription_tier' => $request->plan,
                    'subscription_expires_at' => $endDate,
                ]);
            }
            
            // 6. Log the override
            Log::info('Subscription overridden by Super Admin', [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->full_name,
                'subscription_id' => $subscription->id,
                'new_subscription_id' => $newSubscription->id,
                'institution_id' => $institution->id,
                'institution_name' => $institution->name,
                'old_plan' => $subscription->plan,
                'new_plan' => $request->plan,
                'amount' => $request->amount,
                'period' => $request->period,
                'note' => $request->note,
            ]);
            
            DB::commit();
            
            return redirect()->route('super-admin.subscriptions.show', $newSubscription->id)
                ->with('success', 'Subscription overridden successfully! 
                    ' . ucfirst($request->plan) . ' plan activated until ' . $endDate->format('M d, Y'));
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription override failed: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'institution_id' => $institution->id,
                'error' => $e->getMessage(),
            ]);
            
            return back()->with('error', 'Failed to override subscription: ' . $e->getMessage());
        }
    }
    
    /**
     * Get plan limits
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
     * Activate a pending subscription
     */
   /**
 * Activate a pending subscription
 */
public function activate(Request $request, $id)
{
    $subscription = Subscription::with('institution')->findOrFail($id);
    
    // ✅ Use the model's activate method
    $subscription->activate();
    
    // ✅ Update the institution
    if ($subscription->institution) {
        $subscription->institution->update([
            'subscription_status' => 'active',
            'subscription_tier' => $subscription->plan,
            'subscription_expires_at' => $subscription->ends_at,
        ]);
        
        // ✅ Update all users in this institution
        $subscription->institution->users()->update([
            'subscription_tier' => $subscription->plan,
            'subscription_expires_at' => $subscription->ends_at,
        ]);
    }
    
    return redirect()->back()->with('success', 'Subscription activated successfully!');
}
    
    /**
     * Cancel a subscription
     */
    /**
 * Cancel a subscription
 */
/**
 * Cancel a subscription
 */
public function cancel($id)
{
    $subscription = Subscription::with('institution')->findOrFail($id);
    
    // ✅ Use the model's cancel method
    $subscription->cancel();
    
    // ✅ Also update the institution fields (in case the model method doesn't)
    if ($subscription->institution) {
        $subscription->institution->update([
            'subscription_status' => 'cancelled',
            'subscription_tier' => 'free',
            'subscription_expires_at' => null,
        ]);
        
        // ✅ Update all users in this institution
        $subscription->institution->users()->update([
            'subscription_tier' => 'free',
            'subscription_expires_at' => null,
        ]);
    }
    
    return redirect()->back()->with('warning', 'Subscription cancelled successfully.');
}
    
  /**
 * Mark a subscription as expired
 */
public function markExpired($id)
{
    $subscription = Subscription::with('institution')->findOrFail($id);
    
    // ✅ Use the model's expire method
    $subscription->expire();
    
    // ✅ Also update the institution fields
    if ($subscription->institution) {
        $subscription->institution->update([
            'subscription_status' => 'expired',
            'subscription_tier' => 'free',
            'subscription_expires_at' => null,
        ]);
        
        // ✅ Update all users in this institution
        $subscription->institution->users()->update([
            'subscription_tier' => 'free',
            'subscription_expires_at' => null,
        ]);
    }
    
    return redirect()->back()->with('info', 'Subscription marked as expired.');
}
    
   /**
 * Bulk action on subscriptions
 */
public function bulkAction(Request $request)
{
    $request->validate([
        'subscription_ids' => 'required|array',
        'subscription_ids.*' => 'exists:subscriptions,id',
        'action' => 'required|in:activate,cancel,expire,delete',
    ]);
    
    $count = 0;
    
    foreach ($request->subscription_ids as $id) {
        $subscription = Subscription::with('institution')->find($id);
        
        if (!$subscription) continue;
        
        switch ($request->action) {
            case 'activate':
                $subscription->update(['status' => 'active']);
                if ($subscription->institution) {
                    $subscription->institution->update([
                        'subscription_status' => 'active',
                        'subscription_tier' => $subscription->plan,
                        'subscription_expires_at' => $subscription->ends_at,
                    ]);
                    $subscription->institution->users()->update([
                        'subscription_tier' => $subscription->plan,
                        'subscription_expires_at' => $subscription->ends_at,
                    ]);
                }
                break;
                
            case 'cancel':
                $subscription->update(['status' => 'cancelled', 'cancelled_at' => now()]);
                if ($subscription->institution) {
                    $subscription->institution->update([
                        'subscription_status' => 'cancelled',
                        'subscription_tier' => null,
                        'subscription_expires_at' => null,
                    ]);
                    $subscription->institution->users()->update([
                        'subscription_tier' => null,
                        'subscription_expires_at' => null,
                    ]);
                }
                break;
                
            case 'expire':
                $subscription->update(['status' => 'expired']);
                if ($subscription->institution) {
                    $subscription->institution->update([
                        'subscription_status' => 'expired',
                        'subscription_tier' => null,
                        'subscription_expires_at' => null,
                    ]);
                    $subscription->institution->users()->update([
                        'subscription_tier' => null,
                        'subscription_expires_at' => null,
                    ]);
                }
                break;
                
            case 'delete':
                $subscription->delete();
                break;
        }
        
        $count++;
    }
    
    return redirect()->back()->with('success', $count . ' subscription(s) updated successfully!');
}
    
    /**
     * Export subscriptions to CSV
     */
    public function export(Request $request)
    {
        $query = Subscription::with('institution');
        
        // Apply filters
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->plan && $request->plan !== 'all') {
            $query->where('plan', $request->plan);
        }
        
        $subscriptions = $query->get();
        
        $filename = 'subscriptions_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($subscriptions) {
            $handle = fopen('php://output', 'w');
            
            // Headers
            fputcsv($handle, [
                'ID',
                'Institution',
                'Plan',
                'Amount',
                'Status',
                'Payment Method',
                'Starts At',
                'Ends At',
                'Created At',
            ]);
            
            // Data
            foreach ($subscriptions as $sub) {
                fputcsv($handle, [
                    $sub->id,
                    $sub->institution->name ?? 'N/A',
                    $sub->plan,
                    $sub->amount,
                    $sub->status,
                    $sub->payment_method ?? 'N/A',
                    $sub->starts_at ? $sub->starts_at->format('Y-m-d') : 'N/A',
                    $sub->ends_at ? $sub->ends_at->format('Y-m-d') : 'N/A',
                    $sub->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($handle);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Calculate expiry date
     */
    private function calculateExpiry($startDate, $period)
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
}