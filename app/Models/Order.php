<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'billing_discount', 'billing_discount_code', 'billing_subtotal', 'billing_total',
    ];


    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product')->withPivot('quantity');
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }
}
