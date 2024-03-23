<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventInvitation extends Model
{
    protected $table = 'event_invitations';

    use HasFactory;
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }
    public function team()
    {
        return $this->belongsTo(User::class, 'team_id');
    }
    public function event()
    {
        return $this->belongsTo(EventDetail::class, 'event_id');
    }
}
