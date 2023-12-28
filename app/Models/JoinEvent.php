<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JoinEvent extends Model
{
    use HasFactory;
    protected $table = 'join_events';

    public function eventDetail()
    {
        return $this->belongsTo(EventDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function users()
    {
        return $this->belongsToMany(User::class, 'users');
    }

    public function eventDetails()
    {
        return $this->belongsTo(EventDetail::class, 'event_details_id', 'id');
    }


}
