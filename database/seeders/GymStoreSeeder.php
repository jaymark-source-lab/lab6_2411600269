<?php

namespace Database\Seeders;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GymStoreSeeder extends Seeder
{
    // replace old products with supplements, vitamins and gym accessories
    public function run(): void
    {
        // clear old data (transactions first, then alerts, then products)
        InventoryTransaction::query()->delete();
        DB::table('notifications')->delete();
        Product::query()->delete();

        $products = [
            // Food supplements
            ['name' => 'Whey Protein Isolate 5lb', 'sku' => 'SUP-001', 'category' => 'Food Supplements', 'description' => 'Fast-absorbing whey isolate, 25g protein per scoop', 'quantity' => 24, 'reorder_level' => 8, 'unit_price' => 69.99, 'supplier' => 'Optimum Nutrition'],
            ['name' => 'Creatine Monohydrate 300g', 'sku' => 'SUP-002', 'category' => 'Food Supplements', 'description' => 'Micronized creatine, unflavored', 'quantity' => 30, 'reorder_level' => 10, 'unit_price' => 24.99, 'supplier' => 'MuscleTech'],
            ['name' => 'Pre-Workout Fruit Punch (30 servings)', 'sku' => 'SUP-003', 'category' => 'Food Supplements', 'description' => 'Energy and focus pre-workout formula', 'quantity' => 6, 'reorder_level' => 8, 'unit_price' => 34.99, 'supplier' => 'Cellucor'],
            ['name' => 'BCAA Powder (30 servings)', 'sku' => 'SUP-004', 'category' => 'Food Supplements', 'description' => '2:1:1 amino acids for recovery', 'quantity' => 18, 'reorder_level' => 6, 'unit_price' => 27.50, 'supplier' => 'Xtend'],
            ['name' => 'Mass Gainer 6lb', 'sku' => 'SUP-005', 'category' => 'Food Supplements', 'description' => 'High-calorie gainer for bulking', 'quantity' => 4, 'reorder_level' => 5, 'unit_price' => 54.99, 'supplier' => 'Dymatize'],
            ['name' => 'Protein Bar Box (12 pcs)', 'sku' => 'SUP-006', 'category' => 'Food Supplements', 'description' => '20g protein bars, mixed flavors', 'quantity' => 40, 'reorder_level' => 12, 'unit_price' => 29.99, 'supplier' => 'Quest Nutrition'],

            // Vitamins
            ['name' => 'Multivitamin for Men (90 tablets)', 'sku' => 'VIT-001', 'category' => 'Vitamins', 'description' => 'Daily multivitamin for active adults', 'quantity' => 35, 'reorder_level' => 10, 'unit_price' => 19.99, 'supplier' => 'Centrum'],
            ['name' => 'Vitamin C 1000mg (100 tablets)', 'sku' => 'VIT-002', 'category' => 'Vitamins', 'description' => 'Immune support with rose hips', 'quantity' => 50, 'reorder_level' => 15, 'unit_price' => 12.50, 'supplier' => "Nature's Bounty"],
            ['name' => 'Vitamin D3 5000 IU (120 softgels)', 'sku' => 'VIT-003', 'category' => 'Vitamins', 'description' => 'Bone and muscle support', 'quantity' => 0, 'reorder_level' => 10, 'unit_price' => 14.99, 'supplier' => 'NOW Foods'],
            ['name' => 'Fish Oil Omega-3 (120 softgels)', 'sku' => 'VIT-004', 'category' => 'Vitamins', 'description' => 'EPA/DHA for joints and heart', 'quantity' => 22, 'reorder_level' => 8, 'unit_price' => 18.75, 'supplier' => 'Nordic Naturals'],
            ['name' => 'ZMA Zinc + Magnesium (90 capsules)', 'sku' => 'VIT-005', 'category' => 'Vitamins', 'description' => 'Recovery and sleep support', 'quantity' => 9, 'reorder_level' => 6, 'unit_price' => 16.99, 'supplier' => 'Optimum Nutrition'],

            // Gym accessories
            ['name' => 'Lifting Wrist Wraps (Pair)', 'sku' => 'ACC-001', 'category' => 'Gym Accessories', 'description' => '18-inch wrist wraps with thumb loop', 'quantity' => 45, 'reorder_level' => 12, 'unit_price' => 14.99, 'supplier' => 'Rogue Fitness'],
            ['name' => 'Sweat Wristband (Pair)', 'sku' => 'ACC-002', 'category' => 'Gym Accessories', 'description' => 'Terry cloth sweatbands', 'quantity' => 60, 'reorder_level' => 20, 'unit_price' => 6.99, 'supplier' => 'Nike'],
            ['name' => 'Leather Weightlifting Belt', 'sku' => 'ACC-003', 'category' => 'Gym Accessories', 'description' => '10mm leather belt with steel buckle', 'quantity' => 3, 'reorder_level' => 5, 'unit_price' => 59.99, 'supplier' => 'Rogue Fitness'],
            ['name' => 'Neoprene Waist Trimmer Belt', 'sku' => 'ACC-004', 'category' => 'Gym Accessories', 'description' => 'Adjustable sweat belt for core workouts', 'quantity' => 25, 'reorder_level' => 8, 'unit_price' => 19.99, 'supplier' => 'Sweet Sweat'],
            ['name' => 'Lifting Straps (Pair)', 'sku' => 'ACC-005', 'category' => 'Gym Accessories', 'description' => 'Padded cotton straps for deadlifts', 'quantity' => 30, 'reorder_level' => 10, 'unit_price' => 12.99, 'supplier' => 'Harbinger'],
            ['name' => 'Shaker Bottle 700ml', 'sku' => 'ACC-006', 'category' => 'Gym Accessories', 'description' => 'Leak-proof shaker with mixing ball', 'quantity' => 70, 'reorder_level' => 20, 'unit_price' => 9.99, 'supplier' => 'BlenderBottle'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}