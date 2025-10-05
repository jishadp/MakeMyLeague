<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Income Categories
            [
                'name' => 'Team Registration Fee',
                'description' => 'Income from team registration fees',
                'type' => 'income',
                'is_active' => true
            ],
            [
                'name' => 'Player Registration Fee',
                'description' => 'Income from individual player registration fees',
                'type' => 'income',
                'is_active' => true
            ],
            [
                'name' => 'Sponsor Income',
                'description' => 'Income from sponsors and partnerships',
                'type' => 'income',
                'is_active' => true
            ],
            [
                'name' => 'Ticket Sales',
                'description' => 'Income from ticket sales and entry fees',
                'type' => 'income',
                'is_active' => true
            ],
            [
                'name' => 'Merchandise Sales',
                'description' => 'Income from merchandise and apparel sales',
                'type' => 'income',
                'is_active' => true
            ],
            [
                'name' => 'Miscellaneous Income',
                'description' => 'Other miscellaneous income sources',
                'type' => 'income',
                'is_active' => true
            ],
            
            // Expense Categories
            [
                'name' => 'Transportation',
                'description' => 'Transportation costs for teams, officials, and equipment',
                'type' => 'expense',
                'is_active' => true
            ],
            [
                'name' => 'Rent',
                'description' => 'Venue rental and facility costs',
                'type' => 'expense',
                'is_active' => true
            ],
            [
                'name' => 'Trophies and Awards',
                'description' => 'Costs for trophies, medals, and awards',
                'type' => 'expense',
                'is_active' => true
            ],
            [
                'name' => 'Poster & Marketing',
                'description' => 'Marketing materials, posters, and promotional costs',
                'type' => 'expense',
                'is_active' => true
            ],
            [
                'name' => 'Equipment',
                'description' => 'Sports equipment and maintenance costs',
                'type' => 'expense',
                'is_active' => true
            ],
            [
                'name' => 'Referee Fees',
                'description' => 'Fees for referees and officials',
                'type' => 'expense',
                'is_active' => true
            ],
            [
                'name' => 'Medical & First Aid',
                'description' => 'Medical supplies and first aid costs',
                'type' => 'expense',
                'is_active' => true
            ],
            [
                'name' => 'Food & Beverages',
                'description' => 'Food and beverage costs for events',
                'type' => 'expense',
                'is_active' => true
            ],
            [
                'name' => 'Insurance',
                'description' => 'Insurance costs for the league',
                'type' => 'expense',
                'is_active' => true
            ],
            [
                'name' => 'Miscellaneous Expenses',
                'description' => 'Other miscellaneous expenses',
                'type' => 'expense',
                'is_active' => true
            ]
        ];

        foreach ($categories as $category) {
            ExpenseCategory::create($category);
        }

        $this->command->info('Expense categories seeded successfully!');
    }
}