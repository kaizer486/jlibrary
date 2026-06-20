<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $paymentGateway;
    
    public function __construct(PaymentGatewayService $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }
    
    /**
     * Get available payment methods
     */
    public function getMethods()
    {
        $gateways = $this->paymentGateway->getEnabledGateways();
        
        return response()->json([
            'success' => true,
            'gateways' => $gateways
        ]);
    }
    
    /**
     * Initiate payment with selected gateway
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'gateway' => 'required|string|in:mpesa,tigopesa,halopesa,card,bank,pesapal',
            'amount' => 'required|numeric|min:100|max:1000000',
            'phone' => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        
        $user = Auth::user();
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
            'description' => $request->description ?? 'Wallet deposit',
        ];
        
        $result = $this->paymentGateway->processPayment(
            $gateway, $user, $amount, 'wallet_deposit', $reference, $metadata
        );
        
        if ($result['success']) {
            if (isset($result['checkout_request_id'])) {
                $payment->transaction_id = $result['checkout_request_id'];
                $payment->save();
            }
            
            return response()->json([
                'success' => true,
                'payment_id' => $payment->id,
                'reference' => $reference,
                'checkout_request_id' => $result['checkout_request_id'] ?? null,
                'redirect_url' => $result['redirect_url'] ?? null,
                'message' => $result['message'] ?? 'Payment initiated successfully'
            ]);
        }
        
        $payment->status = 'failed';
        $payment->save();
        
        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Payment initiation failed'
        ], 400);
    }
    
    /**
     * Check payment status
     */
    public function checkStatus($paymentId)
    {
        $payment = Payment::where('id', $paymentId)
            ->where('user_id', Auth::id())
            ->first();
        
        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'status' => $payment->status,
            'amount' => $payment->amount,
            'reference' => $payment->reference,
            'method' => $payment->method,
            'created_at' => $payment->created_at,
        ]);
    }
    
    /**
     * Get payment history
     */
    public function history(Request $request)
    {
        $payments = Payment::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('limit', 15));
        
        return response()->json([
            'success' => true,
            'data' => $payments->items(),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
            ]
        ]);
    }
    
    /**
     * Get single payment details
     */
    public function show($paymentId)
    {
        $payment = Payment::where('id', $paymentId)
            ->where('user_id', Auth::id())
            ->with('user')
            ->first();
        
        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'payment' => $payment
        ]);
    }
    
    /**
     * Cancel pending payment
     */
    public function cancel($paymentId)
    {
        $payment = Payment::where('id', $paymentId)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();
        
        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found or cannot be cancelled'
            ], 404);
        }
        
        $payment->status = 'failed';
        $payment->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Payment cancelled successfully'
        ]);
    }
}