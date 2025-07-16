<?php

namespace App\Http\Requests\Match;

use App\Models\EventDetail;
use App\Models\JoinEvent;
use App\Models\SystemCoupon;
use App\Models\Wallet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Exception;

class WalletCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public ?EventDetail $event;
    public float $total;
    public float $wallet_to_decrement;
    public float $participantPaymentSum;
    public ?JoinEvent $joinEvent;
    public string $status;
    public array $fee;
    public array $couponDetails = [
        'error' => null,
        'success' => null,
        'coupon' => null
    ];

    public function getStatus () {
        return $this->status;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required',
            'member_id' => 'required',
            'teamId' => 'required|exists:teams,id',
            'joinEventId' => 'required',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = $this->attributes->get('user');

            $is_complete_payment = true;
            // load coupon
            [$fee, $isCouponApplied, $error, $coupon] = SystemCoupon::loadCoupon($this->input('coupon_code'), $this->input('amount'), 0.0, 'participant', $user->id);
            $this->couponDetails = [
                'success' => $isCouponApplied,
                'error' => $error,
                'coupon' => $coupon
            ];
            
            // dd($fee);
            $this->wallet_to_decrement = $fee['finalFee'];
            $joinEvent = JoinEvent::select('id', 'event_details_id', 'payment_status')
                ->findOrFail($this->joinEventId);

            $event = EventDetail
                ::select(['id', 'eventName', 'event_tier_id', 'event_type_id', 'event_category_id'])
                ->where('id', $joinEvent->event_details_id)
                ->with(['tier', 'type', 'game',
                    'signup:id,event_id,signup_open,normal_signup_start_advanced_close,signup_close'
                ])->first();
            
            $status = $event->getRegistrationStatus();
            if ($status == config('constants.SIGNUP_STATUS.CLOSED')) {
                $validator->errors()->add('time', "Regisration time is now over!");
                return;
            }
            
            if ($status == config('constants.SIGNUP_STATUS.TOO_EARLY')) {
                $validator->errors()->add('time', "Regisration time has not yet started!");
                return;
            }

            $this->status = $status;
            $this->event = $event;
            $this->joinEvent = $joinEvent;


            $total = $status == config('constants.SIGNUP_STATUS.EARLY')? (float) $event->tier->earlyEntryFee : (float) $event->tier->tierEntryFee;
            $this->total = $total;
            $participantPaymentSum = DB::table('participant_payments')
                ->where('join_events_id', $this->joinEventId)
                ->sum('payment_amount');

            $userWallet = Wallet::retrieveOrCreateCache($user->id);

            if (!$userWallet ||  $userWallet->usable_balance < 0) {
                $validator->errors()->add('wallet', "Your w!");
                return;
            }

            if ( $userWallet->usable_balance < $this->amount ) {
                $is_complete_payment = false;
                $this->wallet_to_decrement = min(
                     $userWallet->usable_balance,
                    ($total - $participantPaymentSum) - config("constants.STRIPE.MINIMUM_RM")
                );
            }
            
            $pendingBeforeWallet = $this->total - $participantPaymentSum ;
            $pendingAfterWallet = $pendingBeforeWallet - $this->wallet_to_decrement;

            if ( $userWallet->usable_balance - $this->wallet_to_decrement < 0) {
                $validator->errors()->add(
                    'amount',
                    "Not enough money in your wallet!"
                );

                return;
            }

            if ($pendingAfterWallet <= config("constants.STRIPE.ZERO")) {
                $is_complete_payment = true;
            } else {
                $is_complete_payment = false;
            }

            if ($pendingBeforeWallet < 0) {
                $validator->errors()->add(
                    'amount',
                    "You've already paid for this event!"
                );

                return;
            }

            if ($pendingAfterWallet < -0.1) {
                $validator->errors()->add(
                    'amount',
                    "Have you already made another payment in another device/tab?"
                );

                return;
            }

            if ($this->wallet_to_decrement < 0) {
                $validator->errors()->add('wallet', "After adjusting for the next transaction, we cannot apply this payment with your wallet!");
                return;    
            }
          
            $stripe_after_wallet = $this->amount - $this->wallet_to_decrement;

            $this->attributes->add([
                'user_wallet' => $userWallet,
                'stripe_after_wallet' => $stripe_after_wallet,
                'complete_payment' => $is_complete_payment,
                'pending_total_after_wallet' => $pendingAfterWallet
            ]);

        });
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, redirect()
            ->route('participant.register.manage', ['id' => $this->teamId])            
            ->with('errorMessage', $validator->errors()->first()));

    }
}