<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Get first user (you can change this to a specific user_id)
        $user = User::first();
        if (!$user) {
            $this->command->error('No users found. Please create a user first.');
            return;
        }

        $categories = Category::all();

        $products = [
            [
                'user_id' => $user->id,
                'category_id' => $categories->where('slug', 'electronics')->first()?->id,
                'name' => 'Wireless Bluetooth Headphones',
                'description' => 'High-quality wireless headphones with noise cancellation and 24-hour battery life.',
                'price' => 299.00,
                'compare_at_price' => 399.00,
                'stock' => 50,
                'sku' => 'WBH-001',
                'is_featured' => true,
                'images' => ['https://via.placeholder.com/400x400/3b82f6/ffffff?text=Headphones'],
            ],
            [
                'user_id' => $user->id,
                'category_id' => $categories->where('slug', 'electronics')->first()?->id,
                'name' => 'Smart Watch Pro',
                'description' => 'Advanced smartwatch with fitness tracking, heart rate monitor, and GPS.',
                'price' => 449.00,
                'compare_at_price' => 599.00,
                'stock' => 30,
                'sku' => 'SWP-001',
                'is_featured' => true,
                'images' => ['https://via.placeholder.com/400x400/3b82f6/ffffff?text=Smart+Watch'],
            ],
            [
                'user_id' => $user->id,
                'category_id' => $categories->where('slug', 'fashion')->first()?->id,
                'name' => 'Premium Leather Jacket',
                'description' => 'Genuine leather jacket with modern design and premium quality.',
                'price' => 899.00,
                'compare_at_price' => 1199.00,
                'stock' => 20,
                'sku' => 'PLJ-001',
                'is_featured' => true,
                'images' => ['https://via.placeholder.com/400x400/ec4899/ffffff?text=Leather+Jacket'],
            ],
            [
                'user_id' => $user->id,
                'category_id' => $categories->where('slug', 'fashion')->first()?->id,
                'name' => 'Designer Sunglasses',
                'description' => 'Stylish sunglasses with UV protection and scratch-resistant lenses.',
                'price' => 199.00,
                'compare_at_price' => 299.00,
                'stock' => 100,
                'sku' => 'DS-001',
                'is_featured' => false,
                'images' => ['https://via.placeholder.com/400x400/ec4899/ffffff?text=Sunglasses'],
            ],
            [
                'user_id' => $user->id,
                'category_id' => $categories->where('slug', 'home-living')->first()?->id,
                'name' => 'Modern LED Desk Lamp',
                'description' => 'Adjustable LED lamp with touch control and USB charging port.',
                'price' => 149.00,
                'compare_at_price' => 199.00,
                'stock' => 75,
                'sku' => 'MLDL-001',
                'is_featured' => true,
                'images' => ['https://via.placeholder.com/400x400/10b981/ffffff?text=Desk+Lamp'],
            ],
            [
                'user_id' => $user->id,
                'category_id' => $categories->where('slug', 'home-living')->first()?->id,
                'name' => 'Cozy Throw Blanket',
                'description' => 'Soft and warm throw blanket perfect for any living room.',
                'price' => 79.00,
                'compare_at_price' => 119.00,
                'stock' => 150,
                'sku' => 'CTB-001',
                'is_featured' => false,
                'images' => ['https://via.placeholder.com/400x400/10b981/ffffff?text=Blanket'],
            ],
            [
                'user_id' => $user->id,
                'category_id' => $categories->where('slug', 'sports-outdoors')->first()?->id,
                'name' => 'Yoga Mat Pro',
                'description' => 'Non-slip yoga mat with extra thickness for comfort.',
                'price' => 89.00,
                'compare_at_price' => 129.00,
                'stock' => 80,
                'sku' => 'YMP-001',
                'is_featured' => true,
                'images' => ['https://via.placeholder.com/400x400/f59e0b/ffffff?text=Yoga+Mat'],
            ],
            [
                'user_id' => $user->id,
                'category_id' => $categories->where('slug', 'sports-outdoors')->first()?->id,
                'name' => 'Camping Tent 4-Person',
                'description' => 'Waterproof camping tent with easy setup for outdoor adventures.',
                'price' => 349.00,
                'compare_at_price' => 499.00,
                'stock' => 25,
                'sku' => 'CT4P-001',
                'is_featured' => false,
                'images' => ['https://via.placeholder.com/400x400/f59e0b/ffffff?text=Camping+Tent'],
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
