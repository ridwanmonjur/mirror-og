<?php

namespace App\Services;

use App\Models\RecordStripe;
use App\Models\StripeConnection;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $stripeClient;

    public function __construct(StripeConnection $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function refundPaymentsForEvents(string| int $joinEventId, float $percentCapture): array
    {
        $paymentData = DB::table('join_events')
            ->where('join_events.id',  $joinEventId )
            ->join('participant_payments', 'join_events.id', '=', 'participant_payments.join_events_id')
            ->join('stripe_transactions', 'stripe_transactions.id', '=', 'participant_payments.payment_id')
            ->select('stripe_transactions.payment_id', 'stripe_transactions.id', 'stripe_transactions.payment_amount',  
                'stripe_transactions.payment_status', 'participant_payments.user_id')
            ->get()
            ->toArray();
        
        $updatedPayments = [];
        $summedDiscounts = [];
        $updatedDiscounts = [];

        foreach ($paymentData as $index => $item) {
            try {
                $userId = $item->user_id;
                $updatedPayments[] = [
                    'user_id' => $userId,
                    'payment_id' => $item->payment_id,
                    'couponed_amount' => 0,
                    'released_amount' => 0
                ];
                
                $capturedAmount = $item->payment_amount * $percentCapture;
                $refundedAmount = $item->payment_amount - $capturedAmount;
                
                $isRequiresCapture = $item->payment_status === 'requires_capture';
                
                if ($isRequiresCapture) {
                    $paymentIntent = $this->stripeClient->retrieveStripePaymentByPaymentId($item->payment_id);
                    if ($paymentIntent->status !== "requires_capture") {
                        continue;
                    }
                
                    $updatedPayments[$index]['payment_status'] = 'released';
                    $updatedPayments[$index]['released_amount'] = $refundedAmount;
                
                    $percentCapture != 0
                        ? $paymentIntent->capture(['amount_to_capture' => $item['payment_amount'] * 100 * $percentCapture])
                        : $paymentIntent->cancel();
                
                    $discountKey = 'released_amount';
                } else {
                    $updatedPayments[$index]['payment_status'] = 'couponed';
                    $updatedPayments[$index]['couponed_amount'] = $refundedAmount;
                    
                    $discountKey = 'couponed_amount';

                    $updatedDiscounts[] = [
                        'user_id' => $userId,
                        'amount' => $refundedAmount
                    ];
                }

                if (!isset($summedDiscounts[$userId])) {
                    $summedDiscounts[$userId] = [];
                }

                if (!isset($summedDiscounts[$userId])) {
                    $summedDiscounts[$userId] = [
                        $discountKey => $refundedAmount
                    ];
                } else if (!isset($summedDiscounts[$userId][$discountKey])){
                    $summedDiscounts[$userId][$discountKey] = $refundedAmount;
                } else {
                    $summedDiscounts[$userId][$discountKey] = 
                        $summedDiscounts[$userId][$discountKey] +  $refundedAmount;
                }
            } catch (Exception $e) {
                Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            }
        }


        if (!empty($updatedPayments)) {
            DB::beginTransaction();
            try {
                foreach ($updatedPayments as $payment) {
                    RecordStripe::where('payment_id', $payment['payment_id'])
                        ->update(['payment_status' => $payment['payment_status']]);
                }
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            }
        }

        if (!empty($updatedDiscounts)) {
            DB::beginTransaction();
            try {

                foreach ($updatedDiscounts as $discount) {
                    $amount = $discount['amount'];

                    $userWallet = DB::table('user_wallet')->where('user_id', $discount['user_id'])->first();

                    if ($userWallet) {
                        DB::table('user_wallet')
                            ->where('user_id', $discount['user_id'])
                            ->update(['usable_balance' => $userWallet->usable_balance + $amount]);
                    } else {
                        DB::table('user_wallet')->insert([
                            'usable_balance' => $amount,
                            'user_id' => $discount['user_id']
                        ]);
                    }
                }
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            }
        }

        $paymentData = DB::table('join_events')
            ->where('id', $joinEventId)     
            ->update(['join_status' => 'canceled']);

        return $summedDiscounts;
    }
}