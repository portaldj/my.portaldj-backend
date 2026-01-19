<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Roles
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
        $djRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'DJ']);
        $visitorRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Visitor']);

        // Permissions (Demo)
        $permissions = [
            'manage taxonomy',
            'manage users',
            'download songs',
            'access academy',
        ];

        foreach ($permissions as $perm) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm]);
        }

        // Assign Permissions
        $adminRole->givePermissionTo($permissions);
        $djRole->givePermissionTo(['download songs', 'access academy']);

        // Create Admin User
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@portaldj.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($adminRole);

        // Ensure Profile exists for Admin (even if empty) to avoid null check errors logic later
        \App\Models\Profile::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'username' => 'admin',
                'first_name' => 'Super',
                'last_name' => 'Admin',
            ]
        );
    }
}
