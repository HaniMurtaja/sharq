<?php

namespace App\Models;

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
        'additional_fields',
        'payment_due_days'
    ];

    protected $casts = [
        'additional_fields' => 'array',
        'payment_due_days' => 'integer'
    ];

    public static function getSettings()
    {
        return self::first() ?? self::create([
            'company_name' => config('app.name', 'Al Shrouq Express'),
            'payment_due_days' => 3
        ]);
    }
}
