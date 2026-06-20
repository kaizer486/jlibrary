<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Services\SmsService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class PaymentWebhookController extends Controller
{
    protected $smsService;
    
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }
    
    /**
     * Handle M-Pesa callback with IDEMPOTENCY
     */
    public function handleMpesaCallback(Request $request): JsonResponse
    {
        Log::info('M-Pesa Callback Received', $request->all());
        
        $data = $request->all();
        
        if (!isset($data['Body']['stkCallback'])) {
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid callback']);
        }
        
        $callback = $data['Body']['stkCallback'];
        $checkoutRequestID = $callback['CheckoutRequestID'];
        $resultCode = $callback['ResultCode'];
        
        // IDEMPOTENCY CHECK - Check if already processed
        $existingPayment = Payment::where('transaction_id', $checkoutRequestID)
            ->where('status', 'completed')
            ->first();
        
        if ($existingPayment) {
            Log::warning('Duplicate M-Pesa callback ignored', [
                'checkout_id' => $checkoutRequestID,
                'payment_id' => $existingPayment->id
            ]);
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Already processed']);
        }
        
        // Find pending payment
        $payment = Payment::where('transaction_id', $checkoutRequestID)
            ->where('status', 'pending')
            ->first();
        
        if (!$payment) {
            Log::error('Payment not found for M-Pesa callback', ['checkout_id' => $checkoutRequestID]);
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Payment not found']);
        }
        
        // Set idempotency key to prevent race condition
        $idempotencyKey = 'mpesa_' . $checkoutRequestID . '_' . time();
        
        if ($resultCode == 0 && !$payment->webhook_processed_at) {
            DB::transaction(function () use ($payment, $callback, $idempotencyKey) {
                // Lock user row
                $user = User::where('id', $payment->user_id)->lockForUpdate()->first();
                
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
                $payment->idempotency_key = $idempotencyKey;
                $payment->webhook_processed_at = now();
                $payment->save();
                
                // Update wallet
                $oldBalance = $user->wallet_balance;
                $user->wallet_balance += $amount;
                $user->save();
                
                // Create transaction record
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'credit',
                    'amount' => $amount,
                    'balance_after' => $user->wallet_balance,
                    'description' => 'M-Pesa deposit',
                    'reference' => $payment->reference,
                    'status' => 'completed',
                    'method' => 'mpesa',
                    'payable_type' => 'App\\Models\\User',
                    'payable_id' => $user->id,
                ]);
                
                // Send SMS notification
                $this->sendPaymentSms($user, $amount, 'M-Pesa', $payment->reference);
            });
            
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
        }
        
        // Payment failed
        $payment->status = 'failed';
        $payment->idempotency_key = $idempotencyKey;
        $payment->save();
        
        return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Payment failed']);
    }
    
    /**
     * Handle TigoPesa callback
     */
    public function handleTigopesaCallback(Request $request): JsonResponse
    {
        Log::info('TigoPesa Callback Received', $request->all());
        
        $data = $request->all();
        $transactionId = $data['transaction_id'] ?? $data['TransactionID'] ?? null;
        $resultCode = $data['result_code'] ?? $data['ResultCode'] ?? 0;
        
        if (!$transactionId) {
            return response()->json(['status' => 'error', 'message' => 'Invalid callback'], 400);
        }
        
        // IDEMPOTENCY CHECK
        $existingPayment = Payment::where('transaction_id', $transactionId)
            ->where('status', 'completed')
            ->first();
        
        if ($existingPayment) {
            Log::warning('Duplicate TigoPesa callback ignored', ['transaction_id' => $transactionId]);
            return response()->json(['status' => 'already_processed']);
        }
        
        $payment = Payment::where('transaction_id', $transactionId)
            ->where('status', 'pending')
            ->first();
        
        if (!$payment) {
            Log::error('Payment not found for TigoPesa callback', ['transaction_id' => $transactionId]);
            return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
        }
        
        $idempotencyKey = 'tigopesa_' . $transactionId . '_' . time();
        
        if ($resultCode == 0 && !$payment->webhook_processed_at) {
            DB::transaction(function () use ($payment, $idempotencyKey) {
                $user = User::where('id', $payment->user_id)->lockForUpdate()->first();
                $amount = $payment->amount;
                
                $payment->status = 'completed';
                $payment->idempotency_key = $idempotencyKey;
                $payment->webhook_processed_at = now();
                $payment->save();
                
                $user->wallet_balance += $amount;
                $user->save();
                
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'credit',
                    'amount' => $amount,
                    'balance_after' => $user->wallet_balance,
                    'description' => 'TigoPesa deposit',
                    'reference' => $payment->reference,
                    'status' => 'completed',
                    'method' => 'tigopesa',
                    'payable_type' => 'App\\Models\\User',
                    'payable_id' => $user->id,
                ]);
                
                $this->sendPaymentSms($user, $amount, 'TigoPesa', $payment->reference);
            });
            
            return response()->json(['status' => 'success']);
        }
        
        $payment->status = 'failed';
        $payment->idempotency_key = $idempotencyKey;
        $payment->save();
        
        return response()->json(['status' => 'failed']);
    }
    
    /**
     * Handle HaloPesa callback
     */
    public function handleHalopesaCallback(Request $request): JsonResponse
    {
        Log::info('HaloPesa Callback Received', $request->all());
        
        $data = $request->all();
        $transactionId = $data['transaction_id'] ?? $data['TransactionReference'] ?? null;
        $resultCode = $data['status'] ?? $data['Status'] ?? 'pending';
        
        if (!$transactionId) {
            return response()->json(['status' => 'error', 'message' => 'Invalid callback'], 400);
        }
        
        // IDEMPOTENCY CHECK
        $existingPayment = Payment::where('transaction_id', $transactionId)
            ->where('status', 'completed')
            ->first();
        
        if ($existingPayment) {
            Log::warning('Duplicate HaloPesa callback ignored', ['transaction_id' => $transactionId]);
            return response()->json(['status' => 'already_processed']);
        }
        
        $payment = Payment::where('transaction_id', $transactionId)
            ->where('status', 'pending')
            ->first();
        
        if (!$payment) {
            Log::error('Payment not found for HaloPesa callback', ['transaction_id' => $transactionId]);
            return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
        }
        
        $idempotencyKey = 'halopesa_' . $transactionId . '_' . time();
        
        if ($resultCode == 'success' && !$payment->webhook_processed_at) {
            DB::transaction(function () use ($payment, $idempotencyKey) {
                $user = User::where('id', $payment->user_id)->lockForUpdate()->first();
                $amount = $payment->amount;
                
                $payment->status = 'completed';
                $payment->idempotency_key = $idempotencyKey;
                $payment->webhook_processed_at = now();
                $payment->save();
                
                $user->wallet_balance += $amount;
                $user->save();
                
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'credit',
                    'amount' => $amount,
                    'balance_after' => $user->wallet_balance,
                    'description' => 'HaloPesa deposit',
                    'reference' => $payment->reference,
                    'status' => 'completed',
                    'method' => 'halopesa',
                    'payable_type' => 'App\\Models\\User',
                    'payable_id' => $user->id,
                ]);
                
                $this->sendPaymentSms($user, $amount, 'HaloPesa', $payment->reference);
            });
            
            return response()->json(['status' => 'success']);
        }
        
        $payment->status = 'failed';
        $payment->idempotency_key = $idempotencyKey;
        $payment->save();
        
        return response()->json(['status' => 'failed']);
    }
    
    /**
     * Handle Stripe webhook with SIGNATURE VERIFICATION
     */
    public function handleStripeWebhook(Request $request): JsonResponse
    {
        Log::info('Stripe Webhook Received');
        
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('payments.gateways.card.webhook_secret');
        
        // VERIFY SIGNATURE 
        if (!$this->verifyStripeSignature($payload, $sigHeader, $webhookSecret)) {
            Log::error('Stripe webhook signature verification failed');
            return response()->json(['error' => 'Invalid signature'], 401);
        }
        
        $event = json_decode($payload, true);
        
        if (!$event || !isset($event['type'])) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }
        
        // IDEMPOTENCY CHECK
        $paymentIntentId = $event['data']['object']['id'] ?? null;
        
        if ($paymentIntentId) {
            $existingPayment = Payment::where('transaction_id', $paymentIntentId)
                ->where('status', 'completed')
                ->first();
            
            if ($existingPayment) {
                Log::warning('Duplicate Stripe webhook ignored', [
                    'payment_intent_id' => $paymentIntentId
                ]);
                return response()->json(['status' => 'already_processed']);
            }
        }
        
        if ($event['type'] === 'payment_intent.succeeded') {
            $paymentIntent = $event['data']['object'];
            $paymentId = $paymentIntent['metadata']['payment_id'] ?? null;
            
            if ($paymentId) {
                $this->processSuccessfulStripePayment($paymentId, $paymentIntent);
            }
        }
        
        return response()->json(['status' => 'success']);
    }
    
    /**
     * Handle Pesapal callback
     */
    public function handlePesapalCallback(Request $request): JsonResponse
    {
        Log::info('Pesapal Callback Received', $request->all());
        
        $orderTrackingId = $request->get('OrderTrackingId');
        $orderMerchantReference = $request->get('OrderMerchantReference');
        
        if (!$orderTrackingId) {
            return response()->json(['status' => 'error', 'message' => 'Invalid callback'], 400);
        }
        
        $payment = Payment::where('reference', $orderMerchantReference)
            ->where('status', 'pending')
            ->first();
        
        if (!$payment) {
            Log::error('Payment not found for Pesapal callback', ['reference' => $orderMerchantReference]);
            return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
        }
        
        $idempotencyKey = 'pesapal_' . $orderTrackingId . '_' . time();
        
        DB::transaction(function () use ($payment, $orderTrackingId, $idempotencyKey) {
            $user = User::where('id', $payment->user_id)->lockForUpdate()->first();
            $amount = $payment->amount;
            
            $payment->status = 'completed';
            $payment->transaction_id = $orderTrackingId;
            $payment->idempotency_key = $idempotencyKey;
            $payment->webhook_processed_at = now();
            $payment->save();
            
            $user->wallet_balance += $amount;
            $user->save();
            
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_after' => $user->wallet_balance,
                'description' => 'PesaPal deposit',
                'reference' => $payment->reference,
                'status' => 'completed',
                'method' => 'pesapal',
                'payable_type' => 'App\\Models\\User',
                'payable_id' => $user->id,
            ]);
            
            $this->sendPaymentSms($user, $amount, 'PesaPal', $payment->reference);
        });
        
        return response()->json(['status' => 'success']);
    }
    
    /**
     * Verify Stripe webhook signature
     */
    private function verifyStripeSignature($payload, $sigHeader, $webhookSecret): bool
    {
        if (empty($webhookSecret) || $webhookSecret === 'whsec_xxxxx') {
            Log::warning('Stripe webhook secret not configured - skipping verification');
            return true;
        }
        
        try {
            \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
            return true;
        } catch (\Exception $e) {
            Log::error('Stripe signature verification error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Process successful Stripe payment
     */
    private function processSuccessfulStripePayment($paymentId, $paymentIntent): void
    {
        $payment = Payment::find($paymentId);
        
        if (!$payment || $payment->status !== 'pending') {
            Log::warning('Stripe payment not found or already processed', ['payment_id' => $paymentId]);
            return;
        }
        
        $idempotencyKey = 'stripe_' . $paymentIntent['id'] . '_' . time();
        
        DB::transaction(function () use ($payment, $paymentIntent, $idempotencyKey) {
            $user = User::where('id', $payment->user_id)->lockForUpdate()->first();
            
            $payment->status = 'completed';
            $payment->transaction_id = $paymentIntent['id'];
            $payment->idempotency_key = $idempotencyKey;
            $payment->webhook_processed_at = now();
            $payment->save();
            
            $user->wallet_balance += $payment->amount;
            $user->save();
            
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $payment->amount,
                'balance_after' => $user->wallet_balance,
                'description' => 'Card payment via Stripe',
                'reference' => $payment->reference,
                'status' => 'completed',
                'method' => 'card',
                'payable_type' => 'App\\Models\\User',
                'payable_id' => $user->id,
            ]);
            
            $this->sendPaymentSms($user, $payment->amount, 'Stripe Card', $payment->reference);
        });
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
        
        $idempotencyKey = 'bank_' . $payment->id . '_' . time();
        
        DB::transaction(function () use ($payment, $idempotencyKey) {
            $payment->status = 'completed';
            $payment->idempotency_key = $idempotencyKey;
            $payment->webhook_processed_at = now();
            $payment->save();
            
            $user = User::where('id', $payment->user_id)->lockForUpdate()->first();
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
            
            $this->sendPaymentSms($user, $payment->amount, 'Bank Transfer', $payment->reference);
        });
        
        return back()->with('success', 'Payment approved and wallet credited');
    }
    
    /**
     * Send SMS notification for successful payment
     */
    private function sendPaymentSms($user, float $amount, string $method, string $reference): void
    {
        $phone = $user->mpesa_phone ?? $user->tigopesa_phone ?? $user->halopesa_phone ?? null;
        
        if ($phone) {
            $this->smsService->sendPaymentConfirmation($phone, $amount, $method, $reference);
        }
    }
}