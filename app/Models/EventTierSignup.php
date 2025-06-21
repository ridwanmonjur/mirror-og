<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTierSignup extends Model
{
    use HasFactory;

    protected $table = 'event_tier_type_signup_dates';

    protected $fillable = [
        'tier_id',
        'type_id',
        'signup_open',
        'signup_close',
        'normal_signup_start_advanced_close'
    ];

    public $timestamps = NULL;

    /**
     * Get the tier that this signup belongs to
     */
    public function tier(): BelongsTo
    {
        return $this->belongsTo(EventTier::class);
    }

    /**
     * Get the type that this signup belongs to
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }
}
