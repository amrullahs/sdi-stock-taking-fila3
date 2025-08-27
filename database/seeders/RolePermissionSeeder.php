<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $stockTakerRole = Role::firstOrCreate(['name' => 'Stock Taker', 'guard_name' => 'web']);
        $viewerRole = Role::firstOrCreate(['name' => 'Viewer', 'guard_name' => 'web']);

        // Get all permissions
        $allPermissions = Permission::all();
        
        // Admin gets all permissions
        $adminRole->syncPermissions($allPermissions);
        
        // Stock Taker gets limited permissions (can view and manage users, roles, permissions but cannot delete)
        $stockTakerPermissions = Permission::whereIn('name', [
            'view_user',
            'view_any_user',
            'create_user',
            'update_user',
            'view_role',
            'view_any_role',
            'view_permission',
            'view_any_permission',
        ])->get();
        $stockTakerRole->syncPermissions($stockTakerPermissions);
        
        // Viewer gets only view permissions
        $viewerPermissions = Permission::whereIn('name', [
            'view_user',
            'view_any_user',
            'view_role',
            'view_any_role',
            'view_permission',
            'view_any_permission',
        ])->get();
        $viewerRole->syncPermissions($viewerPermissions);
        
        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Super Admin role has ' . $adminRole->permissions->count() . ' permissions');
        $this->command->info('Stock Taker role has ' . $stockTakerRole->permissions->count() . ' permissions');
        $this->command->info('Viewer role has ' . $viewerRole->permissions->count() . ' permissions');
    }
}
