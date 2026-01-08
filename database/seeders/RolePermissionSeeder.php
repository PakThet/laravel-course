<?php

namespace Database\Seeders;

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
        // Create permissions
        $permissions = [
            // Product permissions
            'create_products',
            'edit_products',
            'delete_products',
            'view_products',

            // Category permissions
            'create_categories',
            'edit_categories',
            'delete_categories',
            'view_categories',

            // Brand permissions
            'create_brands',
            'edit_brands',
            'delete_brands',
            'view_brands',

            // Order permissions
            'manage_orders',
            'view_orders',
            'cancel_orders',

            // Customer permissions
            'create_customers',
            'edit_customers',
            'delete_customers',
            'view_customers',

            // Review permissions
            'create_reviews',
            'edit_reviews',
            'delete_reviews',
            'view_reviews',

            // Coupon permissions
            'manage_coupons',
            'view_coupons',

            // Address permissions
            'manage_addresses',
            'view_addresses',

            // User permissions
            'manage_users',
            'view_users',

            // Role and Permission management
            'manage_roles',
            'manage_permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $editor = Role::firstOrCreate(['name' => 'editor']);
        $customer = Role::firstOrCreate(['name' => 'customer']);
        $seller = Role::firstOrCreate(['name' => 'seller']);

        // Assign all permissions to admin
        $admin->syncPermissions(Permission::all());

        // Assign editor permissions
        $editorPermissions = [
            'view_products',
            'create_products',
            'edit_products',
            'view_categories',
            'create_categories',
            'edit_categories',
            'view_brands',
            'view_orders',
            'view_customers',
            'create_reviews',
            'edit_reviews',
            'view_reviews',
            'view_coupons',
            'view_addresses',
        ];
        $editor->syncPermissions($editorPermissions);

        // Assign customer permissions
        $customerPermissions = [
            'view_products',
            'view_categories',
            'view_brands',
            'create_reviews',
            'edit_reviews',
            'view_reviews',
            'manage_addresses',
        ];
        $customer->syncPermissions($customerPermissions);

        // Assign seller permissions
        $sellerPermissions = [
            'view_products',
            'create_products',
            'edit_products',
            'view_categories',
            'view_brands',
            'view_orders',
            'view_customers',
            'view_reviews',
            'manage_coupons',
            'view_coupons',
            'view_addresses',
        ];
        $seller->syncPermissions($sellerPermissions);
    }
}
