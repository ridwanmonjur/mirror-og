<?php

namespace App\Services;

use App\Models\NewCart;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\SystemCoupon;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;

class CartService
{
    public function updateTotal(NewCart $cart)
    {
        $cart->total = $cart->items()->sum('subtotal');
        $cart->cachedCount = $cart->items()->sum('quantity');
        $cart->save();
        return $cart->total;
    }

    public function getUserCart($userId)
    {
        return NewCart::firstOrCreate(['user_id' => $userId]);
    }

    public function addItem(NewCart $cart, $productId, $quantity, $price, $variantIds = null)
    {
        if ($quantity <= 0) {
            throw new Exception('Quantity must be greater than 0');
        }

        $existingItem = null;
        
        if ($variantIds && is_array($variantIds)) {
            sort($variantIds);
            $variantIdsCount = count($variantIds);
            
            $existingItem = $cart->items()
                ->where('product_id', $productId)
                ->whereHas('cartProductVariants', function ($query) use ($variantIds) {
                    $query->whereIn('variant_id', $variantIds);
                }, '=', $variantIdsCount)
                ->whereDoesntHave('cartProductVariants', function ($query) use ($variantIds) {
                    $query->whereNotIn('variant_id', $variantIds);
                })
                ->first();
        } else {
            $existingItem = $cart->items()
                ->where('product_id', $productId)
                ->whereDoesntHave('cartProductVariants')
                ->first();
        }
        
        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;
            if ($newQuantity > 20) {
                throw new Exception('Maximum quantity of 20 exceeded');
            }
            $existingItem->quantity = $newQuantity;
            $existingItem->subtotal = $existingItem->quantity * $price;
            $existingItem->save();
        } else {
            if ($quantity > 20) {
                throw new Exception('Maximum quantity of 20 exceeded');
            }
            $cartItem = $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'subtotal' => $quantity * $price
            ]);
            
            if ($variantIds && is_array($variantIds)) {
                $cartItem->cartProductVariants()->attach($variantIds);
            }
        }
        
        $this->updateTotal($cart);
        return $cart;
    }

    public function updateItem(NewCart $cart, $productId, $quantity, $price)
    {
        $item = $cart->items()->where('product_id', $productId)->first();
        
        if ($item) {
            $item->quantity = $quantity;
            $item->subtotal = $quantity * $price;
            $item->save();
            $this->updateTotal($cart);
            $cart->load(['items.product']);
        }
        
        return $cart;
    }

    public function removeItem(NewCart $cart, $productId)
    {
        $cart->items()->where('product_id', $productId)->delete();
        $this->updateTotal($cart);
        return $cart;
    }

    public function clearItems(NewCart $cart)
    {
        $cart->items()->delete();
        $this->updateTotal($cart);
        return $cart;
    }

    public function clearItemsAndDecreaseStock(NewCart $cart)
    {
        try {
            foreach ($cart->getContent() as $item) {
                if ($item->cartProductVariants) {
                    foreach ($item->cartProductVariants as $variant) {
                        $variant->update(['stock' => $variant->stock - $item->quantity]);
                    }
                }
            }
            
            $cart->items()->delete();
            $this->updateTotal($cart);
            return $cart;
        } catch (Exception $e) {
            Log::error('Error clearing cart and decreasing stock: ' . $e->getMessage());
            throw $e;
        }
    }


    
    public function validateStock(NewCart $cart)
    {
        try {
            foreach ($cart->getContent() as $item) {
                if ($item->cartProductVariants) {
                    foreach ($item->cartProductVariants as $variant) {
                        if ($variant->stock <= 0) {
                            throw new Exception("Product '{$item->product_id}' with attribute '{$variant->name}: {$variant->value}' is out of stock");
                        }
                        if ($variant->stock < $item->quantity) {
                            throw new Exception("Insufficient stock for '{$item->product_id}' with attribute '{$variant->name}: {$variant->value}'. Available: {$variant->stock}, Requested: {$item->quantity}");
                        }
                    }
                } 
            }
            return true;
        } catch (Exception $e) {
            Log::error('Cart stock validation error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function addToOrdersTables(NewCart $cart, User $user, ?SystemCoupon $coupon, array $fee)
    {
        try {
            $order = Order::create([
                'user_id' => $user->id,
                'billing_discount' => $fee['discount'] ?? 0,
                'billing_discount_code' => $coupon->code ?? 0,
                'billing_subtotal' => $fee['discount'] ?? 0,
                'billing_total' => $fee['finalFee'] ?? 0,
            ]);

            foreach ($cart->getContent() as $item) {
                $orderProduct = OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                ]);

                if ($item->cartProductVariants) {
                    $variantIds = $item->cartProductVariants->pluck('id')->toArray();
                    $orderProduct->orderProductVariants()->attach($variantIds);
                }
            }

            return $order;
        } catch (Exception $e) {
            Log::error('Shop order creation error: ' . $e->getMessage());
            throw $e;
        }
    }
}