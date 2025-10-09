<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create Admin role
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        // Create first admin user
        $adminUser1 = User::firstOrCreate(
            ['mobile' => '8301867613'],
            [
                'name' => 'Muhammed Niyas K M',
                'email' => 'niyas@makemyleague.app',
                'pin' => Hash::make('1234'),
                'slug' => 'admin-user-1',
            ]
        );

        // Create second admin user
        $adminUser2 = User::firstOrCreate(
            ['mobile' => '9633220696'],
            [
                'name' => 'Jishad P',
                'email' => 'jishad@makemyleague.app',
                'pin' => Hash::make('1234'),
                'slug' => 'admin-user-2',
            ]
        );

        // Assign admin role to both users
        if (!$adminUser1->hasRole('Admin')) {
            $adminUser1->roles()->attach($adminRole->id);
        }

        if (!$adminUser2->hasRole('Admin')) {
            $adminUser2->roles()->attach($adminRole->id);
        }

        $this->command->info('Admin users created successfully!');
        $this->command->info('Admin 1 - Mobile: 8301867613, PIN: 1234');
        $this->command->info('Admin 2 - Mobile: 9633220696, PIN: 1234');
        $this->command->info('Please change the PINs after first login.');
    }
}