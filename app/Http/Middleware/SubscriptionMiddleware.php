<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if ($user && $user->institution) {
            $subscription = $user->institution->activeSubscription;
            
            if ($subscription) {
                $daysLeft = $subscription->daysRemaining();
                
                // Show warning banners
                if ($daysLeft <= 7 && $daysLeft > 0) {
                    $level = $subscription->getExpirationWarningLevel();
                    
                    $message = match($level) {
                        'critical' => '⚠️ Your subscription expires TODAY! Please renew immediately.',
                        'urgent' => '⚠️ Your subscription expires in ' . $daysLeft . ' days! Please renew soon.',
                        'warning' => '📢 Your subscription expires in ' . $daysLeft . ' days.',
                        default => null,
                    };
                    
                    if ($message) {
                        session()->flash('subscription_warning', $message);
                        session()->flash('subscription_warning_level', $level);
                    }
                }
                
                // If expired, redirect to subscription page
                if ($subscription->isExpired()) {
                    session()->flash('subscription_expired', 'Your subscription has expired. Please renew to continue.');
                    
                    // Don't redirect if already on subscription pages
                    $allowedRoutes = ['institution.subscription.index', 'institution.subscription.history'];
                    if (!in_array($request->route()->getName(), $allowedRoutes)) {
                        // Still allow access but show banner
                    }
                }
            }
        }
        
        return $next($request);
    }
}