<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TigopesaService
{
    protected $apiKey;
    protected $apiSecret;
    protected $baseUrl;
    protected $callbackUrl;
    
    public function __construct()
    {
        $this->apiKey = config('services.tigopesa.api_key');
        $this->apiSecret = config('services.tigopesa.api_secret');
        $this->baseUrl = config('services.tigopesa.environment') === 'production'
            ? 'https://api.tigopesa.co.tz'
            : 'https://sandbox.tigopesa.co.tz';
        $this->callbackUrl = config('services.tigopesa.callback_url');
    }
    
    public function initiatePayment(string $phoneNumber, float $amount, string $reference)
    {
        $accessToken = $this->getAccessToken();
        
        $payload = [
            'phone_number' => $this->formatPhoneNumber($phoneNumber),
            'amount' => (int) $amount,
            'reference' => $reference,
            'callback_url' => $this->callbackUrl,
            'description' => 'Subscription Payment',
        ];
        
        $response = Http::withToken($accessToken)
            ->post($this->baseUrl . '/api/v1/payment/initiate', $payload);
            
        if ($response->successful()) {
            return $response->json();
        }
        
        Log::error('TigoPesa Payment Failed', ['response' => $response->body()]);
        throw new \Exception('TigoPesa payment failed: ' . $response->body());
    }
    
    public function queryStatus(string $transactionId)
    {
        $accessToken = $this->getAccessToken();
        
        $response = Http::withToken($accessToken)
            ->get($this->baseUrl . '/api/v1/payment/status/' . $transactionId);
            
        if ($response->successful()) {
            return $response->json();
        }
        
        throw new \Exception('Failed to query TigoPesa status');
    }
    
    protected function getAccessToken()
    {
        $response = Http::withBasicAuth($this->apiKey, $this->apiSecret)
            ->post($this->baseUrl . '/api/v1/auth/token');
            
        if ($response->successful()) {
            return $response->json()['access_token'];
        }
        
        throw new \Exception('Failed to get TigoPesa access token');
    }
    
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '255' . substr($phoneNumber, 1);
        }
        
        if (substr($phoneNumber, 0, 4) === '+255') {
            $phoneNumber = '255' . substr($phoneNumber, 4);
        }
        
        if (strlen($phoneNumber) === 9) {
            $phoneNumber = '255' . $phoneNumber;
        }
        
        return $phoneNumber;
    }
}