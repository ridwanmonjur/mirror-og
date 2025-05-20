<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipantPayment extends Model
{
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
    
    protected $table = 'participant_payments';
    protected $fillable = [
        'team_members_id',
        'user_id',
        'join_events_id',
        'payment_amount',
        'payment_id',
    ];

    public function members(): BelongsTo
    {
        return $this->belongsTo(TeamMember::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(RecordStripe::class, 'payment_id', 'id');
    }
}
