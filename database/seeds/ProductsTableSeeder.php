<?php

use App\Product;
use App\Category;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            // Electronics
            [
                'name' => 'MacBook Pro 16-inch',
                'slug' => 'macbook-pro-16',
                'details' => 'Apple MacBook Pro 16-inch with M1 Pro chip',
                'price' => 249900,
                'description' => 'The most powerful MacBook Pro ever is here. With the blazing-fast M1 Pro chip — the first chip designed specifically for pros.',
                'image' => 'macbook-pro.png',
                'images' => '["macbook-pro.png"]',
                'quantity' => 15,
                'featured' => true,
            ],
            [
                'name' => 'iPhone 14 Pro',
                'slug' => 'iphone-14-pro',
                'details' => 'iPhone 14 Pro with Dynamic Island',
                'price' => 99900,
                'description' => 'iPhone 14 Pro features the Dynamic Island, a 48MP Main camera, and A16 Bionic chip.',
                'image' => 'iphone-14-pro.jpg',
                'images' => '["iphone-14-pro.jpg"]',
                'quantity' => 25,
                'featured' => true,
            ],
            [
                'name' => 'Samsung Galaxy S23',
                'slug' => 'samsung-galaxy-s23',
                'details' => 'Samsung Galaxy S23 5G Smartphone',
                'price' => 79900,
                'description' => 'Experience the power of Samsung Galaxy S23 with advanced camera system and 5G connectivity.',
                'image' => 'samsung-s23.jpg',
                'images' => '["samsung-s23.jpg"]',
                'quantity' => 20,
                'featured' => false,
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'slug' => 'sony-wh-1000xm5',
                'details' => 'Sony WH-1000XM5 Wireless Headphones',
                'price' => 39900,
                'description' => 'Industry-leading noise canceling headphones with exceptional sound quality.',
                'image' => 'sony-headphones.jpg',
                'images' => '["sony-headphones.jpg"]',
                'quantity' => 30,
                'featured' => false,
            ],
            
            // Clothing
            [
                'name' => 'Nike Air Max 90',
                'slug' => 'nike-air-max-90',
                'details' => 'Nike Air Max 90 Sneakers',
                'price' => 12999,
                'description' => 'Classic Nike Air Max 90 sneakers with timeless design and superior comfort.',
                'image' => 'nike-air-max.jpg',
                'images' => '["nike-air-max.jpg"]',
                'quantity' => 50,
                'featured' => true,
            ],
            [
                'name' => 'Levi\'s 501 Jeans',
                'slug' => 'levis-501-jeans',
                'details' => 'Levi\'s 501 Original Fit Jeans',
                'price' => 8999,
                'description' => 'The original blue jean. Modeled after the 1955 501 Jean.',
                'image' => 'levis-jeans.jpg',
                'images' => '["levis-jeans.jpg"]',
                'quantity' => 40,
                'featured' => false,
            ],
            [
                'name' => 'Adidas Ultraboost 22',
                'slug' => 'adidas-ultraboost-22',
                'details' => 'Adidas Ultraboost 22 Running Shoes',
                'price' => 18999,
                'description' => 'Experience incredible energy return with every step in these running shoes.',
                'image' => 'adidas-ultraboost.jpg',
                'images' => '["adidas-ultraboost.jpg"]',
                'quantity' => 35,
                'featured' => false,
            ],
            
            // Books
            [
                'name' => 'Clean Code',
                'slug' => 'clean-code-book',
                'details' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
                'price' => 4999,
                'description' => 'A handbook of agile software craftsmanship by Robert C. Martin.',
                'image' => 'clean-code.jpg',
                'images' => '["clean-code.jpg"]',
                'quantity' => 100,
                'featured' => false,
            ],
            [
                'name' => 'Laravel: Up & Running',
                'slug' => 'laravel-up-running',
                'details' => 'Laravel: Up & Running by Matt Stauffer',
                'price' => 5999,
                'description' => 'A comprehensive guide to Laravel framework development.',
                'image' => 'laravel-book.jpg',
                'images' => '["laravel-book.jpg"]',
                'quantity' => 75,
                'featured' => true,
            ],
            
            // Home & Garden
            [
                'name' => 'Dyson V15 Detect',
                'slug' => 'dyson-v15-detect',
                'details' => 'Dyson V15 Detect Cordless Vacuum',
                'price' => 74999,
                'description' => 'Dyson\'s most powerful cordless vacuum with laser dust detection.',
                'image' => 'dyson-vacuum.jpg',
                'images' => '["dyson-vacuum.jpg"]',
                'quantity' => 15,
                'featured' => false,
            ],
            [
                'name' => 'Instant Pot Duo 7-in-1',
                'slug' => 'instant-pot-duo-7in1',
                'details' => 'Instant Pot Duo 7-in-1 Electric Pressure Cooker',
                'price' => 9999,
                'description' => '7 appliances in 1: Pressure Cooker, Slow Cooker, Rice Cooker, Steamer, Sauté, Yogurt Maker, and Warmer.',
                'image' => 'instant-pot.jpg',
                'images' => '["instant-pot.jpg"]',
                'quantity' => 25,
                'featured' => false,
            ],
            
            // Sports & Fitness
            [
                'name' => 'Peloton Bike+',
                'slug' => 'peloton-bike-plus',
                'details' => 'Peloton Bike+ Indoor Exercise Bike',
                'price' => 249999,
                'description' => 'The ultimate indoor cycling experience with live and on-demand classes.',
                'image' => 'peloton-bike.jpg',
                'images' => '["peloton-bike.jpg"]',
                'quantity' => 5,
                'featured' => true,
            ],
            [
                'name' => 'Yoga Mat Premium',
                'slug' => 'yoga-mat-premium',
                'details' => 'Premium Non-Slip Yoga Mat',
                'price' => 3999,
                'description' => 'High-quality yoga mat with excellent grip and cushioning.',
                'image' => 'yoga-mat.jpg',
                'images' => '["yoga-mat.jpg"]',
                'quantity' => 60,
                'featured' => false,
            ],
            
            // Beauty & Health
            [
                'name' => 'Ordinary Niacinamide',
                'slug' => 'ordinary-niacinamide',
                'details' => 'The Ordinary Niacinamide 10% + Zinc 1%',
                'price' => 799,
                'description' => 'High-strength vitamin and mineral blemish formula.',
                'image' => 'ordinary-niacinamide.jpg',
                'images' => '["ordinary-niacinamide.jpg"]',
                'quantity' => 200,
                'featured' => false,
            ],
            [
                'name' => 'Olaplex Hair Perfector',
                'slug' => 'olaplex-hair-perfector',
                'details' => 'Olaplex No.3 Hair Perfector',
                'price' => 2999,
                'description' => 'At-home treatment to reduce breakage and strengthen hair.',
                'image' => 'olaplex.jpg',
                'images' => '["olaplex.jpg"]',
                'quantity' => 80,
                'featured' => false,
            ],
            
            // Toys & Games
            [
                'name' => 'LEGO Creator 3-in-1',
                'slug' => 'lego-creator-3in1',
                'details' => 'LEGO Creator 3-in-1 Deep Sea Creatures',
                'price' => 7999,
                'description' => 'Build a shark, squid, or anglerfish with this 3-in-1 set.',
                'image' => 'lego-creator.jpg',
                'images' => '["lego-creator.jpg"]',
                'quantity' => 40,
                'featured' => false,
            ],
            [
                'name' => 'Nintendo Switch OLED',
                'slug' => 'nintendo-switch-oled',
                'details' => 'Nintendo Switch OLED Model',
                'price' => 34999,
                'description' => 'Nintendo Switch with 7-inch OLED screen for handheld play.',
                'image' => 'nintendo-switch.jpg',
                'images' => '["nintendo-switch.jpg"]',
                'quantity' => 30,
                'featured' => true,
            ],
            
            // Automotive
            [
                'name' => 'Tesla Model 3 Accessories Kit',
                'slug' => 'tesla-model-3-kit',
                'details' => 'Tesla Model 3 Premium Accessories Kit',
                'price' => 29999,
                'description' => 'Complete accessories kit for Tesla Model 3 including floor mats, organizers, and charging cables.',
                'image' => 'tesla-accessories.jpg',
                'images' => '["tesla-accessories.jpg"]',
                'quantity' => 20,
                'featured' => false,
            ],
            [
                'name' => 'Michelin Pilot Sport 4S',
                'slug' => 'michelin-pilot-sport-4s',
                'details' => 'Michelin Pilot Sport 4S Tires (Set of 4)',
                'price' => 119999,
                'description' => 'High-performance tires for sports cars and performance vehicles.',
                'image' => 'michelin-tires.jpg',
                'images' => '["michelin-tires.jpg"]',
                'quantity' => 12,
                'featured' => false,
            ],
        ];

        // Get categories
        $categories = [
            'electronics' => Category::where('slug', 'electronics')->first(),
            'clothing' => Category::where('slug', 'clothing')->first(),
            'books' => Category::where('slug', 'books')->first(),
            'home-garden' => Category::where('slug', 'home-garden')->first(),
            'sports-fitness' => Category::where('slug', 'sports-fitness')->first(),
            'beauty-health' => Category::where('slug', 'beauty-health')->first(),
            'toys-games' => Category::where('slug', 'toys-games')->first(),
            'automotive' => Category::where('slug', 'automotive')->first(),
        ];

        // Product to category mapping
        $productCategories = [
            'macbook-pro-16' => 'electronics',
            'iphone-14-pro' => 'electronics',
            'samsung-galaxy-s23' => 'electronics',
            'sony-wh-1000xm5' => 'electronics',
            'nike-air-max-90' => 'clothing',
            'levis-501-jeans' => 'clothing',
            'adidas-ultraboost-22' => 'clothing',
            'clean-code-book' => 'books',
            'laravel-up-running' => 'books',
            'dyson-v15-detect' => 'home-garden',
            'instant-pot-duo-7in1' => 'home-garden',
            'peloton-bike-plus' => 'sports-fitness',
            'yoga-mat-premium' => 'sports-fitness',
            'ordinary-niacinamide' => 'beauty-health',
            'olaplex-hair-perfector' => 'beauty-health',
            'lego-creator-3in1' => 'toys-games',
            'nintendo-switch-oled' => 'toys-games',
            'tesla-model-3-kit' => 'automotive',
            'michelin-pilot-sport-4s' => 'automotive',
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);
            
            // Assign product to category
            $categorySlug = $productCategories[$productData['slug']] ?? null;
            if ($categorySlug && isset($categories[$categorySlug])) {
                $product->categories()->attach($categories[$categorySlug]->id);
            }
        }
    }
}
