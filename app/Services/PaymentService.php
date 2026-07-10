<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    protected $mpesaService;
    protected $tigopesaService;
    protected $halopesaService;
    protected $pesapalService;
    
    public function __construct(
        MpesaService $mpesaService,
        TigopesaService $tigopesaService,
        HalopesaService $halopesaService,
        PesapalService $pesapalService
    ) {
        $this->mpesaService = $mpesaService;
        $this->tigopesaService = $tigopesaService;
        $this->halopesaService = $halopesaService;
        $this->pesapalService = $pesapalService;
    }
    
    /**
     * Process payment based on method
     */
    public function processPayment(Subscription $subscription, string $method, array $data = [])
    {
        switch ($method) {
            case 'mpesa':
                return $this->processMpesa($subscription, $data);
            case 'tigopesa':
                return $this->processTigopesa($subscription, $data);
            case 'halopesa':
                return $this->processHalopesa($subscription, $data);
            case 'pesapal':
                return $this->processPesapal($subscription, $data);
            case 'bank':
                return $this->processBankTransfer($subscription, $data);
            default:
                throw new \Exception('Unsupported payment method: ' . $method);
        }
    }
    
    /**
     * Process M-Pesa STK Push
     */
    protected function processMpesa(Subscription $subscription, array $data)
    {
        $phoneNumber = $data['phone_number'] ?? null;
        
        if (!$phoneNumber) {
            throw new \Exception('Phone number required for M-Pesa');
        }
        
        $accountReference = 'SUB-' . $subscription->id . '-' . time();
        $transactionDesc = $subscription->plan . ' Plan Subscription';
        
        $result = $this->mpesaService->stkPush(
            $phoneNumber,
            $subscription->amount,
            $accountReference,
            $transactionDesc
        );
        
        // Update subscription with M-Pesa details
        $subscription->update([
            'payment_method' => 'mpesa',
            'payment_status' => 'pending',
            'status' => 'pending',
            'mpesa_checkout_request_id' => $result['CheckoutRequestID'],
            'mpesa_response_code' => $result['ResponseCode'] ?? '0',
            'mpesa_response_description' => $result['ResponseDescription'] ?? '',
        ]);
        
        // Create transaction record
        $this->createTransaction($subscription, 'mpesa', 'pending', [
            'checkout_request_id' => $result['CheckoutRequestID'],
            'phone_number' => $phoneNumber,
        ]);
        
        return [
            'success' => true,
            'message' => 'M-Pesa STK Push sent! Please check your phone.',
            'checkout_request_id' => $result['CheckoutRequestID'],
            'redirect_url' => route('institution.subscription.payment-status', $subscription->id)
        ];
    }
    
    /**
     * Process TigoPesa Payment
     */
    protected function processTigopesa(Subscription $subscription, array $data)
    {
        $phoneNumber = $data['phone_number'] ?? null;
        
        if (!$phoneNumber) {
            throw new \Exception('Phone number required for TigoPesa');
        }
        
        $result = $this->tigopesaService->initiatePayment(
            $phoneNumber,
            $subscription->amount,
            'SUB-' . $subscription->id
        );
        
        $subscription->update([
            'payment_method' => 'tigopesa',
            'payment_status' => 'pending',
            'status' => 'pending',
            'transaction_reference' => $result['transaction_id'] ?? null,
        ]);
        
        $this->createTransaction($subscription, 'tigopesa', 'pending', [
            'transaction_id' => $result['transaction_id'] ?? null,
            'phone_number' => $phoneNumber,
        ]);
        
        return [
            'success' => true,
            'message' => 'TigoPesa payment initiated! Please check your phone.',
            'redirect_url' => route('institution.subscription.payment-status', $subscription->id)
        ];
    }
    
    /**
     * Process HaloPesa Payment
     */
    protected function processHalopesa(Subscription $subscription, array $data)
    {
        $phoneNumber = $data['phone_number'] ?? null;
        
        if (!$phoneNumber) {
            throw new \Exception('Phone number required for HaloPesa');
        }
        
        $result = $this->halopesaService->initiatePayment(
            $phoneNumber,
            $subscription->amount,
            'SUB-' . $subscription->id
        );
        
        $subscription->update([
            'payment_method' => 'halopesa',
            'payment_status' => 'pending',
            'status' => 'pending',
            'transaction_reference' => $result['transaction_id'] ?? null,
        ]);
        
        $this->createTransaction($subscription, 'halopesa', 'pending', [
            'transaction_id' => $result['transaction_id'] ?? null,
            'phone_number' => $phoneNumber,
        ]);
        
        return [
            'success' => true,
            'message' => 'HaloPesa payment initiated! Please check your phone.',
            'redirect_url' => route('institution.subscription.payment-status', $subscription->id)
        ];
    }
    
    /**
     * Process PesaPal Payment (Redirect)
     */
    protected function processPesapal(Subscription $subscription, array $data)
    {
        $result = $this->pesapalService->initiatePayment(
            $subscription->id,
            $subscription->amount,
            $subscription->plan . ' Plan Subscription'
        );
        
        $subscription->update([
            'payment_method' => 'pesapal',
            'payment_status' => 'pending',
            'status' => 'pending',
            'transaction_reference' => $result['order_tracking_id'] ?? null,
        ]);
        
        $this->createTransaction($subscription, 'pesapal', 'pending', [
            'order_tracking_id' => $result['order_tracking_id'] ?? null,
            'redirect_url' => $result['redirect_url'] ?? null,
        ]);
        
        return [
            'success' => true,
            'message' => 'Redirecting to PesaPal...',
            'redirect_url' => $result['redirect_url'] ?? null,
            'is_redirect' => true
        ];
    }
    
    /**
     * Process Bank Transfer (Manual)
     */
    protected function processBankTransfer(Subscription $subscription, array $data)
    {
        $subscription->update([
            'payment_method' => 'bank',
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);
        
        $this->createTransaction($subscription, 'bank', 'pending', [
            'bank_details' => $this->getBankDetails(),
        ]);
        
        return [
            'success' => true,
            'message' => 'Bank transfer instructions sent.',
            'redirect_url' => route('institution.subscription.payment-instructions', $subscription->id),
            'bank_details' => $this->getBankDetails()
        ];
    }
    
    /**
     * Create payment transaction record
     */
    protected function createTransaction(Subscription $subscription, string $method, string $status, array $metadata = [])
    {
        return PaymentTransaction::create([
            'subscription_id' => $subscription->id,
            'institution_id' => $subscription->institution_id,
            'amount' => $subscription->amount,
            'payment_method' => $method,
            'status' => $status,
            'reference' => 'PAY-' . Str::random(12),
            'metadata' => $metadata,
            'paid_at' => $status === 'completed' ? now() : null,
        ]);
    }
    
    /**
     * Get bank details for manual transfer
     */
    protected function getBankDetails()
    {
        return [
            'bank_name' => 'CRDB Bank',
            'account_name' => 'JLIBRARY Subscription',
            'account_number' => '0123456789',
            'branch' => 'Dar es Salaam',
            'swift_code' => 'CRDBTZTZ',
        ];
    }
}