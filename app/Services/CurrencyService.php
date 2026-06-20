<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    // Supported currencies
    const TZS = 'TZS';
    const USD = 'USD';
    
    // Exchange rate cache key
    const EXCHANGE_RATE_CACHE_KEY = 'tzs_to_usd_rate';
    const CACHE_TTL = 3600; // 1 hour
    
    /**
     * Get current exchange rate (TZS to USD)
     */
    public function getExchangeRate(): float
    {
        return Cache::remember(self::EXCHANGE_RATE_CACHE_KEY, self::CACHE_TTL, function () {
            return $this->fetchExchangeRate();
        });
    }
    
    /**
     * Fetch exchange rate from API (fallback to fixed rate)
     */
    private function fetchExchangeRate(): float
    {
        try {
            // You can replace with a real API like OpenExchangeRates, Fixer.io, etc.
            // For now, using a reasonable fixed rate (1 USD = ~2500 TZS)
            return 2500.00;
            
            // Example with API:
            // $response = Http::get('https://api.exchangerate-api.com/v4/latest/TZS');
            // return $response['rates']['USD'] ?? 2500;
            
        } catch (\Exception $e) {
            // Fallback rate
            return 2500.00;
        }
    }
    
    /**
     * Convert TZS to USD
     */
    public function tzsToUsd(float $amountTzs): float
    {
        $rate = $this->getExchangeRate();
        return round($amountTzs / $rate, 2);
    }
    
    /**
     * Convert USD to TZS
     */
    public function usdToTzs(float $amountUsd): float
    {
        $rate = $this->getExchangeRate();
        return round($amountUsd * $rate, 2);
    }
    
    /**
     * Format amount based on currency
     */
    public function formatAmount(float $amount, string $currency = 'TZS'): string
    {
        if ($currency === 'USD') {
            return '$' . number_format($amount, 2);
        }
        
        return 'TSh ' . number_format($amount, 2);
    }
    
    /**
     * Get currency symbol
     */
    public function getCurrencySymbol(string $currency): string
    {
        return match ($currency) {
            'USD' => '$',
            'TZS' => 'TSh',
            default => 'TSh',
        };
    }
}