<?php

namespace Tests\Unit\Shop;

use Tests\TestCase;
use App\Models\{Category, Product};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CategoryModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_allows_all_attributes_to_be_mass_assigned()
    {
        $category = Category::create([
            'name' => 'Electronics',
            'description' => 'Electronic products',
            'slug' => 'electronics',
        ]);

        $this->assertEquals('Electronics', $category->name);
        $this->assertEquals('Electronic products', $category->description);
        $this->assertEquals('electronics', $category->slug);
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $category = new Category();

        $this->assertEquals('category', $category->getTable());
    }

    /** @test */
    public function it_belongs_to_many_products()
    {
        $category = Category::create([
            'name' => 'Electronics',
        ]);

        $product = Product::create([
            'name' => 'Laptop',
            'slug' => 'laptop',
            'price' => 999.99,
        ]);

        $category->products()->attach($product->id);

        $this->assertCount(1, $category->products);
        $this->assertEquals($product->id, $category->products->first()->id);
    }

    /** @test */
    public function category_can_have_multiple_products()
    {
        $category = Category::create([
            'name' => 'Electronics',
        ]);

        $product1 = Product::create([
            'name' => 'Laptop',
            'slug' => 'laptop',
            'price' => 999.99,
        ]);

        $product2 = Product::create([
            'name' => 'Mouse',
            'slug' => 'mouse',
            'price' => 29.99,
        ]);

        $category->products()->attach([$product1->id, $product2->id]);

        $this->assertCount(2, $category->products);
    }
}
