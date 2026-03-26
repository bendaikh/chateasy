<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Electronic devices and gadgets',
                'icon' => 'devices',
                'color' => '#3b82f6',
                'order' => 1,
            ],
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'description' => 'Clothing and accessories',
                'icon' => 'checkroom',
                'color' => '#ec4899',
                'order' => 2,
            ],
            [
                'name' => 'Home & Living',
                'slug' => 'home-living',
                'description' => 'Home decoration and furniture',
                'icon' => 'home',
                'color' => '#10b981',
                'order' => 3,
            ],
            [
                'name' => 'Sports & Outdoors',
                'slug' => 'sports-outdoors',
                'description' => 'Sports equipment and outdoor gear',
                'icon' => 'sports_soccer',
                'color' => '#f59e0b',
                'order' => 4,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
