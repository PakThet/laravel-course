<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'phone' => '1111111111',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('admin');

        // Create Editor User
        $editorUser = User::firstOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'Editor User',
                'password' => Hash::make('editor123'),
                'phone' => '2222222222',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $editorUser->assignRole('editor');

        // Create Customer User 1
        $customerUser1 = User::firstOrCreate(
            ['email' => 'customer1@example.com'],
            [
                'name' => 'John Customer',
                'password' => Hash::make('customer123'),
                'phone' => '3333333333',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $customerUser1->assignRole('customer');

        // Create Customer User 2
        $customerUser2 = User::firstOrCreate(
            ['email' => 'customer2@example.com'],
            [
                'name' => 'Jane Customer',
                'password' => Hash::make('customer123'),
                'phone' => '4444444444',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $customerUser2->assignRole('customer');

        // Create Seller User 1
        $sellerUser1 = User::firstOrCreate(
            ['email' => 'seller1@example.com'],
            [
                'name' => 'Seller One',
                'password' => Hash::make('seller123'),
                'phone' => '5555555555',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $sellerUser1->assignRole('seller');

        // Create Seller User 2
        $sellerUser2 = User::firstOrCreate(
            ['email' => 'seller2@example.com'],
            [
                'name' => 'Seller Two',
                'password' => Hash::make('seller123'),
                'phone' => '6666666666',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $sellerUser2->assignRole('seller');

        // Create additional test users
        for ($i = 3; $i <= 5; $i++) {
            $testUser = User::firstOrCreate(
                ['email' => "user$i@example.com"],
                [
                    'name' => "Test User $i",
                    'password' => Hash::make('password123'),
                    'phone' => '9' . str_pad($i, 9, '0', STR_PAD_LEFT),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
            // Assign random role
            $roles = ['editor', 'customer', 'seller'];
            $testUser->assignRole($roles[$i % 3]);
        }

        $this->command->info('Users with roles created successfully!');
    }
}
