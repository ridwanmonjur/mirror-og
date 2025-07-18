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
        return $this->items()->sum('quantity');
    }

    public function getSubTotal()
    {
        return $this->items()->sum('subtotal');
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function getContent()
    {
        return $this->items()->with('product')->get();
    }
}
