<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // User permissions
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // Role permissions
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'assign_roles',

            // Permission permissions
            'view_permissions',
            'create_permissions',
            'edit_permissions',
            'delete_permissions',
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        $superAdmin = Role::firstOrCreate(
            ['name' => 'super-admin', 'guard_name' => 'api']
        );
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'api']
        );
        $adminPermissions = [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'assign_roles',
            'view_permissions',
        ]; 
        
        $admin->givePermissionTo($adminPermissions);

        $user = Role::firstOrCreate(
            ['name' => 'user', 'guard_name' => 'api']
        );

        $user->givePermissionTo(['view_users']);

        $superAdminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
        ]);
        
        $superAdminUser->assignRole($superAdmin);
    }
}
