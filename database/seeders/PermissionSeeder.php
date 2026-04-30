<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    /* php artisan db:seed --class=PermissionSeed */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $modules = [
            'dashboard' => ['view'],
            'roles' => ['view', 'create', 'edit', 'delete'],
            'users' => ['view', 'create', 'edit', 'delete'],
            'products' => ['view', 'create', 'edit', 'delete'],
            'categories' => ['view', 'create', 'edit', 'delete'],
            'brands' => ['view', 'create', 'edit', 'delete'],
            'orders' => ['view', 'create', 'edit', 'delete', 'approve'], // Includes special 'approve' permission
            'settings' => ['view', 'create', 'edit', 'delete'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => $module.':'.$action]);
            }
        }

        // create roles
        $role1 = Role::firstOrCreate(['name' => 'user']);
        $role2 = Role::firstOrCreate(['name' => 'staff']);

        // Give staff basic permissions as an example
        $role2->givePermissionTo([
            'orders:view',
            'orders:create',
            'products:view',
            'categories:view',
            'brands:view',
        ]);

        $role3 = Role::firstOrCreate(['name' => 'admin']);
        // gets all permissions via Gate::before rule; see AppServiceProvider

        // create demo users
        $user = User::firstOrCreate([
            'email' => 'fredy.guapacha@gmail.com',
        ], [
            'name' => 'Fredy Guapacha',
            'password' => bcrypt('password'), // or any default
        ]);
        $user->assignRole($role3);

        // Optional: Ensure other users have the basic 'user' role automatically
        $testUser = User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]);
        $testUser->assignRole($role1);
    }
}
