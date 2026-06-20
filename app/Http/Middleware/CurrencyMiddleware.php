<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if currency is already set in session
        if (!session()->has('currency')) {
            $currency = $this->detectCurrency($request);
            session()->put('currency', $currency);
            session()->put('exchange_rate', $this->getExchangeRate($currency));
        }
        
        return $next($request);
    }
    
    /**
     * Detect user's currency based on IP address
     */
    protected function detectCurrency(Request $request): string
    {
        $ip = $request->ip();
        
        // Skip detection for local development
        if (in_array($ip, ['127.0.0.1', '::1'])) {
            return 'TZS';
        }
        
        // Try to get country from cache
        $countryCode = Cache::remember('ip_country_' . $ip, 86400, function () use ($ip) {
            return $this->getCountryFromIp($ip);
        });
        
        // Return currency based on country
        return $this->countryToCurrency($countryCode);
    }
    
    /**
     * Get country code from IP address
     */
    protected function getCountryFromIp(string $ip): string
    {
        try {
            // Using free ip-api.com (50 requests per minute limit)
            $response = Http::get("http://ip-api.com/json/{$ip}");
            
            if ($response->successful() && $response['status'] === 'success') {
                return $response['countryCode'] ?? 'TZ';
            }
        } catch (\Exception $e) {
            // Fallback to Tanzania
        }
        
        return 'TZ';
    }
    
    /**
     * Convert country code to currency
     */
    protected function countryToCurrency(string $countryCode): string
    {
        $currencyMap = [
            'TZ' => 'TZS',  // Tanzania
            'KE' => 'KES',  // Kenya
            'UG' => 'UGX',  // Uganda
            'US' => 'USD',  // United States
            'GB' => 'GBP',  // United Kingdom
            'EU' => 'EUR',  // European Union
        ];
        
        return $currencyMap[$countryCode] ?? 'USD';
    }
    
    /**
     * Get exchange rate for currency
     */
    protected function getExchangeRate(string $currency): float
    {
        if ($currency === 'TZS') {
            return 1;
        }
        
        // Cache exchange rates
        return Cache::remember('exchange_rate_' . $currency, 3600, function () use ($currency) {
            // Base rate: 1 USD = ~2500 TZS (fallback)
            $rates = [
                'USD' => 2500,
                'GBP' => 3100,
                'EUR' => 2700,
                'KES' => 19,
                'UGX' => 0.68,
            ];
            
            return $rates[$currency] ?? 2500;
        });
    }
    
    /**
     * Get current currency from session
     */
    public static function getCurrency(): string
    {
        return session()->get('currency', 'TZS');
    }
    
    /**
     * Get current exchange rate from session
     */
    public static function getRate(): float
    {
        return session()->get('exchange_rate', 1);
    }
    
    /**
     * Convert amount to TZS
     */
    public static function toTzs(float $amount, string $fromCurrency): float
    {
        if ($fromCurrency === 'TZS') {
            return $amount;
        }
        
        $rate = self::getRate();
        return $amount * $rate;
    }
    
    /**
     * Convert amount from TZS to current currency
     */
    public static function fromTzs(float $amountTzs): float
    {
        $currency = self::getCurrency();
        
        if ($currency === 'TZS') {
            return $amountTzs;
        }
        
        $rate = self::getRate();
        return round($amountTzs / $rate, 2);
    }
}