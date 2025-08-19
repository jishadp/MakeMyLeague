<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Organiser',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Owner',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Player',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
