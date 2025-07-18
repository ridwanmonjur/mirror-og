<?php

use Carbon\Carbon;



function presentDate($date)
{
    return Carbon::parse($date)->format('M d, Y');
}

function setActiveCategory($category, $output = 'active')
{
    return request()->category == $category ? $output : '';
}

function productImage($path)
{
    return $path && file_exists('storage/'.$path) ? asset('storage/'.$path) : asset('img/not-found.jpg');
}

function getNumbers()
{
    $discount = session()->get('coupon')['discount'] ?? 0;
    $code = session()->get('coupon')['name'] ?? null;
    
    $userId = auth()->id();
    $cart = $userId ? \App\NewCart::getUserCart($userId) : null;
    
    $cartSubtotal = $cart ? $cart->getSubTotal() : 0;
    $newSubtotal = ($cartSubtotal - $discount);
    if ($newSubtotal < 0) {
        $newSubtotal = 0;
    }
    $newTotal = $newSubtotal;

    return collect([
        'discount' => $discount,
        'code' => $code,
        'newSubtotal' => $newSubtotal,
        'newTotal' => $newTotal,
    ]);
}

function getStockLevel($quantity)
{
    $stockThreshold = config('app.stock_threshold', 5);
    
    if ($quantity > $stockThreshold) {
        $stockLevel = '<div class="badge badge-success">In Stock</div>';
    } elseif ($quantity <= $stockThreshold && $quantity > 0) {
        $stockLevel = '<div class="badge badge-warning">Low Stock</div>';
    } else {
        $stockLevel = '<div class="badge badge-danger">Not available</div>';
    }

    return $stockLevel;
}

function getValidCartQuantity()
{
    $validQuantity = 0;
    $userId = auth()->id();
    $cart = $userId ? \App\NewCart::getUserCart($userId) : null;
    
    if ($cart) {
        foreach ($cart->getContent() as $item) {
            if ($item->product) {
                $validQuantity += $item->quantity;
            }
        }
    }
    return $validQuantity;
}

