<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard permissions
            'dashboard.view',
            // User permissions
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',

            // Role permissions
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',

            // etc. add more permissions as needed
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles if they don't exist
        $admin = Role::create(['name' => 'Admin']);
        $hrd = Role::create(['name' => 'HRD']);
        $user = Role::create(['name' => 'User']);

        // Assign permissions to roles
        // Admin gets all permissions
        $admin->syncPermissions(Permission::all());

        // HRD gets specific permissions
        $hrd->syncPermissions(['user.view', 'user.create', 'user.edit', 'dashboard.view', 'role.view']);

        // Regular User gets basic permissions
        $user->syncPermissions(['user.view', 'role.view', 'dashboard.view']);

        // Assign roles to specific users
        // Find Administrator user and assign Admin role
        $adminUser = User::where('name', 'Administrator')->first();
        if ($adminUser) {
            $adminUser->assignRole('Admin'); // Using role name string
            // Or you can use: $adminUser->assignRole($admin);
        }

        // Optional: Assign HRD role to a specific user
        $hrdUser = User::where('name', 'HRD')->first();
        if ($hrdUser) {
            $hrdUser->assignRole('HRD');
        }

        // Optional: Assign User role to a specific user
        $regularUser = User::where('name', 'User')->first();
        if ($regularUser) {
            $regularUser->assignRole('User');
        }

        // Display success message in console
        $this->command->info('Roles and permissions seeded successfully!');
    }
}
