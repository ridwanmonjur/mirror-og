<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Captain extends Model
{
    protected $fillable = ['eventID', 'userID', 'isCaptain'];
}
