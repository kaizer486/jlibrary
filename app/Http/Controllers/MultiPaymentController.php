<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MultiPaymentController extends Controller
{
    protected $paymentGateway;
    
    public function __construct(PaymentGatewayService $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }
    
/**
 * Handle Pesapal callback
 */
public function pesapalCallback(Request $request)
{
    Log::info('Pesapal Callback', $request->all());
    
    $orderTrackingId = $request->get('OrderTrackingId');
    $orderMerchantReference = $request->get('OrderMerchantReference');
    
    if (!$orderTrackingId) {
        return redirect()->route('wallet.index')->with('error', 'Invalid payment callback');
    }
    
    $pesapal = new \App\Services\PesapalService();
    $status = $pesapal->getOrderStatus($orderTrackingId);
    
    if ($status['success'] && $status['status'] === 'COMPLETED') {
        // Find payment by reference
        $payment = Payment::where('reference', $orderMerchantReference)->first();
        
        if ($payment && $payment->status === 'pending') {
            DB::transaction(function () use ($payment, $orderTrackingId) {
                $user = User::where('id', $payment->user_id)->lockForUpdate()->first();
                
                $payment->status = 'completed';
                $payment->transaction_id = $orderTrackingId;
                $payment->save();
                
                $user->wallet_balance += $payment->amount;
                $user->save();
                
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'credit',
                    'amount' => $payment->amount,
                    'balance_after' => $user->wallet_balance,
                    'description' => 'Deposit via PesaPal',
                    'reference' => $payment->reference,
                    'status' => 'completed',
                    'method' => 'pesapal',
                    'payable_type' => 'App\\Models\\User',
                    'payable_id' => $user->id,
                ]);
            });
            
            return redirect()->route('wallet.index')->with('success', 'Payment successful! Wallet credited.');
        }
    }
    
    return redirect()->route('payment.methods')->with('error', 'Payment failed or is pending.');
}

/**
 * Handle Pesapal IPN (Instant Payment Notification)
 */
public function pesapalIpn(Request $request)
{
    Log::info('Pesapal IPN', $request->all());
    
    $orderTrackingId = $request->get('order_tracking_id');
    
    if ($orderTrackingId) {
        $pesapal = new \App\Services\PesapalService();
        $status = $pesapal->getOrderStatus($orderTrackingId);
        
        if ($status['success'] && $status['status'] === 'COMPLETED') {
            $payment = Payment::where('transaction_id', $orderTrackingId)->first();
            
            if ($payment && $payment->status === 'pending') {
                // Process payment (same as callback)
                DB::transaction(function () use ($payment, $orderTrackingId) {
                    $user = User::where('id', $payment->user_id)->lockForUpdate()->first();
                    $payment->status = 'completed';
                    $payment->save();
                    $user->wallet_balance += $payment->amount;
                    $user->save();
                });
            }
        }
    }
    
    return response()->json(['status' => 'ok']);
}


    public function showMethods(Request $request)
    {
        $amount = $request->get('amount', 0);
        $suggestedAmount = $request->get('suggested', 0);
        
        $gateways = $this->paymentGateway->getEnabledGateways();
        $transactions = auth()->user()->transactions()->latest()->limit(5)->get();
        $totalDeposits = auth()->user()->transactions()->where('type', 'credit')->sum('amount');
        $totalSpent = auth()->user()->transactions()->where('type', 'debit')->sum('amount');
        
        return view('payment.methods', compact('gateways', 'amount', 'suggestedAmount', 'transactions', 'totalDeposits', 'totalSpent'));
    }
    
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'gateway' => 'required|string',
            'amount' => 'required|numeric|min:100|max:1000000',
            'phone' => 'nullable|string',
        ]);
        
        $user = auth()->user();
        $amount = $request->amount;
        $gateway = $request->gateway;
        $reference = 'PAY_' . Str::upper(Str::random(10)) . '_' . $user->id;
        
        // Create pending payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'status' => 'pending',
            'reference' => $reference,
            'method' => $gateway,
            'payable_type' => User::class,
            'payable_id' => $user->id,
        ]);
        
        $metadata = [
            'phone' => $request->phone,
            'payment_id' => $payment->id,
        ];
        
        $result = $this->paymentGateway->processPayment($gateway, $user, $amount, 'wallet_deposit', $reference, $metadata);
        
        if ($result['success']) {
            // Update payment with transaction ID
            if (isset($result['checkout_request_id'])) {
                $payment->transaction_id = $result['checkout_request_id'];
                $payment->save();
            }
            
            return response()->json([
                'success' => true,
                'payment_id' => $payment->id,
                'checkout_request_id' => $result['checkout_request_id'] ?? null,
                'client_secret' => $result['client_secret'] ?? null,
                'bank_details' => $result['bank_details'] ?? null,
                'message' => $result['message'] ?? 'Payment initiated successfully',
            ]);
        }
        
        $payment->status = 'failed';
        $payment->save();
        
        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Payment initiation failed',
        ], 400);
    }
    
    public function checkStatus($paymentId)
    {
        $payment = Payment::find($paymentId);
        
        if (!$payment) {
            return response()->json(['status' => 'not_found']);
        }
        
        return response()->json([
            'status' => $payment->status,
            'amount' => $payment->amount,
        ]);
    }
    
    public function savePaymentDetails(Request $request)
    {
        $user = auth()->user();
        
        $user->mpesa_phone = $request->mpesa_phone ?? $user->mpesa_phone;
        $user->tigopesa_phone = $request->tigopesa_phone ?? $user->tigopesa_phone;
        $user->halopesa_phone = $request->halopesa_phone ?? $user->halopesa_phone;
        $user->bank_name = $request->bank_name ?? $user->bank_name;
        $user->bank_account_number = $request->bank_account_number ?? $user->bank_account_number;
        $user->bank_account_name = $request->bank_account_name ?? $user->bank_account_name;
        $user->save();
        
        return response()->json(['success' => true]);
    }
}