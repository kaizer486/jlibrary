<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentIntent(float $amount, string $subscriptionId, string $description): array
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100, // Convert to cents
                'currency' => 'usd',
                'description' => $description,
                'metadata' => [
                    'subscription_id' => $subscriptionId,
                ],
                'automatic_payment_methods' => ['enabled' => true],
            ]);

            return [
                'payment_intent_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'status' => $paymentIntent->status,
            ];
        } catch (\Exception $e) {
            Log::error('Stripe payment intent creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function verifyWebhook(string $payload, string $signature): bool
    {
        try {
            $endpointSecret = config('services.stripe.webhook_secret');
            \Stripe\Webhook::constructEvent($payload, $signature, $endpointSecret);
            return true;
        } catch (\Exception $e) {
            Log::error('Stripe webhook verification failed: ' . $e->getMessage());
            return false;
        }
    }
}