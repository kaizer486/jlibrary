<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    protected $consumerKey;
    protected $consumerSecret;
    protected $passkey;
    protected $shortcode;
    protected $callbackUrl;
    protected $baseUrl;

    public function __construct()
    {
        $this->consumerKey = config('services.mpesa.consumer_key');
        $this->consumerSecret = config('services.mpesa.consumer_secret');
        $this->passkey = config('services.mpesa.passkey');
        $this->shortcode = config('services.mpesa.shortcode');
        $this->callbackUrl = config('services.mpesa.callback_url');
        $this->baseUrl = config('services.mpesa.environment') === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    public function getAccessToken()
    {
        $url = $this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials';
        
        $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->get($url);
            
        if ($response->successful()) {
            return $response->json()['access_token'];
        }
        
        Log::error('M-Pesa Auth Failed', ['response' => $response->body()]);
        throw new \Exception('Failed to get M-Pesa access token');
    }

    public function stkPush(string $phoneNumber, float $amount, string $accountReference, string $transactionDesc = 'Subscription Payment')
    {
        $accessToken = $this->getAccessToken();
        
        $phoneNumber = $this->formatPhoneNumber($phoneNumber);
        
        $timestamp = now()->format('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);
        
        $url = $this->baseUrl . '/mpesa/stkpush/v1/processrequest';
        
        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int) $amount,
            'PartyA' => $phoneNumber,
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $phoneNumber,
            'CallBackURL' => $this->callbackUrl,
            'AccountReference' => $accountReference,
            'TransactionDesc' => $transactionDesc,
        ];
        
        Log::info('M-Pesa STK Push Initiated', ['payload' => $payload]);
        
        $response = Http::withToken($accessToken)
            ->post($url, $payload);
            
        if ($response->successful()) {
            return $response->json();
        }
        
        Log::error('M-Pesa STK Push Failed', ['response' => $response->body()]);
        throw new \Exception('M-Pesa STK Push failed: ' . $response->body());
    }

    public function queryStatus(string $checkoutRequestId)
    {
        $accessToken = $this->getAccessToken();
        
        $timestamp = now()->format('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);
        
        $url = $this->baseUrl . '/mpesa/stkpushquery/v1/query';
        
        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ];
        
        $response = Http::withToken($accessToken)
            ->post($url, $payload);
            
        if ($response->successful()) {
            return $response->json();
        }
        
        Log::error('M-Pesa Query Status Failed', ['response' => $response->body()]);
        throw new \Exception('Failed to query M-Pesa status');
    }

    /**
     * Format phone number to Tanzanian format (255)
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any spaces, dashes, or special characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // If starts with 0 (e.g., 0712345678), replace with 255
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '255' . substr($phoneNumber, 1);
        }
        
        // If starts with +255, remove the +
        if (substr($phoneNumber, 0, 4) === '+255') {
            $phoneNumber = '255' . substr($phoneNumber, 4);
        }
        
        // If starts with 255, keep as is
        if (substr($phoneNumber, 0, 3) === '255') {
            return $phoneNumber;
        }
        
        // If it's 10 digits (0712345678), prepend 255
        if (strlen($phoneNumber) === 10) {
            return '255' . $phoneNumber;
        }
        
        // If it's 9 digits (712345678), prepend 255
        if (strlen($phoneNumber) === 9) {
            return '255' . $phoneNumber;
        }
        
        return $phoneNumber;
    }
}