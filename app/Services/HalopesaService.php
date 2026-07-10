<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HalopesaService
{
    protected $apiKey;
    protected $apiSecret;
    protected $baseUrl;
    protected $callbackUrl;
    
    public function __construct()
    {
        $this->apiKey = config('services.halopesa.api_key');
        $this->apiSecret = config('services.halopesa.api_secret');
        $this->baseUrl = config('services.halopesa.environment') === 'production'
            ? 'https://api.halopesa.co.tz'
            : 'https://sandbox.halopesa.co.tz';
        $this->callbackUrl = config('services.halopesa.callback_url');
    }
    
    public function initiatePayment(string $phoneNumber, float $amount, string $reference)
    {
        $accessToken = $this->getAccessToken();
        
        $payload = [
            'msisdn' => $this->formatPhoneNumber($phoneNumber),
            'amount' => (int) $amount,
            'reference' => $reference,
            'callback' => $this->callbackUrl,
            'narration' => 'Subscription Payment',
        ];
        
        $response = Http::withToken($accessToken)
            ->post($this->baseUrl . '/api/v1/payment/request', $payload);
            
        if ($response->successful()) {
            return $response->json();
        }
        
        Log::error('HaloPesa Payment Failed', ['response' => $response->body()]);
        throw new \Exception('HaloPesa payment failed: ' . $response->body());
    }
    
    public function queryStatus(string $transactionId)
    {
        $accessToken = $this->getAccessToken();
        
        $response = Http::withToken($accessToken)
            ->get($this->baseUrl . '/api/v1/payment/query/' . $transactionId);
            
        if ($response->successful()) {
            return $response->json();
        }
        
        throw new \Exception('Failed to query HaloPesa status');
    }
    
    protected function getAccessToken()
    {
        $response = Http::withBasicAuth($this->apiKey, $this->apiSecret)
            ->post($this->baseUrl . '/api/v1/auth/token');
            
        if ($response->successful()) {
            return $response->json()['access_token'];
        }
        
        throw new \Exception('Failed to get HaloPesa access token');
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