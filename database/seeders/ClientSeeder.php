<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        
        Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);

        $clients = [
            [
                'first_name' => 'McDonald\'s',
                'last_name' => 'Restaurant',
                'email' => 'mcdonalds@example.com',
                'phone' => '+966501234567',
                'password' => Hash::make('password123'),
                'user_role' => 2, 
                'is_active' => true,
                'client_data' => [
                    'account_number' => 'MCD001',
                    'company_name' => 'McDonald\'s Saudi Arabia',
                    'billing_emails' => ['billing@mcdonalds.sa', 'finance@mcdonalds.sa'],
                    'auto_generate_invoice' => true,
                    'invoice_template_notes' => 'Fast food delivery services',
                    'payment_terms' => 'Net 30',
                    'currency' => 'SAR'
                ]
            ],
            [
                'first_name' => 'KFC',
                'last_name' => 'Restaurant',
                'email' => 'kfc@example.com',
                'phone' => '+966502345678',
                'password' => Hash::make('password123'),
                'user_role' => 2,
                'is_active' => true,
                'client_data' => [
                    'account_number' => 'KFC001',
                    'company_name' => 'KFC Saudi Arabia',
                    'billing_emails' => ['billing@kfc.sa', 'accounts@kfc.sa'],
                    'auto_generate_invoice' => true,
                    'invoice_template_notes' => 'Chicken delivery services',
                    'payment_terms' => 'Net 15',
                    'currency' => 'SAR'
                ]
            ],
            [
                'first_name' => 'Al Baik',
                'last_name' => 'Restaurant',
                'email' => 'albaik@example.com',
                'phone' => '+966503456789',
                'password' => Hash::make('password123'),
                'user_role' => 2,
                'is_active' => true,
                'client_data' => [
                    'account_number' => 'ALB001',
                    'company_name' => 'Al Baik Restaurant',
                    'billing_emails' => ['billing@albaik.com', 'finance@albaik.com'],
                    'auto_generate_invoice' => true,
                    'invoice_template_notes' => 'Local fast food delivery',
                    'payment_terms' => 'Net 30',
                    'currency' => 'SAR'
                ]
            ],
            [
                'first_name' => 'Burger King',
                'last_name' => 'Restaurant',
                'email' => 'burgerking@example.com',
                'phone' => '+966504567890',
                'password' => Hash::make('password123'),
                'user_role' => 2,
                'is_active' => true,
                'client_data' => [
                    'account_number' => 'BK001',
                    'company_name' => 'Burger King Saudi Arabia',
                    'billing_emails' => ['billing@burgerking.sa'],
                    'auto_generate_invoice' => false,
                    'invoice_template_notes' => 'Premium burger delivery services',
                    'payment_terms' => 'Net 30',
                    'currency' => 'SAR'
                ]
            ],
            [
                'first_name' => 'Al Shrouq',
                'last_name' => 'Saudi',
                'email' => 'billing@alshrouqexpress.com',
                'phone' => '+966505678901',
                'password' => Hash::make('password123'),
                'user_role' => 2,
                'is_active' => true,
                'client_data' => [
                    'account_number' => 'ALSHROUQ_SAUDI',
                    'company_name' => 'Al Shrouq Saudi Express',
                    'billing_emails' => [
                        'billing@alshrouqexpress.com',
                        'info@alshrouqExpress.com',
                        'CFO@alshrouqexpress.com',
                        'msk@alshrouqexpress.com',
                        'finance@alshrouqexpress.com'
                    ],
                    'auto_generate_invoice' => true,
                    'invoice_template_notes' => 'Internal company operations',
                    'payment_terms' => 'Due on Receipt',
                    'currency' => 'SAR'
                ]
            ]
        ];

        foreach ($clients as $clientData) {
           
            $user = User::create([
                'first_name' => $clientData['first_name'],
                'last_name' => $clientData['last_name'],
                'email' => $clientData['email'],
                'phone' => $clientData['phone'],
                'password' => $clientData['password'],
                'user_role' => $clientData['user_role'],
                'is_active' => $clientData['is_active'],
                'email_verified_at' => now(),
                'country_id' => 2,
            ]);

           
            $client = Client::create([
                'user_id' => $user->id, 
                'account_number' => $clientData['client_data']['account_number'],
                'company_name' => $clientData['client_data']['company_name'],
                'billing_emails' => $clientData['client_data']['billing_emails'],
                'auto_generate_invoice' => $clientData['client_data']['auto_generate_invoice'],
                'invoice_template_notes' => $clientData['client_data']['invoice_template_notes'],
                'payment_terms' => $clientData['client_data']['payment_terms'],
                'currency' => $clientData['client_data']['currency'],
                'last_invoice_date' => null,
              
                'city_id' => 1, 
                'default_prepartion_time' => '30',
                'min_prepartion_time' => '15',
                'partial_pay' => null,
                'note' => 'Created by accounting seeder',
                'client_group_id' => null,
                'driver_group_id' => null,
            ]);

            $user->assignRole('client');

            Wallet::create([
                'user_id' => $user->id,
                'balance' => rand(1000, 50000), 
            ]);

            $this->command->info("Created client: {$clientData['first_name']} {$clientData['last_name']} (ID: {$user->id})");
        }
    }
}