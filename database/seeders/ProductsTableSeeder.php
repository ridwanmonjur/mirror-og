<?php

namespace Database\Seeders;

use App\Product;
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
                'price' => 199900,
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
                'price' => 229900,
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
                'price' => 2999,
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
                'price' => 3999,
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
                'price' => 1999,
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
                'price' => 4999,
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
                'price' => 7999,
                'description' => 'Complete bundle of page layouts including landing pages, about pages, and contact forms.',
                'image' => 'pages/page1.jpg',
                'images' => '["pages/page1.jpg"]',
                'quantity' => 25,
                'featured' => false,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
