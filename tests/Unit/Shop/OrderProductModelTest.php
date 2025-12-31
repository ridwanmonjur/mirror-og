<?php

namespace Tests\Unit\Shop;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{OrderProduct, Order, Product, ProductVariant};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderProductModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_product()
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

        $this->assertInstanceOf(Product::class, $orderProduct->product);
        $this->assertEquals($product->id, $orderProduct->product->id);
    }

    /** @test */
    public function it_belongs_to_order()
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

        $this->assertInstanceOf(Order::class, $orderProduct->order);
        $this->assertEquals($order->id, $orderProduct->order->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();

        $order = Order::create([
            'user_id' => $user->id,
            'billing_subtotal' => 150.00,
            'billing_total' => 150.00,
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 50.00,
        ]);

        $orderProduct = OrderProduct::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $this->assertEquals($order->id, $orderProduct->order_id);
        $this->assertEquals($product->id, $orderProduct->product_id);
        $this->assertEquals(3, $orderProduct->quantity);
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $orderProduct = new OrderProduct();

        $this->assertEquals('order_product', $orderProduct->getTable());
    }

    /** @test */
    public function it_belongs_to_many_product_variants()
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

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Size',
            'value' => 'Large',
            'stock' => 10,
        ]);

        $orderProduct->orderProductVariants()->attach($variant->id);

        $this->assertCount(1, $orderProduct->orderProductVariants);
    }
}
