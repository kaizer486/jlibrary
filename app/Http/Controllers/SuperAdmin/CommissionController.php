<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    /**
     * Show the Commission Dashboard (Settings + Requests)
     */
    public function index(Request $request)
    {
        $percentage = SiteSetting::where('key', 'platform_commission_percentage')->first()->value ?? 10;
        
        $pendingRequests = Order::where('commission_status', 'awaiting_verification')
            ->with(['book', 'author', 'user'])
            ->latest('commission_paid_at')
            ->paginate(15, ['*'], 'requests_page');

        // Show history of collected/rejected commissions
        $history = Order::whereIn('commission_status', ['collected', 'pending'])
            ->whereNotNull('commission_payment_method')
            ->with(['book', 'author'])
            ->latest('commission_paid_at')
            ->paginate(15, ['*'], 'history_page');

        return view('super-admin.commissions.index', compact('percentage', 'pendingRequests', 'history'));
    }
     public function deleteLog(Order $order)
    {
        // This clears the commission payment data, effectively deleting the log 
        // from the history while leaving the order itself intact.
        $order->update([
            'commission_status' => 'collected', // Keep it as collected so it doesn't go back to pending
            'commission_payment_method' => null,
            'commission_transaction_id' => null,
            'commission_paid_at' => null,
        ]);

        return back()->with('success', 'Commission log deleted permanently.');
    }

    /**
     * Update the global percentage
     */
    public function updateSetting(Request $request)
    {
        $request->validate(['percentage' => 'required|numeric|min:0|max:100']);

        SiteSetting::updateOrCreate(
            ['key' => 'platform_commission_percentage'],
            ['value' => $request->percentage, 'group' => 'commissions']
        );

        return back()->with('success', 'Platform commission percentage updated to ' . $request->percentage . '%');
    }

    /**
     * Super Admin verifies the seller's payment
     */
    public function verifyPayment(Request $request, Order $order)
    {
        $request->validate(['action' => 'required|in:approve,reject']);

        if ($request->action === 'approve') {
            $order->update(['commission_status' => 'collected']);
            return back()->with('success', 'Commission verified and marked as Collected.');
        } else {
            $order->update([
                'commission_status' => 'pending',
                'commission_transaction_id' => null,
                'commission_payment_method' => null,
                'commission_paid_at' => null,
            ]);
            return back()->with('warning', 'Payment rejected. Seller must try again.');
        }
    }
}