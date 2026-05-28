<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MultiPaymentController extends Controller
{
    protected $paymentGateway;

    public function __construct(PaymentGatewayService $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    /**
     * Show payment methods page
     */
  public function showMethods(Request $request)
{
    $user = auth()->user();
    $gateways = $this->paymentGateway->getEnabledGateways();
    
    // Get recent payments
    $payments = Payment::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->limit(20)
        ->get();
        
    // Get recent transactions
    $transactions = Transaction::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->limit(20)
        ->get();
    
    // For redirect after payment
    $suggestedAmount = $request->query('amount');
    $bookId = $request->query('book_id');
    $redirectTo = $request->query('redirect');
        
    return view('payment.methods', compact('user', 'gateways', 'payments', 'transactions', 'suggestedAmount', 'bookId', 'redirectTo'));
}

    /**
     * Initiate payment
     */
    public function initiatePayment(Request $request)
{
    $request->validate([
        'gateway' => 'required|in:mpesa,tigopesa,halopesa,card,bank',
        'amount' => 'required|numeric|min:100',
        'phone' => 'required_if:gateway,mpesa,tigopesa,halopesa|nullable|string',
    ]);

    $user = auth()->user();
    $amount = $request->amount;
    $gateway = $request->gateway;
    $reference = 'DEP_' . time() . '_' . $user->id . '_' . Str::random(6);
    
    // Create pending payment record
    $payment = Payment::create([
        'user_id' => $user->id,
        'payable_type' => 'App\\Models\\User',
        'payable_id' => $user->id,
        'amount' => $amount,
        'status' => 'pending',
        'reference' => $reference,
        'method' => $gateway,
        'transaction_id' => $reference,
    ]);
    
    // Process payment through gateway
    $result = $this->paymentGateway->processPayment(
        $gateway,
        $user,
        $amount,
        'wallet_deposit',
        $reference,
        ['phone' => $request->phone, 'payment_id' => $payment->id]
    );
    
    if (!$result['success']) {
        $payment->markAsFailed();
        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }
    
    // Update payment with gateway response
    $payment->update([
        'transaction_id' => $result['transaction_id'] ?? $result['checkout_request_id'] ?? $reference,
        'payment_details' => $result
    ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Payment initiated successfully',
        'requires_approval' => $gateway === 'bank',
        'bank_details' => $result['bank_details'] ?? null,
        'client_secret' => $result['client_secret'] ?? null,
        'payment_intent_id' => $result['payment_intent_id'] ?? null,
        'payment_id' => $payment->id,
        'gateway' => $gateway
    ]);
}
    /**
     * Check payment status
     *//**
 * Check payment status
 */
public function checkStatus($paymentId)
{
     $payment = Payment::where('id', $paymentId)
        ->where('user_id', auth()->id())
        ->first();
        
    if (!$payment) {
        return response()->json(['success' => false, 'message' => 'Payment not found']);
    }
    
    // TEMPORARY: Auto-complete for sandbox/testing environment
    // Remove this when you have real API credentials
    if (env('APP_ENV') === 'local' && $payment->status === 'pending') {
        $payment->status = 'completed';
        $payment->save();
        
        $user = $payment->user;
        $oldBalance = $user->wallet_balance;
        $user->wallet_balance = $oldBalance + $payment->amount;
        $user->save();
        
        // Create transaction record
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'type' => 'credit',
            'amount' => $payment->amount,
            'balance_after' => $user->wallet_balance,
            'description' => $payment->method . ' deposit',
            'reference' => $payment->reference,
            'status' => 'completed',
            'method' => $payment->method,
            'payable_type' => 'App\\Models\\User',
            'payable_id' => $user->id,
        ]);
    }
    
    return response()->json([
        'success' => true,
        'status' => $payment->status,
        'amount' => $payment->amount,
        'completed_at' => $payment->updated_at
    ]);
}

    /**
     * Handle M-Pesa callback
     */
    public function mpesaCallback(Request $request)
    {
        $data = $request->all();
        Log::info('M-Pesa Callback', $data);
        
        if (isset($data['Body']['stkCallback'])) {
            $callback = $data['Body']['stkCallback'];
            $checkoutRequestID = $callback['CheckoutRequestID'];
            $resultCode = $callback['ResultCode'];
            
            // Find payment by transaction_id (checkoutRequestID stored)
            $payment = Payment::where('transaction_id', $checkoutRequestID)->first();
            
            if ($payment && $resultCode == 0) {
                DB::transaction(function () use ($payment, $callback) {
                    // Mark payment as completed
                    $payment->markAsCompleted();
                    
                    // Get amount from callback metadata
                    $amount = $payment->amount;
                    foreach ($callback['CallbackMetadata']['Item'] as $item) {
                        if ($item['Name'] == 'Amount') {
                            $amount = $item['Value'];
                            break;
                        }
                    }
                    
                    // Create transaction record for wallet credit
                    Transaction::create([
                        'user_id' => $payment->user_id,
                        'type' => 'credit',
                        'amount' => $amount,
                        'balance_after' => $payment->user->wallet_balance + $amount,
                        'description' => 'M-Pesa deposit',
                        'reference' => $payment->reference,
                        'status' => 'completed',
                        'method' => 'mpesa',
                        'payable_type' => User::class,
                        'payable_id' => $payment->user_id,
                    ]);
                    
                    // Add to wallet
                    $user = User::find($payment->user_id);
                    $user->incrementWallet($amount);
                });
            } elseif ($payment) {
                $payment->markAsFailed();
            }
        }
        
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    /**
     * Handle TigoPesa callback
     */
    public function tigopesaCallback(Request $request)
    {
        Log::info('TigoPesa Callback', $request->all());
        
        $data = $request->all();
        $transactionId = $data['transaction_id'] ?? $data['reference'] ?? null;
        
        if ($transactionId) {
            $payment = Payment::where('transaction_id', $transactionId)->first();
            
            if ($payment && ($data['status'] ?? '') === 'success') {
                DB::transaction(function () use ($payment) {
                    $payment->markAsCompleted();
                    
                    Transaction::create([
                        'user_id' => $payment->user_id,
                        'type' => 'credit',
                        'amount' => $payment->amount,
                        'balance_after' => $payment->user->wallet_balance + $payment->amount,
                        'description' => 'TigoPesa deposit',
                        'reference' => $payment->reference,
                        'status' => 'completed',
                        'method' => 'tigopesa',
                        'payable_type' => User::class,
                        'payable_id' => $payment->user_id,
                    ]);
                    
                    $user = User::find($payment->user_id);
                    $user->incrementWallet($payment->amount);
                });
            } elseif ($payment) {
                $payment->markAsFailed();
            }
        }
        
        return response()->json(['status' => 'success']);
    }

    /**
     * Handle HaloPesa callback
     */
    public function halopesaCallback(Request $request)
    {
        Log::info('HaloPesa Callback', $request->all());
        
        $data = $request->all();
        $transactionId = $data['transaction_id'] ?? $data['reference'] ?? null;
        
        if ($transactionId) {
            $payment = Payment::where('transaction_id', $transactionId)->first();
            
            if ($payment && ($data['status'] === 'success' || ($data['ResultCode'] ?? '') === '0')) {
                DB::transaction(function () use ($payment) {
                    $payment->markAsCompleted();
                    
                    Transaction::create([
                        'user_id' => $payment->user_id,
                        'type' => 'credit',
                        'amount' => $payment->amount,
                        'balance_after' => $payment->user->wallet_balance + $payment->amount,
                        'description' => 'HaloPesa deposit',
                        'reference' => $payment->reference,
                        'status' => 'completed',
                        'method' => 'halopesa',
                        'payable_type' => User::class,
                        'payable_id' => $payment->user_id,
                    ]);
                    
                    $user = User::find($payment->user_id);
                    $user->incrementWallet($payment->amount);
                });
            } elseif ($payment) {
                $payment->markAsFailed();
            }
        }
        
        return response()->json(['status' => 'success']);
    }

    /**
     * Handle Stripe webhook
     */
    public function stripeWebhook(Request $request)
    {
        $payload = $request->all();
        Log::info('Stripe Webhook', $payload);
        
        if ($payload['type'] === 'payment_intent.succeeded') {
            $paymentIntent = $payload['data']['object'];
            $paymentId = $paymentIntent['metadata']['payment_id'] ?? null;
            
            if ($paymentId) {
                $payment = Payment::find($paymentId);
                if ($payment && $payment->status === 'pending') {
                    DB::transaction(function () use ($payment) {
                        $payment->markAsCompleted();
                        
                        Transaction::create([
                            'user_id' => $payment->user_id,
                            'type' => 'credit',
                            'amount' => $payment->amount,
                            'balance_after' => $payment->user->wallet_balance + $payment->amount,
                            'description' => 'Card payment',
                            'reference' => $payment->reference,
                            'status' => 'completed',
                            'method' => 'card',
                            'payable_type' => User::class,
                            'payable_id' => $payment->user_id,
                        ]);
                        
                        $user = User::find($payment->user_id);
                        $user->incrementWallet($payment->amount);
                    });
                }
            }
        }
        
        return response()->json(['status' => 'success']);
    }

    /**
     * Save user's payment details
     */
    public function savePaymentDetails(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'mpesa_phone' => 'nullable|string',
            'tigopesa_phone' => 'nullable|string',
            'halopesa_phone' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'bank_account_name' => 'nullable|string',
        ]);
        
        $user->update($request->only([
            'mpesa_phone', 'tigopesa_phone', 'halopesa_phone',
            'bank_name', 'bank_account_number', 'bank_account_name'
        ]));
        
        return response()->json(['success' => true, 'message' => 'Payment details saved']);
    }
}