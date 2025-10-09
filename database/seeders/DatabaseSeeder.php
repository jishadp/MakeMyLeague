<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\League;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default user
        // $user = User::create([
        //     'name'  => 'Niyas KM',
        //     'email'  => 'niyas@gmail.com',
        //     'mobile'  => '9876567876',
        //     'pin'   => bcrypt('4334')
        // ]);

        // Create games
        $cricketGame = Game::create([
            'name' => 'Cricket',
            'description' => 'Twenty20 cricket leagues with auction-based team formation',
            'active' => true
        ]);

        $footballGame = Game::create([
            'name' => 'Football',
            'description' => 'Football leagues with auction-based team formation',
            'active' => true
        ]);

        $badmintonGame = Game::create([
            'name' => 'Badminton',
            'description' => 'Badminton leagues with auction-based team formation',
            'active' => true
        ]);

        $tableTennisGame = Game::create([
            'name' => 'Table Tennis',
            'description' => 'Table Tennis leagues with auction-based team formation',
            'active' => true
        ]);

    

        // Run location seeder
        $this->call(LocationSeeder::class);

        // Run role seeder
        $this->call(RoleSeeder::class);

        // Run admin user seeder
        $this->call(AdminUserSeeder::class);
        
        // Run expense categories seeder
        $this->call(ExpenseCategorySeeder::class);

        // Run game role seeder
        $this->call(GamePositionSeeder::class);

        // Run Wayanad grounds seeder
        $this->call(WayanadGroundsSeeder::class);

    }
}
