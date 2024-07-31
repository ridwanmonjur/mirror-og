<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'user_address';

    protected $fillable = ['addressLine1', 'addressLine2', 'city', 'country', 'user_id'];
}
