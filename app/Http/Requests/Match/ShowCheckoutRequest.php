<?php

namespace App\Http\Requests\Match;

use App\Models\EventDetail;
use App\Models\RosterMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class ShowCheckoutRequest extends FormRequest
{
    public ?EventDetail $event;
    public float $total;
    public float $participantPaymentSum;
    public string|int $id;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teamId' => 'required|exists:teams,id',
            'teamName' => 'required|string',
            'joinEventId' => 'required|exists:join_events,id',
            'memberId' => 'required',
            'amount' => 'required|numeric|min:0',
        ];
    }



    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateRosterMembership($validator);
            $this->validatePaymentAmount($validator);
        });
    }

    private function validateRosterMembership($validator)
    {
        $user = $this->attributes->get('user');
        $this->id = $this->input('id');
        $rosterMember = RosterMember::where([
            'user_id' => $user->id,
            'join_events_id' => $this->joinEventId,
            'team_id' => $this->teamId,
        ])->exists();

        if (!$rosterMember) {
            $validator->errors()->add(
                'roster', 
                "You are a member of {$this->teamName}, but not a member of this event's roster."
            );

            return;
        }
    }

    private function validatePaymentAmount($validator)
    {
        $event = EventDetail::with([
                'tier', 'user', 'user.organizer', 'game',
                'signup:id,event_id,signup_open,normal_signup_start_advanced_close,signup_close'

            ])
            ->where('id', $this->id)->first();
        $this->event = $event;
        if (!$event) {
            $validator->errors()->add(
                'event', 
                "Event with id: {$this->id} can't be found!"
            );
            return;
        }

        $status = $event->getRegistrationStatus();
        if ($status == config('constants.SIGNUP_STATUS.CLOSED')) {
            $validator->errors()->add('time', "Regisration time is now over!");
            return;
        }
        
        if ($status == config('constants.SIGNUP_STATUS.TOO_EARLY')) {
            $validator->errors()->add('time', "Regisration time has not yet started!");
            return;
        }

        if (!$event->tier) {
            $validator->errors()->add(
                'tier', 
                "Event with id: {$this->id} has no event tier chosen"
            );
            return;
        }

        $total = $status == config('constants.SIGNUP_STATUS.EARLY')? (float)  $event->tier->earlyEntryFee : (float) $event->tier->tierEntryFee;
        $this->total = $total;
        $paymentOptionLower = config("constants.STRIPE.MINIMUM_RM");
        $paymentOptionHigher = $total - $paymentOptionLower;

        if ($this->amount < $paymentOptionLower) {
            $validator->errors()->add(
                'amount',
                "You have tried to pay RM $this->amount. If you don't pay full amount, you have to pay higher than RM $paymentOptionLower."
            );

            return;
        }

        $participantPaymentSum = DB::table('participant_payments')
            ->where('join_events_id', $this->joinEventId)
            ->sum('payment_amount');

        $pending = $total - ($participantPaymentSum + $this->amount);
        
        if ($pending > 0.1 && ($participantPaymentSum + $this->amount) > $paymentOptionHigher) {
            $minimum = ($total - $participantPaymentSum) - $paymentOptionLower ;
            $validator->errors()->add(
                'amount',
                "You have tried to pay RM $this->amount. If you don't pay full amount, you have to pay higher than RM $paymentOptionLower and lower than RM $minimum."
            );

            return;
        }

        $pendingBeforePayment = $this->total - $participantPaymentSum ;
        if ($pendingBeforePayment < 0) {
            $validator->errors()->add(
                'amount',
                "You've already paid for this event!"
            );

            return;
        }

        if ($pending < -0.1) {
            $validator->errors()->add(
                'amount',
                "You're paying too much for this event!"
            );

            return;
        }

        $this->total = $total;
        $this->participantPaymentSum = $participantPaymentSum;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, redirect()
            ->route('participant.register.manage', ['id' => $this->teamId])            
            ->with('errorMessage', $validator->errors()->first()));
    }

}
