<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'withdrawal',
        'status',
        'requested_at',
    ];

    protected $casts = [
        'withdrawal' => 'decimal:2',
        'requested_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    const STATUS_COMPLETED = 'completed';

    // Business rules constants
    const MIN_AMOUNT = 5.00;

    const MAX_DAILY_AMOUNT = 5000.00;

    const MAX_TRANSACTION_AMOUNT = 5000.00;

    const PROCESSING_DAYS = 7;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('requested_at', today());
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
        ]);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'RM '.number_format($this->withdrawal, 2);
    }

    public static function checkDailyLimit($userId, $amount): bool
    {
        $todayTotal = self::where('user_id', $userId)
            ->today()
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_COMPLETED])
            ->sum('withdrawal');

        return ($todayTotal + $amount) <= self::MAX_DAILY_AMOUNT;
    }

    public static function getRemainingDailyLimit($userId): float
    {
        $todayTotal = self::where('user_id', $userId)
            ->today()
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_COMPLETED])
            ->sum('withdrawal');

        return max(0, self::MAX_DAILY_AMOUNT - $todayTotal);
    }

    public $timestamps = null;
}
