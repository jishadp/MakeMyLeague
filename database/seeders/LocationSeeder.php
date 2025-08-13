<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\LocalBody;
use App\Models\State;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Kerala state
        $kerala = State::create([
            'name' => 'Kerala',
        ]);

        // Create Wayanad district
        $wayanad = District::create([
            'state_id' => $kerala->id,
            'name' => 'Wayanad',
        ]);

        // Create local bodies for Wayanad district
        $wayanadLocalBodies = [
            'Kalpetta',
            'Mananthavady',
            'Sulthan Bathery',
            'Ambalavayal',
            'Edavaka',
            'Kaniyambetta',
            'Kottathara',
            'Meenangadi',
            'Meppadi',
            'Mullankolly',
            'Muttil',
            'Nenmeni',
            'Padinharethara',
            'Pozhuthana',
            'Pulpally',
            'Thariyode',
            'Thavinjal',
            'Thirunelli',
            'Vellamunda',
            'Vengappally',
            'Vythiri',
        ];

        foreach ($wayanadLocalBodies as $localBody) {
            LocalBody::create([
                'district_id' => $wayanad->id,
                'name' => $localBody,
            ]);
        }
    }
}
