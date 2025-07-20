<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use Searchable;

    protected $fillable = [
        'name', 'slug', 'details', 'price', 'description', 
        'image', 'images', 'quantity', 'featured'
    ];

    protected $perPage = 2;


    public function categories()
    {
        return $this->belongsToMany('App\Category');
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
