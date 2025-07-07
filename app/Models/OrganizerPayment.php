<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizerPayment extends Model
{
    protected $table = 'organizer_payments';

    protected $fillable = [
        'payment_amount',
        'discount_amount',
        'user_id',
        'history_id',
        'payment_id',
    ];

    /**
     * Get the user who received the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    /**
     * Get the transaction history associated with the payment.
     */
    public function history(): BelongsTo
    {
        return $this->belongsTo(TransactionHistory::class, 'history_id');
    }

    /**
     * Get the stripe transaction associated with the payment.
     */
    // public function stripeTransaction(): BelongsTo
    // {
    //     return $this->belongsTo(RecordStripe::class, 'payment_id');
    // }
}
