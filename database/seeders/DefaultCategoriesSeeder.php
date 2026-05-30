<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Entertainment
            ['name' => 'Entertainment', 'group' => 'Entertainment', 'type' => 'expense', 'icon' => 'fa-solid fa-wand-magic-sparkles', 'color' => '#f43f5e'],
            ['name' => 'Cinema', 'group' => 'Entertainment', 'type' => 'expense', 'icon' => 'fa-solid fa-film', 'color' => '#f43f5e'],
            ['name' => 'Sports', 'group' => 'Entertainment', 'type' => 'expense', 'icon' => 'fa-solid fa-futbol', 'color' => '#f43f5e'],
            ['name' => 'Gym', 'group' => 'Entertainment', 'type' => 'expense', 'icon' => 'fa-solid fa-dumbbell', 'color' => '#f43f5e'],
            ['name' => 'Vacation', 'group' => 'Entertainment', 'type' => 'expense', 'icon' => 'fa-solid fa-umbrella-beach', 'color' => '#f43f5e'],

            // Food & drinks
            ['name' => 'Food', 'group' => 'Food & drinks', 'type' => 'expense', 'icon' => 'fa-solid fa-utensils', 'color' => '#60a5fa'],
            ['name' => 'Drink', 'group' => 'Food & drinks', 'type' => 'expense', 'icon' => 'fa-solid fa-wine-glass', 'color' => '#60a5fa'],
            ['name' => 'Coffee', 'group' => 'Food & drinks', 'type' => 'expense', 'icon' => 'fa-solid fa-mug-hot', 'color' => '#60a5fa'],
            ['name' => 'Restaurants', 'group' => 'Food & drinks', 'type' => 'expense', 'icon' => 'fa-solid fa-bowl-food', 'color' => '#60a5fa'],
            ['name' => 'Groceries', 'group' => 'Food & drinks', 'type' => 'expense', 'icon' => 'fa-solid fa-basket-shopping', 'color' => '#60a5fa'],

            // Income
            ['name' => 'Income', 'group' => 'Income', 'type' => 'income', 'icon' => 'fa-solid fa-money-bill', 'color' => '#a3e635'],
            ['name' => 'Salary', 'group' => 'Income', 'type' => 'income', 'icon' => 'fa-solid fa-money-check-dollar', 'color' => '#a3e635'],
            ['name' => 'Investments', 'group' => 'Income', 'type' => 'income', 'icon' => 'fa-solid fa-chart-line', 'color' => '#a3e635'],
            ['name' => 'Interest', 'group' => 'Income', 'type' => 'income', 'icon' => 'fa-solid fa-percent', 'color' => '#a3e635'],

            // Lifestyle
            ['name' => 'Lifestyle', 'group' => 'Lifestyle', 'type' => 'expense', 'icon' => 'fa-solid fa-star', 'color' => '#fb7185'],
            ['name' => 'Shopping', 'group' => 'Lifestyle', 'type' => 'expense', 'icon' => 'fa-solid fa-bag-shopping', 'color' => '#fb7185'],
            ['name' => 'Cosmetic', 'group' => 'Lifestyle', 'type' => 'expense', 'icon' => 'fa-solid fa-spray-can', 'color' => '#fb7185'],
            ['name' => 'Gift', 'group' => 'Lifestyle', 'type' => 'expense', 'icon' => 'fa-solid fa-gift', 'color' => '#fb7185'],
            ['name' => 'Pharmacie', 'group' => 'Lifestyle', 'type' => 'expense', 'icon' => 'fa-solid fa-capsules', 'color' => '#fb7185'],
            ['name' => 'Dentist', 'group' => 'Lifestyle', 'type' => 'expense', 'icon' => 'fa-solid fa-tooth', 'color' => '#fb7185'],
            ['name' => 'Hotel', 'group' => 'Lifestyle', 'type' => 'expense', 'icon' => 'fa-solid fa-bed', 'color' => '#fb7185'],
            ['name' => 'Doctor', 'group' => 'Lifestyle', 'type' => 'expense', 'icon' => 'fa-solid fa-user-doctor', 'color' => '#fb7185'],
            ['name' => 'Donation', 'group' => 'Lifestyle', 'type' => 'expense', 'icon' => 'fa-solid fa-hand-holding-heart', 'color' => '#fb7185'],
            ['name' => 'Education', 'group' => 'Lifestyle', 'type' => 'expense', 'icon' => 'fa-solid fa-graduation-cap', 'color' => '#fb7185'],
            ['name' => 'Clothes', 'group' => 'Lifestyle', 'type' => 'expense', 'icon' => 'fa-solid fa-shirt', 'color' => '#fb7185'],
            ['name' => 'Healthcare', 'group' => 'Lifestyle', 'type' => 'expense', 'icon' => 'fa-solid fa-heart-pulse', 'color' => '#fb7185'],
            ['name' => 'Child care', 'group' => 'Lifestyle', 'type' => 'expense', 'icon' => 'fa-solid fa-child-reaching', 'color' => '#fb7185'],
            ['name' => 'Pets', 'group' => 'Lifestyle', 'type' => 'expense', 'icon' => 'fa-solid fa-paw', 'color' => '#fb7185'],
            ['name' => 'Travel', 'group' => 'Lifestyle', 'type' => 'expense', 'icon' => 'fa-solid fa-plane-departure', 'color' => '#fb7185'],
            ['name' => 'Subscriptions', 'group' => 'Lifestyle', 'type' => 'expense', 'icon' => 'fa-solid fa-file-invoice', 'color' => '#fb7185'],

            // Transportation
            ['name' => 'Transportation', 'group' => 'Transportation', 'type' => 'expense', 'icon' => 'fa-solid fa-car', 'color' => '#64748b'],
            ['name' => 'Gasoline', 'group' => 'Transportation', 'type' => 'expense', 'icon' => 'fa-solid fa-gas-pump', 'color' => '#64748b'],
            ['name' => 'Taxi', 'group' => 'Transportation', 'type' => 'expense', 'icon' => 'fa-solid fa-taxi', 'color' => '#64748b'],
            ['name' => 'Flight', 'group' => 'Transportation', 'type' => 'expense', 'icon' => 'fa-solid fa-plane', 'color' => '#64748b'],
            ['name' => 'Reparation', 'group' => 'Transportation', 'type' => 'expense', 'icon' => 'fa-solid fa-wrench', 'color' => '#64748b'],
            ['name' => 'Maintenance and oil changes', 'group' => 'Transportation', 'type' => 'expense', 'icon' => 'fa-solid fa-oil-can', 'color' => '#64748b'],
            ['name' => 'Parking', 'group' => 'Transportation', 'type' => 'expense', 'icon' => 'fa-solid fa-square-parking', 'color' => '#64748b'],
            ['name' => 'Car Insurance', 'group' => 'Transportation', 'type' => 'expense', 'icon' => 'fa-solid fa-shield-halved', 'color' => '#64748b'],
            ['name' => 'Car loan', 'group' => 'Transportation', 'type' => 'expense', 'icon' => 'fa-solid fa-car-side', 'color' => '#64748b'],

            // Housing
            ['name' => 'Housing', 'group' => 'Housing', 'type' => 'expense', 'icon' => 'fa-solid fa-house', 'color' => '#f59e0b'],
            ['name' => 'Rent', 'group' => 'Housing', 'type' => 'expense', 'icon' => 'fa-solid fa-house-user', 'color' => '#f59e0b'],
            ['name' => 'Insurance', 'group' => 'Housing', 'type' => 'expense', 'icon' => 'fa-solid fa-shield-halved', 'color' => '#f59e0b'],
            ['name' => 'Loan', 'group' => 'Housing', 'type' => 'expense', 'icon' => 'fa-solid fa-building-columns', 'color' => '#f59e0b'],
            ['name' => 'Bills', 'group' => 'Housing', 'type' => 'expense', 'icon' => 'fa-solid fa-file-invoice', 'color' => '#f59e0b'],
            ['name' => 'Electricity', 'group' => 'Housing', 'type' => 'expense', 'icon' => 'fa-solid fa-bolt', 'color' => '#f59e0b'],
            ['name' => 'Home supplies', 'group' => 'Housing', 'type' => 'expense', 'icon' => 'fa-solid fa-broom', 'color' => '#f59e0b'],
            ['name' => 'Water', 'group' => 'Housing', 'type' => 'expense', 'icon' => 'fa-solid fa-droplet', 'color' => '#f59e0b'],
            ['name' => 'Internet', 'group' => 'Housing', 'type' => 'expense', 'icon' => 'fa-solid fa-wifi', 'color' => '#f59e0b'],
            ['name' => 'TV', 'group' => 'Housing', 'type' => 'expense', 'icon' => 'fa-solid fa-tv', 'color' => '#f59e0b'],
            ['name' => 'Maintenance', 'group' => 'Housing', 'type' => 'expense', 'icon' => 'fa-solid fa-hammer', 'color' => '#f59e0b'],

            // Savings
            ['name' => 'Savings', 'group' => 'Savings', 'type' => 'saving', 'icon' => 'fa-solid fa-piggy-bank', 'color' => '#fcd34d'],
            ['name' => 'Vacation Savings', 'group' => 'Savings', 'type' => 'saving', 'icon' => 'fa-solid fa-plane', 'color' => '#fcd34d'],
            ['name' => 'Emergency Savings', 'group' => 'Savings', 'type' => 'saving', 'icon' => 'fa-solid fa-kit-medical', 'color' => '#fcd34d'],

            // Miscellaneous
            ['name' => 'Miscellaneous', 'group' => 'Miscellaneous', 'type' => 'expense', 'icon' => 'fa-solid fa-square', 'color' => '#9ca3af'],
            ['name' => 'Taxes', 'group' => 'Miscellaneous', 'type' => 'expense', 'icon' => 'fa-solid fa-percent', 'color' => '#9ca3af'],
        ];

        // Clear existing default categories
        DB::table('categories')->where('is_default', true)->delete();

        foreach ($categories as $cat) {
            DB::table('categories')->updateOrInsert(
                ['name' => $cat['name'], 'group' => $cat['group']],
                array_merge($cat, [
                    'user_id' => null,
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
