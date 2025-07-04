<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FixPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create the missing permission
        Permission::firstOrCreate([
            'name' => 'basic_dispatcher_view',
            'guard_name' => 'web'
        ]);

        // Find the admin user
        $admin = User::where('email', 'admin@example.com')->first();

        if (!$admin) {
            $this->command->error('Admin user not found!');
            return;
        }

        // Create all required permissions
        $permissions = [
            'accounting_access',
            'basic_dispatcher_view',
            'show_dashboard',
            'control_clients',
            'control_clients_groups', 
            'control_branch_groups',
            'control_areas_zones',
            'control_clients_wallet_option'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Give all permissions directly to admin user
        $admin->givePermissionTo($permissions);

        // Also make sure admin has Admin role
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin', 
            'guard_name' => 'web'
        ]);

        $adminRole->givePermissionTo($permissions);
        $admin->assignRole('Admin');

        $this->command->info('Permissions fixed for admin user!');
        $this->command->info('Admin user now has these permissions:');
        foreach ($permissions as $permission) {
            $this->command->info("- {$permission}");
        }
    }
}
