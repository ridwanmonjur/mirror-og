<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTier extends Model
{
    protected $fillable = ['eventTier', 'tierIcon', 'tierTeamSlot', 'tierPrizePool', 'tierEntryFee'];
    protected $table = 'event_tier';
}
