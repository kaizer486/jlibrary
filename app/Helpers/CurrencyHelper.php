<?php

use App\Http\Middleware\CurrencyMiddleware;

if (!function_exists('displayPrice')) {
    function displayPrice($amountTzs)
    {
        $currency = CurrencyMiddleware::getCurrency();
        $converted = CurrencyMiddleware::fromTzs((float) $amountTzs);
        $symbol = $currency === 'TZS' ? 'TSh' : '$';
        
        return $symbol . ' ' . number_format($converted, 2);
    }
}

if (!function_exists('getCurrency')) {
    function getCurrency()
    {
        return CurrencyMiddleware::getCurrency();
    }
}