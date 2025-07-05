<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
   
    public function run(): void
    {
        
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $clientRole = Role::firstOrCreate(['name' => 'client']);
        $accountingRole = Role::firstOrCreate(['name' => 'accounting']);

    
        $accountingPermissions = [
            'accounting_access',
            'accounting_view_invoices', 
            'accounting_view_clients',
            'accounting_reports',
            'accounting_settings',
            'show_dashboard',
            'basic_dispatcher_view',
            'control_drivers',
            'control_clients',
            'control_users',
            'view_vehicles',
            'previous_orders_basic_view',
            'view_export_reports',
            'view_integration',
            'view_location',
            'view_foodics_clients',
            'control_consolidated_orders_settings'
        ];

        foreach ($accountingPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

       
        $adminRole->syncPermissions(Permission::all());
        
     
        $clientPermissions = [
            'show_dashboard',
            'basic_dispatcher_view',
            'previous_orders_basic_view'
        ];
        $clientRole->syncPermissions($clientPermissions);

       
        $accountingRole->syncPermissions($accountingPermissions);

        $this->command->info('Roles and permissions created successfully.');
    }
}
