<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Adjustable Dumbbell Set 5-50lb', 'sku' => 'STR-001', 'category' => 'Strength Equipment', 'description' => 'Adjustable dumbbell pair with quick-lock system', 'quantity' => 12, 'reorder_level' => 5, 'unit_price' => 249.99, 'supplier' => 'Bowflex'],
            ['name' => 'Olympic Barbell 45lb', 'sku' => 'STR-002', 'category' => 'Strength Equipment', 'description' => '7ft Olympic barbell, 1500lb capacity', 'quantity' => 6, 'reorder_level' => 4, 'unit_price' => 189.00, 'supplier' => 'Rogue Fitness'],
            ['name' => 'Bumper Plates 45lb (Pair)', 'sku' => 'STR-003', 'category' => 'Strength Equipment', 'description' => 'Rubber-coated Olympic bumper plates', 'quantity' => 3, 'reorder_level' => 5, 'unit_price' => 165.00, 'supplier' => 'Rogue Fitness'],
            ['name' => 'Kettlebell 35lb', 'sku' => 'STR-004', 'category' => 'Strength Equipment', 'description' => 'Cast iron kettlebell, vinyl coated', 'quantity' => 20, 'reorder_level' => 8, 'unit_price' => 54.99, 'supplier' => 'Rep Fitness'],
            ['name' => 'Treadmill Commercial X3', 'sku' => 'CAR-005', 'category' => 'Cardio Equipment', 'description' => 'Heavy-duty commercial treadmill, 3.5HP motor', 'quantity' => 2, 'reorder_level' => 2, 'unit_price' => 3499.00, 'supplier' => 'Life Fitness'],
            ['name' => 'Stationary Bike Pro', 'sku' => 'CAR-006', 'category' => 'Cardio Equipment', 'description' => 'Magnetic resistance stationary bike', 'quantity' => 5, 'reorder_level' => 3, 'unit_price' => 899.00, 'supplier' => 'Peloton'],
            ['name' => 'Rowing Machine Concept2', 'sku' => 'CAR-007', 'category' => 'Cardio Equipment', 'description' => 'Air resistance rowing machine', 'quantity' => 4, 'reorder_level' => 2, 'unit_price' => 1099.00, 'supplier' => 'Concept2'],
            ['name' => 'Yoga Mat Premium 6mm', 'sku' => 'ACC-008', 'category' => 'Accessories', 'description' => 'Non-slip TPE yoga mat', 'quantity' => 85, 'reorder_level' => 20, 'unit_price' => 24.99, 'supplier' => 'Manduka'],
            ['name' => 'Resistance Bands Set', 'sku' => 'ACC-009', 'category' => 'Accessories', 'description' => 'Set of 5 resistance bands, varying tension', 'quantity' => 60, 'reorder_level' => 15, 'unit_price' => 19.99, 'supplier' => 'TheraBand'],
            ['name' => 'Foam Roller 18in', 'sku' => 'ACC-010', 'category' => 'Accessories', 'description' => 'High-density foam roller for recovery', 'quantity' => 40, 'reorder_level' => 12, 'unit_price' => 22.50, 'supplier' => 'TriggerPoint'],
            ['name' => 'Weight Bench Adjustable', 'sku' => 'STR-011', 'category' => 'Strength Equipment', 'description' => 'Flat-to-incline adjustable bench', 'quantity' => 7, 'reorder_level' => 4, 'unit_price' => 179.00, 'supplier' => 'Rep Fitness'],
            ['name' => 'Pull-Up Bar Wall Mount', 'sku' => 'STR-012', 'category' => 'Strength Equipment', 'description' => 'Heavy-duty wall-mounted pull-up bar', 'quantity' => 9, 'reorder_level' => 5, 'unit_price' => 65.00, 'supplier' => 'Titan Fitness'],
            ['name' => 'Jump Rope Speed', 'sku' => 'ACC-013', 'category' => 'Accessories', 'description' => 'Adjustable steel cable speed rope', 'quantity' => 95, 'reorder_level' => 25, 'unit_price' => 14.99, 'supplier' => 'Crossrope'],
            ['name' => 'Whey Protein Powder 5lb', 'sku' => 'SUP-014', 'category' => 'Supplements', 'description' => 'Whey protein isolate, chocolate flavor', 'quantity' => 4, 'reorder_level' => 10, 'unit_price' => 59.99, 'supplier' => 'Optimum Nutrition'],
            ['name' => 'Gym Towel Microfiber (Pack of 12)', 'sku' => 'ACC-015', 'category' => 'Accessories', 'description' => 'Quick-dry microfiber gym towels', 'quantity' => 50, 'reorder_level' => 15, 'unit_price' => 34.99, 'supplier' => 'Gym Supply Co.'],
            ['name' => 'Elliptical Trainer E7', 'sku' => 'CAR-016', 'category' => 'Cardio Equipment', 'description' => 'Low-impact elliptical machine', 'quantity' => 3, 'reorder_level' => 2, 'unit_price' => 1799.00, 'supplier' => 'Life Fitness'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}