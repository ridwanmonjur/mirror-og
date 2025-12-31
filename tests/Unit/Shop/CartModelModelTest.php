<?php

namespace Tests\Unit\Shop;

use Tests\TestCase;
use App\Models\CartModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CartModelModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $cartModel = CartModel::create([
            'user_id' => 1,
            'cart_data' => ['item1' => 'value1'],
        ]);

        $this->assertEquals(1, $cartModel->user_id);
        $this->assertIsArray($cartModel->cart_data);
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $cartModel = new CartModel();

        $this->assertEquals('cart_storage', $cartModel->getTable());
    }

    /** @test */
    public function it_serializes_cart_data_on_set()
    {
        $data = ['item1' => 'product1', 'item2' => 'product2'];

        $cartModel = CartModel::create([
            'user_id' => 1,
            'cart_data' => $data,
        ]);

        $this->assertIsArray($cartModel->cart_data);
        $this->assertEquals($data, $cartModel->cart_data);
    }

    /** @test */
    public function it_unserializes_cart_data_on_get()
    {
        $data = ['items' => ['product1', 'product2'], 'total' => 100];

        $cartModel = CartModel::create([
            'user_id' => 1,
            'cart_data' => $data,
        ]);

        $fresh = $cartModel->fresh();

        $this->assertIsArray($fresh->cart_data);
        $this->assertEquals($data, $fresh->cart_data);
    }
}
