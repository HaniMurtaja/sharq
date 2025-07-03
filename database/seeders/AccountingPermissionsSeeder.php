<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccountingPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create accounting permissions
        $permissions = [
            'accounting_access' => 'Access accounting module',
            'accounting_view_invoices' => 'View invoices',
            'accounting_create_invoices' => 'Create invoices',
            'accounting_edit_invoices' => 'Edit invoices',
            'accounting_delete_invoices' => 'Delete invoices',
            'accounting_confirm_invoices' => 'Confirm and send invoices',
            'accounting_view_clients' => 'View client financial data',
            'accounting_edit_clients' => 'Edit client financial data',
            'accounting_manage_payments' => 'Manage payments and receipts',
            'accounting_suspend_clients' => 'Suspend client accounts',
            'accounting_settings' => 'Manage accounting settings',
            'accounting_reports' => 'View accounting reports',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web'
            ], [
                'group_id' => $this->getAccountingGroupId() 
            ]);
        }

      
        $accountantRole = Role::firstOrCreate([
            'name' => 'Accountant',
            'guard_name' => 'web'
        ]);

      
        $accountantRole->givePermissionTo(array_keys($permissions));

        
        $cfoRole = Role::firstOrCreate([
            'name' => 'CFO',
            'guard_name' => 'web'
        ]);

        $cfoRole->givePermissionTo(array_keys($permissions));

        
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(array_keys($permissions));
        }
    }

    private function getAccountingGroupId()
    {
        
        return null;
    }
}
