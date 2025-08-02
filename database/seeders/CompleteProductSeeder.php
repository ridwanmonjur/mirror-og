<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompleteProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data
        ProductVariant::truncate();
        DB::table('category_product')->truncate();
        Product::truncate();
        Category::truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create categories and products with variants
        $this->createElectronicsProducts();
        $this->createClothingProducts();
        $this->createHomeProducts();
        $this->createBooksProducts();
        $this->createSportsProducts();
    }

    private function createElectronicsProducts()
    {
        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        // iPhone 15
        $iphone = Product::create([
            'name' => 'iPhone 15',
            'slug' => 'iphone-15',
            'details' => 'Latest iPhone with advanced camera system',
            'price' => 2999,
            'description' => 'iPhone 15 with advanced camera system, A17 chip, and all-day battery life.',
            'featured' => true,
        ]);
        $iphone->categories()->attach($category->id);

        ProductVariant::create(['product_id' => $iphone->id, 'name' => 'storage', 'value' => '128GB', 'stock' => 15]);
        ProductVariant::create(['product_id' => $iphone->id, 'name' => 'storage', 'value' => '256GB', 'stock' => 12]);
        ProductVariant::create(['product_id' => $iphone->id, 'name' => 'storage', 'value' => '512GB', 'stock' => 8]);
        ProductVariant::create(['product_id' => $iphone->id, 'name' => 'color', 'value' => 'Black', 'stock' => 20]);
        ProductVariant::create(['product_id' => $iphone->id, 'name' => 'color', 'value' => 'Blue', 'stock' => 18]);
        ProductVariant::create(['product_id' => $iphone->id, 'name' => 'color', 'value' => 'Pink', 'stock' => 15]);

        // MacBook Pro
        $macbook = Product::create([
            'name' => 'MacBook Pro 14-inch',
            'slug' => 'macbook-pro-14',
            'details' => 'Professional laptop with M3 chip',
            'price' => 7999,
            'description' => 'MacBook Pro with M3 chip, 14-inch display, perfect for developers and creators.',
            'featured' => true,
        ]);
        $macbook->categories()->attach($category->id);

        ProductVariant::create(['product_id' => $macbook->id, 'name' => 'memory', 'value' => '16GB', 'stock' => 10]);
        ProductVariant::create(['product_id' => $macbook->id, 'name' => 'memory', 'value' => '32GB', 'stock' => 8]);
        ProductVariant::create(['product_id' => $macbook->id, 'name' => 'storage', 'value' => '512GB', 'stock' => 12]);
        ProductVariant::create(['product_id' => $macbook->id, 'name' => 'storage', 'value' => '1TB', 'stock' => 6]);

        // Wireless Headphones
        $headphones = Product::create([
            'name' => 'Wireless Noise-Cancelling Headphones',
            'slug' => 'wireless-headphones',
            'details' => 'Premium noise-cancelling headphones',
            'price' => 899,
            'description' => 'High-quality wireless headphones with active noise cancellation and 30-hour battery life.',
            'featured' => false,
        ]);
        $headphones->categories()->attach($category->id);

        ProductVariant::create(['product_id' => $headphones->id, 'name' => 'color', 'value' => 'Black', 'stock' => 25]);
        ProductVariant::create(['product_id' => $headphones->id, 'name' => 'color', 'value' => 'White', 'stock' => 20]);
        ProductVariant::create(['product_id' => $headphones->id, 'name' => 'color', 'value' => 'Silver', 'stock' => 15]);
    }

    private function createClothingProducts()
    {
        $category = Category::create([
            'name' => 'Clothing',
            'slug' => 'clothing',
        ]);

        // T-Shirt
        $tshirt = Product::create([
            'name' => 'Premium Cotton T-Shirt',
            'slug' => 'premium-cotton-tshirt',
            'details' => '100% organic cotton comfortable fit',
            'price' => 89,
            'description' => 'Soft, comfortable t-shirt made from 100% organic cotton. Perfect for everyday wear.',
            'featured' => true,
        ]);
        $tshirt->categories()->attach($category->id);

        ProductVariant::create(['product_id' => $tshirt->id, 'name' => 'size', 'value' => 'S', 'stock' => 30]);
        ProductVariant::create(['product_id' => $tshirt->id, 'name' => 'size', 'value' => 'M', 'stock' => 35]);
        ProductVariant::create(['product_id' => $tshirt->id, 'name' => 'size', 'value' => 'L', 'stock' => 40]);
        ProductVariant::create(['product_id' => $tshirt->id, 'name' => 'size', 'value' => 'XL', 'stock' => 25]);
        ProductVariant::create(['product_id' => $tshirt->id, 'name' => 'color', 'value' => 'White', 'stock' => 50]);
        ProductVariant::create(['product_id' => $tshirt->id, 'name' => 'color', 'value' => 'Black', 'stock' => 45]);
        ProductVariant::create(['product_id' => $tshirt->id, 'name' => 'color', 'value' => 'Navy', 'stock' => 30]);

        // Jeans
        $jeans = Product::create([
            'name' => 'Classic Denim Jeans',
            'slug' => 'classic-denim-jeans',
            'details' => 'Straight fit denim jeans',
            'price' => 249,
            'description' => 'Classic straight-fit jeans made from premium denim. Comfortable and durable.',
            'featured' => false,
        ]);
        $jeans->categories()->attach($category->id);

        ProductVariant::create(['product_id' => $jeans->id, 'name' => 'size', 'value' => '30', 'stock' => 15]);
        ProductVariant::create(['product_id' => $jeans->id, 'name' => 'size', 'value' => '32', 'stock' => 20]);
        ProductVariant::create(['product_id' => $jeans->id, 'name' => 'size', 'value' => '34', 'stock' => 18]);
        ProductVariant::create(['product_id' => $jeans->id, 'name' => 'size', 'value' => '36', 'stock' => 12]);
        ProductVariant::create(['product_id' => $jeans->id, 'name' => 'color', 'value' => 'Blue', 'stock' => 30]);
        ProductVariant::create(['product_id' => $jeans->id, 'name' => 'color', 'value' => 'Black', 'stock' => 25]);
    }

    private function createHomeProducts()
    {
        $category = Category::create([
            'name' => 'Home & Garden',
            'slug' => 'home-garden',
        ]);

        // Coffee Maker
        $coffeeMaker = Product::create([
            'name' => 'Smart Coffee Maker',
            'slug' => 'smart-coffee-maker',
            'details' => 'WiFi-enabled programmable coffee maker',
            'price' => 599,
            'description' => 'Smart coffee maker with WiFi connectivity, programmable brewing, and smartphone app control.',
            'featured' => true,
        ]);
        $coffeeMaker->categories()->attach($category->id);

        ProductVariant::create(['product_id' => $coffeeMaker->id, 'name' => 'capacity', 'value' => '8-cup', 'stock' => 15]);
        ProductVariant::create(['product_id' => $coffeeMaker->id, 'name' => 'capacity', 'value' => '12-cup', 'stock' => 12]);
        ProductVariant::create(['product_id' => $coffeeMaker->id, 'name' => 'color', 'value' => 'Black', 'stock' => 20]);
        ProductVariant::create(['product_id' => $coffeeMaker->id, 'name' => 'color', 'value' => 'Stainless Steel', 'stock' => 18]);

        // Throw Pillow
        $pillow = Product::create([
            'name' => 'Luxury Throw Pillow',
            'slug' => 'luxury-throw-pillow',
            'details' => 'Premium velvet throw pillow',
            'price' => 129,
            'description' => 'Luxurious velvet throw pillow with down filling. Perfect accent for any room.',
            'featured' => false,
        ]);
        $pillow->categories()->attach($category->id);

        ProductVariant::create(['product_id' => $pillow->id, 'name' => 'size', 'value' => '16x16', 'stock' => 25]);
        ProductVariant::create(['product_id' => $pillow->id, 'name' => 'size', 'value' => '18x18', 'stock' => 20]);
        ProductVariant::create(['product_id' => $pillow->id, 'name' => 'size', 'value' => '20x20', 'stock' => 15]);
        ProductVariant::create(['product_id' => $pillow->id, 'name' => 'color', 'value' => 'Blue', 'stock' => 30]);
        ProductVariant::create(['product_id' => $pillow->id, 'name' => 'color', 'value' => 'Gray', 'stock' => 25]);
        ProductVariant::create(['product_id' => $pillow->id, 'name' => 'color', 'value' => 'Beige', 'stock' => 20]);
    }

    private function createBooksProducts()
    {
        $category = Category::create([
            'name' => 'Books',
            'slug' => 'books',
        ]);

        // Programming Book
        $programmingBook = Product::create([
            'name' => 'Laravel Development Guide',
            'slug' => 'laravel-development-guide',
            'details' => 'Complete guide to Laravel framework',
            'price' => 159,
            'description' => 'Comprehensive guide to Laravel development with practical examples and best practices.',
            'featured' => true,
        ]);
        $programmingBook->categories()->attach($category->id);

        ProductVariant::create(['product_id' => $programmingBook->id, 'name' => 'format', 'value' => 'Paperback', 'stock' => 50]);
        ProductVariant::create(['product_id' => $programmingBook->id, 'name' => 'format', 'value' => 'Hardcover', 'stock' => 30]);
        ProductVariant::create(['product_id' => $programmingBook->id, 'name' => 'format', 'value' => 'eBook', 'stock' => 100]);

        // Novel
        $novel = Product::create([
            'name' => 'The Digital Nomad',
            'slug' => 'the-digital-nomad',
            'details' => 'Fiction novel about modern work culture',
            'price' => 79,
            'description' => 'An engaging novel exploring the world of remote work and digital entrepreneurship.',
            'featured' => false,
        ]);
        $novel->categories()->attach($category->id);

        ProductVariant::create(['product_id' => $novel->id, 'name' => 'format', 'value' => 'Paperback', 'stock' => 40]);
        ProductVariant::create(['product_id' => $novel->id, 'name' => 'format', 'value' => 'eBook', 'stock' => 80]);
    }

    private function createSportsProducts()
    {
        $category = Category::create([
            'name' => 'Sports & Fitness',
            'slug' => 'sports-fitness',
        ]);

        // Running Shoes
        $runningShoes = Product::create([
            'name' => 'Professional Running Shoes',
            'slug' => 'professional-running-shoes',
            'details' => 'High-performance running shoes',
            'price' => 399,
            'description' => 'Professional-grade running shoes with advanced cushioning and breathable mesh upper.',
            'featured' => true,
        ]);
        $runningShoes->categories()->attach($category->id);

        ProductVariant::create(['product_id' => $runningShoes->id, 'name' => 'size', 'value' => '7', 'stock' => 12]);
        ProductVariant::create(['product_id' => $runningShoes->id, 'name' => 'size', 'value' => '8', 'stock' => 15]);
        ProductVariant::create(['product_id' => $runningShoes->id, 'name' => 'size', 'value' => '9', 'stock' => 18]);
        ProductVariant::create(['product_id' => $runningShoes->id, 'name' => 'size', 'value' => '10', 'stock' => 20]);
        ProductVariant::create(['product_id' => $runningShoes->id, 'name' => 'size', 'value' => '11', 'stock' => 15]);
        ProductVariant::create(['product_id' => $runningShoes->id, 'name' => 'color', 'value' => 'Black', 'stock' => 35]);
        ProductVariant::create(['product_id' => $runningShoes->id, 'name' => 'color', 'value' => 'White', 'stock' => 30]);
        ProductVariant::create(['product_id' => $runningShoes->id, 'name' => 'color', 'value' => 'Blue', 'stock' => 25]);

        // Yoga Mat
        $yogaMat = Product::create([
            'name' => 'Premium Yoga Mat',
            'slug' => 'premium-yoga-mat',
            'details' => 'Non-slip eco-friendly yoga mat',
            'price' => 189,
            'description' => 'High-quality eco-friendly yoga mat with excellent grip and cushioning for all types of yoga practice.',
            'featured' => false,
        ]);
        $yogaMat->categories()->attach($category->id);

        ProductVariant::create(['product_id' => $yogaMat->id, 'name' => 'thickness', 'value' => '4mm', 'stock' => 20]);
        ProductVariant::create(['product_id' => $yogaMat->id, 'name' => 'thickness', 'value' => '6mm', 'stock' => 25]);
        ProductVariant::create(['product_id' => $yogaMat->id, 'name' => 'thickness', 'value' => '8mm', 'stock' => 15]);
        ProductVariant::create(['product_id' => $yogaMat->id, 'name' => 'color', 'value' => 'Purple', 'stock' => 30]);
        ProductVariant::create(['product_id' => $yogaMat->id, 'name' => 'color', 'value' => 'Green', 'stock' => 25]);
        ProductVariant::create(['product_id' => $yogaMat->id, 'name' => 'color', 'value' => 'Pink', 'stock' => 20]);
    }
}
