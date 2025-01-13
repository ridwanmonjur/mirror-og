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
    public $timestamps = false;  // Add this line


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
