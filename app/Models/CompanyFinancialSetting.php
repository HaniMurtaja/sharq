<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyFinancialSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'tax_id',
        'commercial_registration',
        'address',
        'phone',
        'email',
        'bank_account',
        'iban',
        'payment_due_days',
        'additional_fields'
    ];

    protected $casts = [
        'additional_fields' => 'array',
        'payment_due_days' => 'integer'
    ];

    public static function getSettings()
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'company_name' => 'Al Shrouq Express Delivery',
                'tax_id' => '300012345600003',
                'commercial_registration' => '1010123456',
                'address' => 'King Fahd Road, Al Olaya District, Riyadh 12613, Saudi Arabia',
                'phone' => '+966-11-123-4567',
                'email' => 'info@alshrouqexpress.com',
                'bank_account' => '123456789012',
                'iban' => 'SA0312345678901234567890',
                'payment_due_days' => 30,
                'additional_fields' => []
            ]);
        }
        
        return $settings;
    }
}
