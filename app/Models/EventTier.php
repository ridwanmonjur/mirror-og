<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTier extends Model
{
    use HasFactory;

    protected $fillable = ['eventTier', 'tierIcon', 'tierTeamSlot', 'tierPrizePool', 'tierEntryFee'];

    protected $table = 'event_tier';
}
