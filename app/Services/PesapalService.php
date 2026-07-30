<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PesapalService
{
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $environment;
    protected string $baseUrl;
    protected ?string $ipnId = null;

    public function __construct()
{
    $this->consumerKey = config('services.pesapal.consumer_key');
    $this->consumerSecret = config('services.pesapal.consumer_secret');
    $this->environment = config('services.pesapal.environment', 'sandbox');

    $this->baseUrl = $this->environment === 'production'
        ? 'https://pay.pesapal.com/v3'
        : 'https://cybqa.pesapal.com/pesapalv3';

    $this->ipnId = $this->getOrRegisterBookIpnId();
}

protected function getOrRegisterBookIpnId(): ?string
{
    return Cache::remember('pesapal_book_ipn_id_' . $this->environment, now()->addDays(7), function () {
        $token = $this->getToken();
        if (!$token) return null;

        $ipnUrl = config('services.pesapal.book_ipn_url');
        $ipnUrl = str_starts_with($ipnUrl, 'http') ? $ipnUrl : url($ipnUrl);

        $response = Http::timeout(30)->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($this->baseUrl . '/api/URLSetup/RegisterIPN', [
            'url' => $ipnUrl,
            'ipn_notification_type' => 'GET',
        ]);

        if ($response->successful() && isset($response['ipn_id'])) {
            Log::info('Pesapal book IPN registered', ['ipn_id' => $response['ipn_id'], 'url' => $ipnUrl]);
            return $response['ipn_id'];
        }

        Log::warning('Pesapal book IPN registration failed', ['response' => $response->body()]);
        return null;
    });
}
    public function getToken(): ?string
    {
        return Cache::remember('pesapal_token_' . $this->environment, now()->addMinutes(4), function () {
            try {
                $response = Http::timeout(30)->post($this->baseUrl . '/api/Auth/RequestToken', [
                    'consumer_key' => $this->consumerKey,
                    'consumer_secret' => $this->consumerSecret,
                ]);

                if ($response->successful() && isset($response['token'])) {
                    return $response['token'];
                }

                Log::error('Pesapal token error', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            } catch (\Exception $e) {
                Log::error('Pesapal token exception: ' . $e->getMessage());
                return null;
            }
        });
    }

    public function submitOrder(array $orderData): array
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Failed to authenticate with Pesapal'];
        }
        if (!$this->ipnId) {
            return ['success' => false, 'message' => 'Pesapal IPN not registered — check the book IPN URL is reachable'];
        }

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

            Log::info('Pesapal submit order', ['status' => $response->status(), 'body' => $response->json()]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'order_tracking_id' => $data['order_tracking_id'] ?? null,
                    'merchant_reference' => $data['merchant_reference'] ?? $orderData['id'],
                    'redirect_url' => $data['redirect_url'] ?? null,
                    'raw' => $data,
                ];
            }

            $errorMsg = $response['error']['message'] ?? $response['message'] ?? 'Payment submission failed';
            Log::error('Pesapal submit order error', ['status' => $response->status(), 'error' => $errorMsg]);
            return ['success' => false, 'message' => $errorMsg];
        } catch (\Exception $e) {
            Log::error('Pesapal submit order exception: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getOrderStatus(string $orderTrackingId): array
    {
        $token = $this->getToken();
        if (!$token) return ['success' => false, 'message' => 'Failed to authenticate'];

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
                    'status' => $data['payment_status_description'] ?? $data['status'] ?? 'PENDING',
                    'payment_method' => $data['payment_method'] ?? null,
                    'amount' => $data['amount'] ?? 0,
                    'confirmation_code' => $data['confirmation_code'] ?? null,
                    'raw' => $data,
                ];
            }

            return ['success' => false, 'message' => 'Failed to get order status'];
        } catch (\Exception $e) {
            Log::error('Pesapal status exception: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function formatPhoneNumber(?string $phone): string
    {
        if (!$phone) return '255700000000';

        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (substr($phone, 0, 1) === '0') $phone = '255' . substr($phone, 1);
        if (substr($phone, 0, 4) === '2550') $phone = '255' . substr($phone, 4);
        if (strlen($phone) === 9) $phone = '255' . $phone;
        if (strlen($phone) !== 12) $phone = '255700000000';

        return $phone;
    }

public function buildBillingAddress($user): array
{
    $nameParts = explode(' ', trim($user->full_name ?? 'JLibrary User'), 2);
    $phone = $user->mpesa_phone ?? $user->tigopesa_phone ?? $user->halopesa_phone ?? null;

    return [
        'email_address' => $user->email ?? 'noreply@jlibrary.co.tz',
        'phone_number' => $this->formatPhoneNumber($phone),
        'country_code' => 'TZ',
        'first_name' => $nameParts[0] ?? 'JLibrary',
        'last_name' => $nameParts[1] ?? 'User',
        'line_1' => 'N/A',
        'city' => 'Dar es Salaam',
        'postal_code' => '00000',
    ];
}
}