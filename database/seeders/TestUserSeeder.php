<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $organiserRole = Role::where('name', 'Organiser')->first();
        $ownerRole = Role::where('name', 'Owner')->first();
        $playerRole = Role::where('name', 'Player')->first();

        // Test users data with different roles
        $testUsers = [
            // Organisers (5 users)
            [
                'name' => 'Rajesh Kumar',
                'email' => 'rajesh.organiser@test.com',
                'mobile' => '9876543300',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'role' => 'Organiser'
            ],
            [
                'name' => 'Priya Sharma',
                'email' => 'priya.organiser@test.com',
                'mobile' => '9876543301',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'role' => 'Organiser'
            ],
            [
                'name' => 'Amit Patel',
                'email' => 'amit.organiser@test.com',
                'mobile' => '9876543302',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'role' => 'Organiser'
            ],
            [
                'name' => 'Sneha Gupta',
                'email' => 'sneha.organiser@test.com',
                'mobile' => '9876543303',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'role' => 'Organiser'
            ],
            [
                'name' => 'Vikram Singh',
                'email' => 'vikram.organiser@test.com',
                'mobile' => '9876543304',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'role' => 'Organiser'
            ],

            // Team Owners (5 users)
            [
                'name' => 'Arjun Reddy',
                'email' => 'arjun.owner@test.com',
                'mobile' => '9876543305',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'role' => 'Owner'
            ],
            [
                'name' => 'Kavya Nair',
                'email' => 'kavya.owner@test.com',
                'mobile' => '9876543306',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'role' => 'Owner'
            ],
            [
                'name' => 'Rohit Mehta',
                'email' => 'rohit.owner@test.com',
                'mobile' => '9876543307',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'role' => 'Owner'
            ],
            [
                'name' => 'Anjali Joshi',
                'email' => 'anjali.owner@test.com',
                'mobile' => '9876543308',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'role' => 'Owner'
            ],
            [
                'name' => 'Suresh Iyer',
                'email' => 'suresh.owner@test.com',
                'mobile' => '9876543309',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'role' => 'Owner'
            ],

            // Players (10 users)
            [
                'name' => 'Rahul Sharma',
                'email' => 'rahul.player@test.com',
                'mobile' => '9876543310',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'position_id' => 1, // Batter
                'role' => 'Player'
            ],
            [
                'name' => 'Deepak Kumar',
                'email' => 'deepak.player@test.com',
                'mobile' => '9876543311',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'position_id' => 2, // Bowler
                'role' => 'Player'
            ],
            [
                'name' => 'Manish Verma',
                'email' => 'manish.player@test.com',
                'mobile' => '9876543312',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'position_id' => 1, // Batter
                'role' => 'Player'
            ],
            [
                'name' => 'Sachin Tendulkar',
                'email' => 'sachin.player@test.com',
                'mobile' => '9876543313',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'position_id' => 1, // Batter
                'role' => 'Player'
            ],
            [
                'name' => 'Virat Kohli',
                'email' => 'virat.player@test.com',
                'mobile' => '9876543314',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'position_id' => 1, // Batter
                'role' => 'Player'
            ],
            [
                'name' => 'MS Dhoni',
                'email' => 'dhoni.player@test.com',
                'mobile' => '9876543315',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'position_id' => 4, // Wicket-Keeper Batter
                'role' => 'Player'
            ],
            [
                'name' => 'Rohit Sharma',
                'email' => 'rohit.player@test.com',
                'mobile' => '9876543316',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'position_id' => 1, // Batter
                'role' => 'Player'
            ],
            [
                'name' => 'Jasprit Bumrah',
                'email' => 'bumrah.player@test.com',
                'mobile' => '9876543317',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'position_id' => 2, // Bowler
                'role' => 'Player'
            ],
            [
                'name' => 'Hardik Pandya',
                'email' => 'hardik.player@test.com',
                'mobile' => '9876543318',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'position_id' => 3, // All-Rounder
                'role' => 'Player'
            ],
            [
                'name' => 'KL Rahul',
                'email' => 'klrahul.player@test.com',
                'mobile' => '9876543319',
                'country_code' => '+91',
                'pin' => Hash::make('1234'),
                'position_id' => 1, // Batter
                'role' => 'Player'
            ],
        ];

        foreach ($testUsers as $userData) {
            // Remove role from user data before creating user
            $role = $userData['role'];
            unset($userData['role']);

            // Create user
            $user = User::create($userData);

            // Assign role based on the role name
            switch ($role) {
                case 'Organiser':
                    $user->roles()->attach($organiserRole->id);
                    break;
                case 'Owner':
                    $user->roles()->attach($ownerRole->id);
                    break;
                case 'Player':
                    $user->roles()->attach($playerRole->id);
                    break;
            }

            $this->command->info("Created user: {$user->name} ({$role})");
        }

        $this->command->info('Successfully created 20 test users with different roles!');
    }
}
