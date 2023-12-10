<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }
    public function participant()
    {
        return $this->belongsTo(User::class, 'participant_id');
    }
    public function event()
    {
        return $this->belongsTo(EventDetail::class, 'event_id');
    }
}
