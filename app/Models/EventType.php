<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    protected $fillable = ['eventType', 'eventDefinitions'];
    protected $table = 'event_type';
   

}
