<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewCart extends Model
{
    use HasFactory;
    
    protected $table = 'final_carts';
    
    protected $fillable = [
        'user_id', 'total'
    ];

    protected $casts = [
        'total' => 'decimal:2'
    ];

    protected $cachedCount = null;

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function items()
    {
        return $this->hasMany('App\CartItem', 'cart_id');
    }

    public function updateTotal()
    {
        $this->total = $this->items()->sum('subtotal');
        $this->cachedCount = null; // Clear cache when items change
        $this->save();
        return $this->total;
    }

    public static function getUserCart($userId)
    {
        return static::firstOrCreate(['user_id' => $userId]);
    }

    public function addItem($productId, $quantity, $price)
    {
        $existingItem = $this->items()->where('product_id', $productId)->first();
        
        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;
            if ($newQuantity > 20) {
                throw new \Exception('Maximum quantity of 20 exceeded');
            }
            $existingItem->quantity = $newQuantity;
            $existingItem->subtotal = $existingItem->quantity * $price;
            $existingItem->save();
        } else {
            if ($quantity > 20) {
                throw new \Exception('Maximum quantity of 20 exceeded');
            }
            $this->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'subtotal' => $quantity * $price
            ]);
        }
        
        $this->updateTotal();
        return $this;
    }

    public function updateItem($productId, $quantity, $price)
    {
        $item = $this->items()->where('product_id', $productId)->first();
        
        if ($item) {
            $item->quantity = $quantity;
            $item->subtotal = $quantity * $price;
            $item->save();
            $this->updateTotal();
            $this->load(['items.product']); // Reload relationships after changes
        }
        
        return $this;
    }

    public function removeItem($productId)
    {
        $this->items()->where('product_id', $productId)->delete();
        $this->updateTotal();
        return $this;
    }

    public function clearItems()
    {
        $this->items()->delete();
        $this->updateTotal();
        return $this;
    }

    public function getCount()
    {
        if ($this->cachedCount === null) {
            $this->cachedCount = $this->items()->sum('quantity');
        }
        return $this->cachedCount;
    }

    public function getSubTotal()
    {
        return $this->total ?? $this->items()->sum('subtotal');
    }

    public function getTotal()
    {
        return $this->total ?? $this->getSubTotal();
    }

    public function getContent()
    {
        if ($this->relationLoaded('items')) {
            return $this->items;
        }
        
        return $this->items()->with('product')->get();
    }

    /**
     * Get cart numbers including discount calculations
     */
    public function getNumbers()
    {
        $discount = session()->get('coupon')['discount'] ?? 0;
        $code = session()->get('coupon')['name'] ?? null;
        
        $cartSubtotal = $this->getSubTotal();
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

    /**
     * Get valid cart quantity (items with available products)
     */
    public function getValidQuantity()
    {
        $validQuantity = 0;
        
        foreach ($this->getContent() as $item) {
            if ($item->product) {
                $validQuantity += $item->quantity;
            }
        }
        
        return $validQuantity;
    }
}
