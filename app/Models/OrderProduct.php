<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $table = 'order_product';

    protected $fillable = ['order_id', 'product_id', 'quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderProductVariants()
    {
        return $this->belongsToMany(ProductVariant::class, 'order_item_product_variants', 'order_product_id', 'variant_id');
    }
}
