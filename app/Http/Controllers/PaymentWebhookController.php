<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Handle M-Pesa callback (real payment confirmation)
     */
    public function handleMpesaCallback(Request $request)
    {
        Log::info('M-Pesa Real Callback Received', $request->all());
        
        $data = $request->all();
        
        if (isset($data['Body']['stkCallback'])) {
            $callback = $data['Body']['stkCallback'];
            $checkoutRequestID = $callback['CheckoutRequestID'];
            $resultCode = $callback['ResultCode'];
            
            // Find the payment by checkout request ID
            $payment = Payment::where('transaction_id', $checkoutRequestID)->first();
            
            if ($payment && $resultCode == 0) {
                DB::transaction(function () use ($payment, $callback) {
                    // Get amount from callback
                    $amount = $payment->amount;
                    if (isset($callback['CallbackMetadata']['Item'])) {
                        foreach ($callback['CallbackMetadata']['Item'] as $item) {
                            if ($item['Name'] == 'Amount') {
                                $amount = $item['Value'];
                                break;
                            }
                        }
                    }
                    
                    // Update payment
                    $payment->status = 'completed';
                    $payment->save();
                    
                    // Update wallet
                    $user = User::find($payment->user_id);
                    $oldBalance = $user->wallet_balance;
                    $user->wallet_balance = $oldBalance + $amount;
                    $user->save();
                    
                    // Create transaction record
                    Transaction::create([
                        'user_id' => $user->id,
                        'type' => 'credit',
                        'amount' => $amount,
                        'balance_after' => $user->wallet_balance,
                        'description' => 'M-Pesa deposit - Real payment',
                        'reference' => $payment->reference,
                        'status' => 'completed',
                        'method' => 'mpesa',
                        'payable_type' => 'App\\Models\\User',
                        'payable_id' => $user->id,
                    ]);
                });
                
                return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
            }
        }
        
        return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Failed']);
    }
    
    /**
     * Handle Stripe webhook (real card payment)
     */
    public function handleStripeWebhook(Request $request)
    {
        Log::info('Stripe Webhook Received', $request->all());
        
        $payload = $request->all();
        
        if ($payload['type'] === 'payment_intent.succeeded') {
            $paymentIntent = $payload['data']['object'];
            $paymentId = $paymentIntent['metadata']['payment_id'] ?? null;
            
            if ($paymentId) {
                $payment = Payment::find($paymentId);
                
                if ($payment && $payment->status === 'pending') {
                    DB::transaction(function () use ($payment) {
                        $payment->status = 'completed';
                        $payment->save();
                        
                        $user = User::find($payment->user_id);
                        $oldBalance = $user->wallet_balance;
                        $user->wallet_balance = $oldBalance + $payment->amount;
                        $user->save();
                        
                        Transaction::create([
                            'user_id' => $user->id,
                            'type' => 'credit',
                            'amount' => $payment->amount,
                            'balance_after' => $user->wallet_balance,
                            'description' => 'Card payment - Real payment',
                            'reference' => $payment->reference,
                            'status' => 'completed',
                            'method' => 'card',
                            'payable_type' => 'App\\Models\\User',
                            'payable_id' => $user->id,
                        ]);
                    });
                }
            }
        }
        
        return response()->json(['status' => 'success']);
    }
    
    /**
     * Admin: Manually approve bank transfer
     */
    public function approveBankTransfer($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        
        if ($payment->method !== 'bank' || $payment->status !== 'pending') {
            return back()->with('error', 'Invalid payment');
        }
        
        DB::transaction(function () use ($payment) {
            $payment->status = 'completed';
            $payment->save();
            
            $user = User::find($payment->user_id);
            $oldBalance = $user->wallet_balance;
            $user->wallet_balance = $oldBalance + $payment->amount;
            $user->save();
            
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $payment->amount,
                'balance_after' => $user->wallet_balance,
                'description' => 'Bank transfer approved by admin',
                'reference' => $payment->reference,
                'status' => 'completed',
                'method' => 'bank',
                'payable_type' => 'App\\Models\\User',
                'payable_id' => $user->id,
            ]);
        });
        
        return back()->with('success', 'Payment approved and wallet credited');
    }
}