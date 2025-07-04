<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('password123'),
                'user_role' => 1, 
                'is_active' => 1,
                'country_id' => 2,
                'email_verified_at' => now(),
            ]
        );

        $permissions = [
            'accounting_access',
            'accounting_view_invoices',
            'accounting_create_invoices',
            'accounting_edit_invoices',
            'accounting_confirm_invoices',
            'accounting_view_clients',
            'accounting_edit_clients',
            'accounting_manage_payments',
            'accounting_settings',
            'accounting_reports'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web'
        ]);

        
        $adminRole->givePermissionTo($permissions);

        
        $admin->assignRole('Admin');

       
        $admin->givePermissionTo($permissions);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Password: password123');
    }
}