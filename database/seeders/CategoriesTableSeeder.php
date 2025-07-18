<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Category;

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
                'name' => 'Templates',
                'slug' => 'templates',
            ],
            [
                'name' => 'Themes',
                'slug' => 'themes',
            ],
            [
                'name' => 'Digital Products',
                'slug' => 'digital-products',
            ],
            [
                'name' => 'Design Assets',
                'slug' => 'design-assets',
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
