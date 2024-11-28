<?php

namespace App\Http\Requests\Match;

use App\Models\EventDetail;
use App\Models\JoinEvent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Exception;

class DiscountCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public ?EventDetail $event;
    public float $total;
    public float $discount_applied_amount;
    public float $participantPaymentSum;
    public ?JoinEvent $joinEvent;

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
            $is_complete_payment = true;
            $this->discount_applied_amount = $this->amount;
            $joinEvent = JoinEvent::select('id', 'event_details_id', 'payment_status')
                ->findOrFail($this->joinEventId);

            $event = EventDetail::select(['id', 'event_tier_id'])
                ->where('id', $joinEvent->event_details_id)
                ->with('tier')->first();

            $total = (float) $event->tier->tierEntryFee;
            $this->total = $total;
            $participantPaymentSum = DB::table('participant_payments')
                ->where('join_events_id', $this->joinEventId)
                ->sum('payment_amount');

            $user = $this->attributes->get('user');
            $userDiscount = DB::table('user_discounts')
                ->where('user_id', $user->id)
                ->first();

            if (!$userDiscount || $userDiscount->amount < 0) {
                $validator->errors()->add('discount', "Discount doesn't exist!");
                return;
            }

            if ($userDiscount->amount < $this->amount) {
                $is_complete_payment = false;
                $this->discount_applied_amount = min(
                    $userDiscount->amount,
                    ($total - $participantPaymentSum) - config("constants.STRIPE.MINIMUM_RM")
                );
            }
            
            $pendingBeforeDiscount = $this->total - $participantPaymentSum ;
            $pendingAfterDiscount = $pendingBeforeDiscount - $this->discount_applied_amount;

            if ($userDiscount->amount - $this->discount_applied_amount < 0) {
                $validator->errors()->add(
                    'amount',
                    "Not enough money in discount wallet!"
                );

                return;
            }

            if ($pendingBeforeDiscount < 0) {
                $validator->errors()->add(
                    'amount',
                    "You've already paid for this event!"
                );

                return;
            }

            if ($pendingAfterDiscount < -0.1) {
                $validator->errors()->add(
                    'amount',
                    "Have you already made another payment in another device/tab?"
                );

                return;
            }

            if ($this->discount_applied_amount < 0) {
                $validator->errors()->add('discount', "After adjusting for the next transaction, we cannot apply this discount!");
                return;    
            }
          
            $new_amount_to_pay_after_discount = $this->amount - $this->discount_applied_amount;

            $this->attributes->add([
                'user_discount' => $userDiscount,
                'new_amount_after_discount' => $new_amount_to_pay_after_discount,
                'complete_payment' => $is_complete_payment,
                'pending_total_after_discount' => $pendingAfterDiscount
            ]);

            $this->event = $event;
            $this->joinEvent = $joinEvent;
        });
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, redirect()
            ->route('participant.register.manage', ['id' => $this->teamId])            
            ->with('errorMessage', $validator->errors()->first()));

    }
}