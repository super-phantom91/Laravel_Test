<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'product_name' => 'Sample Widget',
            'quantity_in_stock' => 10,
            'price_per_item' => 9.99,
            'submitted_at' => now()->subDay(),
        ]);

        Product::create([
            'product_name' => 'Demo Gadget',
            'quantity_in_stock' => 5,
            'price_per_item' => 24.50,
            'submitted_at' => now()->subHours(3),
        ]);
    }
}
