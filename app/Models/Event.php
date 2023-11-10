<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['esport_title', 'event_type', 'event_tier', 'event_region'];
}
