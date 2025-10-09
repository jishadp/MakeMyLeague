<?php

namespace Database\Seeders;

use App\Models\Ground;
use App\Models\State;
use App\Models\District;
use App\Models\LocalBody;
use Illuminate\Database\Seeder;

class WayanadGroundsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Wayanad district and state
        $wayanadDistrict = District::where('name', 'Wayanad')->first();
        $keralaState = State::where('name', 'Kerala')->first();
        
        if (!$wayanadDistrict || !$keralaState) {
            $this->command->error('Wayanad district or Kerala state not found. Please run DatabaseSeeder first.');
            return;
        }

        // Real cricket grounds in Wayanad from CricHeroes tournaments and matches
        $grounds = [
            [
                'name' => 'Mini Stadium Kambalakkad',
                'description' => 'Hosted the Alligators Cricket Tournament (Feb 24 - Mar 3, 2024). Popular venue for local tournaments.',
                'capacity' => 500,
                'address' => 'Kambalakkad, Wayanad',
                'state_id' => $keralaState->id,
                'district_id' => $wayanadDistrict->id,
                'localbody_id' => $this->getLocalBodyId('Kalpetta'), // Kambalakkad is near Kalpetta
                'contact_person' => 'Ground Manager',
                'contact_phone' => '9876543210',
                'is_available' => true,
            ],
            [
                'name' => 'GHSS Padinjarathara',
                'description' => 'Hosted Monsoon Cricketers Wayanad vs Samskara Padinjarathara match (Sep 17, 2023) in Monsoon Cricket Fest-15.',
                'capacity' => 300,
                'address' => 'Padinjarathara, Wayanad',
                'state_id' => $keralaState->id,
                'district_id' => $wayanadDistrict->id,
                'localbody_id' => $this->getLocalBodyId('Padinharethara'),
                'contact_person' => 'School Principal',
                'contact_phone' => '9876543211',
                'is_available' => true,
            ],
            [
                'name' => 'Panchayat Stadium Panamaram',
                'description' => 'Venue for Wayanad Test Championship (Jul 30 - Sep 25, 2022). Official panchayat stadium.',
                'capacity' => 800,
                'address' => 'Panamaram, Wayanad',
                'state_id' => $keralaState->id,
                'district_id' => $wayanadDistrict->id,
                'localbody_id' => $this->getLocalBodyId('Kalpetta'), // Panamaram is near Kalpetta
                'contact_person' => 'Panchayat Sports Officer',
                'contact_phone' => '9876543212',
                'is_available' => true,
            ],
            [
                'name' => 'High School Ground Mananthavady',
                'description' => 'Venue for Wayanad Test Championship (Jul 30 - Sep 25, 2022). High school cricket ground.',
                'capacity' => 400,
                'address' => 'Mananthavady, Wayanad',
                'state_id' => $keralaState->id,
                'district_id' => $wayanadDistrict->id,
                'localbody_id' => $this->getLocalBodyId('Mananthavady'),
                'contact_person' => 'School Sports Coordinator',
                'contact_phone' => '9876543213',
                'is_available' => true,
            ],
            [
                'name' => 'ST Marys Ground',
                'description' => 'Hosted matches during Wayanad Test Championship (Jul 30 - Sep 25, 2022). Church/school affiliated ground.',
                'capacity' => 350,
                'address' => 'ST Marys Area, Wayanad',
                'state_id' => $keralaState->id,
                'district_id' => $wayanadDistrict->id,
                'localbody_id' => $this->getLocalBodyId('Kalpetta'), // Assuming near Kalpetta
                'contact_person' => 'Ground Keeper',
                'contact_phone' => '9876543214',
                'is_available' => true,
            ],
            [
                'name' => 'Krishnagiri Stadium',
                'description' => 'International level stadium in Krishnagiri, hosted India A vs South Africa A matches.',
                'capacity' => 1500,
                'address' => 'Krishnagiri, Wayanad',
                'state_id' => $keralaState->id,
                'district_id' => $wayanadDistrict->id,
                'localbody_id' => $this->getLocalBodyId('Kalpetta'), // Krishnagiri is near Kalpetta
                'contact_person' => 'Stadium Manager',
                'contact_phone' => '9876543215',
                'is_available' => true,
            ],
            [
                'name' => 'Sulthan Bathery Cricket Ground',
                'description' => 'Popular cricket ground in Sulthan Bathery, regularly used for local matches and tournaments.',
                'capacity' => 400,
                'address' => 'Sulthan Bathery, Wayanad',
                'state_id' => $keralaState->id,
                'district_id' => $wayanadDistrict->id,
                'localbody_id' => $this->getLocalBodyId('Sulthan Bathery'),
                'contact_person' => 'Ground Supervisor',
                'contact_phone' => '9876543216',
                'is_available' => true,
            ],
            [
                'name' => 'Vythiri Sports Club Ground',
                'description' => 'Private sports club ground in Vythiri, well-maintained with good facilities.',
                'capacity' => 350,
                'address' => 'Vythiri, Wayanad',
                'state_id' => $keralaState->id,
                'district_id' => $wayanadDistrict->id,
                'localbody_id' => $this->getLocalBodyId('Vythiri'),
                'contact_person' => 'Club Secretary',
                'contact_phone' => '9876543217',
                'is_available' => true,
            ],
            [
                'name' => 'Pulpally Cricket Academy Ground',
                'description' => 'Cricket academy ground in Pulpally, used for training and competitive matches.',
                'capacity' => 450,
                'address' => 'Pulpally, Wayanad',
                'state_id' => $keralaState->id,
                'district_id' => $wayanadDistrict->id,
                'localbody_id' => $this->getLocalBodyId('Pulpally'),
                'contact_person' => 'Academy Director',
                'contact_phone' => '9876543218',
                'is_available' => true,
            ],
            [
                'name' => 'Ambalavayal Community Ground',
                'description' => 'Community cricket ground in Ambalavayal, used by local cricket clubs and teams.',
                'capacity' => 300,
                'address' => 'Ambalavayal, Wayanad',
                'state_id' => $keralaState->id,
                'district_id' => $wayanadDistrict->id,
                'localbody_id' => $this->getLocalBodyId('Ambalavayal'),
                'contact_person' => 'Community Sports Leader',
                'contact_phone' => '9876543219',
                'is_available' => true,
            ],
            [
                'name' => 'Meenangadi Cricket Ground',
                'description' => 'Local cricket ground in Meenangadi area, used for practice and local matches.',
                'capacity' => 250,
                'address' => 'Meenangadi, Wayanad',
                'state_id' => $keralaState->id,
                'district_id' => $wayanadDistrict->id,
                'localbody_id' => $this->getLocalBodyId('Meenangadi'),
                'contact_person' => 'Local Sports Coordinator',
                'contact_phone' => '9876543220',
                'is_available' => true,
            ],
            [
                'name' => 'Muttil Cricket Ground',
                'description' => 'Local cricket ground in Muttil area, popular for weekend matches and practice sessions.',
                'capacity' => 320,
                'address' => 'Muttil, Wayanad',
                'state_id' => $keralaState->id,
                'district_id' => $wayanadDistrict->id,
                'localbody_id' => $this->getLocalBodyId('Muttil'),
                'contact_person' => 'Ground Manager',
                'contact_phone' => '9876543221',
                'is_available' => true,
            ],
            [
                'name' => 'Edavaka Sports Complex',
                'description' => 'Multi-purpose sports complex in Edavaka with dedicated cricket facilities.',
                'capacity' => 500,
                'address' => 'Edavaka, Wayanad',
                'state_id' => $keralaState->id,
                'district_id' => $wayanadDistrict->id,
                'localbody_id' => $this->getLocalBodyId('Edavaka'),
                'contact_person' => 'Complex Manager',
                'contact_phone' => '9876543222',
                'is_available' => true,
            ],
            [
                'name' => 'Meppadi Cricket Academy',
                'description' => 'Professional cricket academy in Meppadi with coaching facilities and training grounds.',
                'capacity' => 400,
                'address' => 'Meppadi, Wayanad',
                'state_id' => $keralaState->id,
                'district_id' => $wayanadDistrict->id,
                'localbody_id' => $this->getLocalBodyId('Meppadi'),
                'contact_person' => 'Academy Coach',
                'contact_phone' => '9876543223',
                'is_available' => true,
            ],
            [
                'name' => 'Thavinjal Community Ground',
                'description' => 'Community cricket ground in Thavinjal, used by local cricket enthusiasts and clubs.',
                'capacity' => 250,
                'address' => 'Thavinjal, Wayanad',
                'state_id' => $keralaState->id,
                'district_id' => $wayanadDistrict->id,
                'localbody_id' => $this->getLocalBodyId('Thavinjal'),
                'contact_person' => 'Community Sports Coordinator',
                'contact_phone' => '9876543224',
                'is_available' => true,
            ],
        ];

        // Create grounds
        foreach ($grounds as $groundData) {
            Ground::updateOrCreate(
                ['name' => $groundData['name']],
                $groundData
            );
        }

        $this->command->info('Successfully created ' . count($grounds) . ' cricket grounds in Wayanad!');
    }

    /**
     * Get local body ID by name
     */
    private function getLocalBodyId(string $localBodyName): int
    {
        $localBody = LocalBody::where('name', $localBodyName)->first();
        return $localBody ? $localBody->id : 1; // Default to Kalpetta if not found
    }
}
