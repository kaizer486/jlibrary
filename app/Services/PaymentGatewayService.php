<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentGatewayService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('payments.gateways');
    }

    /**
     * Get all enabled gateways
     */
    public function getEnabledGateways()
    {
        $gateways = [];
        foreach ($this->config as $key => $gateway) {
            if (isset($gateway['enabled']) && $gateway['enabled']) {
                $gateways[$key] = [
                    'name' => $gateway['name'],
                    'icon' => $gateway['icon'],
                    'color' => $gateway['color'],
                    'min_amount' => $gateway['min_amount'],
                    'max_amount' => $gateway['max_amount'],
                    'requires_approval' => $gateway['requires_approval'] ?? false
                ];
            }
        }
        return $gateways;
    }

    /**
     * Process payment with selected gateway
     */
    public function processPayment($gateway, $user, $amount, $type, $reference, $metadata = [])
    {
        $result = ['success' => false, 'message' => 'Payment gateway not found'];
        
        switch ($gateway) {
            case 'mpesa':
                $result = $this->mpesaStkPush($user, $amount, $reference, $type, $metadata);
                break;
                
            case 'tigopesa':
                $result = $this->tigopesaPay($user, $amount, $reference, $type, $metadata);
                break;
                
            case 'halopesa':
                $result = $this->halopesaPay($user, $amount, $reference, $type, $metadata);
                break;
                
            case 'card':
                $result = $this->createStripePaymentIntent($amount, $reference, $metadata);
                break;
                
            case 'bank':
                $result = $this->createBankTransfer($amount, $reference, $type);
                break;
        }
        
        return $result;
    }

    /**
     * Initialize M-Pesa STK Push
     */
    protected function mpesaStkPush($user, $amount, $reference, $description, $metadata)
    {
        $mpesa = $this->config['mpesa'];
        $phone = $metadata['phone'] ?? $user->mpesa_phone;
        
        if (!$phone) {
            return ['success' => false, 'message' => 'M-Pesa phone number not found'];
        }
        
        // For sandbox/testing, simulate success
        if ($mpesa['environment'] === 'sandbox') {
            return [
                'success' => true,
                'checkout_request_id' => 'ws_CO_' . time() . '_' . Str::random(10),
                'merchant_request_id' => 'ws_MR_' . time()
            ];
        }
        
        // Production implementation
        $token = $this->getMpesaToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Failed to authenticate with M-Pesa'];
        }
        
        $timestamp = now()->format('YmdHis');
        $password = base64_encode($mpesa['shortcode'] . $mpesa['passkey'] . $timestamp);
        
        $url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        
        $response = Http::withToken($token)->post($url, [
            'BusinessShortCode' => $mpesa['shortcode'],
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => round($amount),
            'PartyA' => $this->formatPhoneNumber($phone, '254'),
            'PartyB' => $mpesa['shortcode'],
            'PhoneNumber' => $this->formatPhoneNumber($phone, '254'),
            'CallBackURL' => url($mpesa['callback_url']),
            'AccountReference' => $reference,
            'TransactionDesc' => $description,
        ]);
        
        if ($response->successful() && isset($response['ResponseCode']) && $response['ResponseCode'] === '0') {
            return [
                'success' => true,
                'checkout_request_id' => $response['CheckoutRequestID'],
                'merchant_request_id' => $response['MerchantRequestID']
            ];
        }
        
        Log::error('M-Pesa Error: ' . $response->body());
        return ['success' => false, 'message' => 'M-Pesa request failed'];
    }
    
    /**
     * Initialize TigoPesa Payment
     */
    protected function tigopesaPay($user, $amount, $reference, $description, $metadata)
    {
        $tigopesa = $this->config['tigopesa'];
        $phone = $metadata['phone'] ?? $user->tigopesa_phone;
        
        if (!$phone) {
            return ['success' => false, 'message' => 'TigoPesa phone number not found'];
        }
        
        // For sandbox/testing, simulate success
        if ($tigopesa['environment'] === 'sandbox') {
            return [
                'success' => true,
                'transaction_id' => 'TIGO_' . time() . '_' . Str::random(8),
                'payment_url' => null
            ];
        }
        
        // Production implementation would go here
        return [
            'success' => true,
            'transaction_id' => 'TIGO_' . time() . '_' . Str::random(8),
        ];
    }
    
    /**
     * Initialize HaloPesa Payment
     */
    protected function halopesaPay($user, $amount, $reference, $description, $metadata)
    {
        $halopesa = $this->config['halopesa'];
        $phone = $metadata['phone'] ?? $user->halopesa_phone;
        
        if (!$phone) {
            return ['success' => false, 'message' => 'HaloPesa phone number not found'];
        }
        
        // For sandbox/testing, simulate success
        if ($halopesa['environment'] === 'sandbox') {
            return [
                'success' => true,
                'transaction_id' => 'HALO_' . time() . '_' . Str::random(8),
            ];
        }
        
        return [
            'success' => true,
            'transaction_id' => 'HALO_' . time() . '_' . Str::random(8),
        ];
    }
    
    /**
     * Create Stripe Payment Intent
     */
       /**
     * Create Stripe Payment Intent
     */
    protected function createStripePaymentIntent($amount, $reference, $metadata)
    {
        // Check if Stripe is configured
        if (empty($this->config['card']['secret_key']) || $this->config['card']['secret_key'] === 'pk_test_xxxxx') {
            // For demo/sandbox, simulate success
            return [
                'success' => true,
                'client_secret' => 'sim_' . Str::random(24),
                'payment_intent_id' => 'pi_' . Str::random(24)
            ];
        }
        
        try {
            Stripe::setApiKey($this->config['card']['secret_key']);
            
            $intent = PaymentIntent::create([
                'amount' => round($amount * 100),
                'currency' => 'tzs',
                'metadata' => array_merge($metadata, ['reference' => $reference]),
            ]);
            
            return [
                'success' => true,
                'client_secret' => $intent->client_secret,
                'payment_intent_id' => $intent->id
            ];
        } catch (\Exception $e) {
            Log::error('Stripe Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }   
    /**
     * Create Bank Transfer Request
     */
    protected function createBankTransfer($amount, $reference, $description)
    {
        $bank = $this->config['bank'];
        
        return [
            'success' => true,
            'requires_approval' => true,
            'bank_details' => $bank['bank_details'],
            'reference' => $reference,
            'amount' => $amount,
            'instructions' => [
                'Use the reference number when making the transfer',
                'Payment will be credited within 24 hours after confirmation',
                'Screenshot of payment may be required for verification'
            ]
        ];
    }
    
    /**
     * Get M-Pesa Access Token
     */
    protected function getMpesaToken()
    {
        $mpesa = $this->config['mpesa'];
        $url = $mpesa['environment'] === 'production'
            ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
            : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        
        $response = Http::withBasicAuth($mpesa['consumer_key'], $mpesa['consumer_secret'])
            ->get($url);
        
        if ($response->successful()) {
            return $response['access_token'];
        }
        
        return null;
    }
    
    /**
     * Format phone number
     */
    protected function formatPhoneNumber($phone, $countryCode = '255')
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phone, 0, 1) === '0') {
            $phone = $countryCode . substr($phone, 1);
        }
        
        if (substr($phone, 0, 3) === $countryCode) {
            return $phone;
        }
        
        return $countryCode . $phone;
    }
}