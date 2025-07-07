<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTierPrize extends Model
{
    use HasFactory;

    protected $table = 'event_tier_prize';

    protected $fillable = [
        'event_tier_id',
        'position',
        'prize_sum',
    ];

    protected $casts = [
        'position' => 'integer',
        'prize_sum' => 'decimal:2',
    ];

    public function eventTier(): BelongsTo
    {
        return $this->belongsTo(EventTier::class);
    }

    public $timestamps = false;
}
