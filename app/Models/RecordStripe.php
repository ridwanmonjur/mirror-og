<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RecordStripe extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'payment_id',
        'saved_card_id',
        'currency',
        'payment_status',
        'payment_amount',
        'brand',
        'last4',
        'exp_month',
        'exp_year',
        'metadata',
    ];

    protected $table = 'stripe_transactions';

    public static function createTransaction(
        \Stripe\PaymentIntent $paymentIntent,
        \Stripe\PaymentMethod $paymentMethod,
        int $userId,
        ?string $setAsDefault = 'false',
        ?string $saveHistory = 'false'
    ) {

        if ($saveHistory == 'true') {
            $existingCard = DB::table('saved_cards')
                ->where('user_id', $userId)
                ->where('fingerprint', $paymentMethod->card->fingerprint)
                ->first();

            if ($setAsDefault == 'true') {
                DB::table('saved_cards')
                    ->where('user_id', $userId)
                    ->update(['is_default' => false]);

                if ($existingCard) {
                    DB::table('saved_cards')
                        ->where('user_id', $userId)
                        ->where('fingerprint', $paymentMethod->card->fingerprint)
                        ->update(['is_default' => true]);
                }
            }

            $savedCardId = null;
            if ($existingCard) {
                $savedCardId = $existingCard->id;
            } else {
                $savedCardId = DB::table('saved_cards')->insertGetId([
                    'user_id' => $userId,
                    'stripe_payment_method_id' => $paymentMethod->id,
                    'brand' => $paymentMethod->card->brand,
                    'last4' => $paymentMethod->card->last4,
                    'exp_month' => $paymentMethod->card->exp_month,
                    'fingerprint' => $paymentMethod->card->fingerprint,
                    'exp_year' => $paymentMethod->card->exp_year,
                    'is_default' => $setAsDefault == 'true' ? true : false,
                    'created_at' => DB::raw('NOW()'),
                    'updated_at' => DB::raw('NOW()'),
                ]);
            }

            DB::table('saved_payments')->insertGetId([
                'payment_intent_id' => $paymentIntent->id,
                'user_id' => $userId,
                'saved_card_id' => $savedCardId,
                'currency' => strtoupper($paymentIntent->currency),
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount / 100, // Convert from cents
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ]);
        }

        // 3. Create the main transaction record
        $transactionData = [
            'user_id' => $userId,
            'payment_id' => $paymentIntent->id,
            'currency' => strtoupper($paymentIntent->currency),
            'payment_status' => $paymentIntent->status,
            'payment_amount' => $paymentIntent->amount / 100, // Convert from cents
            'brand' => $paymentMethod->card->brand,
            'last4' => $paymentMethod->card->last4,
            'exp_month' => $paymentMethod->card->exp_month,
            'exp_year' => $paymentMethod->card->exp_year,
            'metadata' => json_encode($paymentIntent->metadata ?? []),
        ];

        $transaction = self::create($transactionData);

        return $transaction;

    }
}
