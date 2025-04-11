<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventTier extends Model
{
    use HasFactory;

    protected $fillable = ['eventTier', 'tierIcon', 'tierTeamSlot', 'tierPrizePool', 'tierEntryFee'];

    protected $table = 'event_tier';
    public $timestamps = false;  // Add this line

    public static function scopeByUserOrNullUser(Builder $query, $userId)
    {
        return $query->where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhereNull('user_id');
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function tierSignups(): HasMany
    {
        return $this->hasMany(EventTierSignup::class, 'tier_id');
    }
}
