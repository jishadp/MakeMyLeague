<?php

namespace Database\Seeders;

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
        User::create([
            'name'  =>'Niyas KM',
            'email'  =>'niyas@gmail.com',
            'mobile'  =>'9876567876',
            'pin'   => bcrypt('4334')
        ]);
    }
}
