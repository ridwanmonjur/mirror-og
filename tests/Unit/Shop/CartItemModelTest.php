<?php

namespace Tests\Unit\Shop;

use Tests\TestCase;
use App\Models\{CartItem, NewCart, Product, ProductVariant};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CartItemModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_cart()
    {
        $cart = NewCart::create([
            'user_id' => 1,
            'total' => 100.00,
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 50.00,
        ]);

        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'subtotal' => 100.00,
        ]);

        $this->assertInstanceOf(NewCart::class, $cartItem->cart);
        $this->assertEquals($cart->id, $cartItem->cart->id);
    }

    /** @test */
    public function it_belongs_to_product()
    {
        $cart = NewCart::create([
            'user_id' => 1,
            'total' => 100.00,
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 50.00,
        ]);

        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'subtotal' => 100.00,
        ]);

        $this->assertInstanceOf(Product::class, $cartItem->product);
        $this->assertEquals($product->id, $cartItem->product->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $cart = NewCart::create([
            'user_id' => 1,
            'total' => 150.00,
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 50.00,
        ]);

        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'subtotal' => 150.00,
        ]);

        $this->assertEquals($cart->id, $cartItem->cart_id);
        $this->assertEquals($product->id, $cartItem->product_id);
        $this->assertEquals(3, $cartItem->quantity);
        $this->assertEquals('150.00', $cartItem->subtotal);
    }

    /** @test */
    public function it_casts_subtotal_to_decimal()
    {
        $cart = NewCart::create([
            'user_id' => 1,
            'total' => 100.00,
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 50.00,
        ]);

        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'subtotal' => 100,
        ]);

        $this->assertIsString($cartItem->subtotal);
        $this->assertEquals('100.00', $cartItem->subtotal);
    }

    /** @test */
    public function it_belongs_to_many_product_variants()
    {
        $cart = NewCart::create([
            'user_id' => 1,
            'total' => 100.00,
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 50.00,
        ]);

        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'subtotal' => 100.00,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Size',
            'value' => 'Large',
            'stock' => 10,
        ]);

        $cartItem->cartProductVariants()->attach($variant->id);

        $this->assertCount(1, $cartItem->cartProductVariants);
    }

    /** @test */
    public function it_can_update_quantity()
    {
        $cart = NewCart::create([
            'user_id' => 1,
            'total' => 100.00,
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 50.00,
        ]);

        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'subtotal' => 100.00,
        ]);

        $cartItem->update(['quantity' => 5]);

        $this->assertEquals(5, $cartItem->fresh()->quantity);
    }
}
