<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FixLeaderPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the leader role
        $leaderRole = Role::where('name', 'leader')->first();
        
        if (!$leaderRole) {
            $this->command->error('Leader role not found!');
            return;
        }

        // Define permissions that leader should NOT have (remove excessive permissions)
        $permissionsToRevoke = [
            'delete_line::sto',
            'delete_line::sto::detail',
            'update_line::sto', 
            'update_line::sto::detail',
            'force_delete_line::sto',
            'force_delete_any_line::sto',
            'restore_line::sto',
            'restore_any_line::sto',
            'replicate_line::sto',
            'reorder_line::sto',
            'reorder_line::sto::detail'
        ];

        // Revoke excessive permissions
        foreach ($permissionsToRevoke as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission && $leaderRole->hasPermissionTo($permission)) {
                $leaderRole->revokePermissionTo($permission);
                $this->command->info("Revoked permission: {$permissionName} from leader role");
            }
        }

        // Define permissions that leader should have (basic permissions only)
        $permissionsToGrant = [
            'view_any_line::sto',
            'view_line::sto',
            'view_line::sto::detail',
            'create_line::sto',
            'create_line::sto::detail',
            // Note: update and delete permissions will be controlled by policy based on ownership
            'update_line::sto',
            'update_line::sto::detail',
            'delete_line::sto',
            'delete_line::sto::detail'
        ];

        // Grant basic permissions
        foreach ($permissionsToGrant as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission && !$leaderRole->hasPermissionTo($permission)) {
                $leaderRole->givePermissionTo($permission);
                $this->command->info("Granted permission: {$permissionName} to leader role");
            }
        }

        $this->command->info('Leader role permissions have been fixed!');
        $this->command->info('Leader can now only edit/delete their own records unless given specific "others" permissions.');
    }
}