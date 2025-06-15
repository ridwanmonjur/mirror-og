<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryRegion extends Model
{
    use HasFactory;

    public $timestamps = NULL;

    protected $table = 'countries_and_regions';
    
    protected $fillable = [
        'name',
        'emoji_flag',
        'type',
        'sort_order',
    ];

    // Scopes
   
}