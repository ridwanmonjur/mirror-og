<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = [
        'name', 'slug', 'details', 'price', 'description',
        'image', 'images', 'featured', 'isPhysical',
    ];

    protected $casts = [
        'images' => 'array',
        'featured' => 'boolean',
        'isPhysical' => 'boolean',
    ];

    protected $perPage = 12;

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category');
    }

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

}
