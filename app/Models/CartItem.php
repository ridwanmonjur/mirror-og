<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'cart_id', 'product_id', 'quantity', 'subtotal'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2'
    ];

    public function cart()
    {
        return $this->belongsTo('App\Models\NewCart', 'cart_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function cartProductVariants()
    {
        return $this->belongsToMany(ProductVariant::class, 'cart_item_product_variants', 'cart_item_id', 'variant_id');
    }
}
