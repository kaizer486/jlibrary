<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $apiKey;
    protected $senderId;
    protected $enabled;
    
    public function __construct()
    {
        // You can replace with your SMS provider (Africa's Talking, Twilio, etc.)
        $this->apiKey = env('SMS_API_KEY');
        $this->senderId = env('SMS_SENDER_ID', 'JLIBRARY');
        $this->enabled = env('SMS_ENABLED', false);
    }
    
    /**
     * Send SMS to a single recipient
     */
    public function send(string $phone, string $message): bool
    {
        if (!$this->enabled) {
            Log::info('SMS disabled, would have sent:', ['phone' => $phone, 'message' => $message]);
            return true;
        }
        
        try {
            // Format phone number
            $phone = $this->formatPhoneNumber($phone);
            
            // Example using Africa's Talking API
            $response = Http::withHeaders([
                'apiKey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.africastalking.com/version1/messaging', [
                'username' => 'sandbox',
                'to' => $phone,
                'from' => $this->senderId,
                'message' => $message,
            ]);
            
            if ($response->successful()) {
                Log::info('SMS sent successfully', ['phone' => $phone]);
                return true;
            }
            
            Log::error('SMS failed', ['phone' => $phone, 'response' => $response->body()]);
            return false;
            
        } catch (\Exception $e) {
            Log::error('SMS exception', ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Send payment confirmation SMS
     */
    public function sendPaymentConfirmation(string $phone, float $amount, string $method, string $reference): bool
    {
        $message = "JLIBRARY: Payment of TSh " . number_format($amount, 2) . 
                   " via {$method} received. Ref: {$reference}. Thank you!";
        
        return $this->send($phone, $message);
    }
    
    /**
     * Send withdrawal notification SMS
     */
    public function sendWithdrawalNotification(string $phone, float $amount, string $status, string $reference): bool
    {
        if ($status === 'approved') {
            $message = "JLIBRARY: Your withdrawal of TSh " . number_format($amount, 2) . 
                       " has been approved. Ref: {$reference}. Funds will be sent shortly.";
        } else {
            $message = "JLIBRARY: Your withdrawal request of TSh " . number_format($amount, 2) . 
                       " has been {$status}. Contact support for details.";
        }
        
        return $this->send($phone, $message);
    }
    
    /**
     * Send wallet credit SMS
     */
    public function sendWalletCredit(string $phone, float $amount, float $newBalance): bool
    {
        $message = "JLIBRARY: TSh " . number_format($amount, 2) . 
                   " added to your wallet. New balance: TSh " . number_format($newBalance, 2);
        
        return $this->send($phone, $message);
    }
    
    /**
     * Format phone number to international format
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Remove leading 0
        if (substr($phone, 0, 1) === '0') {
            $phone = '255' . substr($phone, 1);
        }
        
        // Add country code if missing
        if (substr($phone, 0, 3) !== '255') {
            $phone = '255' . $phone;
        }
        
        return $phone;
    }
}