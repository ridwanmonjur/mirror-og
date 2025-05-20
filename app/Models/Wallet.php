<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    use HasFactory;

    protected $table = 'user_wallet';
    protected $fillable = [
        'usable_balance',
        'current_balance',
        'user_id',
        'stripe_connect_id',
        'payouts_enabled',
        'details_submitted',
        'charges_enabled',
        'has_bank_account',
        'bank_last4',
        'bank_name',
        'balance',
        'last_payout_at'
    ];

    public $timestamps = NULL;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'payouts_enabled' => 'boolean',
        'details_submitted' => 'boolean',
        'charges_enabled' => 'boolean',
        'has_bank_account' => 'boolean',
        'balance' => 'decimal:2',
        'last_payout_at' => 'datetime',
    ];

    public function isReadyForPayouts()
    {
        return $this->payouts_enabled && $this->has_bank_account;
    }

}
