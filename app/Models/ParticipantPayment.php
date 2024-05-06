<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipantPayment extends Model
{
    protected $table = 'participant_payments';
    protected $fillable = [
        'team_members_id',
        'user_id',
        'join_events_id',
        'payment_amount',
        'payment_id'
    ];

    public function members()
    {
        return $this->belongsTo(TeamMember::class);
    }
}
