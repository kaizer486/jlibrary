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
        
        return view('super-admin.subscriptions.index', compact(
            'subscriptions',
            'statusCounts',
            'planCounts',
            'paymentMethods'
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
     * Activate a pending subscription
     */
    public function activate(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);
        
        DB::transaction(function () use ($subscription) {
            // Cancel any other active subscriptions for this institution
            $subscription->institution->subscriptions()
                ->where('id', '!=', $subscription->id)
                ->where('status', 'active')
                ->update(['status' => 'cancelled']);
            
            // Activate this subscription
            $subscription->update([
                'status' => 'active',
                'payment_status' => 'paid',
                'starts_at' => now(),
                'ends_at' => now()->addMonth(),
            ]);
            
            // Update institution
            $subscription->institution->update([
                'subscription_tier' => $subscription->plan,
                'subscription_expires_at' => $subscription->ends_at,
                'subscription_status' => 'active',
            ]);
        });
        
        return redirect()->back()->with('success', 'Subscription activated successfully!');
    }
    
    /**
     * Cancel a subscription
     */
    public function cancel($id)
    {
        $subscription = Subscription::findOrFail($id);
        
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'auto_renew' => false,
        ]);
        
        // Update institution
        $subscription->institution->update([
            'subscription_status' => 'cancelled',
        ]);
        
        return redirect()->back()->with('warning', 'Subscription cancelled.');
    }
    
    /**
     * Mark a subscription as expired
     */
    public function markExpired($id)
    {
        $subscription = Subscription::findOrFail($id);
        
        $subscription->update([
            'status' => 'expired',
        ]);
        
        $subscription->institution->update([
            'subscription_status' => 'expired',
            'subscription_tier' => 'free',
        ]);
        
        return redirect()->back()->with('info', 'Subscription marked as expired.');
    }
    
    /**
     * Override subscription plan (Super Admin only)
     */
    public function override(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);
        
        $request->validate([
            'plan' => 'required|in:basic,premium,enterprise',
            'period' => 'required|in:monthly,quarterly,semi_annual,annual',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|in:mpesa,tigopesa,halopesa,pesapal,bank',
            'note' => 'nullable|string|max:500',
        ]);
        
        $endDate = $this->calculateExpiry(now(), $request->period);
        
        DB::transaction(function () use ($subscription, $request, $endDate) {
            // Cancel existing
            $subscription->status = 'cancelled';
            $subscription->save();
            
            // Create new subscription with overridden plan
            $newSubscription = Subscription::create([
                'subscribable_type' => Institution::class,
                'subscribable_id' => $subscription->institution_id,
                'institution_id' => $subscription->institution_id,
                'plan' => $request->plan,
                'amount' => $request->amount,
                'status' => 'active',
                'payment_status' => 'paid',
                'payment_method' => $request->payment_method ?? $subscription->payment_method,
                'starts_at' => now(),
                'ends_at' => $endDate,
                'auto_renew' => false,
                'transaction_reference' => 'OVERRIDE-' . Str::random(12),
                'mpesa_response_description' => $request->note ?? 'Overridden by Super Admin',
            ]);
            
            // Update institution
            $subscription->institution->update([
                'subscription_tier' => $request->plan,
                'subscription_expires_at' => $endDate,
                'subscription_status' => 'active',
                'subscription_price_paid' => $request->amount,
            ]);
        });
        
        return redirect()->route('super-admin.subscriptions.show', $subscription->id)
            ->with('success', 'Subscription overridden successfully! New plan: ' . ucfirst($request->plan));
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
            $subscription = Subscription::find($id);
            
            if (!$subscription) continue;
            
            switch ($request->action) {
                case 'activate':
                    $subscription->update(['status' => 'active']);
                    $subscription->institution->update(['subscription_status' => 'active']);
                    break;
                case 'cancel':
                    $subscription->update(['status' => 'cancelled', 'cancelled_at' => now()]);
                    $subscription->institution->update(['subscription_status' => 'cancelled']);
                    break;
                case 'expire':
                    $subscription->update(['status' => 'expired']);
                    $subscription->institution->update(['subscription_status' => 'expired']);
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
        
        return response()->stream(
            function() use ($handle) {},
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
    
    /**
     * Calculate expiry date
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
}