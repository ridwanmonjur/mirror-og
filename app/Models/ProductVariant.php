<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'name', 'value', 'stock'
    ];

    protected $casts = [
        'stock' => 'integer'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItems()
    {
        return $this->belongsToMany(CartItem::class, 'cart_item_product_variants', 'variant_id', 'cart_item_id');
    }
}
