<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssignCustomPermissionToSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the super_admin role
        $superAdminRole = Role::where('name', 'super_admin')->first();
        
        if (!$superAdminRole) {
            $this->command->error('Super admin role not found!');
            return;
        }

        // Define custom permissions that super admin should have
        $customPermissions = [
            'update_others_line::sto',
            'delete_others_line::sto',
            'update_others_line::sto::detail',
            'delete_others_line::sto::detail'
        ];

        // Assign custom permissions to super admin
        foreach ($customPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission && !$superAdminRole->hasPermissionTo($permission)) {
                $superAdminRole->givePermissionTo($permission);
                $this->command->info("Granted permission: {$permissionName} to super_admin role");
            } elseif ($permission) {
                $this->command->info("Permission already exists: {$permissionName} for super_admin role");
            } else {
                $this->command->error("Permission not found: {$permissionName}");
            }
        }

        $this->command->info('Custom permissions have been assigned to super_admin role!');
        $this->command->info('Super admin can now edit/delete all records including those owned by others.');
    }
}