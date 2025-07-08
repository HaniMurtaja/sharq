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
        // Create accounting-specific permissions
        $accountingPermissions = [
            'accounting_access',
            'accounting_view_invoices',
            'accounting_create_invoices',
            'accounting_edit_invoices',
            'accounting_confirm_invoices',
            'accounting_view_clients',
            'accounting_edit_clients',
            'accounting_manage_payments',
            'accounting_settings',
            'accounting_reports',
        ];

        // Create all permissions
        $allPermissions = array_merge($accountingPermissions, [
            'basic_dispatcher_view',
            'show_dashboard',
            'control_clients',
            'control_clients_groups',
            'control_branch_groups',
            'control_areas_zones',
            'control_clients_wallet_option',
            'control_drivers',
            'control_users',
            'view_vehicles',
            'previous_orders_basic_view',
            'view_export_reports',
            'view_integration',
            'view_location',
            'view_foodics_clients',
            'control_consolidated_orders_settings'
        ]);

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $accountantRole = Role::firstOrCreate(['name' => 'Accountant', 'guard_name' => 'web']);
        $cfoRole = Role::firstOrCreate(['name' => 'CFO', 'guard_name' => 'web']);
        $dispatcherRole = Role::firstOrCreate(['name' => 'Dispatcher', 'guard_name' => 'web']);

        // Assign permissions to roles
        $adminRole->givePermissionTo($allPermissions); // Admin gets everything

        // Accountant gets accounting permissions + basic access
        $accountantRole->givePermissionTo(array_merge($accountingPermissions, [
            'show_dashboard',
            'basic_dispatcher_view',
            'view_export_reports'
        ]));

        // CFO gets accounting permissions + some management access
        $cfoRole->givePermissionTo(array_merge($accountingPermissions, [
            'show_dashboard',
            'basic_dispatcher_view',
            'control_clients',
            'view_export_reports',
            'control_users'
        ]));

        // Dispatcher gets basic operational permissions (NO accounting access)
        $dispatcherRole->givePermissionTo([
            'show_dashboard',
            'basic_dispatcher_view',
            'previous_orders_basic_view',
            'control_drivers'
        ]);

        // Create admin user
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

        // Create accountant user
        $accountant = User::firstOrCreate(
            ['email' => 'accountant@example.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Accountant',
                'password' => Hash::make('password123'),
                'user_role' => 1,
                'is_active' => 1,
                'country_id' => 2,
                'email_verified_at' => now(),
            ]
        );

        // Create dispatcher user (no accounting access)
        $dispatcher = User::firstOrCreate(
            ['email' => 'dispatcher@example.com'],
            [
                'first_name' => 'Jane',
                'last_name' => 'Dispatcher',
                'password' => Hash::make('password123'),
                'user_role' => 1,
                'is_active' => 1,
                'country_id' => 2,
                'email_verified_at' => now(),
            ]
        );

        // Assign roles
        $admin->assignRole('Admin');
        $accountant->assignRole('Accountant');
        $dispatcher->assignRole('Dispatcher');

        $this->command->info('Users created successfully!');
        $this->command->info('Admin - Email: admin@example.com, Password: password123');
        $this->command->info('Accountant - Email: accountant@example.com, Password: password123');
        $this->command->info('Dispatcher - Email: dispatcher@example.com, Password: password123 (NO accounting access)');
    }
}