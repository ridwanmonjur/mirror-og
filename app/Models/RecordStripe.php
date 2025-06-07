<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RecordStripe extends Model
{
    use HasFactory;
    public const UPDATED_AT = null;

    protected $fillable = ['payment_id', 'payment_status', 'payment_amount', 'created_at'];

    protected $table = 'stripe_transactions';

    public static function createTransaction(
        string| int| null $paymentId,
        string $paymentStatus,
        float $paymentAmount,
    ): ?self {
        return self::create([
            'payment_id' => $paymentId,
            'payment_status' => $paymentStatus,
            'payment_amount' => $paymentAmount,
            'created_at' => DB::raw('NOW()')
        ]);
    }

}
