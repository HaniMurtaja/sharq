<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyFinancialSetting;

class CompanyFinancialSettingsSeeder extends Seeder
{
    public function run()
    {
        
        CompanyFinancialSetting::updateOrCreate(
            ['id' => 1], 
            [
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
            ]
        );

        $this->command->info('Company financial settings created successfully!');
    }
}