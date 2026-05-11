<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronics = Category::where('name', 'Electronics')->first();
        $fashion = Category::where('name', 'Fashion')->first();

        if ($electronics) {
            Product::create([
                'name' => 'iPhone 15 Pro',
                'category_id' => $electronics->id,
                'description' => 'Titanium design, A17 Pro chip, customizable Action button, and a more versatile Pro camera system.',
                'price' => 999.00,
                'stock' => 50,
                'status' => 'active',
            ]);

            Product::create([
                'name' => 'MacBook Air M2',
                'category_id' => $electronics->id,
                'description' => 'Strikingly thin and fast, the M2 chip brings exceptional performance and up to 18 hours of battery life.',
                'price' => 1199.00,
                'stock' => 30,
                'status' => 'active',
            ]);
        }

        if ($fashion) {
            Product::create([
                'name' => 'Leather Jacket',
                'category_id' => $fashion->id,
                'description' => 'Premium quality leather jacket with a modern fit.',
                'price' => 199.99,
                'stock' => 20,
                'status' => 'active',
            ]);

            Product::create([
                'name' => 'Casual Sneakers',
                'category_id' => $fashion->id,
                'description' => 'Comfy and stylish sneakers for everyday wear.',
                'price' => 79.50,
                'stock' => 100,
                'status' => 'active',
            ]);
        }

        // Generate 50 random products
        Product::factory(50)->create();
    }
}
