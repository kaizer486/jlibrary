<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Book;
use App\Services\PaymentGatewayService;
use App\Services\PesapalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MultiPaymentController extends Controller
{
    protected $paymentGateway;
    protected $pesapalService;
    
    public function __construct(PaymentGatewayService $paymentGateway, PesapalService $pesapalService)
    {
        $this->paymentGateway = $paymentGateway;
        $this->pesapalService = $pesapalService;
    }
    
    /**
     * Initiate PesaPal payment for book purchase
     */
    public function initiatePesapalPayment(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $book = Book::findOrFail($request->book_id);
        $user = auth()->user();
        $amount = (float) $request->amount;

        // Create a unique reference
        $reference = 'BOOK-' . $book->id . '-' . time() . '-' . Str::random(6);

        // Create payment record - FIX: Add payable_type and payable_id
        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'reference' => $reference,
            'status' => 'pending',
            'method' => 'pesapal',
            'description' => 'Purchase book: ' . $book->title,
            'metadata' => json_encode(['book_id' => $book->id]),
            'payable_type' => 'App\\Models\\User',  // ADD THIS
            'payable_id' => $user->id,              // ADD THIS
        ]);

        // Build billing address using PesapalService
        $billingAddress = $this->pesapalService->buildBillingAddress($user);

        // Prepare order data for PesaPal
        $orderData = [
            'id' => $reference,
            'currency' => 'TZS',
            'amount' => $amount,
            'description' => 'Book Purchase: ' . $book->title,
            'callback_url' => route('payment.pesapal.callback', ['payment_id' => $payment->id]),
            'billing_address' => $billingAddress,
        ];

        // Submit order to PesaPal
        $result = $this->pesapalService->submitOrder($orderData);

        if ($result['success']) {
            // Update payment with tracking ID
            $payment->transaction_id = $result['order_tracking_id'];
            $payment->save();

            // Store pending purchase info
            session([
                'pending_pesapal_payment_id' => $payment->id,
                'pending_pesapal_book_id' => $book->id,
            ]);

            // Redirect to PesaPal payment page
            return redirect($result['redirect_url']);
        }

        return redirect()->back()->with('error', $result['message'] ?? 'Failed to initiate PesaPal payment');
    }

    /**
     * Handle Pesapal callback
     */
    public function pesapalCallback(Request $request)
    {
        Log::info('Pesapal Callback Received', $request->all());
        
        $orderTrackingId = $request->get('OrderTrackingId');
        $orderMerchantReference = $request->get('OrderMerchantReference');
        $paymentId = $request->get('payment_id');
        
        if (!$orderTrackingId) {
            return redirect()->route('wallet.index')->with('error', 'Invalid payment callback');
        }
        
        // Find payment by transaction_id or payment_id
        $payment = Payment::where('transaction_id', $orderTrackingId)
            ->orWhere('id', $paymentId)
            ->first();
        
        if (!$payment) {
            Log::error('Payment not found for PesaPal callback', [
                'tracking_id' => $orderTrackingId,
                'payment_id' => $paymentId
            ]);
            return redirect()->route('wallet.index')->with('error', 'Payment not found');
        }
        
        // Check if already processed
        if ($payment->status === 'completed') {
            return $this->handleSuccessfulPesapalPayment($payment);
        }
        
        // Verify payment status with PesaPal
        $statusResult = $this->pesapalService->getOrderStatus($orderTrackingId);
        
        if ($statusResult['success']) {
            $status = strtoupper($statusResult['status']);
            
            if ($status === 'COMPLETED' || $status === 'SUCCESS') {
                // Process successful payment
                DB::transaction(function () use ($payment, $orderTrackingId) {
                    $user = User::where('id', $payment->user_id)->lockForUpdate()->first();
                    
                    $payment->status = 'completed';
                    $payment->transaction_id = $orderTrackingId;
                    $payment->webhook_processed_at = now();
                    $payment->save();
                    
                    // Credit user wallet
                    $user->wallet_balance += $payment->amount;
                    $user->save();
                    
                    // Create transaction record
                    Transaction::create([
                        'user_id' => $user->id,
                        'type' => 'credit',
                        'amount' => $payment->amount,
                        'balance_after' => $user->wallet_balance,
                        'description' => 'Deposit via PesaPal - Book Purchase',
                        'reference' => $payment->reference,
                        'status' => 'completed',
                        'method' => 'pesapal',
                        'payable_type' => 'App\\Models\\User',
                        'payable_id' => $user->id,
                    ]);
                    
                    // If it's a book purchase, add book to user's library
                    $metadata = json_decode($payment->metadata, true);
                    if (isset($metadata['book_id'])) {
                        $book = Book::find($metadata['book_id']);
                        if ($book) {
                            $user->books()->syncWithoutDetaching([$book->id]);
                        }
                    }
                });
                
                return $this->handleSuccessfulPesapalPayment($payment);
            } else {
                $payment->status = 'failed';
                $payment->save();
                
                return redirect()->route('payment.methods')->with('error', 'Payment failed: ' . $status);
            }
        }
        
        return redirect()->route('payment.methods')->with('error', 'Unable to verify payment status');
    }
    
    /**
     * Handle successful PesaPal payment
     */
    private function handleSuccessfulPesapalPayment($payment)
    {
        // Clear session
        session()->forget(['pending_pesapal_payment_id', 'pending_pesapal_book_id']);
        
        $metadata = json_decode($payment->metadata, true);
        $bookId = $metadata['book_id'] ?? null;
        
        if ($bookId) {
            $book = Book::find($bookId);
            if ($book) {
                // Redirect to success page or library
                return redirect()->route('library.show', $book->id)
                    ->with('success', 'Payment successful! Book added to your library.');
            }
        }
        
        return redirect()->route('wallet.index')->with('success', 'Payment successful! Wallet credited.');
    }

    /**
     * Handle Pesapal IPN (Instant Payment Notification)
     */
    public function pesapalIpn(Request $request)
    {
        Log::info('Pesapal IPN Received', $request->all());
        
        $orderTrackingId = $request->get('order_tracking_id');
        $orderMerchantReference = $request->get('order_merchant_reference');
        
        if ($orderTrackingId) {
            $statusResult = $this->pesapalService->getOrderStatus($orderTrackingId);
            
            if ($statusResult['success'] && strtoupper($statusResult['status']) === 'COMPLETED') {
                $payment = Payment::where('transaction_id', $orderTrackingId)
                    ->orWhere('reference', $orderMerchantReference)
                    ->first();
                
                if ($payment && $payment->status === 'pending') {
                    DB::transaction(function () use ($payment, $orderTrackingId) {
                        $user = User::where('id', $payment->user_id)->lockForUpdate()->first();
                        
                        $payment->status = 'completed';
                        $payment->transaction_id = $orderTrackingId;
                        $payment->webhook_processed_at = now();
                        $payment->save();
                        
                        $user->wallet_balance += $payment->amount;
                        $user->save();
                        
                        Transaction::create([
                            'user_id' => $user->id,
                            'type' => 'credit',
                            'amount' => $payment->amount,
                            'balance_after' => $user->wallet_balance,
                            'description' => 'Deposit via PesaPal (IPN)',
                            'reference' => $payment->reference,
                            'status' => 'completed',
                            'method' => 'pesapal',
                            'payable_type' => 'App\\Models\\User',
                            'payable_id' => $user->id,
                        ]);
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
            'payable_type' => 'App\\Models\\User',  // ADD THIS
            'payable_id' => $user->id,              // ADD THIS
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