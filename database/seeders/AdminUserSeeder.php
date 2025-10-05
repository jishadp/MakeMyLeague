<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@makemyleague.com'],
            [
                'name' => 'Admin User',
                'mobile' => '9876543210',
                'pin' => Hash::make('1234'),
                'email' => 'admin@makemyleague.com',
                'position_id' => null, // Admin doesn't need a playing position
                'local_body_id' => null,
                'country_code' => '+91',
            ]
        );

        // Get or create Admin role
        $adminRole = Role::firstOrCreate(
            ['name' => User::ROLE_ADMIN]
        );

        // Assign Admin role to the user
        UserRole::firstOrCreate(
            [
                'user_id' => $admin->id,
                'role_id' => $adminRole->id,
            ]
        );

        // Also assign Player role (everyone should have Player role)
        $playerRole = Role::firstOrCreate(
            ['name' => User::ROLE_PLAYER]
        );

        UserRole::firstOrCreate(
            [
                'user_id' => $admin->id,
                'role_id' => $playerRole->id,
            ]
        );

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@makemyleague.com');
        $this->command->info('Password: admin123');
        $this->command->info('Roles: Admin, Player');
    }
}