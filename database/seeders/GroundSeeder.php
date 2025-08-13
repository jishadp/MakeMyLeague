<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Ground;
use App\Models\LocalBody;
use App\Models\State;
use Illuminate\Database\Seeder;

class GroundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Kerala state
        $kerala = State::where('name', 'Kerala')->first();
        
        // Get Wayanad district
        $wayanad = District::where('name', 'Wayanad')->first();
        
        if (!$kerala || !$wayanad) {
            $this->command->error('State or District not found. Please run LocationSeeder first.');
            return;
        }
        
        // Define Wayanad grounds by local body
        $groundsByLocalBody = [
            'Kalpetta' => [
                [
                    'name' => 'Kalpetta Municipal Stadium',
                    'address' => 'Near Bus Stand, Kalpetta, Wayanad',
                    'capacity' => 5000,
                    'description' => 'Main cricket stadium in Kalpetta with floodlights and pavilion',
                    'contact_person' => 'Rahul Nair',
                    'contact_phone' => '9445678901',
                ],
                [
                    'name' => 'MES College Ground',
                    'address' => 'MES College, Kalpetta, Wayanad',
                    'capacity' => 2000,
                    'description' => 'College ground with basic facilities',
                    'contact_person' => 'Sajith Kumar',
                    'contact_phone' => '9556789012',
                ],
            ],
            'Mananthavady' => [
                [
                    'name' => 'Mananthavady Sports Complex',
                    'address' => 'Sports Council Road, Mananthavady, Wayanad',
                    'capacity' => 4000,
                    'description' => 'Multi-purpose sports complex with cricket pitch',
                    'contact_person' => 'Arun Thomas',
                    'contact_phone' => '9667890123',
                ],
                [
                    'name' => 'Don Bosco School Ground',
                    'address' => 'Don Bosco School, Mananthavady, Wayanad',
                    'capacity' => 1500,
                    'description' => 'School ground with well-maintained pitch',
                    'contact_person' => 'Fr. Joseph',
                    'contact_phone' => '9778901234',
                ],
            ],
            'Sulthan Bathery' => [
                [
                    'name' => 'Sulthan Bathery Stadium',
                    'address' => 'Stadium Road, Sulthan Bathery, Wayanad',
                    'capacity' => 3500,
                    'description' => 'Municipal stadium with cricket and football facilities',
                    'contact_person' => 'Anoop Menon',
                    'contact_phone' => '9889012345',
                ],
                [
                    'name' => 'Government College Ground',
                    'address' => 'Govt. College, Sulthan Bathery, Wayanad',
                    'capacity' => 1800,
                    'description' => 'College ground used for local tournaments',
                    'contact_person' => 'Dr. Sujith',
                    'contact_phone' => '9990123456',
                ],
            ],
            'Meenangadi' => [
                [
                    'name' => 'Meenangadi Cricket Ground',
                    'address' => 'Meenangadi, Wayanad',
                    'capacity' => 2000,
                    'description' => 'Local cricket ground with basic amenities',
                    'contact_person' => 'Vishnu Prasad',
                    'contact_phone' => '9012345678',
                ],
            ],
            'Meppadi' => [
                [
                    'name' => 'Meppadi Sports Club Ground',
                    'address' => 'Sports Club Road, Meppadi, Wayanad',
                    'capacity' => 1500,
                    'description' => 'Local sports club ground with cricket pitch',
                    'contact_person' => 'Jithin George',
                    'contact_phone' => '9123456789',
                ],
            ],
            'Ambalavayal' => [
                [
                    'name' => 'Ambalavayal Community Ground',
                    'address' => 'Near Panchayat Office, Ambalavayal, Wayanad',
                    'capacity' => 1200,
                    'description' => 'Community ground maintained by local clubs',
                    'contact_person' => 'Amal Jose',
                    'contact_phone' => '9234567890',
                ],
            ],
            'Pulpally' => [
                [
                    'name' => 'Pulpally Cricket Stadium',
                    'address' => 'Stadium Road, Pulpally, Wayanad',
                    'capacity' => 2500,
                    'description' => 'Cricket stadium with good outfield and facilities',
                    'contact_person' => 'Santhosh Kumar',
                    'contact_phone' => '9345678901',
                ],
            ],
        ];
        
        // Create grounds for each local body
        foreach ($groundsByLocalBody as $localBodyName => $grounds) {
            $localBody = LocalBody::where('name', $localBodyName)->first();
            
            if (!$localBody) {
                $this->command->warn("Local body '$localBodyName' not found, skipping its grounds.");
                continue;
            }
            
            foreach ($grounds as $groundData) {
                Ground::create([
                    'name' => $groundData['name'],
                    'address' => $groundData['address'],
                    'localbody_id' => $localBody->id,
                    'district_id' => $wayanad->id,
                    'state_id' => $kerala->id,
                    'capacity' => $groundData['capacity'],
                    'description' => $groundData['description'],
                    'contact_person' => $groundData['contact_person'],
                    'contact_phone' => $groundData['contact_phone'],
                    'is_available' => true,
                ]);
                
                $this->command->info("Created ground: {$groundData['name']}");
            }
        }
    }
}
