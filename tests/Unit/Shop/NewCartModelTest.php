<?php

namespace Tests\Unit\Shop;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{NewCart, User, CartItem, Product};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NewCartModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();

        $cart = NewCart::create([
            'user_id' => $user->id,
            'total' => 100.00,
        ]);

        $this->assertInstanceOf(User::class, $cart->user);
        $this->assertEquals($user->id, $cart->user->id);
    }

    /** @test */
    public function it_has_many_items()
    {
        $user = $this->createParticipant();

        $cart = NewCart::create([
            'user_id' => $user->id,
            'total' => 100.00,
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 50.00,
        ]);

        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'subtotal' => 100.00,
        ]);

        $this->assertCount(1, $cart->items);
        $this->assertEquals($item->id, $cart->items->first()->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();

        $cart = NewCart::create([
            'user_id' => $user->id,
            'total' => 250.50,
        ]);

        $this->assertEquals($user->id, $cart->user_id);
        $this->assertEquals('250.50', $cart->total);
    }

    /** @test */
    public function it_casts_total_to_decimal()
    {
        $user = $this->createParticipant();

        $cart = NewCart::create([
            'user_id' => $user->id,
            'total' => 100,
        ]);

        $this->assertIsString($cart->total);
        $this->assertEquals('100.00', $cart->total);
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $cart = new NewCart();

        $this->assertEquals('final_carts', $cart->getTable());
    }

    /** @test */
    public function it_can_get_user_cart()
    {
        $user = $this->createParticipant();

        $cart = NewCart::getUserCart($user->id);

        $this->assertInstanceOf(NewCart::class, $cart);
        $this->assertEquals($user->id, $cart->user_id);
    }

    /** @test */
    public function it_can_get_count()
    {
        $user = $this->createParticipant();

        $cart = NewCart::create([
            'user_id' => $user->id,
            'total' => 100.00,
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 50.00,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'subtotal' => 150.00,
        ]);

        $count = $cart->getCount();

        $this->assertEquals(3, $count);
    }

    /** @test */
    public function it_can_get_subtotal()
    {
        $user = $this->createParticipant();

        $cart = NewCart::create([
            'user_id' => $user->id,
            'total' => 150.00,
        ]);

        $subtotal = $cart->getSubTotal();

        $this->assertEquals('150.00', $subtotal);
    }

    /** @test */
    public function it_can_get_total()
    {
        $user = $this->createParticipant();

        $cart = NewCart::create([
            'user_id' => $user->id,
            'total' => 200.00,
        ]);

        $total = $cart->getTotal();

        $this->assertEquals('200.00', $total);
    }

    /** @test */
    public function it_can_get_content()
    {
        $user = $this->createParticipant();

        $cart = NewCart::create([
            'user_id' => $user->id,
            'total' => 100.00,
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 50.00,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'subtotal' => 100.00,
        ]);

        $content = $cart->getContent();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $content);
        $this->assertCount(1, $content);
    }

    /** @test */
    public function it_can_get_numbers()
    {
        $user = $this->createParticipant();

        $cart = NewCart::create([
            'user_id' => $user->id,
            'total' => 100.00,
        ]);

        $numbers = $cart->getNumbers();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $numbers);
        $this->assertArrayHasKey('discount', $numbers);
        $this->assertArrayHasKey('newSubtotal', $numbers);
        $this->assertArrayHasKey('newTotal', $numbers);
    }
}
