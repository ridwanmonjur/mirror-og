<?php

namespace Tests\Unit\Shop;

use Tests\TestCase;
use App\Models\{Product, Category, ProductVariant};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'details' => 'Product details',
            'price' => 99.99,
            'description' => 'Product description',
            'image' => 'product.jpg',
            'images' => ['image1.jpg', 'image2.jpg'],
            'featured' => true,
            'isPhysical' => true,
        ]);

        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals('test-product', $product->slug);
        $this->assertEquals('Product details', $product->details);
        $this->assertEquals(99.99, $product->price);
        $this->assertEquals('Product description', $product->description);
        $this->assertEquals('product.jpg', $product->image);
        $this->assertTrue($product->featured);
        $this->assertTrue($product->isPhysical);
    }

    /** @test */
    public function it_casts_images_to_array()
    {
        $images = ['image1.jpg', 'image2.jpg', 'image3.jpg'];

        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 99.99,
            'images' => $images,
        ]);

        $this->assertIsArray($product->images);
        $this->assertEquals($images, $product->images);
    }

    /** @test */
    public function it_casts_featured_to_boolean()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 99.99,
            'featured' => 1,
        ]);

        $this->assertIsBool($product->featured);
        $this->assertTrue($product->featured);
    }

    /** @test */
    public function it_casts_is_physical_to_boolean()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 99.99,
            'isPhysical' => 0,
        ]);

        $this->assertIsBool($product->isPhysical);
        $this->assertFalse($product->isPhysical);
    }

    /** @test */
    public function it_belongs_to_many_categories()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 99.99,
        ]);

        $category = Category::create([
            'name' => 'Test Category',
        ]);

        $product->categories()->attach($category->id);

        $this->assertCount(1, $product->categories);
        $this->assertEquals($category->id, $product->categories->first()->id);
    }

    /** @test */
    public function it_has_many_product_variants()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 99.99,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Size',
            'value' => 'Large',
            'stock' => 10,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $product->productVariants);
        $this->assertCount(1, $product->productVariants);
        $this->assertEquals($variant->id, $product->productVariants->first()->id);
    }

    /** @test */
    public function it_has_per_page_set_to_twelve()
    {
        $product = new Product();

        $this->assertEquals(12, $product->getPerPage());
    }
}
