<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewCart extends Model
{
    use HasFactory;

    protected $table = 'final_carts';

    protected $fillable = [
        'user_id', 'total',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public $cachedCount = null;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function items()
    {
        return $this->hasMany('App\Models\CartItem', 'cart_id');
    }

    public static function getUserCart($userId)
    {
        return static::firstOrCreate(['user_id' => $userId]);
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

        $this->load(['items.product', 'items.cartProductVariants']);

        return $this->items;
    }

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
}
