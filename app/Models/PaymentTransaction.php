<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = ['payment_id', 'payment_status']; 
    protected $table = 'payment_transactions';

    public static function createTransaction(
        $paymentId, $paymentStatus, $paymentAmount
    ) {
        return self::create([
            'payment_id' => $paymentId,
            'payment_status' => $paymentStatus,
            'payment_amount' => $paymentAmount
        ]);
    }
}
