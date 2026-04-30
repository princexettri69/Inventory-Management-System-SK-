<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\FinanceCategory;
use App\Enums\FinanceCategoryType;

class FinanceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Income
            [
                'name'        => 'Product Sales',
                'type'        => FinanceCategoryType::Income,
                'description' => 'Revenue from direct product sales.',
            ],
            [
                'name'        => 'Service Income',
                'type'        => FinanceCategoryType::Income,
                'description' => 'Income from services or consultations.',
            ],
            [
                'name'        => 'Investment Returns',
                'type'        => FinanceCategoryType::Income,
                'description' => 'Dividends or interest from capital investments.',
            ],
            [
                'name'        => 'Other Income',
                'type'        => FinanceCategoryType::Income,
                'description' => 'Income outside core business operations.',
            ],

            // Expenses
            [
                'name'        => 'Staff Salaries',
                'type'        => FinanceCategoryType::Expense,
                'description' => 'Monthly salaries and allowances for employees.',
            ],
            [
                'name'        => 'Rent',
                'type'        => FinanceCategoryType::Expense,
                'description' => 'Shop or warehouse rental expenses.',
            ],
            [
                'name'        => 'Electricity & Water',
                'type'        => FinanceCategoryType::Expense,
                'description' => 'Monthly utility bills.',
            ],
            [
                'name'        => 'Internet & Phone',
                'type'        => FinanceCategoryType::Expense,
                'description' => 'Communication and internet connectivity costs.',
            ],
            [
                'name'        => 'Marketing & Advertising',
                'type'        => FinanceCategoryType::Expense,
                'description' => 'Costs for promotions, social media ads, and print.',
            ],
            [
                'name'        => 'Maintenance & Repairs',
                'type'        => FinanceCategoryType::Expense,
                'description' => 'Asset and equipment maintenance costs.',
            ],
            [
                'name'        => 'Transportation & Logistics',
                'type'        => FinanceCategoryType::Expense,
                'description' => 'Fuel, delivery, and travel expenses.',
            ],
            [
                'name'        => 'Stock Purchase',
                'type'        => FinanceCategoryType::Expense,
                'description' => 'Cost of goods purchased for resale (COGS).',
            ],
            [
                'name'        => 'Office Supplies',
                'type'        => FinanceCategoryType::Expense,
                'description' => 'Stationery and office consumables.',
            ],
            [
                'name'        => 'Miscellaneous Expenses',
                'type'        => FinanceCategoryType::Expense,
                'description' => 'Other business expenses not categorized above.',
            ],
        ];

        foreach ($categories as $category) {
            FinanceCategory::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name'        => $category['name'],
                    'type'        => $category['type'],
                    'description' => $category['description'],
                ]
            );
        }
    }
}
