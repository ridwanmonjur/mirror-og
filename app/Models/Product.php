<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use Searchable;

    protected $fillable = [
        'name', 'slug', 'details', 'price', 'description', 
        'image', 'images', 'featured', 'isPhysical'
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

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        $extraFields = [
            'categories' => $this->categories->pluck('name')->toArray(),
        ];

        return array_merge($array, $extraFields);
    }
    
    public function shouldBeSearchable()
    {
        return true;
    }

}
