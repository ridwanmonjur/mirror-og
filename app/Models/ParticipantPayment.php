<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipantPayment extends Model
{
    protected $table = 'participant_payments';

    public function members()
    {
        return $this->belongsTo(TeamMember::class);
    }
}
