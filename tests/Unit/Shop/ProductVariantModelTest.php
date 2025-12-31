<?php

namespace Tests\Unit\Shop;

use Tests\TestCase;
use App\Models\{Product, ProductVariant, CartItem};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductVariantModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_product()
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

        $this->assertInstanceOf(Product::class, $variant->product);
        $this->assertEquals($product->id, $variant->product->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 99.99,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Color',
            'value' => 'Red',
            'stock' => 25,
        ]);

        $this->assertEquals($product->id, $variant->product_id);
        $this->assertEquals('Color', $variant->name);
        $this->assertEquals('Red', $variant->value);
        $this->assertEquals(25, $variant->stock);
    }

    /** @test */
    public function it_casts_stock_to_integer()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 99.99,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Size',
            'value' => 'Medium',
            'stock' => '50',
        ]);

        $this->assertIsInt($variant->stock);
        $this->assertEquals(50, $variant->stock);
    }

    /** @test */
    public function it_belongs_to_many_cart_items()
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

        $cartItem = CartItem::create([
            'cart_id' => 1,
            'product_id' => $product->id,
            'quantity' => 2,
            'subtotal' => 199.98,
        ]);

        $variant->cartItems()->attach($cartItem->id);

        $this->assertCount(1, $variant->cartItems);
    }

    /** @test */
    public function it_can_update_stock()
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

        $variant->update(['stock' => 5]);

        $this->assertEquals(5, $variant->fresh()->stock);
    }
}
