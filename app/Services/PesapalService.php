<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PesapalService
{
    protected $consumerKey;
    protected $consumerSecret;
    protected $environment;
    protected $baseUrl;
    protected $ipnId;
    
    public function __construct()
    {
        $this->consumerKey = config('payments.gateways.pesapal.consumer_key');
        $this->consumerSecret = config('payments.gateways.pesapal.consumer_secret');
        $this->environment = config('payments.gateways.pesapal.environment', 'sandbox');
        
        // Correct Pesapal API v3 endpoints
        $this->baseUrl = $this->environment === 'production'
            ? 'https://pay.pesapal.com/v3'
            : 'https://cybqa.pesapal.com/pesapalv3';
        
        // Register IPN on instantiation
        $this->registerIpnUrl();
    }
    
    /**
     * Register IPN URL (Required for Pesapal)
     */
    protected function registerIpnUrl(): void
    {
        try {
            $token = $this->getToken();
            if (!$token) return;
            
            $callbackUrl = url(config('payments.gateways.pesapal.ipn_url', '/webhooks/pesapal'));
            
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/api/URLSetup/RegisterIPN', [
                'url' => $callbackUrl,
                'ipn_notification_type' => 'GET',
            ]);
            
            if ($response->successful() && isset($response['ipn_id'])) {
                $this->ipnId = $response['ipn_id'];
                Log::info('Pesapal IPN registered', ['ipn_id' => $this->ipnId]);
            } else {
                Log::warning('Pesapal IPN registration failed', ['response' => $response->body()]);
            }
            
        } catch (\Exception $e) {
            Log::error('Pesapal IPN exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Get authentication token from Pesapal
     */
    public function getToken(): ?string
    {
        try {
            $response = Http::timeout(30)->post($this->baseUrl . '/api/Auth/RequestToken', [
                'consumer_key' => $this->consumerKey,
                'consumer_secret' => $this->consumerSecret,
            ]);
            
            Log::info('Pesapal Token Response', [
                'status' => $response->status(),
                'success' => $response->successful()
            ]);
            
            if ($response->successful() && isset($response['token'])) {
                return $response['token'];
            }
            
            Log::error('Pesapal token error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return null;
            
        } catch (\Exception $e) {
            Log::error('Pesapal token exception: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Submit order to Pesapal
     */
    public function submitOrder(array $orderData): array
    {
        $token = $this->getToken();
        
        if (!$token) {
            return ['success' => false, 'message' => 'Failed to authenticate with Pesapal'];
        }
        
        // Prepare order request with IPN ID
        $requestData = [
            'id' => $orderData['id'],
            'currency' => $orderData['currency'],
            'amount' => $orderData['amount'],
            'description' => $orderData['description'],
            'callback_url' => $orderData['callback_url'],
            'notification_id' => $this->ipnId,
            'billing_address' => $orderData['billing_address'],
        ];
        
        try {
            $response = Http::timeout(60)->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/api/Transactions/SubmitOrderRequest', $requestData);
            
            Log::info('Pesapal Submit Order Response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'order_tracking_id' => $data['order_tracking_id'] ?? null,
                    'merchant_reference' => $data['merchant_reference'] ?? $orderData['id'],
                    'redirect_url' => $data['redirect_url'] ?? null,
                ];
            }
            
            $errorMsg = $response['error_description'] ?? $response['message'] ?? 'Payment submission failed';
            Log::error('Pesapal submit order error', [
                'status' => $response->status(),
                'error' => $errorMsg
            ]);
            return ['success' => false, 'message' => $errorMsg];
            
        } catch (\Exception $e) {
            Log::error('Pesapal submit order exception: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Get order status from Pesapal
     */
    public function getOrderStatus(string $orderTrackingId): array
    {
        $token = $this->getToken();
        
        if (!$token) {
            return ['success' => false, 'message' => 'Failed to authenticate'];
        }
        
        try {
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/api/Transactions/GetTransactionStatus', [
                'orderTrackingId' => $orderTrackingId,
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'status' => $data['status'] ?? 'PENDING',
                    'payment_method' => $data['payment_method'] ?? null,
                    'amount' => $data['amount'] ?? 0,
                    'message' => $data['message'] ?? null,
                ];
            }
            
            return ['success' => false, 'message' => 'Failed to get order status'];
            
        } catch (\Exception $e) {
            Log::error('Pesapal status exception: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Format phone number for Pesapal (Tanzania format)
     */
    public function formatPhoneNumber(?string $phone): string
    {
        if (!$phone) {
            return '255700000000';
        }
        
        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Remove leading 0
        if (substr($phone, 0, 1) === '0') {
            $phone = '255' . substr($phone, 1);
        }
        
        // Remove leading 2550 (double country code)
        if (substr($phone, 0, 4) === '2550') {
            $phone = '255' . substr($phone, 4);
        }
        
        // Ensure correct length (12 digits for Tanzania: 255 + 9 digits)
        if (strlen($phone) === 9) {
            $phone = '255' . $phone;
        }
        
        // Default fallback if still invalid
        if (strlen($phone) !== 12) {
            $phone = '255700000000';
        }
        
        return $phone;
    }
}