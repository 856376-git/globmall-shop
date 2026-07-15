<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@globmall.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('123456'),
                'role' => 'customer',
                'status' => 1,
            ]
        );

        // Operations Manager
        $ops = User::firstOrCreate(
            ['email' => 'ops@globmall.com'],
            [
                'name' => 'Ops Manager',
                'password' => Hash::make('123456'),
                'role' => 'customer',
                'status' => 1,
            ]
        );

        // Customer Service
        $cs = User::firstOrCreate(
            ['email' => 'cs@globmall.com'],
            [
                'name' => 'CS Agent',
                'password' => Hash::make('123456'),
                'role' => 'customer',
                'status' => 1,
            ]
        );

        // Viewer
        $viewer = User::firstOrCreate(
            ['email' => 'viewer@globmall.com'],
            [
                'name' => 'Viewer',
                'password' => Hash::make('123456'),
                'role' => 'customer',
                'status' => 1,
            ]
        );

        // Demo Admin (uses the 'admin' RBAC role, not super-admin)
        $demo = User::firstOrCreate(
            ['email' => 'demo@globmall.com'],
            [
                'name' => 'Demo Admin',
                'password' => Hash::make('123456'),
                'role' => 'customer',
                'status' => 1,
            ]
        );

        // Regular customer — John Doe
        $customer = User::firstOrCreate(
            ['email' => 'john@test.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('123456'),
                'role' => 'customer',
                'status' => 1,
            ]
        );

        // Give customer default addresses
        Address::firstOrCreate(
            ['user_id' => $customer->id, 'label' => 'Home'],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '+1234567890',
                'country' => 'US',
                'state' => 'California',
                'city' => 'Los Angeles',
                'address_line1' => '123 Main Street',
                'address_line2' => 'Apt 4B',
                'zipcode' => '90001',
                'is_default' => 1,
            ]
        );

        Address::firstOrCreate(
            ['user_id' => $customer->id, 'label' => 'Office'],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '+1234567890',
                'country' => 'US',
                'state' => 'California',
                'city' => 'San Francisco',
                'address_line1' => '456 Tech Blvd',
                'zipcode' => '94102',
                'is_default' => 0,
            ]
        );
    }
}
