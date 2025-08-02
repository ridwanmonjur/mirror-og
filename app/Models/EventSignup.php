<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventSignup extends Model
{
    use HasFactory;

    protected $table = 'event_signup_dates';

    protected $fillable = [
        'event_id',
        'signup_open',
        'normal_signup_start_advanced_close',
        'signup_close',
    ];

    public $timestamps = null;

    /**
     * Get the event details that this signup belongs to
     */
    public function eventDetails(): BelongsTo
    {
        return $this->belongsTo(EventDetail::class, 'event_id');
    }
}
