<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPayment;
use App\Models\User;
use App\Models\Institution;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Create a new subscription for a user or institution
     */
    public function createSubscription($subscribable, int $planId, string $paymentMethod): Subscription
    {
        $plan = SubscriptionPlan::findOrFail($planId);
        
        DB::beginTransaction();
        
        try {
            // Cancel any existing active subscription
            $this->cancelExistingSubscription($subscribable);
            
            // Create new subscription
            $subscription = Subscription::create([
                'subscribable_type' => get_class($subscribable),
                'subscribable_id' => $subscribable->id,
                'subscription_plan_id' => $planId,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'auto_renew' => true,
                'trial_ends_at' => $this->calculateTrialEndDate(),
            ]);
            
            DB::commit();
            
            return $subscription;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Activate a subscription after payment
     */
    public function activateSubscription(Subscription $subscription, ?string $gatewayId = null): void
    {
        DB::beginTransaction();
        
        try {
            if ($gatewayId) {
                $subscription->gateway_subscription_id = $gatewayId;
            }
            
            $subscription->activate();
            
            // Create first payment record
            $this->createPaymentRecord($subscription);
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Process recurring payment for a subscription
     */
    public function processRecurringPayment(Subscription $subscription): ?SubscriptionPayment
    {
        if (!$subscription->isActive()) {
            return null;
        }
        
        DB::beginTransaction();
        
        try {
            $payment = $this->createPaymentRecord($subscription);
            
            // Update subscription dates for next period
            $subscription->start_date = Carbon::now();
            $subscription->end_date = Carbon::now()->addMonth();
            $subscription->save();
            
            DB::commit();
            
            return $payment;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Cancel a subscription
     */
    public function cancelSubscription(Subscription $subscription): void
    {
        DB::beginTransaction();
        
        try {
            $subscription->cancel();
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Get all available plans
     */
    public function getAvailablePlans(): array
    {
        return SubscriptionPlan::active()
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get()
            ->toArray();
    }
    
    /**
     * Check if a user/institution has access to a feature
     */
    public function hasFeature($subscribable, string $feature): bool
    {
        $subscription = $this->getActiveSubscription($subscribable);
        
        if (!$subscription) {
            // Check free tier limits
            return $this->checkFreeTierLimit($subscribable, $feature);
        }
        
        $features = $subscription->plan->features ?? [];
        
        return isset($features[$feature]) && $features[$feature] === true;
    }
    
    /**
     * Get active subscription for a user/institution
     */
    public function getActiveSubscription($subscribable): ?Subscription
    {
        return Subscription::where('subscribable_type', get_class($subscribable))
            ->where('subscribable_id', $subscribable->id)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>', Carbon::now());
            })
            ->first();
    }
    
    /**
     * Cancel any existing active subscription
     */
    private function cancelExistingSubscription($subscribable): void
    {
        $existing = $this->getActiveSubscription($subscribable);
        
        if ($existing) {
            $existing->cancel();
        }
    }
    
    /**
     * Calculate trial end date (7 days from now)
     */
    private function calculateTrialEndDate(): ?Carbon
    {
        return Carbon::now()->addDays(7);
    }
    
    /**
     * Create payment record for subscription
     */
    private function createPaymentRecord(Subscription $subscription): SubscriptionPayment
    {
        return SubscriptionPayment::create([
            'subscription_id' => $subscription->id,
            'invoice_number' => (new SubscriptionPayment())->generateInvoiceNumber(),
            'amount' => $subscription->plan->price,
            'currency' => $subscription->plan->currency,
            'status' => 'pending',
            'billing_period_start' => $subscription->start_date ?? Carbon::now(),
            'billing_period_end' => $subscription->end_date ?? Carbon::now()->addMonth(),
            'payment_method' => $subscription->payment_method,
        ]);
    }
    
    /**
     * Check free tier limits
     */
    private function checkFreeTierLimit($subscribable, string $feature): bool
    {
        $freeLimits = [
            'max_books' => 5,
            'max_users' => 10,
            'download_pdf' => false,
            'ai_assistant' => false,
            'analytics' => false,
        ];
        
        return $freeLimits[$feature] ?? false;
    }
}