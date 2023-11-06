<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'eventTags' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function eventDetail()
    {
         return $this->hasOne(EventDetail::class, 'id');
   }

    // public function eventCategory()
    //  {
    //      return $this->hasOne(EventCategory::class, 'id');
    //  }

}
