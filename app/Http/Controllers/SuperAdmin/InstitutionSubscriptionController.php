<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Institution;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class InstitutionSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Institution::with(['activeSubscription']);
        
        // Search filter
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by subscription status
        if ($request->subscription_status && $request->subscription_status !== 'all') {
            if ($request->subscription_status === 'active') {
                $query->whereHas('subscriptions', function($q) {
                    $q->where('status', 'active')
                        ->where(function($sub) {
                            $sub->whereNull('ends_at')
                                ->orWhere('ends_at', '>', now());
                        });
                });
            } elseif ($request->subscription_status === 'expired') {
                $query->whereDoesntHave('subscriptions', function($q) {
                    $q->where('status', 'active')
                        ->where(function($sub) {
                            $sub->whereNull('ends_at')
                                ->orWhere('ends_at', '>', now());
                        });
                });
            } elseif ($request->subscription_status === 'pending') {
                $query->whereHas('subscriptions', function($q) {
                    $q->where('status', 'pending');
                });
            } elseif ($request->subscription_status === 'cancelled') {
                $query->whereHas('subscriptions', function($q) {
                    $q->where('status', 'cancelled');
                });
            }
        }
        
        // Filter by plan
        if ($request->plan && $request->plan !== 'all') {
            $query->whereHas('subscriptions', function($q) use ($request) {
                $q->where('plan', $request->plan)->where('status', 'active');
            });
        }
        
        // Filter by institution type
        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        $institutions = $query->latest()->paginate(20);
        
        // Stats
        $total = Institution::count();
        $active = Institution::whereHas('subscriptions', function($q) {
            $q->where('status', 'active')
                ->where(function($sub) {
                    $sub->whereNull('ends_at')->orWhere('ends_at', '>', now());
                });
        })->count();
        
        $stats = [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'expiring_soon' => Institution::whereHas('subscriptions', function($q) {
                $q->where('status', 'active')
                    ->where('ends_at', '<=', now()->addDays(7))
                    ->where('ends_at', '>', now());
            })->count(),
            'pending' => Institution::whereHas('subscriptions', function($q) {
                $q->where('status', 'pending');
            })->count(),
            'cancelled' => Institution::whereHas('subscriptions', function($q) {
                $q->where('status', 'cancelled');
            })->count(),
            'plan_distribution' => [],
            'revenue_by_plan' => [],
        ];
        
        $plans = ['basic', 'premium', 'enterprise'];
        
        return view('super-admin.institutions.subscriptions', compact('institutions', 'stats', 'plans'));
    }
    
    public function show($id)
    {
        $institution = Institution::with(['subscriptions' => function($q) {
            $q->latest();
        }, 'activeSubscription'])->findOrFail($id);
        
        $allSubscriptions = $institution->subscriptions()->latest()->paginate(10);
        $plans = ['basic', 'premium', 'enterprise'];
        
        return view('super-admin.institutions.subscription-detail', compact('institution', 'allSubscriptions', 'plans'));
    }
    
    public function assign(Request $request, $id)
    {
        $institution = Institution::findOrFail($id);
        
        $request->validate([
            'plan' => 'required|in:basic,premium,enterprise',
            'period' => 'required|in:monthly,quarterly,semi_annual,annual',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,pending',
        ]);
        
        $endDate = $this->calculateExpiry(now(), $request->period);
        
        DB::transaction(function () use ($institution, $request, $endDate) {
            $institution->subscriptions()->where('status', 'active')->update(['status' => 'cancelled']);
            
            Subscription::create([
                'subscribable_type' => Institution::class,
                'subscribable_id' => $institution->id,
                'institution_id' => $institution->id,
                'plan' => $request->plan,
                'amount' => $request->amount,
                'status' => $request->status,
                'payment_status' => $request->status === 'active' ? 'paid' : 'pending',
                'payment_method' => 'manual',
                'starts_at' => now(),
                'ends_at' => $endDate,
                'auto_renew' => false,
                'transaction_reference' => 'ADMIN-' . Str::random(12),
            ]);
            
            $institution->update([
                'subscription_tier' => $request->plan,
                'subscription_expires_at' => $endDate,
                'subscription_status' => $request->status,
            ]);
        });
        
        return redirect()->route('super-admin.institutions.subscriptions.show', $institution->id)
            ->with('success', 'Subscription assigned successfully!');
    }
    
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'institution_ids' => 'required|array',
            'plan' => 'required|in:basic,premium,enterprise',
            'period' => 'required|in:monthly,quarterly,semi_annual,annual',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,pending',
        ]);
        
        $endDate = $this->calculateExpiry(now(), $request->period);
        $count = 0;
        $institutionIds = json_decode($request->institution_ids);
        
        foreach ($institutionIds as $id) {
            $institution = Institution::find($id);
            if (!$institution) continue;
            
            DB::transaction(function () use ($institution, $request, $endDate, &$count) {
                $institution->subscriptions()->where('status', 'active')->update(['status' => 'cancelled']);
                
                Subscription::create([
                    'subscribable_type' => Institution::class,
                    'subscribable_id' => $institution->id,
                    'institution_id' => $institution->id,
                    'plan' => $request->plan,
                    'amount' => $request->amount,
                    'status' => $request->status,
                    'payment_status' => $request->status === 'active' ? 'paid' : 'pending',
                    'payment_method' => 'manual',
                    'starts_at' => now(),
                    'ends_at' => $endDate,
                    'auto_renew' => false,
                    'transaction_reference' => 'BULK-' . Str::random(12),
                ]);
                
                $institution->update([
                    'subscription_tier' => $request->plan,
                    'subscription_expires_at' => $endDate,
                    'subscription_status' => $request->status,
                ]);
                
                $count++;
            });
        }
        
        return redirect()->route('super-admin.institutions.subscriptions.index')
            ->with('success', $count . ' institution(s) updated!');
    }
    
    public function activate($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->update(['status' => 'active', 'payment_status' => 'paid']);
        $subscription->institution->update(['subscription_status' => 'active']);
        
        return redirect()->back()->with('success', 'Subscription activated!');
    }
    
    public function cancel($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->update(['status' => 'cancelled', 'cancelled_at' => now()]);
        $subscription->institution->update(['subscription_status' => 'cancelled']);
        
        return redirect()->back()->with('warning', 'Subscription cancelled.');
    }
    
    public function destroy($id)
    {
        Subscription::findOrFail($id)->delete();
        return redirect()->back()->with('info', 'Subscription deleted.');
    }
    
    public function export(Request $request)
    {
        $query = Institution::with('activeSubscription');
        
        if ($request->subscription_status && $request->subscription_status !== 'all') {
            if ($request->subscription_status === 'active') {
                $query->whereHas('subscriptions', function($q) {
                    $q->where('status', 'active')
                        ->where(function($sub) {
                            $sub->whereNull('ends_at')->orWhere('ends_at', '>', now());
                        });
                });
            } else {
                $query->whereDoesntHave('subscriptions', function($q) {
                    $q->where('status', 'active')
                        ->where(function($sub) {
                            $sub->whereNull('ends_at')->orWhere('ends_at', '>', now());
                        });
                });
            }
        }
        
        $institutions = $query->get();
        
        $filename = 'institution_subscriptions_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        fputcsv($handle, ['Institution', 'Type', 'Plan', 'Status', 'Amount', 'Users', 'Books']);
        
        foreach ($institutions as $inst) {
            $sub = $inst->activeSubscription;
            fputcsv($handle, [
                $inst->name,
                $inst->type ?? 'N/A',
                $sub ? ucfirst($sub->plan) : 'None',
                $sub ? ucfirst($sub->status) : 'No Subscription',
                $sub ? $sub->amount : 0,
                $inst->users()->count(),
                $inst->books()->count(),
            ]);
        }
        
        fclose($handle);
        
        return response()->stream(
            function() use ($handle) {},
            200,
            ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="' . $filename . '"']
        );
    }
    
    private function calculateExpiry($startDate, $period)
    {
        $map = ['monthly' => 'addMonth', 'quarterly' => 'addMonths', 'semi_annual' => 'addMonths', 'annual' => 'addYear'];
        $counts = ['monthly' => 1, 'quarterly' => 3, 'semi_annual' => 6, 'annual' => 1];
        $method = $map[$period];
        $count = $counts[$period];
        return $method === 'addMonths' ? $startDate->copy()->addMonths($count) : $startDate->copy()->$method();
    }
}