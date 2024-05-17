<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTier extends Model
{
    protected $fillable = ['eventTier', 'tierIcon', 'tierTeamSlot', 'tierPrizePool', 'tierEntryFee'];

    protected $table = 'event_tier';

    public function tier()
    {
        return $this->belongsTo(EventTier::class, 'event_tier_id');
    }
}
