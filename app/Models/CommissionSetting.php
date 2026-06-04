<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionSetting extends Model
{
    protected $fillable = ['key', 'value', 'description'];
    
    protected $casts = [
        'value' => 'decimal:2',
    ];
    
    public static function getValue($key)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : 0;
    }
    
    public static function getInstitutionCommission()
    {
        return self::getValue('institution_commission');
    }
    
    public static function getPlatformCommission()
    {
        return self::getValue('platform_commission');
    }
    
    public static function getAuthorCommission()
    {
        return self::getValue('author_commission');
    }
    
    public static function getMinWithdrawal()
    {
        return self::getValue('min_withdrawal');
    }
}