<?php

namespace Tests\Unit\Shop;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{Order, User, Product, OrderProduct};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();

        $order = Order::create([
            'user_id' => $user->id,
            'billing_subtotal' => 100.00,
            'billing_total' => 100.00,
        ]);

        $this->assertInstanceOf(User::class, $order->user);
        $this->assertEquals($user->id, $order->user->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();

        $order = Order::create([
            'user_id' => $user->id,
            'billing_discount' => 10.00,
            'billing_discount_code' => 'SAVE10',
            'billing_subtotal' => 100.00,
            'billing_total' => 90.00,
        ]);

        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals(10.00, $order->billing_discount);
        $this->assertEquals('SAVE10', $order->billing_discount_code);
        $this->assertEquals(100.00, $order->billing_subtotal);
        $this->assertEquals(90.00, $order->billing_total);
    }

    /** @test */
    public function it_belongs_to_many_products()
    {
        $user = $this->createParticipant();

        $order = Order::create([
            'user_id' => $user->id,
            'billing_subtotal' => 100.00,
            'billing_total' => 100.00,
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 50.00,
        ]);

        $order->products()->attach($product->id, ['quantity' => 2]);

        $this->assertCount(1, $order->products);
        $this->assertEquals($product->id, $order->products->first()->id);
        $this->assertEquals(2, $order->products->first()->pivot->quantity);
    }

    /** @test */
    public function it_has_many_order_products()
    {
        $user = $this->createParticipant();

        $order = Order::create([
            'user_id' => $user->id,
            'billing_subtotal' => 100.00,
            'billing_total' => 100.00,
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 50.00,
        ]);

        $orderProduct = OrderProduct::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $order->orderProducts);
        $this->assertCount(1, $order->orderProducts);
        $this->assertEquals($orderProduct->id, $order->orderProducts->first()->id);
    }

    /** @test */
    public function it_can_calculate_order_with_discount()
    {
        $user = $this->createParticipant();

        $order = Order::create([
            'user_id' => $user->id,
            'billing_discount' => 15.00,
            'billing_discount_code' => 'SAVE15',
            'billing_subtotal' => 150.00,
            'billing_total' => 135.00,
        ]);

        $this->assertEquals(135.00, $order->billing_total);
        $this->assertEquals(15.00, $order->billing_subtotal - $order->billing_total);
    }
}
