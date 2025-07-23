<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\NewCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\CartService;

class ShopService
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    public function getProductsWithFilters(Request $request)
    {
        $categories = Category::all();
        
        if ($request->category && $request->category !== 'all') {
            $products = Product::with('categories')->whereHas('categories', function ($query) use ($request) {
                $query->where('slug', $request->category);
            });
            $categoryName = optional($categories->where('slug', $request->category)->first())->name;
        } elseif (!$request->category || $request->category === 'all' || $request->category === '') {
            $products = Product::with('categories');
            $categoryName = 'All Categories';
        } else {
            $products = Product::where('featured', true);
            $categoryName = 'Featured';
        }

        $products = $this->applySorting($products, $request->sort);

        return [
            'products' => $products,
            'categories' => $categories,
            'categoryName' => $categoryName,
        ];
    }

    private function applySorting($products, $sort)
    {
        switch ($sort) {
            case 'low_high':
                return $products->orderBy('price')->simplePaginate();
            case 'high_low':
                return $products->orderBy('price', 'desc')->simplePaginate();
            case 'Newest':
                return $products->orderBy('id', 'desc')->simplePaginate();
            case 'price_50_to_100':
                return Product::where('price', '>=', 50)
                    ->where('price', '<=', 100)
                    ->orderBy('id', 'desc')
                    ->simplePaginate();
            case 'price_100_to_150':
                return Product::where('price', '>=', 100)
                    ->where('price', '<=', 150)
                    ->orderBy('id', 'desc')
                    ->simplePaginate();
            case 'price_150_or_more':
                return Product::where('price', '>=', 150)
                    ->orderBy('id', 'desc')
                    ->simplePaginate();
            case 'price_less_than_50':
                return Product::where('price', '<=', 50)
                    ->orderBy('id', 'desc')
                    ->simplePaginate();
            default:
                return $products->simplePaginate();
        }
    }

    public function getProductDetails(string $slug)
    {
        $product = Product::with('productVariants')->where('slug', $slug)->firstOrFail();
        $products_sa = Product::orderBy('id', 'DESC')->simplePaginate();

        return [
            'product' => $product,
            'products_sa' => $products_sa,
        ];
    }

    public function searchProducts(string $query)
    {
        return Product::search($query)->simplePaginate();
    }

    public function getUserCart(int $userId)
    {
        return $this->cartService->getUserCart($userId);
    }

    public function getCartData(int $userId)
    {
        $cart = $this->getUserCart($userId);
        $discount = 0;
        $cartTotal = $cart->getTotal();
        
        return [
            'cart' => $cart,
            'discount' => $discount,
            'newSubtotal' => $cartTotal,
            'newTotal' => $cartTotal - $discount,
        ];
    }

    public function addItemToCart(int $userId, Product $product, $variantIds = null, $quantity = 1)
    {
        $cart = $this->getUserCart($userId);
        
        try {
            if ($quantity <= 0) {
                return ['success' => false, 'message' => 'Quantity must be greater than 0.'];
            }

            if ($variantIds && is_array($variantIds)) {
                foreach ($variantIds as $variantId) {
                    $variant = $product->productVariants()->find($variantId);
                    if (!$variant) {
                        return ['success' => false, 'message' => 'Selected variant not found.'];
                    }
                    if ($variant->stock <= 0) {
                        return ['success' => false, 'message' => "Product variant '{$variant->name}: {$variant->value}' is out of stock."];
                    }
                    if ($variant->stock < $quantity) {
                        return ['success' => false, 'message' => "Insufficient stock for variant '{$variant->name}: {$variant->value}'. Available: {$variant->stock}, Requested: {$quantity}"];
                    }
                }
            }
            
            $this->cartService->addItem($cart, $product->id, $quantity, $product->price, $variantIds);
            return ['success' => true, 'message' => 'Item was added to your cart!'];
        } catch (\Exception $e) {
            Log::error($e);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function updateCartItem(int $userId, int $productId, int $quantity, int $maxStock = null)
    {
        $cart = $this->getUserCart($userId);
        
        if (!$cart) {
            return ['success' => false, 'message' => 'Cart not found'];
        }

        if ($quantity < 1 || $quantity > 20) {
            return ['success' => false, 'message' => 'Quantity must be between 1 and 20.'];
        }

        if ($maxStock !== null && $quantity > $maxStock) {
            return ['success' => false, 'message' => 'We currently do not have enough items in stock.'];
        }

        $product = Product::find($productId);
        if ($product) {
            $this->cartService->updateItem($cart, $productId, $quantity, $product->price);
            return ['success' => true, 'message' => 'Quantity was updated successfully!'];
        }
        
        return ['success' => false, 'message' => 'Product not found'];
    }

    public function updateCartItemById(int $userId, int $cartItemId, int $quantity, int $maxStock)
    {
        $cart = $this->getUserCart($userId);
        
        if (!$cart) {
            return ['success' => false, 'message' => 'Cart not found'];
        }

        if ($quantity < 1 || $quantity > 20) {
            return ['success' => false, 'message' => 'Quantity must be between 1 and 20.'];
        }

        if ($quantity > $maxStock) {
            return ['success' => false, 'message' => 'We currently do not have enough items in stock.'];
        }

        $cartItem = $cart->items()->find($cartItemId);
        if (!$cartItem) {
            return ['success' => false, 'message' => 'Cart item not found'];
        }

        $product = $cartItem->product;
        if ($product) {
            $cartItem->quantity = $quantity;
            $cartItem->subtotal = $quantity * $product->price;
            $cartItem->save();
            $this->cartService->updateTotal($cart);
            return ['success' => true, 'message' => 'Quantity was updated successfully!'];
        }
        
        return ['success' => false, 'message' => 'Product not found'];
    }

    public function removeItemFromCart(int $userId, int $productId)
    {
        $cart = $this->getUserCart($userId);
        
        if ($cart) {
            $this->cartService->removeItem($cart, $productId);
            return ['success' => true, 'message' => 'Item has been removed!'];
        }
        
        return ['success' => false, 'message' => 'Cart not found'];
    }

    public function removeItemFromCartById(int $userId, int $cartItemId)
    {
        $cart = $this->getUserCart($userId);
        
        if ($cart) {
            $cart->items()->where('id', $cartItemId)->delete();
            $this->cartService->updateTotal($cart);
            return ['success' => true, 'message' => 'Item has been removed!'];
        }
        
        return ['success' => false, 'message' => 'Cart not found'];
    }
}