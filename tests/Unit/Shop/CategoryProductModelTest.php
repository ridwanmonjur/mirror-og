<?php

namespace Tests\Unit\Shop;

use Tests\TestCase;
use App\Models\CategoryProduct;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CategoryProductModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $categoryProduct = CategoryProduct::create([
            'product_id' => 1,
            'category_id' => 1,
        ]);

        $this->assertEquals(1, $categoryProduct->product_id);
        $this->assertEquals(1, $categoryProduct->category_id);
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $categoryProduct = new CategoryProduct();

        $this->assertEquals('category_product', $categoryProduct->getTable());
    }

    /** @test */
    public function it_can_create_multiple_associations()
    {
        CategoryProduct::create([
            'product_id' => 1,
            'category_id' => 1,
        ]);

        CategoryProduct::create([
            'product_id' => 1,
            'category_id' => 2,
        ]);

        $this->assertEquals(2, CategoryProduct::where('product_id', 1)->count());
    }
}
