<?php
namespace Database\Seeders;

use App\Category;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'Laptops', 'slug' => 'laptops'],
            ['name' => 'Desktops', 'slug' => 'desktops'],
            ['name' => 'Mobile Phones', 'slug' => 'mobile-phones'],
            ['name' => 'Tablets', 'slug' => 'tablets'],
            ['name' => 'TVs', 'slug' => 'tvs'],
            ['name' => 'Digital Cameras', 'slug' => 'digital-cameras'],
            ['name' => 'Appliances', 'slug' => 'appliances'],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }
    }
}
