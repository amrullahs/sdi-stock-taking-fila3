<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RemoveOthersPermissionFromLeaderSeeder extends Seeder
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

        // Define "others" permissions that leader should NOT have
        $othersPermissions = [
            'update_others_line::sto',
            'delete_others_line::sto',
            'update_others_line::sto::detail',
            'delete_others_line::sto::detail'
        ];

        // Revoke "others" permissions from leader
        foreach ($othersPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission && $leaderRole->hasPermissionTo($permission)) {
                $leaderRole->revokePermissionTo($permission);
                $this->command->info("Revoked permission: {$permissionName} from leader role");
            }
        }

        $this->command->info('All "others" permissions have been removed from leader role!');
        $this->command->info('Leader can now only edit/delete their own records.');
    }
}