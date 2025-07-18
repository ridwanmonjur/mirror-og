<?php

use Illuminate\Database\Seeder;
use App\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
            ],
            [
                'name' => 'Clothing',
                'slug' => 'clothing',
            ],
            [
                'name' => 'Books',
                'slug' => 'books',
            ],
            [
                'name' => 'Home & Garden',
                'slug' => 'home-garden',
            ],
            [
                'name' => 'Sports & Fitness',
                'slug' => 'sports-fitness',
            ],
            [
                'name' => 'Beauty & Health',
                'slug' => 'beauty-health',
            ],
            [
                'name' => 'Toys & Games',
                'slug' => 'toys-games',
            ],
            [
                'name' => 'Automotive',
                'slug' => 'automotive',
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::firstOrNew([
                'slug' => $categoryData['slug'],
            ]);
            if (!$category->exists) {
                $category->fill($categoryData)->save();
            }
        }
    }
}
