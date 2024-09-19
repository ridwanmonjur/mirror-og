<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['payment_id', 'payment_status', 'payment_amount'];

    protected $table = 'all_payment_transactions';

    public static function createTransaction(
        string| int $paymentId,
        string $paymentStatus,
        float $paymentAmount
    ): ?self {
        return self::create([
            'payment_id' => $paymentId,
            'payment_status' => $paymentStatus,
            'payment_amount' => $paymentAmount,
        ]);
    }

}
