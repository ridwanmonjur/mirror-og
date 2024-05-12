<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'user_address';
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['addressLine1', 'addressLine2', 'city', 'country', 'user_id'];
}
