<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BracketDeadlineSetup extends Model
{
    use HasFactory;

    protected $table = 'bracket_deadline_setup';

    public $timestamps = false;

    protected $fillable = [
        'tier_id',
        'type_id',
        'deadline_config',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'deadline_config' => 'array',
    ];

    /**
     * Get the event tier that owns this bracket deadline setup.
     */
    public function eventTier(): BelongsTo
    {
        return $this->belongsTo(EventTier::class, 'tier_id', 'id');
    }

    /**
     * Get the event type that owns this bracket deadline setup.
     */
    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class, 'type_id', 'id');
    }
}
