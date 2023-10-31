<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['name', 'status', 'venue', 'caption'];
    protected $table = 'events';

    // public function eventCategory()
    // {
    //     return $this->hasOne(EventCategory::class, 'event_id');
    // }
    // public function eventDetail()
    // {
    //     return $this->hasOne(EventDetail::class, 'event_id');
    // }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
