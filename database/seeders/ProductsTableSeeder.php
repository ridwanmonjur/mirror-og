<?php

namespace Database\Seeders;

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
            [
                'name' => 'MacBook Pro',
                'slug' => 'macbook-pro',
                'details' => 'Apple MacBook Pro 16-inch with M1 Pro chip',
                'price' => 8500, // ~$1999 / 100 = $19.99, then ~RM 85
                'description' => 'The most powerful MacBook Pro ever is here. With the blazing-fast M1 Pro chip — the first chip designed specifically for pros.',
                'image' => 'macbook-pro.png',
                'images' => '["macbook-pro.png","macbook-pro-laravel.png"]',
                'quantity' => 15,
                'featured' => true,
            ],
            [
                'name' => 'Laravel MacBook Pro',
                'slug' => 'laravel-macbook-pro',
                'details' => 'MacBook Pro with Laravel development environment',
                'price' => 9700, // ~$2299 / 100 = $22.99, then ~RM 97
                'description' => 'MacBook Pro specially configured for Laravel development with all necessary tools pre-installed.',
                'image' => 'macbook-pro-laravel.png',
                'images' => '["macbook-pro-laravel.png","macbook-pro.png"]',
                'quantity' => 8,
                'featured' => true,
            ],
            [
                'name' => 'Blog Post Template',
                'slug' => 'blog-post-template',
                'details' => 'Premium blog post template design',
                'price' => 127, // ~$29.99 / 100 = $0.30, then ~RM 1.27
                'description' => 'Beautiful and responsive blog post template perfect for any blogging platform.',
                'image' => 'blog1.png',
                'images' => '["blog1.png","blog2.png","blog3.png"]',
                'quantity' => 50,
                'featured' => false,
            ],
            [
                'name' => 'Creative Blog Theme',
                'slug' => 'creative-blog-theme',
                'details' => 'Modern creative blog theme',
                'price' => 170, // ~$39.99 / 100 = $0.40, then ~RM 1.70
                'description' => 'A modern and creative blog theme with stunning visual elements and smooth animations.',
                'image' => 'blog2.png',
                'images' => '["blog2.png","blog1.png","blog3.png"]',
                'quantity' => 30,
                'featured' => false,
            ],
            [
                'name' => 'Minimalist Blog Design',
                'slug' => 'minimalist-blog-design',
                'details' => 'Clean minimalist blog design',
                'price' => 85, // ~$19.99 / 100 = $0.20, then ~RM 0.85
                'description' => 'Clean and minimalist blog design focusing on content readability and user experience.',
                'image' => 'blog3.png',
                'images' => '["blog3.png","blog1.png","blog2.png"]',
                'quantity' => 40,
                'featured' => false,
            ],
            [
                'name' => 'Social Media Post Pack',
                'slug' => 'social-media-post-pack',
                'details' => 'Collection of social media post templates',
                'price' => 212, // ~$49.99 / 100 = $0.50, then ~RM 2.12
                'description' => 'Professional social media post templates for various platforms including Instagram, Facebook, and Twitter.',
                'image' => 'posts/post1.jpg',
                'images' => '["posts/post1.jpg","posts/post2.jpg","posts/post3.jpg","posts/post4.jpg"]',
                'quantity' => 100,
                'featured' => true,
            ],
            [
                'name' => 'Page Layout Bundle',
                'slug' => 'page-layout-bundle',
                'details' => 'Complete page layout bundle',
                'price' => 340, // ~$79.99 / 100 = $0.80, then ~RM 3.40
                'description' => 'Complete bundle of page layouts including landing pages, about pages, and contact forms.',
                'image' => 'pages/page1.jpg',
                'images' => '["pages/page1.jpg"]',
                'quantity' => 25,
                'featured' => false,
            ],
            [
                'name' => 'Gaming Desktop PC',
                'slug' => 'gaming-desktop-pc',
                'details' => 'High-performance gaming desktop',
                'price' => 6375, // ~$1499.99 / 100 = $15.00, then ~RM 63.75
                'description' => 'Powerful gaming desktop with latest graphics card and processor for ultimate gaming experience.',
                'image' => 'desktop-gaming.jpg',
                'images' => '["desktop-gaming.jpg"]',
                'quantity' => 12,
                'featured' => true,
            ],
            [
                'name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'details' => 'Latest iPhone with Pro features',
                'price' => 4250, // ~$999.99 / 100 = $10.00, then ~RM 42.50
                'description' => 'iPhone 15 Pro with advanced camera system and A17 Pro chip.',
                'image' => 'iphone-15-pro.jpg',
                'images' => '["iphone-15-pro.jpg"]',
                'quantity' => 20,
                'featured' => true,
            ],
            [
                'name' => 'iPad Air',
                'slug' => 'ipad-air',
                'details' => 'Lightweight and powerful tablet',
                'price' => 2550, // ~$599.99 / 100 = $6.00, then ~RM 25.50
                'description' => 'iPad Air with M1 chip, perfect for productivity and entertainment.',
                'image' => 'ipad-air.jpg',
                'images' => '["ipad-air.jpg"]',
                'quantity' => 15,
                'featured' => false,
            ],
            [
                'name' => 'Samsung 65" 4K TV',
                'slug' => 'samsung-65-4k-tv',
                'details' => 'Ultra HD Smart TV',
                'price' => 3400, // ~$799.99 / 100 = $8.00, then ~RM 34.00
                'description' => 'Samsung 65-inch 4K Smart TV with HDR and built-in streaming apps.',
                'image' => 'samsung-tv.jpg',
                'images' => '["samsung-tv.jpg"]',
                'quantity' => 8,
                'featured' => false,
            ],
            [
                'name' => 'Canon EOS R5',
                'slug' => 'canon-eos-r5',
                'details' => 'Professional mirrorless camera',
                'price' => 16575, // ~$3899.99 / 100 = $39.00, then ~RM 165.75
                'description' => 'Canon EOS R5 full-frame mirrorless camera with 8K video recording.',
                'image' => 'canon-r5.jpg',
                'images' => '["canon-r5.jpg"]',
                'quantity' => 5,
                'featured' => true,
            ],
            [
                'name' => 'Smart Refrigerator',
                'slug' => 'smart-refrigerator',
                'details' => 'Wi-Fi enabled smart fridge',
                'price' => 8500, // ~$1999.99 / 100 = $20.00, then ~RM 85.00
                'description' => 'Energy-efficient smart refrigerator with touchscreen and app control.',
                'image' => 'smart-fridge.jpg',
                'images' => '["smart-fridge.jpg"]',
                'quantity' => 6,
                'featured' => false,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::firstOrCreate(
                ['slug' => $productData['slug']],
                $productData
            );
            
            // Attach categories to products
            switch ($product->slug) {
                case 'macbook-pro':
                case 'laravel-macbook-pro':
                    $laptops = Category::where('slug', 'laptops')->first();
                    if ($laptops && !$product->categories()->where('category_id', $laptops->id)->exists()) {
                        $product->categories()->attach($laptops->id);
                    }
                    break;
                case 'gaming-desktop-pc':
                    $desktops = Category::where('slug', 'desktops')->first();
                    if ($desktops && !$product->categories()->where('category_id', $desktops->id)->exists()) {
                        $product->categories()->attach($desktops->id);
                    }
                    break;
                case 'iphone-15-pro':
                    $phones = Category::where('slug', 'mobile-phones')->first();
                    if ($phones && !$product->categories()->where('category_id', $phones->id)->exists()) {
                        $product->categories()->attach($phones->id);
                    }
                    break;
                case 'ipad-air':
                    $tablets = Category::where('slug', 'tablets')->first();
                    if ($tablets && !$product->categories()->where('category_id', $tablets->id)->exists()) {
                        $product->categories()->attach($tablets->id);
                    }
                    break;
                case 'samsung-65-4k-tv':
                    $tvs = Category::where('slug', 'tvs')->first();
                    if ($tvs && !$product->categories()->where('category_id', $tvs->id)->exists()) {
                        $product->categories()->attach($tvs->id);
                    }
                    break;
                case 'canon-eos-r5':
                    $cameras = Category::where('slug', 'digital-cameras')->first();
                    if ($cameras && !$product->categories()->where('category_id', $cameras->id)->exists()) {
                        $product->categories()->attach($cameras->id);
                    }
                    break;
                case 'smart-refrigerator':
                case 'blog-post-template':
                case 'creative-blog-theme':
                case 'minimalist-blog-design':
                case 'social-media-post-pack':
                case 'page-layout-bundle':
                    $appliances = Category::where('slug', 'appliances')->first();
                    if ($appliances && !$product->categories()->where('category_id', $appliances->id)->exists()) {
                        $product->categories()->attach($appliances->id);
                    }
                    break;
            }
        }
    }
}
