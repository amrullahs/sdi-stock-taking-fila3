<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CustomPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Custom permissions untuk LineSto
        $lineStoCustomPermissions = [
            'update_others_line::sto' => 'Update Line STO milik user lain',
            'delete_others_line::sto' => 'Delete Line STO milik user lain',
        ];

        // Custom permissions untuk LineStoDetail
        $lineStoDetailCustomPermissions = [
            'update_others_line::sto::detail' => 'Update Line STO Detail milik user lain',
            'delete_others_line::sto::detail' => 'Delete Line STO Detail milik user lain',
        ];

        // Gabungkan semua custom permissions
        $allCustomPermissions = array_merge($lineStoCustomPermissions, $lineStoDetailCustomPermissions);

        // Buat permissions jika belum ada
        foreach ($allCustomPermissions as $permissionName => $description) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['guard_name' => 'web']
            );
            
            $this->command->info("Created permission: {$permissionName}");
        }

        // Assign custom permissions ke role super_admin
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            foreach (array_keys($allCustomPermissions) as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && !$superAdminRole->hasPermissionTo($permission)) {
                    $superAdminRole->givePermissionTo($permission);
                    $this->command->info("Assigned {$permissionName} to super_admin role");
                }
            }
        }

        // Assign custom permissions ke role leader (jika ada)
        $leaderRole = Role::where('name', 'leader')->first();
        if ($leaderRole) {
            foreach (array_keys($allCustomPermissions) as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && !$leaderRole->hasPermissionTo($permission)) {
                    $leaderRole->givePermissionTo($permission);
                    $this->command->info("Assigned {$permissionName} to leader role");
                }
            }
        }

        $this->command->info('Custom permissions created and assigned successfully!');
    }
}